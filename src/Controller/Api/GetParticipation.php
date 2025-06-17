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

class GetParticipation extends AbstractController
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
     *     name="get_participation",
     *     path = "/participation/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Participation::class,
     *         "_api_collection_operation_name"="get_participation"
     *     }
     * )
     */
    public function __invoke(Request $request, Participation $data, ParticipationRepository $ccr, ApiHelper $api_helper, ParticipationRepository $pr)
    {
        $participation = $pr->find($data->getId());

        if (!$participation) {
            return $this->api_helper->apiException('Participation not found for id '.$data->getId(),['code' => Response::HTTP_BAD_REQUEST,"message"=>'Participation not found for id '.$data->getId()],Response::HTTP_BAD_REQUEST);
        }

        return $participation;
    }
}