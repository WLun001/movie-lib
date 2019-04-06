<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $fillable = ['name', 'sex', 'age'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
