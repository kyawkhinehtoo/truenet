<?php

namespace App\Exports;

use App\Models\BillReminder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as StyleNumberFormat;
use Carbon\Carbon;
class BillRemindersExport implements FromQuery, WithMapping, WithHeadings,WithColumnFormatting
{
    use Exportable;
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {

        $today = Carbon::now()->format('Y-m-d');
        $daysBeforeExpiry = $this->request->input('days', 7);
        $endDate = Carbon::now()->addDays($daysBeforeExpiry)->format('Y-m-d');
        
        $query = BillReminder::query();
        
        // Apply filters if they exist
        if ($this->request->filled('filter_id')) {
            $query->where('ftth_id', 'like', '%' . $this->request->filter_id . '%');
        }

        if ($this->request->filled('filter_name')) {
            $query->where('name', 'like', '%' . $this->request->filter_name . '%');
        }

        if ($this->request->filled('filter_phone')) {
            $query->where('phone_1', 'like', '%' . $this->request->filter_phone . '%');
        }

        if ($this->request->filled('filter_expiry')) {
            $startDate = Carbon::parse($this->request?->filter_expiry[0])->format('Y-m-d');
            $endDate =Carbon::parse($this->request?->filter_expiry[1])->format('Y-m-d');
  
            $query->where('service_off_date', '>=', $startDate);
            $query->where('service_off_date', '<=',  Carbon::parse($endDate)->endOfDay());
        }

        if ($this->request->has('filter_no_phone')) {
            $noPhoneValue = $this->request->filter_no_phone;
            // Handle string 'true'/'false' from query parameters
            if (is_string($noPhoneValue) && $noPhoneValue === 'true') {
                $noPhoneValue = true;
            }
            
            if ($noPhoneValue === true || $noPhoneValue === 1) {
                $query->where(function ($query) {
                    $query->whereNull('phone_1')
                        ->orWhere('phone_1', '')
                        ->orWhereRaw("TRIM(phone_1) = ''");
                });
            }
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Phone',
            'Service Off Date',
            'SMS Status',
            'SMS Sent At',
            // Add other columns as needed
        ];
    }
    public function columnFormats(): array
    {
        return [
            'C' => StyleNumberFormat::FORMAT_TEXT,
            'D' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
    public function map($customer): array
    {

        return [
            $customer->ftth_id,
            $customer->name,
            $customer->phone_1,
            ($customer->service_off_date) ? Date::stringToExcel($customer->service_off_date) : null,
            $customer->sms_status,
            $customer->sms_sent_at ? Date::stringToExcel($customer->sms_sent_at) : null,
        ];
    }
}