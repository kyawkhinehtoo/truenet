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
class ContractUpdate implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if($row[0] != 'No' ){
            
            $customer = Customer::where('ftth_id','LIKE',trim($row[1]).'%')->first();
            
            // $active_satus = Status::where('name','LIKE','%Active%')->first();  
            // $suspend_satus = Status::where('name','LIKE','%Suspend%')->first();  
             if($customer){
     

                    
                $customer->contract_term = trim($row[2]);
                $customer->installation_date = trim($row[3]);
                $customer->update();
                $log = $row[0]." Updated";
                Storage::append('contract_update.log',$log);
               
           
             }else{
                $log = $row[0]." Not Found";
                Storage::append('contract_update.log',$log);
             }
     

         
        }
    }
}
