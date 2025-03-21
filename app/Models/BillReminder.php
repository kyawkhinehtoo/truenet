<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillReminder extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'temp_bill_reminders';

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
    public static function getCustomersForReminder($expiration)
    {
      
        $startDate = Carbon::parse($expiration[0])->format('Y-m-d');
        $endDate = Carbon::parse($expiration[1])->format('Y-m-d');
        // Clear existing data in the table
        self::truncate();
        
        // Insert fresh data into the table
        DB::table('temp_bill_reminders')->insertUsing(
            ['customer_id','ftth_id', 'name', 'email', 'phone_1', 'status_id', 'service_off_date', 'days_remaining', 'package_name', 'package_price'],
            DB::table('customers as c')
                ->leftJoin('packages as p', 'c.package_id', '=', 'p.id')
                ->select(
                    'c.id as customer_id',
                    'c.ftth_id',
                    'c.name',
                    'c.email',
                    'c.phone_1',
                    'c.status_id',
                    'c.service_off_date',
                    DB::raw('DATEDIFF(c.service_off_date, NOW()) as days_remaining'),
                    'p.name as package_name',
                    'p.price as package_price'
                )
                ->where('c.status_id', 2)
                ->where('c.service_off_date', '>=', $startDate)
                ->where('c.service_off_date', '<=', Carbon::parse($endDate)->endOfDay())
        );
        
        // Return data from the table
        return self::paginate(50);
    }
}