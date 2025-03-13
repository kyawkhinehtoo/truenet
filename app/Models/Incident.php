<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{

     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'incidents';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'priority',
        'incharge_id',
        'customer_id',
        'package_id',
        'type',
        'topic',
        'suspense_from',
        'suspense_to',
        'resume',
        'new_address',
        'location',
        'termination',
        'date',
        'close_date',
        'time',
        'close_time',
        'description',
        'status',
        'closed_by',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code'=> 'string',
        'priority'=> 'string',
        'incharge_id'=> 'string',
        'customer_id'=> 'string',
        'package_id'=> 'string',
        'type'=> 'string',
        'topic'=> 'string',
        'suspense_from'=> 'timestamp',
        'suspense_to'=> 'timestamp',
        'resume'=> 'timestamp',
        'new_address'=> 'string',
        'location'=> 'string',
        'termination'=> 'timestamp',
        'date'=> 'timestamp',
        'close_date'=> 'timestamp',
        'time'=> 'timestamp',
        'close_time'=> 'timestamp',
       'description' => 'string',
       'status' => 'string', 
       'closed_by'=> 'string',
       'created_at' => 'timestamp', 
       'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'suspense_from','suspense_to','resume','termination','date', 'created_at', 'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;

}
