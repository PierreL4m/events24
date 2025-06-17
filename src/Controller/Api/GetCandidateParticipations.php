<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateParticipation;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\CandidateParticipationRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetCandidateParticipations extends AbstractController
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
     *     name="get_participations",
     *     path="/candidate/participations",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipation::class,
     *         "_api_collection_operation_name"="get_participations"
     *     }
     * )
     */
    public function __invoke(ApiHelper $api_helper, CandidateParticipationRepository $cpr)
    {
        $user = $this->getUser();
        $type = $user->getType();
        if ($type != 'Candidat'){
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }else{
            $participations = $cpr->findAllByCandidate($user);
            return $participations;
        }
    }
}