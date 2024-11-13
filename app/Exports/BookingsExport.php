<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\Export;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;

class BookingsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    
    public function collection()
    {
            
        return Booking::with('users')->get()->map(function ($booking, $index) {
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

    public function downloadPdf()
{
    $exportpdf = [
        'judul' => 'Laporan PDF', // Misalnya, judul laporan
        'content' => 'Ini adalah isi laporan yang akan dicetak pada PDF.' // Isi laporan
    ];
    $exportpdf = Booking::with('users')->get();

    dd($exportpdf);

    // Memuat tampilan 'view.pdf.exportpdf' dengan data exportpdf
    $pdf = Pdf::loadView('pdf.exportpdf', compact('exportpdf'));

    // Menyediakan PDF untuk diunduh
    return $pdf->download('laporan.pdf');
}

public function exportExcel()
{
    return Excel::download(new BookingsExport, 'AtmiBookingRooms.xlsx');
}

}
