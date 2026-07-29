<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the player once payment clears. SMS isn't wired up yet — no provider
 * (e.g. Africa's Talking, Twilio) is configured. To add it, create an 'sms'
 * channel and list it alongside 'mail' in via() below.
 */
class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->booking->loadMissing('pitch');

        return (new MailMessage)
            ->subject(__('Booking confirmed — :pitch', ['pitch' => $this->booking->pitch->nameFor()]))
            ->greeting(__('You\'re all set, :name!', ['name' => $notifiable->name]))
            ->line(__(':pitch — :city', ['pitch' => $this->booking->pitch->nameFor(), 'city' => $this->booking->pitch->city]))
            ->line(__('Date: :date', ['date' => $this->booking->booking_date->format('d/m/Y')]))
            ->line(__('Time: :start – :end', ['start' => $this->booking->start_time, 'end' => $this->booking->end_time]))
            ->action(__('View booking'), route('bookings.show', $this->booking))
            ->line(__('Show this confirmation at the pitch.'));
    }
}
