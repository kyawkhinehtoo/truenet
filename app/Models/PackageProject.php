<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageProject extends Model
{
    use HasFactory;
    protected $table = 'package_project';
    protected $primaryKey = 'id';
    protected $fillable = [
        'package_id', 'project_id','created_at', 'updated_at'
    ];
    protected $dates = [
        'created_at', 'updated_at'
    ];
     // Relations ...
     public function package()
     {
         return $this->hasMany(Package::class);
     }
     public function project()
     {
         return $this->hasMany(Project::class);
     }
}
