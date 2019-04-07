<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable = ['name', 'user_id'];

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
