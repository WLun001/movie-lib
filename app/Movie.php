<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = ['name', 'rating', 'year', 'duration', 'studio_id'];

    public function studio()
    {
        return $this->hasOne(Studio::class);
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }
}
