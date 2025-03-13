<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model
{
    protected $table = 'bill_adjustment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'invoice_id', 
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
        'tax_to',
        'tax',
        'email',
        'phone',
        'mail_sent_date',
        'sms_sent_date',
        'mail_sent_status',
        'sms_sent_status',
        'reminder_sms_sent_date',
        'reminder_sms_sent_status',
        'file',
        'url',
        'bill_month',
        'bill_year',
        'adjustment_type',
        'coupon_id',
        'adjusted_by',
        'created_at',
        'updated_at'
        
    ];
}
