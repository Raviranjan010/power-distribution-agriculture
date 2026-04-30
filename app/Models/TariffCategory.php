<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TariffCategory extends Model
{
    protected $fillable = ['name', 'rate_per_unit', 'fixed_charge_per_kw', 'applicable_to', 'effective_from', 'is_active'];
    protected $casts = [
        'effective_from' => 'date',
        'is_active' => 'boolean'
    ];
}