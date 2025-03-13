<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\NewNotificationEvent;

class BroadcastNotificationListener
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

        // Trigger the broadcast event with the notification data
        $data = $event->notification->toArray($event->notifiable);

        // Dispatch the custom broadcasting event
        event(new NewNotificationEvent($data, $event->notifiable->id));
    }
}