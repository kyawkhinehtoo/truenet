<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;

class RevenueExport implements FromQuery, WithMapping, WithHeadings
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
            ->leftjoin('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('invoices.bill_id', '=', $request->id)
            ->groupBy('invoices.id')
            ->select('invoices.*', 'receipt_records.receipt_number as receipt_number', 'receipt_records.collected_amount', 'receipt_records.payment_channel', 'receipt_records.collected_person', 'receipt_records.receipt_date', 'receipt_records.status', 'receipt_records.transition');

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
            'QTU',
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
            'Phone',
            'Receipt ID',
            'Receipt Amount',
            'Payment Channel',
            'Transition ID',
            'Collected Person',
            'Receipt Date',
            'Receipt Status'
        ];
    }

    public function map($billings): array
    {


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
            $billings->phone,
            ($billings->receipt_number) ? 'REC' . substr($billings->bill_number, 0, 4) . str_pad($billings->receipt_number, 5, "0", STR_PAD_LEFT) : null,
            $billings->collected_amount,
            $billings->payment_channel,
            $billings->transition,
            ($billings->collected_person) ? $this->collectedPerson($billings->collected_person) : null,
            $billings->receipt_date,
            $billings->status
        ];
    }
    public function collectedPerson($id)
    {
        $person = User::find($id);
        return $person?->name;
    }
}
