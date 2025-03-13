<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'general',
        'packages',
        'packages_invert',
        'townships',
        'townships_invert',
        'projects',
        'projects_invert',
        'customer_status',
        'customer_status_invert',
        'payment_type',
        'deposit_status',
        'announcement_type',
        'template_id',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'projects' => 'array',
    ];
}
