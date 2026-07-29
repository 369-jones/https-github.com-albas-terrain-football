<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingOwnerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->booking->loadMissing(['pitch', 'user']);

        return (new MailMessage)
            ->subject(__('New paid booking — :pitch', ['pitch' => $this->booking->pitch->nameFor()]))
            ->greeting(__('New booking confirmed'))
            ->line(__(':player booked :pitch', ['player' => $this->booking->user->name, 'pitch' => $this->booking->pitch->nameFor()]))
            ->line(__('Date: :date, :start – :end', [
                'date' => $this->booking->booking_date->format('d/m/Y'),
                'start' => $this->booking->start_time,
                'end' => $this->booking->end_time,
            ]))
            ->action(__('Open owner dashboard'), route('admin.dashboard'));
    }
}
