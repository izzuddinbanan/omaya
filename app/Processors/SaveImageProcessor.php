<?php

namespace App\Processors;

use File;
use Intervention\Image\Facades\Image;

class SaveImageProcessor
{
    /**
     * @var mixed
     */
    protected $data;
    protected $path;

    /**
     * @param $data, $path
     */
    public function __construct($data, $path)
    {
        $this->data = $data;
        $this->path = $path;
    }

    /**
     * static call method
     * @return static
     */
    public static function make($data, $path)
    {
        return new static($data, $path);
    }

    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {

        $image_name = time() . rand(10, 99);
        $image_ori_name = $image_name . '.png';
        $image_name_thumbnail = $image_name . '.jpg';

        $path = storage_path($this->path);
        $path_thumbnail = storage_path($this->path. '/thumbnails');

        if (!File::isDirectory($path)) {

            File::makeDirectory($path, 0775, true);
        }

        if (!File::isDirectory($path_thumbnail)) {

            File::makeDirectory($path_thumbnail, 0775, true);
        }

        $ori_image = Image::make($this->data);

        $ori_image->save($path . DIRECTORY_SEPARATOR . $image_ori_name);
        
        $width = $ori_image->width();
        $height = $ori_image->height();

        $ori_image->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', 0)->save($path_thumbnail . DIRECTORY_SEPARATOR . $image_name_thumbnail);

        return $image_ori_name;
    }

    
}

