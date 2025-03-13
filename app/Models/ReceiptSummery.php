<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSummery extends Model
{
    protected $table = 'receipt_summery';

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
        'customer_id','1','2','3','4','5','6','7','8','9','10','11','12','year','created_at', 'updated_at'
    ];
 
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        
    ];
 
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
       'created_at' => 'timestamp', 'updated_at' => 'timestamp'
    ];
 
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];
 
    /**
     * Indicates if the model should be timestamped.
     *
     * @var boolean
     */
    public $timestamps = false;
}
