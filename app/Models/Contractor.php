<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Contractor extends Model
{
    /** @use HasFactory<\Database\Factories\ContractorFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'name', 'code', 'email', 'phone_number', 'company_name', 'balance'];

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
