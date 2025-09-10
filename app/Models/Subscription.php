<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'name', 'stripe_id', 'stripe_status', 'trial_ends_at', 'ends_at','status'];
    protected $dates = ['trial_ends_at', 'ends_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}