<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $fillable = [
        'connection_number',
        'consumer_id',
        'connection_type',
        'field_name',
        'sanctioned_load_kw',
        'meter_number',
        'tariff_category_id',
        'status',
        'installation_date',
        'sdo_id',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'sanctioned_load_kw' => 'decimal:2',
    ];

    public function consumer()
    {
        return $this->belongsTo(User::class, 'consumer_id');
    }

    public function tariffCategory()
    {
        return $this->belongsTo(TariffCategory::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function sdo()
    {
        return $this->belongsTo(User::class, 'sdo_id');
    }
}
