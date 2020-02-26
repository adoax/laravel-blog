<?php


namespace App\Services;


use App\Post;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{

    /**
     * Permet dec créer des images pour les articles
     * @param UploadedFile|null $image
     * @param Post|null $post
     * @return void
     */
    public function handleUploadImage($image, $post = null)
    {
        if (!is_null($image)) {

            if (!is_null($post)) {
                Storage::delete('public/images/' . $post->image);
                Storage::delete('public/images/thumbs/' . $post->image);

            };
            Storage::disk('public')->makeDirectory('images');
            Image::make($image)->resize(1280, 720)->save(storage_path('app/public/images/' . $image->hashName()));
            Storage::disk('public')->makeDirectory('images/thumbs');
            Image::make($image)->resize(450, 230)->save(storage_path('app/public/images/thumbs/' . $image->hashName()));

        }
    }

    /**
     * Permet de supprimer les images qui ne sont plus utilisée
     *
     * @param Post $post
     * @return void
     */
    public function handleDeleteImage($post)
    {
        Storage::delete('public/images/' . $post->image);
        Storage::delete('public/images/thumbs/' . $post->image);
    }
}
