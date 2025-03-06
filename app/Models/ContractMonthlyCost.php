<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractMonthlyCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_contract_id',
        'monthly_cost_id',
        'pay_type',
        'price',
    ];

    public function contract()
    {
        return $this->belongsTo(TenantContract::class, 'tenant_contract_id');
    }

    public function monthlyCost()
    {
        return $this->belongsTo(MonthlyCost::class);
    }
}
