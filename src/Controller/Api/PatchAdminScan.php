<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateParticipation;
use App\Form\Api\ScanType;
use App\Helper\ApiHelper;
use App\Repository\CandidateParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class PatchAdminScan extends AbstractController
{
    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    public function __construct(ApiHelper $api_helper)

    {
        $this->api_helper = $api_helper;
    }
    /**
     * @Route(
     *     name="patch_admin_scan",
     *     path="/admin/scan/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipation::class,
     *         "_api_collection_operation_name"="patch_admin_scan"
     *     }
     * )
     */
    public function __invoke(Request $request, CandidateParticipation $data, CandidateParticipationRepository $cpr, ApiHelper $api_helper)
    {
        $user = $this->getUser();
        if($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SCAN')){
            $form = $this->createForm(ScanType::class);
            $form->submit($request->request->all());

            if ($form->isValid() || Response::HTTP_BAD_REQUEST) {

                $em = $this->getDoctrine()->getManager();
                /* FIXME : WTF ?!
                $scan = $form->get('scannedAt')->getData();
                $scan = \DateTime::createFromFormat('dmYHi', $scan);
                */
                $scan = new \DateTime();

                $data->setScannedAt($scan);

                $em->flush();

                return $data;

            }
            else{
                return $this->api_helper->formException($form,Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
    }
}