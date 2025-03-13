<?php

namespace App\Exports;


use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Bills;
use App\Models\BillingTemp;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class TempBillingExport implements FromQuery, WithMapping,WithHeadings
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

           
            $billings = BillingTemp::query()
                ->join('customers', 'customers.id', '=', 'temp_billings.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
                })
                ->when($request->keyword, function ($query, $search = null) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
                })->when($request->general, function ($query, $general) {
                    $query->where(function ($query) use ($general) {
                        $query->where('customers.name', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->installation, function ($query, $installation) {
                    $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                    $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                    $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
                })
                ->when($request->payment_type, function ($query, $payment_type) {
                    $type = ($payment_type == 1) ? 1 : 0;
                    $query->where('customers.payment_type', '=', $type);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
                })
                ->when($request->project, function ($query, $project) {
                    $query->where('customers.project_id', '=', $project);
                })
                ->when($request->township, function ($query, $township) {
                    $query->where('customers.township_id', '=', $township);
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('customers.status_id', '=', $status);
                })
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })
                ->when($request->sort, function ($query, $sort = null) {
                    $sort_by = 'temp_billings.id';
                    if ($sort == 'cid') {
                        $sort_by = 'temp_billings.id';
                    }

                    $query->orderBy($sort_by, 'asc');
                }, function ($query) {
                    $query->orderBy('temp_billings.id', 'asc');
                })
                ->select('temp_billings.*');
        return $billings;
    
    }
    public function headings(): array
    {
        return [
            'Period Covered',
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
            'Usage Days',
            'Customer Status',
            'Normal Cost',
            'Type',
            'Discount',
            'Total Payable',
            'Commercial_tax',
            'Phone'
        ];
    }

    public function map($billings): array
    {

        return [
            $billings->period_covered,
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
            $billings->usage_days,
            $billings->customer_status,
            $billings->normal_cost,
            $billings->type,
            $billings->discount,
            $billings->total_payable,
            $billings->commercial_tax,
            $billings->phone
         ];
    }
}
