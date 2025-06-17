<?php

namespace App\Helper;

use App\Entity\Image;
use phpDocumentor\Reflection\File;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImageHelper
{
    private $validator;
    private $folder_helper;

    public function __construct(ValidatorInterface $validator, ImageFolderHelper $folder_helper)
    {
        $this->validator = $validator;
        $this->folder_helper = $folder_helper;
    }

    public function resizeAndCrop(Image $image)
    {

        $file = $image->getFile();

        if (empty($file)){
            return;
        }
        // create upload dir if not exists
        $fileSystem = new Filesystem();
        $dir = $this->folder_helper->getUploadRootDir($image);


        if (!$fileSystem->exists($dir)){
            try {
                $fileSystem->mkdir($dir);
            } catch (IOExceptionInterface $exception) {
               throw new \Exception("An error occurred while creating your directory at ".$exception->getPath());
            }
        }

        $path = $this->folder_helper->getAbsolutePath($image,true);

        //save original
        if (!$image->keepOrigin()){
            move_uploaded_file($file->getPathName(), $path);
        }
        else{
            $path = $file->getPathName();
        }

        if ($image->getWidth()){
            $save_path = $this->folder_helper->getAbsolutePath($image);
            $mime_type = $file->getClientMimeType();

            $wdh = !is_null($image->getWidth()) ? $image->getWidth() : 0;
            $hgt = !is_null($image->getHeight()) ? $image->getHeight() : 0;
            $canvas = $wdh."x".$hgt;
            $resize = $canvas."\>";

            switch ($mime_type){
                case 'image/x-eps':
                case 'image/vnd.adobe.photoshop':
                case 'application/illustrator':
                case 'application/pdf' :
                case 'image/gif':
                case 'image/png':
                case 'image/jpeg':
                case 'application/postscript' :
                    break;

                default:
                    throw new \Exception($this->errorMessage($mime_type));

            }

            // switch ($mime_type){
            //     case 'image/x-eps':
            //     case 'image/vnd.adobe.photoshop':
            //        exec("convert ".(realpath($path))." -flatten -resample 300 -trim -thumbnail ".$resize." ".$save_path);
            //        //do not set colorspace RBG !!!!
            //        break;

            //     case 'application/illustrator':
            //         exec("convert -density 900 ".(realpath($path))." -resample 300 -trim -thumbnail ".$resize." ".$save_path);
            //         break;

            //     case 'application/pdf' :
            //        exec("convert -density 900 ".(realpath($path))." -colorspace RGB -resample 300 -trim -thumbnail ".$resize." ".$save_path);
            //        $path = $save_path;
            //        break;

            //     case 'image/gif';
            //     case 'image/png';
            //     exec("convert ".(realpath($path))." -flatten -colorspace sRGB -resample 300 -trim -thumbnail ".$resize." ".$save_path);
            //         break;
            //     case 'image/jpeg';
            //         exec("convert ".(realpath($path))." -flatten -colorspace sRGB -resample 150 -trim -thumbnail ".$resize." ".$save_path);
            //         //do not set colorspace sRBG !!!!
            //         break;
            // }

            $profile_rgb = "";
            $profile_cmyk = "";
            $resample = " -resample 300";
            $density = "";
            $pdf_or_ai = $png = null;

            if ($mime_type == 'application/pdf' || $mime_type == 'application/illustrator' || $mime_type == 'application/postscript'){
                $pdf_or_ai = true;
            }

            if ($mime_type == 'image/png'){
                $png = true;
            }

            if ($mime_type == 'image/jpeg'){
                $resample="";
            }

            $im = new \IMagick(realpath($path));
            $images_nb = $im->getNumberImages();

            if ($images_nb > 1 && $pdf_or_ai){
                $im = new \Imagick();
                $im->readImage($path.'[0]');
                $im->writeImage($path);
            }

            if ($im->getImageColorspace() == \IMagick::COLORSPACE_CMYK) {
               $profile_cmyk = " -profile ".(realpath(__DIR__.'/../Resources/Profiles/USWebCoatedSWOP.icc'));
               $profile_rgb = " -profile ".realpath(__DIR__.'/../Resources/Profiles/AdobeRGB1998.icc');
            }
            if($im->getImageAlphaChannel()){
                $background = "-background white ";
                $resample = "";
            }

            if ($pdf_or_ai){
                if ($im->getImageSize() < 1000000){
                    $density = "-density 900 ";
                }
            }

            $command = "convert ".$density.(realpath($path))." -trim".$profile_cmyk.$profile_rgb." -flatten".$resample." -trim -thumbnail ".$resize." ".$save_path;

            $this->exec($command);

            // if ($pdf_or_ai){
            //     exec("convert ".$save_path." -trim ".$save_path);
            // }
            if ($image->getBox()){
                exec("convert ".realpath($save_path)." -background white -gravity center -extent ".$canvas." ".realpath($save_path));
            }
        }
        else{
            //no thumb
            $image->setPath($image->getAbsolutePath());
        }
    }

    /**
     * @param string
     */
    public function EscapeFrenchChar($name)
    {
        setlocale(LC_CTYPE, 'fr_FR.UTF-8');

        return  iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
    }

    public function validate($image, $form)
    {
        $errors = $this->validator->validate($image);

        if (count($errors) > 0) {
            foreach ($errors as $e) {
               $error = $e->getMessage();
            }

            $form->addError(new FormError($error));

            return true;
        }
        else{
            return false;
        }
    }

    public function checkFormat($image,$form_field)
    {
        $extension = $image->getClientMimeType();
        $types = array("image/png", "image/jpeg", "image/jpg", "image/svg+xml", "image/gif", "application/postscript", "application/octet-stream", "application/pdf");


        if (!in_array(strtolower($extension), $types)){
            $form_field->get('file')->addError(new FormError($this->errorMessage($extension)));
            return false;

        }

        return true;
    }


    public function handleImage($em, $image,$form_field,$folder,$bannerName=null, $width=null,$height=null,$box=null,$format=null)
    {
        $ok = true;
        if ($image instanceof UploadedFile){
            $image = new Image();
            $image->setFile($form_field->getData());
            $em->persist($image);
            $file = $image->getFile();
            if ($file){ // handle file upload
                $ok = $this->checkFormat($file,$form_field);

                if ($ok){
                    $image->setPath(uniqid());
                    $image->setAlt(uniqid());
                    $image->setUp($folder, $width, $height,$box,$format);
                    $image->setOriginalPath("/uploads/".$folder."/".uniqid());
                    $image->setUpdated(new \DateTime());
                }
            }
        }
        elseif ($image instanceof File){
            $file = $image->getFile();

            if ($file){ // handle file upload

                $ok = $this->checkFormat($file,$form_field);

                if ($ok){
                    $image->setUp($folder, $width, $height,$box,$format);
                    $image->setUpdated(new \DateTime());
                }
            }
        }
        return [$ok, $image];
    }
    public function errorMessage($extension)
    {
        return 'Le format ".'.$extension.'" n\'est pas valide. Merci de réesayer avec une image au format jpg, png, gif, eps, psd, pdf ou ai.' ;
    }


   public function duplicateImage($entity, $last, $slug)
    {
        $getter = 'get'.$slug;
        $setter = 'set'.$slug;
        $image = $last->$getter();

        if ($image){
            $new_img = clone($image);
            $entity->$setter($new_img);
        }
    }

    public function exec($command, $timeout = 30)
    {
        $process = new Process($command);
        $process->setTimeout($timeout);
        $pid = $process->getPid();

        $process->start();
        $pid = $process->getPid();
        //exec("cpulimit --pid ".$pid." --limit 30"); useless

        while ($process->isRunning()) {
             $process->checkTimeout(); //will throw exception
        }
    }
}
