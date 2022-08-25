<?php

namespace App\Processors;

use File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;


class SaveFileProcessor
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

        $file_name = time() . rand(10, 99) . '.' . $this->data->getClientOriginalExtension();
        
        $path = storage_path($this->path);

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0775, true);
        }

        $this->data->move($path, $file_name);

        return $file_name;
    }

    
}

