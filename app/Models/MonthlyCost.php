<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function contractCosts()
    {
        return $this->hasMany(ContractMonthlyCost::class);
    }
}
