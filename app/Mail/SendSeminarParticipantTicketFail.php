<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendSeminarParticipantTicketFail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $name;
    protected $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $reason)
    {
        $this->email = $email;
        $this->name = $name;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Pendaftaran Seminar Gagal',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'mails.send_seminar_ticket_fail',
            with: [
                'participantName' => $this->name,
                'participantEmail' => $this->email,
                'reason' => $this->reason
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
