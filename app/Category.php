<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['name', 'slug'];

    /**
     * @param string $atr
     * @return void
     */
    public function setNameAttribute(string $atr)
    {
        $this->attributes['name'] = $atr;
        $this->attributes['slug'] = Str::slug($atr);
    }

    /**
     * @return BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
