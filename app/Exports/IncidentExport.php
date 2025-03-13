<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\Incident;
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

class IncidentExport implements FromQuery, WithMapping, WithHeadings
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
        $incidents =   DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('sla', 'packages.sla_id', '=', 'sla.id')
            ->join('users as u1', 'u1.id', '=', 'incidents.incharge_id')
            ->leftjoin('users as u2', 'u2.id', '=', 'incidents.closed_by')

            ->when($request->type, function ($query, $type) {
                $query->where('incidents.type', '=', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('incidents.status', '=', $status);
            })
            ->when($request->period, function ($query, $period) {
                if (isset($period['0']) && isset($period['1'])) {
                    if ($period['0'] != null && $period['1'] != null) {
                      
                        $startDate = Carbon::parse($period[0])->format('Y-m-d');
                        $endDate = Carbon::parse($period[1])->format('Y-m-d');
                        return $query->whereBetween('incidents.date', [$startDate, $endDate]);
                    }
                }
                return $query->whereRaw('Date(incidents.date)= CURDATE()');
            }, function ($query) {
                $query->whereDate('incidents.date', Carbon::today());
            })
            ->when($request->general, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->where('incidents.status', '<>', 4)
            ->orderBy('customers.id')
            ->select(
                'incidents.*',

                'customers.ftth_id as ftth_id',
                'u1.name as incharge',
                'u2.name as closed_person',
                'sla.percentage as sla',
                DB::raw("TIMESTAMPDIFF(minute, concat(incidents.date,' ', incidents.time) ,concat(incidents.close_date,' ', incidents.close_time))AS total_minutes ")
            );
        return $incidents;
    }
    public function headings(): array
    {
        return [
            'Ticket ID',
            'Customer ID',
            'Type',
            'Topic',
            'Issue Detail',
            'Open Date',
            'Open Time',
            'Close Date',
            'Close Time',
            'Status',
            'Duration(mins)',
            'Base SLA',
            'Ticket Opened By',
            'Ticket Closed By'
        ];
    }

    public function map($incidents): array
    {

        return [
            $incidents->code,
            $incidents->ftth_id,
            $incidents->type,
            $incidents->topic,
            $incidents->description,
            $incidents->date,
            $incidents->time,
            $incidents->close_date,
            $incidents->close_time,
            $this->getStatus($incidents->status),
            $incidents->total_minutes,
            $incidents->sla,
            $incidents->incharge,
            $incidents->closed_person,

        ];
    }
    function getStatus($data)
    {
        $status = "Open";
        if ($data == 1) {
            $status = "Open";
        } else if ($data == 2) {
            $status = "Escalated";
        } else if ($data == 3) {
            $status = "Closed";
        } else if ($data == 4) {
            $status = "Deleted";
        } else if ($data == 5) {
            $status = "Resolved";
        }
        return $status;
    }
}
