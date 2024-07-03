<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class sendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $welcome;
    public $pdfData;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param string $welcome
     * @param mixed $pdfData
     * @param mixed $invoice
     * @return void
     */
    public function __construct($welcome, $pdfData, $invoice)
    {
        $this->welcome = $welcome;
        $this->pdfData = $pdfData;
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('send_invoice') 
                    ->subject('Invoice Mail')
                    ->attachData($this->pdfData, 'invoice.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with(['invoice' => $this->invoice]); 
    }
}
