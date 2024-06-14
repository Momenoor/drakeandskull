<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Haikala extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public mixed $data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'القضية رقم 110/2023 إجراءات إفلاس',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.iflas-mail',
            with: ['data' => $this->data],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath('attachments/mails/إعلانات النشر.pdf')->withMime('application/pdf'),
            Attachment::fromPath('attachments/mails/حكم المحكمة.pdf')->withMime('application/pdf'),
            Attachment::fromPath('attachments/mails/رخصة الشركة.pdf')->withMime('application/pdf'),
            Attachment::fromPath('attachments/mails/قرار التعيين.pdf')->withMime('application/pdf'),
        ];
    }
}
