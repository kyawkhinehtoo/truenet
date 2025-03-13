<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageCity extends Model
{
    use HasFactory;
    protected $table = 'package_city';
    protected $primaryKey = 'id';
    protected $fillable = [
        'package_id', 'city_id','created_at', 'updated_at'
    ];
    protected $dates = [
        'created_at', 'updated_at'
    ];
     // Relations ...
     public function package()
     {
         return $this->hasMany(Package::class);
     }
     public function city()
     {
         return $this->hasMany(City::class);
     }

}
