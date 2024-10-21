<p>
    Hai {{ $member->name }}, kamu diundang oleh {{ $booking->user->name }} untuk hadir ke {{ $booking->name }}
    pada waktu {{ $booking->date }} {{ $booking->start_time }} - {{ $booking->end_time }}.
</p>