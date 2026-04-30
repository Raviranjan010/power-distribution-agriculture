<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerSubsidy extends Model
{
    protected $fillable = [
        'consumer_id', 'scheme_id', 'approved_by', 'status', 'applied_at', 
        'approved_at', 'remarks'
    ];
    protected $casts = [
        'applied_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    public function consumer() { return $this->belongsTo(User::class, 'consumer_id'); }
    public function scheme() { return $this->belongsTo(SubsidyScheme::class, 'scheme_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}