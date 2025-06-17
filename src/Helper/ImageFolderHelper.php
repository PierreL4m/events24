<?php

namespace App\Helper;

use App\Entity\Image;

class ImageFolderHelper
{  
    private $public_dir;

    public function __construct($public_dir)
    {      
        $this->public_dir = $public_dir;
    }

    public function getAbsolutePath(Image $image,$origin=false)
    {
    	$path = $this->getPath($image,$origin);

        return null === $path
            ? null
            : $this->getUploadRootDir($image).'/'.$path;
    }

    public function getWebPath(Image $image,$origin=false)
    {
    	$path = $this->getPath($image,$origin);
    	
        return null === $path
            ? null
            : '/'.$this->getUploadDirPath($image).'/'.$path;
    }
   
    public function getUploadRootDir(Image $image)
    {
        return $this->public_dir.'/'.$this->getUploadDirPath($image);
    }

    public function getUploadDirPath(Image $image)
    {
        return 'uploads/'.$image->getUploadDir();
    }

    public function getPath(Image $image, $origin)
    {
    	if($origin){
    		return $image->getOriginalPath();
    	}
    	else{
    		return $image->getPath();
    	}
    }
}
