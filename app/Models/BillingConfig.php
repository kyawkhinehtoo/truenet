<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingConfig extends Model
{
    use HasFactory;
    protected $table = 'bill_configuration';
    protected $primaryKey = 'id';
    protected $fillable = [
        'exclude_list','mrc_day','prepaid_day','mrc_month','prepaid_month', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'exclude_list' => 'string','mrc_day'=>'integer','prepaid_day'=>'integer','mrc_month'=>'integer','prepaid_month' => 'integer' ,'created_at' => 'timestamp', 'updated_at' => 'timestamp'
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
