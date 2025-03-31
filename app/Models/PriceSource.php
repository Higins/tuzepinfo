<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriceSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'url',
        'config',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    public function priceHistory(): HasMany
    {
        return $this->hasMany(PriceHistory::class);
    }
}
