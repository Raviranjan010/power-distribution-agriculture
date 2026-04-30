<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    protected $fillable = [
        'connection_id',
        'log_date',
        'units_consumed',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }
}
