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
        'company',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
