<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class HaikalaIndormCreditors extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public array $name,public $toMails, public $subject, public $oldContent, public array $oldHeaders)
    {
        $this->toMails = Arr::wrap($this->toMails);
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        foreach ($this->toMails as &$toMail) {
            preg_match($pattern, $toMail, $matches);
            $toMail = $matches[0];
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->toMails,
            //cc: 'm.elbaz@jpaemirates.com',
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.haikala-inform-creditors',
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
            Attachment::fromPath('attachments/mails/كشف الديون العمالية المضافة لقائمة الدائنين.pdf')->withMime('application/pdf'),
            ];
    }
}
