<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;


class TypeOfWork extends Model
{
    /** @use HasFactory<\Database\Factories\TypeOfWorkFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'rate', 'code'];

    public function jobOrders() {
        return $this->hasMany(JobOrder::class);
    }

    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }
}
