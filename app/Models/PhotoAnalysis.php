<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'calories',
        'ingredients',
        'nutrients',
        'recommendations',
        'is_gluten_free',
        'product_id',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'nutrients' => 'array',
        'recommendations' => 'array',
        'is_gluten_free' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}