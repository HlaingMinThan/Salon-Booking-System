<?php

namespace App\Models;

use Database\Factories\SlotFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slot extends Model
{
    /** @use HasFactory<SlotFactory> */
    use HasFactory;

    protected $fillable = ['service_id', 'date', 'start_time', 'is_available'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_available' => 'boolean',
        ];
    }

    /** @return BelongsTo<Service, $this> */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /** @return HasOne<Booking, $this> */
    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
