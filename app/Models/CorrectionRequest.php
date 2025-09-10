<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    protected $fillable = ['user_id', 'meal_id', 'user_comments', 'corrected_results', 'status'];
    protected $casts = ['corrected_results' => 'encrypted:array', 'status' => 'string'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}