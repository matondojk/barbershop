<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'is_active',
    ];

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class)
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }
}
