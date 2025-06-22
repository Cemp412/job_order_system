<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Conductor extends Model
{
    /** @use HasFactory<\Database\Factories\ConductorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'first_name', 'middle_name', 'last_name', 'staff_id', 'email', 'phone_number', 'department_name'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function jobOrders() {
        return $this->hasMany(JobOrder::class);
    }

    
    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }
}
