<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
    protected $fillable = ['name'];

    public function movies()
    {
        $this->hasMany(Movie::class);
    }
}