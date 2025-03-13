<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\CustomerHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class CustomersUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'Customer ID' ){
            
            $customer = Customer::where('ftth_id','LIKE',trim($row[0]).'%')->first();
         //   $status = Status::where('name','LIKE','%'.$row[1].'%')->first();  
            // $active_satus = Status::where('name','LIKE','%Active%')->first();  
            // $suspend_satus = Status::where('name','LIKE','%Suspend%')->first();  
             Storage::prepend("update.log","Updating the Data : ".date("Y-m-j h:m:s"));
             if($customer){
     

                    // CustomerHistory::where('customer_id', '=', $customer->id)->update(['active'=>0]);

                    // $active_history = new CustomerHistory();
                    // $active_history->customer_id = $customer->id;
                    // $active_history->new_status = $status->id;
                    // //$active_history->start_date =  date("Y-m-j h:m:s");
                    // $active_history->type = $status->name;
                    // $active_history->active = 1;
                    // $active_history->actor_id = Auth::user()->id;
                    // $active_history->save();
            
                $customer->onu_serial = trim($row[1]);
                $customer->update();
                $log = $row[0]." Updated";
                Storage::append('update.log',$log);
               
           
             }else{
                $log = $row[0]." Not Found";
                Storage::append('update.log',$log);
             }
     

         
        }
    }
}
