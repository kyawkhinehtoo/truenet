<?php

namespace App\Jobs;

use App\Events\BillSMSSendProgress;
use App\Models\Invoice;
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

class ReminderSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MarkupTrait, SMSTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $invoice_id, $total_invoices, $auth_id;
    public function __construct($id, $total_invoices, $auth_id)
    {
        $this->invoice_id = $id;
        $this->total_invoices = $total_invoices;
        $this->auth_id = $auth_id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->invoice_id) {
            $invoice = Invoice::where('id', $this->invoice_id)->first();
            if ($invoice && $invoice->phone) {
                $sms_template = EmailTemplate::whereJsonContains('default_name',  ['key' => 'unpaid_reminder'])
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {

                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    $message = $this->replaceInvoiceMarkup($this->invoice_id, $sms_message);

                    //testing start
                    //$success = true;
                    // $pattern = "/^09[0-9]{7,9}$/";
                    // if (preg_match($pattern, $invoice->phone)) {
                    //     Storage::append('sms.log', $invoice->phone . " : " . $message);
                    //     $success = true;
                    // }
                    //testing end

                    // production start
                    $success = $this->deliverSMS($invoice->phone, $message);
                    //   production end

                    if ($success) {
                        $billing_data = Invoice::find($this->invoice_id);
                        $billing_data->reminder_sms_sent_date = date('Y-m-d H:i:s');
                        $billing_data->reminder_sms_sent_status = ($success) ? "sent" : "error";
                        $billing_data->update();

                        $receipts = ReceiptRecord::where('bill_id', '=', $invoice->bill_id)
                            ->select('invoice_id')
                            ->get()
                            ->toArray();
                        $sent_invoice =   Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
                            ->where('invoices.total_payable', '>', 0)
                            ->where(function ($query) {
                                return $query->where('customers.deleted', '=', 0)
                                    ->orwherenull('customers.deleted');
                            })
                            ->whereNotIn('invoices.id', $receipts)
                            ->where('bill_id', '=', $invoice->bill_id)
                            ->whereNotNull('reminder_sms_sent_date')
                            ->whereNotNull('reminder_sms_sent_status')
                            ->select('invoices.*')
                            ->count();
                        // // $sent_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                        // //     ->whereNotNull('reminder_sms_sent_date')
                        // //     ->count();
                        // echo "Invoice : " . $this->invoice_id;
                        // echo " Total : " . $this->total_invoices;
                        // echo ", Sent : " . $sent_invoice;
                        $progress = round(($sent_invoice / $this->total_invoices) * 100);
                        event(new BillSMSSendProgress($progress, $this->invoice_id));
                    }
                } // end of check sms template
            } // end of check phone exists or not  

        } //end of check ID exists or not


    }
}
