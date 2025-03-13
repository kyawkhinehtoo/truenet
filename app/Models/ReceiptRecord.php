<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class ReceiptRecord extends Model
{

    protected $table = 'receipt_records';

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
        'customer_id',
        'receipt_number',
        'month',
        'year',
        'bill_no',
        'invoice_id',
        'status',
        'issue_amount',
        'issue_currenty',
        'receipt_person',
        'payment_channel',
        'collected_person',
        'receipt_date',
        'remark',
        'collected_amount',
        'outstanding_amount',
        'collected_currency',
        'collected_exchangerate',
        'receipt_file',
        'receipt_url',
        'created_at',
        'updated_at',
        'transition'
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
        'receipt_date' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'receipt_date',
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
    public function getCreatedAtAttribute($date)
    {
        return  date('d-M-Y H:i:s', strtotime($date));
    }
    public function getReceiptDateAttribute($date)
    {
        return  date('d-M-Y H:i:s', strtotime($date));
    }
}
