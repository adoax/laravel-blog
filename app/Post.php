<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Post extends Model
{
    /**
     * Permet de definir le créateur automatiquement
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (auth()->user()) {
            static::creating(function ($model) {
                $model->user_id = Auth::id();
            });
        }
    }

    /**
     * @var string[]
     */
    protected $fillable = ['title', 'slug', 'content', 'excerpt', 'image', 'user_id'];

    /**
     * Permets de générer un slug automatiquement à partir du titre, si le slug n'est pas renseigné.
     * @param string $atr
     * @return void
     */
    public function setTitleAttribute($atr)
    {
        $this->attributes['title'] = $atr;
        $this->attributes['slug'] = Str::slug($atr);
    }

    /**
     * Permet de générer le slug à partir de la valeur renseignée
     * @param string $atr
     * @return void
     */
    public function setSlugAttribute($atr)
    {
        $this->attributes['slug'] = Str::slug($atr);
    }

    /**
     * Permets de générer m'extrait à partir du contenir, si l'extrait n'est pas renseigné
     * @param string $atr
     * @return void
     */
    public function setContentAttribute($atr)
    {
        $this->attributes['content'] = $atr;
        $this->attributes['excerpt'] = Str::words($atr, 50);
    }

    /**
     * Permet de générer l'extrait a partir donné valeur renseigner
     * @param string $atr
     * @return void
     */
    public function setExcerptAttribute($atr)
    {
        $this->attributes['excerpt'] = $atr;

    }

    /**
     * Permet de definir le nom de l'image
     * @param UploadedFile|null $atr
     * @return void
     */
    public function setImageAttribute($atr)
    {

        if ($atr instanceof UploadedFile ) {
            $this->attributes['image'] = $atr->hashName();
        }

        if (!$atr instanceof UploadedFile) {
            $this->attributes['image'] = $atr;
        }
    }


    /**
     * Relation avec la table Users
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * @return BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * @return HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
