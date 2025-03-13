<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GponInfo extends Model
{
    use HasFactory;
    protected $table = 'gpon_info';
    protected $fillable = [
        'pop_id',
        'olt_name',
        'no_of_frame'
    ];
}