<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutageReport extends Model
{
    protected $fillable = [
        'farmer_id', 'zone_id', 'reported_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime'
    ];

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }
}
