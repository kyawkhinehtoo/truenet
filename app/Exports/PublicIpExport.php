<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\PublicIpAddress;
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
class PublicIpExport implements FromQuery, WithMapping,WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;
    protected $count;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->count = 1;
    }
    public function query()
    {
            $request = $this->request;
            $startDate = (isset($request->end_date['0']))?$request->end_date['0'] :null;
            $endDate = (isset($request->end_date['1']))?$request->end_date['1']: null;
          
            $public_ips = PublicIpAddress::query()
            ->join('customers','public_ip_addresses.customer_id','=','customers.id')
            ->join('packages','customers.package_id','=','packages.id')
            ->join('status','customers.status_id','=','status.id')
            ->join('ip_usage_history','public_ip_addresses.id','=','ip_usage_history.ip_id')
            ->when(!is_null($startDate) && !is_null($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('ip_usage_history.end_date',[$startDate,$endDate]);
            })
            ->when($request->general, function ($query, $search) {
                $query->where(function ($query) use($search) {
                    $query->where('customers.ftth_id','LIKE', '%'.$search.'%')
                        ->orWhere('customers.name','LIKE', '%'.$search.'%')
                        ->orWhere('public_ip_addresses.ip_address','LIKE', '%'.$search.'%')
                        ->orWhere('public_ip_addresses.description','LIKE', '%'.$search.'%');
                });
            })
            ->where(function($query){
                return $query->where('customers.deleted', '=', 0)
                ->orwherenull('customers.deleted');
            })
            ->orderBy('public_ip_addresses.id')
            ->select('public_ip_addresses.id as id', 'customers.id as customer_id', 'customers.name as customer_name',
                'customers.ftth_id as ftth_id', 'public_ip_addresses.ip_address as ip_address', 'public_ip_addresses.description as description',
                'public_ip_addresses.annual_charge as annual_charge','public_ip_addresses.currency as currency','packages.name as package_name','ip_usage_history.start_date as start_date',
                'ip_usage_history.end_date as end_date','status.name as status_name');
       
        return $public_ips;
    
    }
    public function headings(): array
    {
        return [
            'No.',
            'Public IP',
            'Customer ID',
            'Customer Name',
            'Package',
            'Description',
            'Annual Fees',
            'Currency',
            'Start Date',
            'End Date',
            'Customer Status'
        ];
    }

    public function map($public_ips): array
    {
        
        return [
            $this->count++,
            $public_ips->ip_address,
            $public_ips->ftth_id,
            $public_ips->customer_name,
            $public_ips->package_name,
            $public_ips->description,
            $public_ips->annual_charge,
            $public_ips->currency,
            $public_ips->start_date,
            $public_ips->end_date,
            $public_ips->status_name
        ];
    }
   

}
