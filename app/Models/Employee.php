<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['division_id', 'location_id', 'name', 'position', 'email', 'phone', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function division(): BelongsTo { return $this->belongsTo(Division::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function visits(): HasMany { return $this->hasMany(Visit::class); }
}
