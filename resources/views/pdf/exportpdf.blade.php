<!DOCTYPE html>
<html>
<head>
    <title>Bookings PDF</title>
</head>
<body>
    <h1>Bookings</h1>
    <table border="1" cellpadding="5" cellspacing="0">
    <tbody>
     
        @foreach ($exportpdf as $booking)
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
</body>
</html>
