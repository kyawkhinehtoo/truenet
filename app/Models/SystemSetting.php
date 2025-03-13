<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo_small',
        'logo_large',
        'application_name',
        'theme_color',
        'accent_color',
    ];
}
