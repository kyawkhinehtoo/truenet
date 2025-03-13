<?php

namespace App\Imports;

use App\Models\BillReminder;
use App\Models\Bills;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Package;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
class BillReminderUpdate implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        $billReminder = BillReminder::where('ftth_id', trim($row['id']))->first();
        $customer = Customer::with('package')->where('ftth_id', trim($row['id']))
        ->first();
        $serviceOffDate = (trim($row['service_off_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_off_date'])) : null;
        $daysRemaining = $serviceOffDate ? Carbon::now()->diffInDays($serviceOffDate, false) : 0;
        if ($billReminder && $customer) {
            
           
                $billReminder->update([
                    'ftth_id' => $row['id'],
                    'name' => $row['customer_name'],
                    'customer_id'=> $customer->id,
                    'status_id'=> $customer->status_id,
                    'email'=> $customer->email,
                    'days_remaining' => $daysRemaining,
                    'package_name' => $customer->package->name,
                    'package_price' =>  $customer->package->price,
                    'phone_1' => $row['phone'],
                    'service_off_date' =>  (trim($row['service_off_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_off_date'])) : null,
                    'sms_staus' => $row['sms_status'],
                    'sms_sent_at' =>(trim($row['sms_sent_at'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['sms_sent_at'])) : null,
                  
                ]);
                return $billReminder;
            
            
        } else if($customer){
            return new BillReminder([
                'ftth_id' => $row['id'],
                'name' => $row['customer_name'],
                'customer_id'=> $customer->id,
                'status_id'=> $customer->status_id,
                'email'=> $customer->email,
                'days_remaining' => $daysRemaining,
                'package_name' => $customer->package->name,
                'package_price' =>  $customer->package->price,
                'phone_1' => $row['phone'],
                'service_off_date' =>  (trim($row['service_off_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_off_date'])) : null,
                'sms_staus' => $row['sms_status'],
                'sms_sent_at' =>(trim($row['sms_sent_at'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['sms_sent_at'])) : null,
         ]);
        }       
    }
}
