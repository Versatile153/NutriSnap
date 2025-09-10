<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'photo_url',
        'meal_type',
        'calories',
        'portion_size',
        'analysis',
        'feedback',
         'platform',
        'share_link',
        'uuid',
        'status',
        'health_condition',
        'leftover_photo_url',
        'leftover_analysis',
        'correction_request',
        'corrected_calories',
        'corrected_food',
        'share_proof',
    ];

    protected $casts = [
        'analysis' => 'array',
        'share_link' => 'array',
        'leftover_analysis' => 'array',
        'share_proof' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function correctionRequest()
    {
        return $this->hasOne(CorrectionRequest::class);
    }
}