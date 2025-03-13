<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiusConfig extends Model
{
    use HasFactory;
    protected $table = 'radius_config';
    protected $primaryKey = 'id';
    protected $fillable = [
        'server','port','enabled','admin_user','admin_password', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'server' => 'string','admin_user'=>'string','admin_password'=>'string','port'=>'integer','enabled' => 'integer' ,'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

}
