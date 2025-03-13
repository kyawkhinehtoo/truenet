<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'rate', 'contact_person', 'email', 'phone', 'disabled', 'created_at', 'updated_at'
    ];
}
