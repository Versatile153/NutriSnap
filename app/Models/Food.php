<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods'; // force table name

    protected $fillable = [
        'name',
        'calories_per_100g',
        'nutrients',
    ];

    // 👇 Laravel will auto-cast JSON to array
    protected $casts = [
        'nutrients' => 'array',
    ];
}
