<?php
namespace App\Controller\Api;
use App\Entity\CandidateUser;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Helper\TwigHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Hshn\Base64EncodedFile\HttpFoundation\File\Base64EncodedFile;
class PatchCvCandidat extends AbstractController
{
    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;
    public function __construct(ApiHelper $api_helper) {
        $this->api_helper = $api_helper;
    }

    /**
     * @Route(
     *     name="cv_candidate",
     *     path="/candidate/cv/update/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="cv_candidate"
     *     }
     * )
     */
    public function __invoke(CandidateUser $id, Request $request, FormHelper $form_helper, SluggerInterface $slugger)
    {

        $full_data = json_decode($request->getContent());
        $file = new Base64EncodedFile($full_data->cv->base64);
        $cvDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/cvs';
        if ($file) {
            $originalFilename = pathinfo($file->getPathname(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move($cvDirectory, $newFilename);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $id->setCv($newFilename);
        }
        return $id;
    }
}