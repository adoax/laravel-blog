<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['name'];

    /**
     * @return BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(User::class);
    }
}
