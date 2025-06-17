<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Participation;
use App\Form\Api\TokenNotificationsType;
use App\Helper\ApiHelper;
use App\Helper\FormHelper;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\CandidateParticipationComment;

class PatchToken extends AbstractController
{
    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    /**
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(ApiHelper $api_helper, EntityManagerInterface $em)

    {
        $this->em = $em;
        $this->api_helper = $api_helper;
    }

    /**
     * @Route(
     *     name="patch_token",
     *     path="/candidate/edit-token-notifications",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="patch_token"
     *     }
     * )
     */
    public function __invoke(Request $request, ApiHelper $api_helper, CandidateUser $data)
    {

        $candidate = $this->getUser();
        if($candidate->hasRole('ROLE_CANDIDATE')){
            $form = $this->createForm(TokenNotificationsType::class,$candidate);
            $form->submit($request->request->all());

            if ($form->isValid()) {
                $this->em->flush();
                return $candidate;
            }
            else{
                return $this->api_helper->formException($form,Response::HTTP_BAD_REQUEST);
            }
        }else{
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
    }
}