<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'type', 'unit', 'sack_size_id', 'price', 'stock'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'decimal:2',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function sackSize(): BelongsTo
    {
        return $this->belongsTo(SackSize::class);
    }
}
