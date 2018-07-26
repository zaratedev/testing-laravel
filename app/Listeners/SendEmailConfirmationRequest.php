<?php

namespace App\Listeners;

use App\Mail\ConfirmedYourEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class SendEmailConfirmationRequest
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        Mail::to($event->user)->send(new ConfirmedYourEmail($event->user));
    }
}
