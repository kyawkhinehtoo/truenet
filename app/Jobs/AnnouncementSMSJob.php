<?php

namespace App\Jobs;

use App\Events\AnnouncementSMSSendProgress;
use App\Models\Announcement;
use App\Models\AnnouncementLog;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailTemplate;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;
use DB;

class AnnouncementSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MarkupTrait, SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $customers;
    private $announcement_id;
    private $auth_id;
    private $total_announcement;
    private $is_bulk;
    public function __construct($customers, $announcement_id, $total_announcement, $auth_id, $is_bulk = false)
    {
      
        $this->customers = $is_bulk ? $customers : Customer::find($customers);
        $this->announcement_id = $announcement_id;
        $this->total_announcement = $total_announcement;
        $this->auth_id = $auth_id;
        $this->is_bulk = $is_bulk;
        $this->initializeSMSGateway();
    }

    public function handle()
    {
        $announcement = Announcement::find($this->announcement_id);
        if ($announcement) {
            $sms_template = EmailTemplate::where('id', '=', $announcement->template_id)
                ->where('type', '=', 'sms')
                ->first();

            if ($sms_template) {
                $success = false;
                
                if ($this->is_bulk) {
                    // For bulk SMS, collect all phone numbers from all customers
                    $phones = [];
                  
                    foreach ($this->customers as $customer) {
                        if (!empty($customer->phone)) {
                            $phones[] = $customer->phone;
                        }
                    }
                   
                    $success = $this->deliverSMS($phones, $sms_template->body, true);

                    // Log for each customer
                    foreach ($this->customers as $customer) {
                        $this->createLog($customer, $announcement, $sms_template, $success);
                    }
                } else {
                    // For individual SMS
                    $customer = $this->customers; // Single customer
                    $phone = trim($customer->phone) ;
                    $message = $this->replaceAnnouncementMarkup($customer->id, $sms_template->body);
                    $success = $this->deliverSMS($phone, $message);
                    
                    $this->createLog($customer, $announcement, $sms_template, $success, $message);
                }
            }
        }
    }

    private function createLog($customer, $announcement, $sms_template, $success, $message = null)
    {
        $log_check = DB::table('announcement_log')
            ->where('announcement_id', '=', $this->announcement_id)
            ->where('customer_id', '=', $customer->id)
            ->where('status', '=', 'sent')
            ->first();

        if (!$log_check) {
            $log = new AnnouncementLog();
            $log->customer_id = $customer->id;
            $log->sender_id = $this->auth_id;
            $log->template_id = $announcement->template_id;
            $log->announcement_id = $announcement->id;
            $log->title = 'SMS';
            $log->detail = $message ?? $sms_template->body;
            $log->status = $success;
            $log->message_id = '';
            $log->date = date("Y-m-j h:m:s");
            $log->type = $announcement->announcement_type;
            $log->save();
        }
    }
}
