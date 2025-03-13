<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBBackup extends Model
{
    use HasFactory;
    protected $table = 'db_backup';
    protected $fillable = [
        'backup_email',
        'backup_frequency',
        'created_at',
        'updated_at'
    ];
}