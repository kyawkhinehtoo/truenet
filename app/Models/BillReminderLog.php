<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillReminderLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temp_bill_reminder_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id','ftth_id', 'name', 'email', 'phone_1', 'status_id',
        'service_off_date', 'days_remaining', 'package_name', 'package_price','sms_status','sms_sent_at','send_by','onetimecode'
    ];

    /**
     * Get customers who need bill reminders based on days before service_off_date
     *
     * @param int $daysBeforeExpiry Number of days before service_off_date to check
     * @return \Illuminate\Support\Collection
     */
    public function user()
    {
        return $this->hasMany(User::class,'id','send_by');
    }
}