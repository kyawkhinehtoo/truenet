<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingTemp extends Model
{
    protected $table = 'temp_billings';
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'period_covered',
        'bill_number',
        'customer_id',
        'ftth_id',
        'date_issued',
        'bill_to',
        'attn',
        'previous_balance',
        'current_charge',
        'compensation',
        'otc',
        'sub_total',
        'payment_duedate',
        'service_description',
        'qty',
        'usage_days',
        'customer_status',
        'normal_cost',
        'type',
        'total_payable',
        'discount',
        'amount_in_word',
        'commercial_tax',
        'tax',
        'email',
        'phone',
        'file',
        'bill_month',
        'bill_year',
        'popsite_id',
        'start_date',
        'end_date',
        'usage_day',
        'usage_month',
        'bonus_day',
        'bonus_month',
        'public_ip',
        'created_at',
        'updated_at'

    ];
}
