<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomFeeCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_contract_id',
        'apartment_room_id',
        'tenant_id',
        'electricity_number_before',
        'electricity_number_after',
        'water_number_before',
        'water_number_after',
        'charge_date',
        'total_debt',
        'total_price',
        'total_paid',
        'fee_collection_uuid',
    ];

    protected $casts = [
        'charge_date' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(TenantContract::class, 'tenant_contract_id');
    }

    public function room()
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function histories()
    {
        return $this->hasMany(RoomFeeCollectionHistory::class);
    }

    public function isFullyPaid()
    {
        return $this->total_paid >= $this->total_price;
    }
}
