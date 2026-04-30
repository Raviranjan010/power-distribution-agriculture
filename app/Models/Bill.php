<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_number', 'connection_id', 'meter_reading_id', 'billing_month', 
        'billing_year', 'units_consumed', 'rate_per_unit', 'energy_charges', 
        'fixed_charges', 'taxes', 'subsidy_amount', 'net_payable', 'due_date', 
        'status', 'generated_by'
    ];
    protected $casts = [
        'due_date' => 'date'
    ];

    public function connection() { return $this->belongsTo(Connection::class); }
    public function meterReading() { return $this->belongsTo(MeterReading::class); }
    public function payments() { return $this->hasMany(Payment::class); }
}