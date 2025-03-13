<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerHistory;
use Illuminate\Support\Facades\Auth;
use DateTime;

class CustomerHistoryService
{
    /**
     * Store a new Customer History entry if there are changes.
     *
     * @param  Customer  $customer     The newly updated customer model (after changes)
     * @param  Customer  $oldCustomer  The old snapshot of the customer model (before changes)
     * @param  array     $changes      Array of changed attributes [attribute => newValue]
     * @param  int       $requestId    The ID from the request (customer_id)
     * @param  string|null $startDate  (Optional) Start date from the request, if any
     *
     * @return void
     */
    protected array $foreignKeyMap = [
        'township_id'    => [\App\Models\Township::class, 'name'],
        'project_id'     => [\App\Models\Project::class, 'name'],
        'sale_person_id' => [\App\Models\User::class, 'name'],
        'status_id'      => [\App\Models\Status::class, 'name'],
        'package_id'      => [\App\Models\Package::class, 'name'],
        'subcom_id'      => [\App\Models\Subcom::class, 'name'],
        'pop_id'      => [\App\Models\Pop::class, 'site_name'],
        'pop_device_id'      => [\App\Models\PopDevice::class, 'device_name'],
        'dn_id'      => [\App\Models\DnPorts::class, 'name'],
        'sn_id'      => [\App\Models\SnPorts::class, 'name'],

        // Add others as needed
    ];

    public function storeCustomerHistory(
        Customer $customer,
        ?Customer $oldCustomer,
        array $changes,
        int $requestId,
        ?string $startDate = null
    ): void {
        // Only proceed if there are changes
        if (empty($changes)) {
            return;
        }
        $general = null;
        $startDate = null;
        foreach ($changes as $changeKey => $changeValue) {
            // The fields we want to skip entirely
            $notCheck = ['payment_type', 'foc', 'onu_property','deleted','created_at','updated_at'];
        
            if (! in_array($changeKey, $notCheck)) {

                // If the field is defined in our $foreignKeyMap ...
                 // Get old ID from the old customer record
                 $oldId = null;
                 $oldRecord = null;
                 $oldName = null;
                 if($oldCustomer){
                 $oldId = $oldCustomer->$changeKey;
                
                 }
                if (array_key_exists($changeKey, $this->foreignKeyMap)) {
                    [$modelClass, $displayField] = $this->foreignKeyMap[$changeKey];
                    
                  
                   if($oldRecord){
                    $oldRecord = $modelClass::find($oldId);
                    $oldName = $oldRecord ? $oldRecord->$displayField : '(unknown)';
                   }
              
                    
                    // Get new ID from the changes array
                    $newRecord = $modelClass::find($changeValue);
                    $newName = $newRecord ? $newRecord->$displayField : '(unknown)';
                    
                    // Log both old and new
                    if($newRecord || $oldRecord){
                        $general .= strtoupper($changeKey). ': &nbsp;';
                        $general .= ($oldRecord)?$oldName . ' → '.$newName. '<br />':$newName . '<br />';
                    }
        
                } else {
                    // For *non*-foreign-key fields, do your usual logic
                    // (OR check if it’s one of your custom “special” fields like 'start_date', 'end_date', etc.)
                    if($oldRecord){
                        $oldName = $oldRecord ? $oldRecord->$changeKey : '(unknown)';
                    }       
                    if($changeValue){
                        $general .= strtoupper($changeKey). ': &nbsp;';
                        $general .= ($oldRecord)?$oldName . ' → '.strtoupper($changeValue). '<br />':strtoupper($changeValue) . '<br />';
                    }
                  
                }

            }
        }
        if($general){
             // 1) Mark the old history record(s) for this customer as inactive
             CustomerHistory::where('customer_id', $requestId)
             ->update(['active' => 0]);
            // 2) Prepare a new CustomerHistory model
            $newHistory = new CustomerHistory();
            // Initialize 'general' field so we can safely append
            $newHistory->general = $general;
            $newHistory->customer_id = $customer->id;
            $newHistory->actor_id    = Auth::id();
            $newHistory->start_date = $startDate;
            // 3) Finalize the new record
            $newHistory->active = 1;
            // Use standard datetime format for consistency
            $newHistory->date   = date('Y-m-d H:i:s'); 
            $newHistory->save();
        }
        
    }
}