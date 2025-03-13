<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\DnPorts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DNUpdate implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // if($row[0] != 'DN Name' ){
        //     $dn = DnPorts::where('name','=',trim($row[0]))->first();  
        //      if($dn){
        //         $dn->location = trim($row[1]);
        //         $dn->input_dbm = trim($row[2]);
        //         $dn->update();
        //         $log = $row[0]." Updated";
        //         Storage::append('dn_update.log',$log);
        //       }else{
        //         $log = $row[0]." Not Found";
        //         Storage::append('dn_update.log',$log);
        //      }



        // }
        return new DnPorts([
            'name'  => $row['name'],
            'description' => "DN First Update",
            'location' => "22.116054,95.157630",
            'input_dbm' => 1,
            'pop' => 1,
            'pop_device_id' => 1,
            'gpon_frame' => $row['frame'],
            'gpon_slot' => $row['slot'],
            'gpon_port' => $row['port'],
        ]);
    }
}