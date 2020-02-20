<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageService
{

    /**
     * Permet dec créer des images pour les articles
     * @param $image
     * @param null $post
     */
    public function handleUploadImage($image, $post = null)
    {
        if (!is_null($image)) {
            Image::make($image)->resize(450, 230)->save(storage_path('app/public/images/thumbs/'. $image->hashName() ));
            Image::make($image)->resize(1280, 720)->save(storage_path('app/public/images/' . $image->hashName()));

        }
    }

    /**
     * Permet de supprimer les images qui ne sont plus utilisée
     *
     * @param $post
     */
    public function handleDeleteImage($post)
    {
        if (!is_null($post)) {
            Storage::delete('public/images/' . $post->image);
            Storage::delete('public/images/thumbs/' . $post->image);
        }
    }
}