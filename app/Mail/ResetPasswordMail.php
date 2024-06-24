<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $actionURL;

    public function __construct($actionURL)
    {
        $this->actionURL = $actionURL;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Request | TechStore',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $expireTime = config('auth.passwords.admins.expire');
        $emailContent = [
            'subject' => 'Password Reset Request | TechStore',
            'body' => 'To reset your password, please click on the following link:',
            'button_title' => 'RESET PASSWORD',
        ];
        
        return new Content(
            view: 'mails.password-reset',
            with: [
                'actionURL' => $this->actionURL,
                'emailContent' => $emailContent,
                'expireTime' => $expireTime,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
