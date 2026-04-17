<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        #[\SensitiveParameter] public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('admin.welcome_email_subject', ['app' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-welcome',
            with: [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => route('admin.login'),
                'appName' => config('app.name'),
            ],
        );
    }
}
