<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Package;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class PackageUpdate implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        $package = Package::where('name', 'LIKE', '%'.trim($row['srv_name']).'%')->first();

        if ($package) {
            $package->update([
                'speed' => $row['total_speed'],
                'radius_package' => $row['srv_id'],
                'contract_period' => strtolower($row['contract_period']),
                'type' => $row['type'],
                'sla_id' => 1,
                'status' => 1,
                'price' => 10000,
                'currency' => 'mmk',
            ]);
            return $package;
        } else {
            return new Package([
                'name'  => $row['srv_name'],
                'speed' => $row['total_speed'],
                'radius_package' => $row['srv_id'],
                'contract_period' => strtolower($row['contract_period']),
                'type' => $row['type'],
                'sla_id' => 1,
                'status' => 1,
                'price' => 10000,
                'currency' => 'mmk',
            ]);
        }       
    }
}
