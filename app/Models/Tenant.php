<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
