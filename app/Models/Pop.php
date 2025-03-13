<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pop extends Model
{
    use HasFactory;
    protected $table = 'pops';
    protected $primaryKey = 'id';
    protected $fillable = [
        'site_name','site_description', 'site_location','created_at', 'updated_at'
    ];
    protected $casts = [
        'site_name' => 'string','site_description'=>'string', 'site_location'=>'string','created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];
    public function package()
    {
        return $this->hasMany(Package::class);
    }
}
