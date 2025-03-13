<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopDevice extends Model
{
    use HasFactory;
    protected $table = 'pop_devices';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pop_id','device_name','qty','remark', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'device_name' => 'string','remark'=>'string','qty'=>'integer','created_at' => 'timestamp', 'updated_at' => 'timestamp'
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
