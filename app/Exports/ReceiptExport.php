<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;

class ReceiptExport implements FromQuery, WithMapping, WithHeadings
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
        $receipt_records = ReceiptRecord::query()
            ->join('customers', 'customers.id', '=', 'receipt_records.customer_id')
            ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->join('bills', 'bills.id', '=', 'receipt_records.bill_id')
            ->leftjoin('users', 'users.id', 'receipt_records.collected_person')
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                        ->orWhere('invoices.invoice_number', 'LIKE', '%' . $general . '%')
                        ->orWhere('receipt_records.receipt_number', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->bill_id, function ($query, $bills) {
                $b_list = array();
                foreach ($bills as $value) {
                    array_push($b_list, $value['id']);
                }
                // dd($b_list);
                $query->whereIn('bills.id',  $b_list);
            })
            ->when($request->date, function ($query, $date) {
                if (isset($date['0']) && isset($date['1'])) {
                    if ($date['0'] != null && $date['1'] != null) {
                        return $query->whereBetween('receipt_records.created_at', [$date['0'] . ' 00:00:00', $date['1'] . ' 23:00:00']);
                    }
                }
                $query->whereDate('receipt_records.created_at', Carbon::today());
            }, function ($query) {

                $query->whereDate('receipt_records.created_at', Carbon::today());
            })
            ->select('bills.name as bill_name',  'invoices.invoice_number', 'invoices.bill_number', 'receipt_records.receipt_number', 'customers.ftth_id', 'receipt_records.issue_amount', 'receipt_records.collected_amount', 'receipt_records.month', 'receipt_records.year', 'receipt_records.created_at', 'receipt_records.receipt_date', 'receipt_records.status as receipt_status', 'receipt_records.payment_channel', 'receipt_records.transition', 'invoices.period_covered', 'invoices.usage_days', 'invoices.qty', 'invoices.normal_cost', 'users.name as user_name', 'invoices.start_date', 'invoices.end_date', 'invoices.usage_day', 'invoices.usage_month', 'invoices.bonus_day', 'invoices.bonus_month');

        return $receipt_records;
    }
    public function headings(): array
    {
        return [
            'Bill Name',
            'Bill Start Date',
            'Bill End Date',
            'Bill For',
            'Bill Number',
            'Invoice Number',
            'Receipt Number',
            'Customer ID',
            'Package',
            'Package Price',
            'Usage Day',
            'Usage Month',
            'Bonus Day',
            'Bonus Month',
            'Covered Period',
            'Issue Amount',
            'Receipt Amount',
            'Receipt Date',
            'Customer Paid Date',
            'Receipt By',
            'Receipt Status',
            'Payment Channel',
            'Transition'
        ];
    }

    public function map($receipt_records): array
    {
        $t_date = null;
        if (strpos($receipt_records->period_covered, ' to ') !== false) {
            $t_date = explode(" to ",  $receipt_records->period_covered);
        }

        return [
            $receipt_records->bill_name,
            $receipt_records->start_date,
            $receipt_records->end_date,
            $receipt_records->year . '-' . $receipt_records->month,
            $receipt_records->bill_number,
            ($receipt_records->invoice_number) ? 'INV' . substr($receipt_records->bill_number, 0, 4) . str_pad($receipt_records->invoice_number, 5, "0", STR_PAD_LEFT) : null,
            ($receipt_records->receipt_number) ? 'REC' . substr($receipt_records->bill_number, 0, 4) . str_pad($receipt_records->receipt_number, 5, "0", STR_PAD_LEFT) : null,
            $receipt_records->ftth_id,
            $receipt_records->qty,
            $receipt_records->normal_cost,
            $receipt_records->usage_day,
            $receipt_records->usage_month,
            $receipt_records->bonus_day,
            $receipt_records->bonus_month,
            $receipt_records->period_covered,
            $receipt_records->issue_amount,
            $receipt_records->collected_amount,
            $receipt_records->created_at,
            $receipt_records->receipt_date,
            ($receipt_records->user_name) ? $receipt_records->user_name : '',
            $receipt_records->receipt_status,
            $receipt_records->payment_channel,
            $receipt_records->transition,
        ];
    }
}
