<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'instance_id',
        'token',
        'is_active',
    ];

    public static function getActiveInstanceName()
    {
        return self::where('is_active', true)->value('name');
    }
}
