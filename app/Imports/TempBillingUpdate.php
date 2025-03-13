<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\BillingTemp;
use Illuminate\Support\Facades\Storage;
class TempBillingUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'Period Covered' ){
            
            $temp_billing = BillingTemp::where('ftth_id','LIKE',trim($row[2]).'%')->first();
            $customer = Customer::where('ftth_id','LIKE',trim($row[2]).'%')->first();
             if($temp_billing){
                if($row[0])
                $temp_billing->period_covered = $row[0];
                $temp_billing->bill_number = $row[1];
                $temp_billing->customer_id = $customer->id;
                $temp_billing->ftth_id = trim($row[2]);
                $temp_billing->date_issued = (trim($row[3]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[3]):null;
                $temp_billing->bill_to = trim($row[4]);
                $temp_billing->attn = trim($row[5]);
                $temp_billing->previous_balance = $row[6];
                $temp_billing->current_charge = $row[7];
                $temp_billing->compensation = $row[8];
                $temp_billing->otc = $row[9];
                $temp_billing->sub_total = $row[10];
                $temp_billing->payment_duedate = (trim($row[11]) != '')?\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[11]):null;;
                $temp_billing->service_description = $row[12];
                $temp_billing->qty = $row[13];
                $temp_billing->usage_days = $row[14];
                $temp_billing->customer_status = $row[15];
                $temp_billing->normal_cost = $row[16];
                $temp_billing->type = $row[17];
                $temp_billing->discount = $row[18];
                $temp_billing->total_payable = $row[19];
                // $temp_billing->commercial_tax = $row[21];
                // $temp_billing->email = $row[22];
                // $temp_billing->phone = $row[23];
                $temp_billing->update();
                $log = 'Updated the Temp billing for '.trim($row[2]);
                Storage::append('update_bill.log',$log);
                }else{
                $log = $row[2]." Not Found";
                Storage::append('update_bill.log',$log);
             }
     

         
        }
    }
}
