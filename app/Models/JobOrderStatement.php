<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobOrderStatement extends Model
{
    use SoftDeletes;

    protected $fillable = ['reference_number', 'contractor_id', 'conductor_id', 'month', 'total_amount', 'paid_amount', 'balance_amount', 'remarks'];

    protected static function booted()
    {
        static::creating(function ($jos) {
            do {
                $prefix = 'JOS-' . now()->format('Ym');
                $count = self::where('reference_number', 'like', $prefix . '%')->count() + 1;
                $reference = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            } while (self::where('reference_number', $reference)->exists());

            $jos->reference_number = $reference;
        });
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Conductor::class);
    }

    public function jobOrders(): BelongsToMany
    {
        return $this->belongsToMany(JobOrder::class, 'jos_job_order_links');
    }

}
