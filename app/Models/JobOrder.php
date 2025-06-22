<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Str;

class JobOrder extends Model
{
    /** @use HasFactory<\Database\Factories\JobOrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'date', 'jos_date', 'type_of_work_id', 'contractor_id', 'conductor_id', 'actual_work_completed', 'remarks', 'reference_number'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($jo) {
            $prefix = 'JO-' . now()->format('Ym');

            do {
                $random = strtoupper(Str::random(4)); // e.g., 4-char random alphanumeric
                $reference = "{$prefix}-{$random}";
            } while (self::where('reference_number', $reference)->exists());

            $jo->reference_number = $reference;
        });
    }


    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function typeOfWork() {
        return $this->belongsTo(TypeOfWork::class);
    }

    public function Contractor() {
        return $this->belongsTo(Contractor::class); 
    }

    public function conductor() {
        return $this->belongsTo(Conductor::class);
    }

    public function jobOrderStatementLinks() {
        return $this->hasMany(JosJobOrderLink::class);
    }
}

