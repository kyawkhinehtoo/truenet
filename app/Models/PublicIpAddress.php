<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicIpAddress extends Model
{
    use HasFactory;
    protected $table = 'public_ip_addresses';
    protected $fillable = [
        'ip_address','description','annual_charge','currency','customer_id','created_at', 'updated_at'
    ];
    protected $dates = [
        'created_at', 'updated_at'
    ];
    public function getCreatedAtAttribute($date)
    {
        return  date('d-M-Y H:i:s', strtotime($date));
    }
    public function getUpdatedAtAttribute($date)
    {
      return  date('d-M-Y H:i:s', strtotime($date));
       
    }   
}
