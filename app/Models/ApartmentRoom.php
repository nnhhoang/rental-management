<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApartmentRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_id',
        'room_number',
        'default_price',
        'max_tenant',
        'image',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function contracts()
    {
        return $this->hasMany(TenantContract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(TenantContract::class)
            ->whereNull('end_date')
            ->orWhere('end_date', '>', now());
    }

    public function electricityUsages()
    {
        return $this->hasMany(ElectricityUsage::class);
    }

    public function waterUsages()
    {
        return $this->hasMany(WaterUsage::class);
    }

    public function feeCollections()
    {
        return $this->hasMany(RoomFeeCollection::class);
    }
}
