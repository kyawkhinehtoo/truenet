<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string     $name
 * @property int        $created_at
 * @property int        $updated_at
 */
class Role extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
        'permission',
        'read_customer',
        'read_incident',
        'write_incident',
        'edit_invoice',
        'bill_generation',
        'bill_receipt',
        'delete_customer',
        'radius_read',
        'radius_write',
        'incident_report',
        'bill_report',
        'radius_report',
        'incident_only',
        'read_only_ip',
        'add_ip',
        'edit_ip',
        'delete_ip',
        'ip_report',
        'delete_invoice',
        'enable_customer_export',
        'activity_log',
        'system_setting',
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
        'permission' => 'string',
        'read_customer' => 'integer',
        'read_incident' => 'integer',
        'write_incident' => 'integer',
        'edit_invoice' => 'integer',
        'bill_generation' => 'integer',
        'bill_receipt' => 'integer',
        'radius_read' => 'integer',
        'radius_write' => 'integer',
        'incident_report' => 'integer',
        'bill_report' => 'integer',
        'radius_report' => 'integer',
        'incident_only' => 'integer',
        'enable_customer_export' => 'integer',
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
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'id');
        // 'role' is the foreign key in the users table
        // 'id' is the primary key in the roles table
    }
}
