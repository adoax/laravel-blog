<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug'];

    public function setNameAttribute($atr)
    {
        $this->attributes['name'] = $atr;
        $this->attributes['slug'] = Str::slug($atr);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}