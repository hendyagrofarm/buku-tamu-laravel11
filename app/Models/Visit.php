<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'visitor_id',
        'location_id',
        'employee_id',
        'employee_name',
        'purpose',
        'number_of_people',
        'identity_photo',
        'check_in_at',
        'check_out_at',
        'status',
        'satisfaction_rating',
        'surveyed_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'surveyed_at' => 'datetime',
            'number_of_people' => 'integer',
            'satisfaction_rating' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'visit_number';
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}