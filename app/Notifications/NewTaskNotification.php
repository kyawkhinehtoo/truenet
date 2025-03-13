<?php

namespace App\Notifications;

use App\Models\Customer;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class NewTaskNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $title;
    protected $action;

    /**
     * Create a new notification instance.
     *
     * @param $incident
     * @param $title
     * @param $action
     */
    public function __construct($task, $title, $action)
    {
        $this->task = $task;
        $this->title = $title;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        //$incharge = User::where('id', $this->incident->incharge_id)->value('name');
        $actor = auth()->user()->name;
        $incident = Incident::find($this->task->incident_id)->first();
        $ftthID = Customer::where('id', $incident->customer_id)->value('ftth_id');

        $message = $actor . " " . $this->action . " an Task On " . $incident->code . " for Customer ID " . $ftthID . ".";

        return [
            'message' => $message,
            'title' => $this->title,
            'time' => now()->format('Y-m-d H:i:s'),
        ];
    }


    /**
     * Get the broadcast representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    // public function toBroadcast($notifiable)
    // {
    //     $incharge = User::where('id', $this->incident->incharge_id)->value('name');
    //     $ftthID = Customer::where('id', $this->incident->customer_id)->value('ftth_id');

    //     $message = $incharge . " " . $this->action . " an Incident (" . $this->incident->code . ") for Customer ID " . $ftthID . ".";

    //     return new BroadcastMessage([
    //         'message' => $message,
    //         'title' => $this->title,
    //         'time' => now()->toDateTimeString(),
    //     ]);
    // }

    /**
     * Get the status representation of the incident.
     *
     * @param int $data
     * @return string
     */
    public function getStatus($data)
    {
        $statuses = [
            1 => "Open",
            2 => "Escalated",
            3 => "Closed",
            4 => "Deleted",
            5 => "Resolved",
        ];

        return $statuses[$data] ?? "Open";
    }

    /**
     * Format a string (helper function).
     *
     * @param string $input
     * @return string
     */
    public function formatString($input)
    {
        $input = str_replace('_', ' ', $input);
        $formatted = ucwords($input);
        return str_replace(' ', '', $formatted);
    }
}