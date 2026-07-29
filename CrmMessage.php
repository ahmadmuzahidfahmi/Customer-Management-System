<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CrmMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $messageSubject;
    public string $messageBody;
    public string $senderName;

    /**
     * @param  string  $messageSubject  The email subject line.
     * @param  string  $messageBody     The email body (plain text).
     * @param  string  $senderName      Display name of the CRM user sending it.
     */
    public function __construct(string $messageSubject, string $messageBody, string $senderName)
    {
        $this->messageSubject = $messageSubject;
        $this->messageBody = $messageBody;
        $this->senderName = $senderName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->messageSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crm-message',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
