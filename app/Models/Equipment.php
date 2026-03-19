<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'name',
        'brand',
        'model',
        'category_id',
        'location_id',
        'status',
        'purchase_date',
        'warranty_expiry',
        'notes',
        'qr_code_path',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Add any other model methods here
}