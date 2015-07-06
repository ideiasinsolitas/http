<?php

namespace Deck\Http;

use Deck\Types\Collection;

class Files extends Collection
{

    protected $uploadDirectory;
    protected $originalFiles;
    protected $files;
    protected $allowedExtensions;

    public function __construct(array $files, $uploadDirectory, array $allowedExtensions)
    {
        if (!is_string($uploadDirectory)) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->allowedExtensions = $allowedExtensions;
        $this->uploadDirectory = $uploadDirectory;
        $this->originalFiles = $files;
        $files = $this->arrangeFilesArray($files);
        $this->map($files);
    }

    public function arrangeFilesArray($files)
    {
        
        foreach ($files as $key => $all) {
            foreach ($all as $i => $val) {
                $arrangedFiles[$i][$key] = $val;
            }
        }
        
        return $arrangedFiles;
    }

    /*
    $path_parts = pathinfo('/www/htdocs/index.html');

    echo $path_parts['dirname'], "\n";
    echo $path_parts['basename'], "\n";
    echo $path_parts['extension'], "\n";
    echo $path_parts['filename'], "\n"; // Since PHP 5.2.0

     */
    
    public function getPathInfo($file)
    {
        return pathinfo($file);
    }

    public function stripExtension($name)
    {
        return str_replace($this->getExtension($name), "", $name);
    }

    public function getExtension($name)
    {
        return substr(strrchr($filename, "."), 1);
    }

    public function moveUploadedFile($tmpName, $destination)
    {
        return move_uploaded_file($tmpName, $destination);
    }

    public function renameFile($oldName, $newName)
    {
        while (file_exists($newName)) {
            $newName = $this->stripExtension($newName);
        }

        return $newName;
    }


    public function uploadSuccesful()
    {
        $val = true;

        foreach ($item as $file) {

            if (is_uploaded_file($file['tmp_name'])) {
                $destination = $this->uploadDirectory . '/' . $file['name'];
                $val = move_uploaded_file($file['tmp_name'], $destination);

                if ($val === false) {
                    return $val;
                }
            }
        }
        return $val;
    }
}
