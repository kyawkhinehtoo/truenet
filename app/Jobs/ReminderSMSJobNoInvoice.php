<?php

namespace App\Jobs;


use App\Events\ReminderSMSSendProgress;
use App\Models\BillReminder;
use App\Models\BillReminderLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;
use Illuminate\Support\Facades\Storage;

class ReminderSMSJobNoInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MarkupTrait, SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $id, $total_item, $auth_id,$onetimecode;
    public function __construct($id, $total_item, $auth_id, $onetimecode)
    {
        $this->id = $id;
        $this->total_item = $total_item;
        $this->auth_id = $auth_id;
        $this->onetimecode = $onetimecode;        
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->id) {
            $reminder = BillReminder::where('id', $this->id)->first();
            if ($reminder && $reminder->phone_1) {
                $sms_template = EmailTemplate::whereJsonContains('default_name',  ['key' => 'just_reminder_sms'])
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {

                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    $message = $this->replaceReminderMarkup($this->id, $sms_message);

                    //testing start
                    // $success = true;
                    // $pattern = "/^09[0-9]{7,9}$/";
                    // if (preg_match($pattern, $reminder->phone_1)) {
                    //     Storage::append('reminder.log', $reminder->phone_1 . " : " . $message);
                    //     $success = true;
                    // }
                    //testing end

                //     production start
                   $success = $this->deliverSMS($reminder->phone_1, $message);
                //       production end
                \Log::debug('Check Response. : ' . $success);
                    if ($success) {
                        $reminder_data = BillReminder::find($this->id);
                        $reminder_data->sms_sent_at = date('Y-m-d H:i:s');
                        $reminder_data->sms_status = ($success) ? "sent" : "error";
                        $reminder_data->send_by = $this->auth_id;
                        $reminder_data->update();

                          // Create a new log entry with all data from reminder_data except ID
                          $reminder_log = new BillReminderLog();
                          $reminderAttributes = $reminder_data->getAttributes();
                          unset($reminderAttributes['id']); // Remove the ID field
                          
                          foreach ($reminderAttributes as $key => $value) {
                              $reminder_log->{$key} = $value;
                          }
                          
                          $reminder_log->save();

                        $sent_items =   BillReminder::whereNotNull('sms_sent_at')
                            ->whereNotNull('sms_status')
                            ->where('onetimecode', $this->onetimecode)
                            ->count();
                        // // $sent_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                        // //     ->whereNotNull('reminder_sms_sent_date')
                        // //     ->count();
                        // echo "Invoice : " . $this->invoice_id;
                        // echo " Total : " . $this->total_invoices;
                        // echo ", Sent : " . $sent_invoice;
                        sleep(1);
                        echo "Reminder Count : ". $sent_items.PHP_EOL;
                        echo "Reminder Total : ". $this->total_item.PHP_EOL;
                        $progress = round(($sent_items / $this->total_item) * 100);
                       event(new ReminderSMSSendProgress($progress, $this->id));
                    }
                } // end of check sms template
            } // end of check phone exists or not  

        } //end of check ID exists or not


    }
}
