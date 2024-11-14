@extends('layouts.app')

@section('head')
<script>
    let inputFilterDate;
    $(document).ready(() => {
        inputFilterDate = $('#input-filter-date');

        setInputFilterDate();
        inputFilterDate.change((e) => {
            const url = new URL(location.href);
            url.searchParams.set('date', e.currentTarget.value);
            location.href = url.toString();
        });
    });

    function setInputFilterDate(){
        const url = new URL(location.href);
        inputFilterDate[0].valueAsDate = url.searchParams.get('date') ? new Date(url.searchParams.get('date')) : new Date()
    }
</script>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-6"><h3>{{ $room->name }}</h3></div>
        <div class="col-6 d-flex justify-content-end">
            <div>Filter Tanggal : <input id="input-filter-date" type="date"></div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-3">
    <!-- Download Buttons -->
    <a href="{{ route('bookings.export', ['room' => $room->id, 'date' => request()->get('date') ]) }}" class="btn btn-success me-2">Download Excel</a>
    <a href="{{ route('bookings.export', ['room' => $room->id, 'date' => request()->get('date'), 'type' => 'pdf' ]) }}" class="btn btn-danger">Download PDF</a>
</div>

    <div class="table-responsive mt-3">
    <table class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th scope="col">No.</th>
            <th scope="col">Tanggal & Waktu</th>
            <th scope="col">Nama Kegiatan</th>
            <th scope="col">Peserta</th>
            {{-- <th scope="col">Status</th> --}}
            {{-- <th scope="col">Aksi</th> --}}
        </tr>
    </thead>
    <tbody>
        @if(count($bookings) < 1)
            <tr><td colspan="4">Tidak ada data peminjaman...</td></tr>
        @endif
        @foreach ($bookings as $booking)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $booking->date }} ({{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }})</td>
                <td>{{ $booking->description }}</td> 
                <td>
                    @foreach($booking->users as $user)
                        {{ $user->name }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                {{-- <td>
                    @if ($booking->approved)
                        <span class="badge bg-success">Approved</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </td>
                <td>
                    @if (!$booking->approved)
                        <form action="{{ route('admin.bookings.approve', $booking->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                        </form>
                    @else
                        <button class="btn btn-secondary btn-sm" disabled>Already Approved</button>
                    @endif
                </td> --}}
            </tr>
        @endforeach
    </tbody>
</table>

    </div>
</div>
@endsection
