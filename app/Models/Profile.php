<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'height',
        'weight',
        'target_weight',
        'goal_days',
        'goal',
        'daily_calories',
        'conditions', // Changed from health_conditions
        'plan',
    ];

    protected $casts = [
        'conditions' => 'array', // Changed from health_conditions
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}