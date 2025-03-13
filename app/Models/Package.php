<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string     $name
 * @property int        $created_at
 * @property int        $updated_at
 */
class Package extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'packages';

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
        'speed',
        'type',
        'status',
        'sla_id',
        'price',
        'contract_period',
        'radius_package',
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
        'qty' => 'string',
        'price' => 'string',
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
    public $timestamps = false;

    // Scopes...

    // Functions ...

    // Relations ...
    public function pop()
    {
        return $this->hasOne(Pop::class);
    }
    public function packageBundle()
    {
        return $this->hasMany(PackageBundle::class);
    }
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}
