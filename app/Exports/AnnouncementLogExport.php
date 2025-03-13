<?php

namespace App\Exports;

use App\Models\AnnouncementLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class AnnouncementLogExport implements FromQuery, WithMapping,WithHeadings
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

           
            $logs = AnnouncementLog::query()
            ->join('announcements', 'announcement_log.announcement_id', 'announcements.id')
            ->join('users', 'announcement_log.sender_id', 'users.id')
            ->join('customers', 'announcement_log.customer_id', 'customers.id')
            ->join('email_templates', 'announcement_log.template_id', 'email_templates.id')
            ->where('announcement_id', '=', $request->id)
            ->select('announcement_log.*', 'users.name as sender_name', 'customers.ftth_id as ftth_id','customers.phone_1','customers.phone_2', 'email_templates.name as template_name','announcements.name as announcement_name');
        return $logs;
    
    }
    public function headings(): array
    {
        return [
            'Customer ID',
            'Phone 1',
            'Phone 2',
            'Sender',
            'Announcement Name',
            'Template Name',
            'Type',
            'Date',
            'Status',
            'Title',
            'Body',
        ];
    }

    public function map($logs): array
    {

        return [
            $logs->ftth_id,
            $logs->phone_1,
            $logs->phone_2,
            $logs->sender_name,
            $logs->announcement_name,
            $logs->template_name,
            $logs->type,
            $logs->date,
            $logs->status,
            $logs->title,
            $logs->detail,
          ];
    }
}
