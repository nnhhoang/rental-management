<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
