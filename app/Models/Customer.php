<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'whatsapp',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
