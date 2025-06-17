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

class GetAllEventsParticipations extends AbstractController
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
     *     name="get_all_events_participations",
     *     path = "/participations/all/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_all_events_participations"
     *     }
     * )
     */
    public function __invoke(Request $request, Event $data, ParticipationRepository $ccr, ApiHelper $api_helper, ParticipationRepository $pr)
    {
        $participations = $pr->findAllEventsParticipations($data->getId());

        if (!$participations) {
            return $this->api_helper->apiException('Participation not found for id '.$data->getId(),['code' => Response::HTTP_BAD_REQUEST,"message"=>'Participation not found for id '.$data->getId()],Response::HTTP_BAD_REQUEST);
        }

        return $participations;
    }
}