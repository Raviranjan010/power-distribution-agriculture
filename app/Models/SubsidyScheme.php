<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubsidyScheme extends Model
{
    protected $fillable = [
        'scheme_name', 'description', 'eligibility_criteria', 'discount_percentage', 
        'max_units_covered', 'start_date', 'end_date', 'is_active'
    ];
    protected $casts = [
        'eligibility_criteria' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];
}