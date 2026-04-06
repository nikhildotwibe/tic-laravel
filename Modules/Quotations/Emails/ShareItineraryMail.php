<?php

namespace Modules\Quotations\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareItineraryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $htmlContent;
    public $emailSubject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailSubject, $htmlContent)
    {
        $this->emailSubject = $emailSubject;
        $this->htmlContent = $htmlContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->view('emails.raw_html', [
                        'htmlContent' => $this->htmlContent
                    ]);
    }
}
