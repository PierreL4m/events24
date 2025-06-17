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
use App\Repository\JobRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetJobsByEvent extends AbstractController
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
     *     name="get_jobs_event",
     *     path = "/event/jobs/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_jobs_event"
     *     }
     * )
     */
    public function __invoke(Request $request, Event $data, JobRepository $jr, ApiHelper $api_helper)
    {
        $jobs = $jr->getJobByEvent($data->getId());
        if (!$jobs) {
            return $this->api_helper->apiException('Aucune offres pour cette participation',['code' => Response::HTTP_BAD_REQUEST,"message"=>'Aucune offres pour cette participation'],Response::HTTP_BAD_REQUEST);
        }
        return $jobs;
    }
}