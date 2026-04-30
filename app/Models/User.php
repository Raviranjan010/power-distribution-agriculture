<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role', 'division_id', 
        'zone_id', 'farmer_id_number', 'address', 'village', 'district', 
        'state', 'aadhar_number', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function connections() { return $this->hasMany(Connection::class, 'consumer_id'); }
    public function zone() { return $this->belongsTo(Zone::class); }
}