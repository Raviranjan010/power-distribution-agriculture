<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name', 'district', 'state', 'division_id', 'sdo_id'];

    public function sdo() { return $this->belongsTo(User::class, 'sdo_id'); }
}