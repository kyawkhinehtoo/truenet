<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
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
        'invoice_number',
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
        'public_ip',
        'email',
        'phone',
        'sent_date',
        'mail_sent_status',
        'sms_sent_status',
        'invoice_file',
        'invoice_url',
        'bill_month',
        'bill_year',
        'reminder_sms_sent_status',
        'reminder_sms_sent_date',
        'popsite_id',
        'start_date',
        'end_date',
        'usage_day',
        'usage_month',
        'bonus_day',
        'bonus_month',
        'created_at',
        'updated_at'

    ];
}
