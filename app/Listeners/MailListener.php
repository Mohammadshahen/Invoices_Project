<?php

namespace App\Listeners;

use App\Events\MailEvent;
use App\Mail\InvoiceMail;
use App\Models\Invoices\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class MailListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MailEvent $event): void
    {
        $invoice = $event->invoice;
        Mail::to('mohammadshahen222@gmail.com')->send(new InvoiceMail($invoice));
    }
}
