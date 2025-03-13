<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFeeCollectionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_fee_collection_id',
        'paid_date',
        'price',
    ];

    protected $casts = [
        'paid_date' => 'datetime',
    ];

    public function feeCollection()
    {
        return $this->belongsTo(RoomFeeCollection::class, 'room_fee_collection_id');
    }
}
