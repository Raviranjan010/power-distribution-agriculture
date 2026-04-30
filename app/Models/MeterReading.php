<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $fillable = [
        'connection_id', 'lineman_id', 'reading_date', 'previous_reading', 
        'current_reading', 'units_consumed', 'is_verified', 'remarks'
    ];
    protected $casts = [
        'reading_date' => 'date',
        'is_verified' => 'boolean'
    ];

    public function connection() { return $this->belongsTo(Connection::class); }
    public function lineman() { return $this->belongsTo(User::class, 'lineman_id'); }
}