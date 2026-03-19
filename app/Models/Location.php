<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building',
        'floor',
        'room',
        'description',
        'is_active',
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}