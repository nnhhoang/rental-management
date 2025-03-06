<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tel',
        'identity_card_number',
        'email',
    ];

    public function contracts()
    {
        return $this->hasMany(TenantContract::class);
    }

    public function feeCollections()
    {
        return $this->hasMany(RoomFeeCollection::class);
    }
}
