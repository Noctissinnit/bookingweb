<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking; 
use App\Models\Member; 
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Calendar;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        protected Booking $booking,
        protected Member $member
    )
    {
        //
    }
    
    public function generateCalendar(): string
    {
        $booking = $this->booking;
        $event = Event::create('Booking Invitation '.$booking->department)
            ->startsAt(Carbon::parse($booking->date.' '.$booking->start_time))
            ->endsAt(Carbon::parse($booking->date.' '.$booking->end_time))
            ->address($booking->room->name)
            ->description($booking->description)
            ->organizer($booking->email);
            
        $calendar = Calendar::create()
            ->event($event)->get();
        
        $filename = Str::uuid().'.ics';
        Storage::put('calendars/'.$filename, $calendar);
        
        return $filename;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [ 'member' => $this->member, 'booking' => $this->booking ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $filename = $this->generateCalendar();
        return [
            Attachment::fromStorageDisk('local', $filename)->as('invite.ics')->withMime('text/calendar;charset=UTF-8;method=REQUEST')
        ];
    }
}
