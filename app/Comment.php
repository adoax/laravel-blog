<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['content', 'user_id', 'post_id'];

    /**
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
