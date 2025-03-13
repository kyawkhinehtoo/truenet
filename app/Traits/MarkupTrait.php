<?php

namespace App\Traits;

use App\Models\BillReminder;
use App\Models\Invoice;
use App\Models\Customer;
use Datetime;

trait MarkupTrait
{

    public function replaceInvoiceMarkup($id, $sms_message)
    {
        $invoice = Invoice::find($id);

        if ($invoice) {
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = "";
            if ($invoice->invoice_url) {
                $app_url = getenv('APP_URL', 'http://localhost:8000');
                $bill_url = $app_url . '/s/' . $invoice->invoice_url;
            }

            $search = array('{{customer_name}}', '{{ftth_id}}', '{{bill_number}}', '{{period_covered}}', '{{month}}', '{{year}}', '{{bill_to}}', '{{attn}}', '{{usage_days}}', '{{total_payable}}', '{{payment_duedate}}', '{{url}}');
            $replace = array($invoice->bill_to, $invoice->bill_number, $invoice->period_covered, $month, $invoice->bill_year, $invoice->bill_to, $invoice->attn, $invoice->usage_days, $invoice->total_payable, $invoice->payment_duedate, $bill_url);
            $replaced = str_replace($search, $replace, $sms_message);
            return $replaced;
        }
        return $sms_message;
    }
    public function replaceAnnouncementMarkup($id, $sms_message)
    {
        $customer = Customer::join('packages', 'packages.id', 'customers.package_id')
            ->where('customers.id', '=', $id)
            ->select('customers.*', 'packages.name as package_name', 'packages.speed as package_speed', 'packages.type as package_type', 'packages.currency as package_currency', 'packages.price as package_price')
            ->first();

        if ($customer) {
            $search = array('{{customer_name}}', '{{ftth_id}}', '{{package_name}}', '{{package_speed}}', '{{package_price}}', '{{package_currency}}', '{{package_type}}');
            $replace = array($customer->name, $customer->ftth_id, $customer->package_name, $customer->package_speed, $customer->package_price, strtoupper($customer->package_currency), strtoupper($customer->package_type));
            $replaced = str_replace($search, $replace, $sms_message);
            return $replaced;
        }
    }
    public function replaceReminderMarkup($id, $sms_message)
    {
        $reminder = BillReminder::find($id);

        if ($reminder) {
           $search = array('{{customer_name}}', '{{ftth_id}}','{{service_off_date}}','{{days_remaining}}','{{package_name}}','{{package_price}}');
            $replace = array($reminder->name, $reminder->ftth_id, $reminder->service_off_date, $reminder->days_remaining, $reminder->package_name, $reminder->package_price);
            $replaced = str_replace($search, $replace, $sms_message);
            return $replaced;
        }
        return $sms_message;
    }
}
