<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DnPorts extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dn_ports';

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
        'name',
        'port',
        'description',
        'location',
        'input_dbm',
        'pop',
        'pop_device_id',
        'gpon_frame',
        'gpon_slot',
        'gpon_port',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'port' => 'integer',
        'description' => 'string',
        'location' => 'string',
        'input_dbm' => 'string',
        'pop' => 'json',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'dn_id');
    }
}