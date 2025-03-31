<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    use HasFactory;

    protected $table = 'price_history';

    protected $fillable = [
        'product_id',
        'price_source_id',
        'price',
        'currency',
        'collected_at',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'currency' => 'string',
        'collected_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceSource(): BelongsTo
    {
        return $this->belongsTo(PriceSource::class);
    }
}
