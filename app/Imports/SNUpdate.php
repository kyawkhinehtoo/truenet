<?php

namespace App\Imports;

use App\Models\DnPorts;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\SnPorts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SNUpdate implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $dn = DnPorts::where('name', $row['dn_name'])->first();
        if ($dn) {
            return new SnPorts([
                'name'  => $row['sn_name'],
                'dn_id' => $dn->id,
                'description' => "SN First Update",
                'location' => "22.116054,95.157630",
                'input_dbm' => 1,
            ]);
        }
    }
}