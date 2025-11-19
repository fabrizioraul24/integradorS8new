<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'department',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'delivery_city_id');
    }
}
