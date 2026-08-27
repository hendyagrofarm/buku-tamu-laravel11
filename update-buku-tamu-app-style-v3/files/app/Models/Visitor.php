<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'address',
        'identity_number',
        'vehicle_number',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function getMaskedNameAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];

        return collect($parts)
            ->filter()
            ->map(function (string $part): string {
                $length = mb_strlen($part);

                if ($length <= 1) {
                    return $part;
                }

                return mb_substr($part, 0, 1).str_repeat('•', min($length - 1, 5));
            })
            ->implode(' ');
    }
}
