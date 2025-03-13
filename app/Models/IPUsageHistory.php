<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPUsageHistory extends Model
{
    use HasFactory;
    protected $table = 'ip_usage_history';
    protected $fillable = [
        'ip_id','customer_id','start_date','end_date','created_at', 'updated_at'
    ];
    protected $dates = [
        'created_at', 'updated_at'
    ];

}
