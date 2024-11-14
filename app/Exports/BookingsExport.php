<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;

class BookingsExport implements FromCollection
{
    public Room $room;
    public $date = null;

    public function __construct(Room $room) {
        $this->room = $room;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {       
        return $this->room->bookings()->with('users')->where('date', $this->date ?? Carbon::today())->get()->map(function ($booking, $index) {
            return [
                'No' => $index + 1,
                'Tanggal & Waktu' => $booking->date . ' (' . substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5) . ')',
                'Nama Kegiatan' => $booking->activity_name,
                'Peserta' => $booking->users->pluck('name')->join(', '),
            ];
        });
    }

    public function headings(): array
    {
        return ['No.', 'Tanggal & Waktu', 'Nama Kegiatan', 'Peserta'];
    }
}
