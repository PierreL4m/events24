<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class AuthentificationAndFilesHelper extends AuthentificationHelper
{
    protected $docx_file;
    protected $jpg_file;
    protected $odt_file;
    protected $pdf_file;
    protected $png_file;
    protected $gif_file;

    public function setUp() : void
    {
        parent::setUp();
        $this->docx_file = new UploadedFile(__DIR__.'/files/docx.docx', 'docx.docx');
        $this->jpg_file = new UploadedFile(__DIR__.'/files/jpg.jpg', 'jpg.jpg');
        $this->odt_file = new UploadedFile(__DIR__.'/files/odt.odt', 'odt.odt');
        $this->pdf_file = new UploadedFile(__DIR__.'/files/pdf.pdf', 'pdf.pdf');
        $this->png_file = new UploadedFile(__DIR__.'/files/png.png', 'png.png');
        $this->gif_file = new UploadedFile(__DIR__.'/files/gif.gif', 'gif.gif');
    }
}