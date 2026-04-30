<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'bill_id', 'consumer_id', 'amount', 'payment_method', 'transaction_id', 
        'gateway_response', 'status', 'paid_at', 'received_by'
    ];
    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime'
    ];

    public function bill() { return $this->belongsTo(Bill::class); }
    public function consumer() { return $this->belongsTo(User::class, 'consumer_id'); }
}