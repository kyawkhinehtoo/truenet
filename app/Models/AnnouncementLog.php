<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementLog extends Model
{
    protected $table = 'announcement_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'customer_id', 
        'sender_id', 
        'template_id', 
        'announcement_id', 
        'title', 
        'detail',
        'status',
        'type',
        'message_id',
        'date'
    ];
}
