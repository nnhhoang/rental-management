<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_room_id',
        'usage_number',
        'input_date',
        'image',
    ];

    protected $casts = [
        'input_date' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }
}
