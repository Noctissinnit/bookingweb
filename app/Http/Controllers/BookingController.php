<?php

namespace App\Http\Controllers;

use App\Mail\BookingApprovedMail;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Mail\InvitationMail;

class BookingController extends Controller
{
    public function list(Request $request)
    {
        $bookings = Booking::with('room')->with('user:id_user,nis');
        if($request->date) $bookings = $bookings->whereDate('date', $request->date);
        if($request->room_id) $bookings = $bookings->where('room_id', $request->room_id);
        
        $bookings = $bookings->get();
        
        return response()->json($bookings);
    }
    // Hanya menampilkan form booking untuk user biasa
    public function create(int $id)
    {
        $roomId = $id;
        $room = Room::where('id', $roomId)->first();
        $members = Member::all();
        return view("bookings.create", compact("room", "roomId", "members"));
    }

    public function login(Request $request)
    {
        $request->validate([
            "nis" => ["required"],
            "password" => ["required"],
        ]);
        $user = User::where("nis", $request->nis)->first();
        $success = $user !== null && Hash::check($request->password, $user->password);

        return response()->json([
            "success" => $success,
            "data" => $success ? [
                "email" => $user->email,
                "name" => $user->name,
            ] : null
        ]);
    }

    // Menyimpan booking baru
    public function store(Request $request)
    {
        $request->validate([
            "nis" => "required",
            "password" => "required",
            "room_id" => "required",
            "date" => "required|date",
            "start_time" => "required",
            "end_time" => "required",
            "description" => "required",
            "nama" => "required",
            "email" => "required",
            "department" => "required",
            "members" => "nullable",
        ]);

        $user = User::where("nis", $request->nis)->first();
        if (!Hash::check($request->password, $user->password)) {
            return redirect()
                ->route("bookings.create", $request->room_id)
                ->with("failed", "Booking gagal ditambahkan.");
        }

        $booking = Booking::create([
            "user_id" => $user->id_user,
            "room_id" => $request->room_id,
            "date" => $request->date,
            "start_time" => $request->start_time,
            "end_time" => $request->end_time,
            "description" => $request->description,
            "nama" => $request->nama,
            "email" => $request->email,
            "department" => $request->department,
            // 'approved' => false, // Menunggu approval
            "approved" => true, // Otomatis approve
        ]);
        if($request->members) $booking->members()->sync($request->members);
        
        $members = Booking::where('id', $booking->id)->first()->members;
        foreach($members as $member){
            Mail::to($member)->send(new InvitationMail($booking, $member));
        }

        return redirect()
            ->route("bookings.create", $request->room_id)
            ->with("success", "Booking berhasil ditambahkan.");
    }
    
    public function destroy(Request $request){
        if (Auth::user()->role !== "admin") {
            return redirect()
                ->route("home")
                ->with("error", "Unauthorized access");
        }
        
        $booking = Booking::where('id', $request->id);
        $roomId = $booking->first()->room_id;
        $booking->delete();
        
        return redirect()
            ->route("bookings.create", $roomId)
            ->with("success", "Booking berhasil dihapus.");
    }

    // Menampilkan booking untuk admin
    public function indexAdmin()
    {
        if (Auth::user()->role !== "admin") {
            return redirect()
                ->route("home")
                ->with("error", "Unauthorized access");
        }

        $bookings = Booking::all();
        return view("admin.bookings.index", compact("bookings"));
    }

    // Proses approve booking oleh admin
    public function approve($id)
    {
        if (Auth::user()->role !== "admin") {
            return redirect()
                ->route("home")
                ->with("error", "Unauthorized access");
        }

        $booking = Booking::find($id);
        if ($booking) {
            $booking->approved = true;
            $booking->save();

            // Kurangi 7 jam dari waktu mulai dan selesai
            $startDateTime = Carbon::parse(
                $booking->date . " " . $booking->start_time
            )->subHours(7);
            $endDateTime = Carbon::parse(
                $booking->date . " " . $booking->end_time
            )->subHours(7);

            // Buat event di sistem Anda (misal dengan Spatie Google Calendar)
            // $event = new Event;
            // $event->name = 'Meeting Room Booking: ' . $booking->room->name;
            // $event->startDateTime = $startDateTime;
            // $event->endDateTime = $endDateTime;
            // $event->description = $booking->description;
            // $event->save();

            // Kirim email notifikasi setelah approve
            Mail::to($booking->user->email)->send(
                new BookingApprovedMail($booking)
            );

            // Ambil token OAuth dari session
            $accessToken = session("google_access_token");

            // Jika token tidak ada, arahkan pengguna untuk login ulang dengan Google
            if (!$accessToken) {
                return redirect()
                    ->route("login.google")
                    ->with(
                        "error",
                        "Please login with Google to sync your calendar."
                    );
            }

            // Inisialisasi Google Client dengan token
            $client = new \Google_Client();
            $client->setAccessToken($accessToken);

            $service = new \Google_Service_Calendar($client);

            // Membuat event untuk disimpan di kalender pengguna
            $googleEvent = new \Google_Service_Calendar_Event([
                "summary" => "Meeting Room Booking: " . $booking->room->name,
                "start" => [
                    "dateTime" => $startDateTime->toRfc3339String(), // Gunakan waktu yang sudah dikurangi 7 jam
                    "timeZone" => "Asia/Jakarta",
                ],
                "end" => [
                    "dateTime" => $endDateTime->toRfc3339String(), // Gunakan waktu yang sudah dikurangi 7 jam
                    "timeZone" => "Asia/Jakarta",
                ],
                "attendees" => [
                    ["email" => $booking->user->email], // email pengguna yang diundang
                ],
            ]);

            // Simpan event ke kalender utama pengguna
            $service->events->insert("primary", $googleEvent);

            return redirect()
                ->route("admin.bookings.index")
                ->with(
                    "success",
                    "Booking approved and event created in Google Calendar."
                );
        }

        return redirect()
            ->route("admin.bookings.index")
            ->with("error", "Booking not found.");
    }
}
