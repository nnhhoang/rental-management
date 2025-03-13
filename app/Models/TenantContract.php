<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'apartment_room_id',
        'tenant_id',
        'pay_period',
        'price',
        'electricity_pay_type',
        'electricity_price',
        'electricity_number_start',
        'water_pay_type',
        'water_price',
        'water_number_start',
        'number_of_tenant_current',
        'note',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(ApartmentRoom::class, 'apartment_room_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function monthlyCosts()
    {
        return $this->hasMany(ContractMonthlyCost::class);
    }

    public function feeCollections()
    {
        return $this->hasMany(RoomFeeCollection::class);
    }

    public function isActive()
    {
        return is_null($this->end_date) || $this->end_date > now();
    }
}
