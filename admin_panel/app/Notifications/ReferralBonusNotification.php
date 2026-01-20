<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralBonusNotification extends Notification
{
    use Queueable;

    protected $newUser;
    protected $level;
    protected $bonus;

    /**
     * Create a new notification instance.
     */
    public function __construct($newUser, $level, $bonus)
    {
        $this->newUser = $newUser;
        $this->level = $level;
        $this->bonus = $bonus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Referral Bonus Received!',
            'body' => "You earned {$this->bonus} gems from a Level {$this->level} referral ({$this->newUser->name}).",
            'type' => 'referral_bonus',
            'amount' => $this->bonus,
            'level' => $this->level,
        ];
    }
}
