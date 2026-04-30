<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'grv_number', 'consumer_id', 'connection_id', 'complaint_type', 
        'description', 'priority', 'status', 'assigned_to', 'assigned_by', 
        'resolution_remarks', 'filed_at', 'resolved_at'
    ];
    protected $casts = [
        'filed_at' => 'datetime',
        'resolved_at' => 'datetime'
    ];

    public function consumer() { return $this->belongsTo(User::class, 'consumer_id'); }
    public function connection() { return $this->belongsTo(Connection::class); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
}