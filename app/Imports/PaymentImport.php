<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\PaymentRecord;


class PaymentImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'No' ){
        $customer = Customer::where('ftth_id','LIKE','%'.trim($row[1]).'%')->first();
        if($customer){
            return new PaymentRecord([
                'customer_id'=> $customer->id,
                'issue_amount'=> (trim($row[2]))?ceil(trim($row[2])):0,
                'paid_amount'=>(trim($row[3]))?ceil(trim($row[3])):0,
                'month'=> $row[4],
                'year'=> $row[5],
                'bill_no'=> $row[6],
                'remark'=> $row[7],
            ]);
        }
        
            
        }
    }
}
