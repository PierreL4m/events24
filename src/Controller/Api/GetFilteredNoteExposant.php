<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Helper\ApiHelper;
use App\Repository\CandidateParticipationCommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetFilteredNoteExposant extends AbstractController
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
     *     name="get_filtered_note_exposant",
     *     path = "/exposant/event/{id}/candidates",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_filtered_note_exposant"
     *     }
     * )
     */
    public function __invoke(Event $data, CandidateParticipationCommentRepository $cpc, Request $request, ApiHelper $helper)
    {
        $user = $this->getUser();

        if($user->getType() != 'Exposant'){
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }

        $filter = $request->get('filter');
        $candidate_participations_comment = $cpc->findByEventAndExposant($data,$user,$filter);

        return $candidate_participations_comment;
    }
}