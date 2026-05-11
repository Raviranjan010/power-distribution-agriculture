<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id', 'title', 'description', 'scheduled_date', 
        'from_time', 'to_time', 'reason', 'posted_by'
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
