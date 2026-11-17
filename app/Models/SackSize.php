<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SackSize extends Model
{
    protected $fillable = ['weight_kg'];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
