<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReminderSMSSendProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $progress;
    public $id;

    public function __construct($progress, $id)
    {
        $this->progress = $progress;
        $this->id = $id;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('reminderprogressbar');
    }

    public function broadcastAs(): string
    {
        return 'remindersms';
    }

    public function broadcastWith(): array
    {
        return [
            'progress' => $this->progress,
            'id' => $this->id,
        ];
    }
}
