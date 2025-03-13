<?php

namespace App\Exports;


use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Bills;
use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;

class BillingExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function query()
    {
        $request = $this->request;

        $billings = Invoice::query()
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('invoices.bill_id', '=', $request->bill_id)
            ->select(
                'invoices.*',
                'receipt_records.receipt_number as receipt_number',
                'receipt_records.status as receipt_status',
                DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date'),
                'receipt_records.collected_person as collected_person',
                'receipt_records.collected_amount as collected_amount',
                'receipt_records.transition as transition',
                'receipt_records.remark as remark',
                'receipt_records.payment_channel as payment_channel',
            )
            ->groupBy('invoices.id');
        return $billings;
    }
    public function headings(): array
    {
        return [
            'Invoice Number',
            'Bill Start Date',
            'Bill End Date',
            'Bill Number',
            'Customer Id',
            'Date Issued',
            'Bill To',
            'Attn',
            'Previous Balance',
            'Current Charge',
            'Compensation',
            'OTC',
            'Sub Total',
            'Payment Duedate',
            'Service Description',
            'QTY',
            'Usage Day',
            'Usage Month',
            'Bonus Day',
            'Bonus Month',
            'Customer Status',
            'Normal Cost',
            'Type',
            'Discount',
            'Total Payable',
            'Commercial_tax',
            'Email',
            'Phone',
            'Receipt Status',
            'Receipt Number',
            'Receipt Date',
            'Collected Person',
            'Collected Amount',
            'Payment Channel',
            'Transition ID',
            'Remark'
        ];
    }

    public function map($billings): array
    {



        // dd($last_invoices->period_covered);
        $collected_by = User::find($billings->collected_person);

        return [
            ($billings->invoice_number) ? 'INV' . substr($billings->bill_number, 0, 4) . str_pad($billings->invoice_number, 5, "0", STR_PAD_LEFT) : null,
            $billings->start_date,
            $billings->end_date,
            $billings->bill_number,
            $billings->ftth_id,
            $billings->date_issued,
            $billings->bill_to,
            $billings->attn,
            $billings->previous_balance,
            $billings->current_charge,
            $billings->compensation,
            $billings->otc,
            $billings->sub_total,
            $billings->payment_duedate,
            $billings->service_description,
            $billings->qty,
            $billings->usage_day,
            $billings->usage_month,
            $billings->bonus_day,
            $billings->bonus_month,
            $billings->customer_status,
            $billings->normal_cost,
            $billings->type,
            $billings->discount,
            $billings->total_payable,
            $billings->commercial_tax,
            $billings->email,
            $billings->phone,

            $billings->receipt_status,
            ($billings->receipt_number) ? 'REC' . substr($billings->bill_number, 0, 4) . str_pad($billings->receipt_number, 5, "0", STR_PAD_LEFT) : null,
            $billings->receipt_date,
            ($collected_by) ? $collected_by->name : '',
            $billings->collected_amount,
            $billings->payment_channel,
            $billings->transition,
            $billings->remark,



        ];
    }
    public function replaceMarkup($id)
    {
        $sms_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'sms')
            ->first();
        $data = $sms_template->body;
        $invoice = Invoice::find($id);

        if ($invoice) {
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = 'https://oss.marga.com.mm/s/' . $invoice->url;
            $search = array('{{ftth_id}}', '{{bill_number}}', '{{period_covered}}', '{{month}}', '{{year}}', '{{bill_to}}', '{{attn}}', '{{usage_days}}', '{{total_payable}}', '{{payment_duedate}}', '{{url}}');
            $replace = array($invoice->ftth_id, $invoice->bill_number, $invoice->period_covered, $month, $invoice->bill_year, $invoice->bill_to, $invoice->attn, $invoice->usage_days, $invoice->total_payable, $invoice->payment_duedate, $bill_url);
            $replaced = str_replace($search, $replace, $data);
            return $replaced;
        }
    }
}
