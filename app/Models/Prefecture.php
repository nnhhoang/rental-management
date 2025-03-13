<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_id',
        'ward_name',
        'ward_name_en',
        'ward_level',
        'district_id',
        'district_name',
        'province_id',
        'province_name',
    ];
}
