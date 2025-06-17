<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipation;
use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\CandidateParticipationComment;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\ParticipationRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetScannedExposantApi extends AbstractController
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
     *     name="get_scanned_exposant",
     *     path = "/candidate/seen-exposant/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Participation::class,
     *         "_api_collection_operation_name"="get_scanned_exposant"
     *     }
     * )
     */
    public function __invoke(Event $data, ParticipationRepository $ccr, ApiHelper $api_helper)
    {
        $user = $this->getUser();
        $type = $user->getType();
        if ($type != 'Candidat'){
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
        $em = $this->getDoctrine()->getManager();
        //check if any candidate participation for this event
        $candidate_participation = $em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($user,$data);

        if(!$candidate_participation){
            return $this->api_helper->apiException("Ce candidat ne s'est pas inscrit à cet événement",["message"=>"Ce candidat ne s'est pas inscrit à cet événement"],Response::HTTP_BAD_REQUEST);
        }

        $participations = $em->getRepository(Participation::class)->findByCandidateParticipation($candidate_participation);

        return $participations;
    }
}