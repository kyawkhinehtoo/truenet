<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class PdfGenerationProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $progress;
    public $invoice_id;
    public $invoice_file;
    public $invoice_url;
    public function __construct($progress, $invoice_id, $invoice_file, $invoice_url)
    {
        //
        $this->progress = $progress;
        $this->invoice_id = $invoice_id;
        $this->invoice_file = $invoice_file;
        $this->invoice_url = $invoice_url;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('pdfprogressbar');
    }
    public function broadcastAs()
    {
        return 'pdfgen';
    }
    public function broadcastQueue()
    {
        return 'sync';
    }
}
