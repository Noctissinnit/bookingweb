<?php

namespace App\Http\Controllers;

use App\Mail\BookingApprovedMail;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    // Hanya menampilkan form booking untuk user biasa
    public function create()
    {
        $rooms = Room::all();
        return view('bookings.create', compact('rooms'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    // Menyimpan booking baru
    public function store(Request $request)
    {
        Log::debug(Auth::user());
        $request->validate([
            'room_id' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'required',
            'nama' => 'required',
            'email' => 'required',
            'nip' => 'required',
            'department' => 'required',
        ]);

        Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'description' => $request->description,
            'nama' => $request->nama,
            'email' => $request->email,
            'nip' => $request->nip,
            'department' => $request->department,
            'approved' => false, // Menunggu approval
        ]);

        // Logout setelah menambahkan data
        Auth::logout();
        return response()->json(['success' => true]);

        // return redirect()->route('home')->with('success', 'Booking submitted, waiting for admin approval.');
    }

    // Menampilkan booking untuk admin
    public function indexAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $bookings = Booking::all();
        return view('admin.bookings.index', compact('bookings'));
    }

    // Proses approve booking oleh admin
    public function approve($id)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        $booking = Booking::find($id);
        if ($booking) {
            $booking->approved = true;
            $booking->save();

            // Kurangi 7 jam dari waktu mulai dan selesai
            $startDateTime = Carbon::parse($booking->date . ' ' . $booking->start_time)->subHours(7);
            $endDateTime = Carbon::parse($booking->date . ' ' . $booking->end_time)->subHours(7);

            // Buat event di sistem Anda (misal dengan Spatie Google Calendar)
            // $event = new Event;
            // $event->name = 'Meeting Room Booking: ' . $booking->room->name;
            // $event->startDateTime = $startDateTime;
            // $event->endDateTime = $endDateTime;
            // $event->description = $booking->description;
            // $event->save();

            // Kirim email notifikasi setelah approve
            Mail::to($booking->user->email)->send(new BookingApprovedMail($booking));

            // Ambil token OAuth dari session
            $accessToken = session('google_access_token');

            // Jika token tidak ada, arahkan pengguna untuk login ulang dengan Google
            if (!$accessToken) {
                return redirect()->route('login.google')->with('error', 'Please login with Google to sync your calendar.');
            }

            // Inisialisasi Google Client dengan token
            $client = new \Google_Client();
            $client->setAccessToken($accessToken);

            $service = new \Google_Service_Calendar($client);

            // Membuat event untuk disimpan di kalender pengguna
            $googleEvent = new \Google_Service_Calendar_Event([
                'summary' => 'Meeting Room Booking: ' . $booking->room->name,
                'start' => [
                    'dateTime' => $startDateTime->toRfc3339String(),  // Gunakan waktu yang sudah dikurangi 7 jam
                    'timeZone' => 'Asia/Jakarta',
                ],
                'end' => [
                    'dateTime' => $endDateTime->toRfc3339String(),  // Gunakan waktu yang sudah dikurangi 7 jam
                    'timeZone' => 'Asia/Jakarta',
                ],
                'attendees' => [
                    ['email' => $booking->user->email],  // email pengguna yang diundang
                ],
            ]);

            // Simpan event ke kalender utama pengguna
            $service->events->insert('primary', $googleEvent);

            return redirect()->route('admin.bookings.index')->with('success', 'Booking approved and event created in Google Calendar.');
        }

        return redirect()->route('admin.bookings.index')->with('error', 'Booking not found.');
    }
}
