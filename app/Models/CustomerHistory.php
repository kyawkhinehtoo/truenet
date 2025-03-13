<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerHistory extends Model
{
    use HasFactory;
    protected $table = 'customer_histories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'status_id', 'customer_id', 'actor_id','type', 'start_date', 'end_date', 'old_address', 'old_location', 'new_address', 'new_location','active','date','general'
    ];
}
