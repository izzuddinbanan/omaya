<?php

namespace App\Processors;

use File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;


class SaveLicenseProcessor
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


        $path = storage_path('app/public/tenants/' . $this->path);


        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0775, true);
        }
        


        file_put_contents(storage_path("app/public/tenants/". $this->path ."/tenant.license"), $this->data);

        return $path;

    }

    
}

