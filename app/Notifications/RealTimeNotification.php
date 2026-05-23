<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RealTimeNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $title, string $message, ?string $url = null, ?string $icon = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon ?: 'fa-solid fa-bell';
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
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
