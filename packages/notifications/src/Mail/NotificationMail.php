<?php

namespace Vigilant\Notifications\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Vigilant\Notifications\Notifications\Notification;

class NotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Notification $notification)
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: implode(' - ', [
                $this->notification->level()->name,
                $this->notification->title(),
                'Vigilant'
            ])
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notifications::mails.notification',
            with: [
                'description' => $this->notification->description(),
                'viewUrl' => $this->notification->viewUrl(),
                'url' => $this->notification->url(),
                'urlTitle' => $this->notification->urlTitle(),
            ],
        );
    }
}
