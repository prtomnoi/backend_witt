<?php

namespace App\Http\Controllers;
use Intervention\Image\Laravel\Facades\Image; // install readme. https://github.com/Intervention/image-laravel , read use image https://image.intervention.io/v3/modifying/resizing, php.info use extention=gd
use Illuminate\Support\Facades\File;

abstract class Controller
{
    protected $config_lang;

    public function __construct()
    {
        // Fetch the Site Settings object
        $this->config_lang = $this->lang();
    }
    public function lang()
    {
        return ['en'];
    }

    public function addImage($image, $oldImage, $folder)
    {
        // change name image
        $new_nameImage = uniqid() . "_" . time() . "." .$image->getClientOriginalExtension();
        // check old image and delete;
        if (File::exists(public_path($oldImage)))
        {
            File::delete(public_path($oldImage));
        }
        // path image
        $path = public_path('app/' . $folder . '/' .$new_nameImage);
        // read image file
        $manager = Image::read($image);
        // scale image to width 300px height auto
        $manager->scale(width:300);
        // save image to path public/upload/...
        $manager->save($path);
        return $new_nameImage;
    }
}
