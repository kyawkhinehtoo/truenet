<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    protected $table = 'bills';
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'default_period_covered',
        'bill_number',
        'bill_month',
        'bill_year',
        'status',
        'created_at',
        'updated_at'
     ];
}
