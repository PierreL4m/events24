<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipationComment;
use App\Helper\ApiHelper;
use App\Helper\MailerHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationCommentRepository;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;

class GetSlotsByEvent extends AbstractController
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
     *     name="get_slots_event",
     *     path = "/slots/event/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_slots_event"
     *     }
     * )
     */
    public function __invoke(EventRepository $er, $id, ApiHelper $helper, TwigHelper $twig_helper)
    {
        $event = $er->find($id);

        if (!$event) {
            return $this->api_helper->apiException('Event not found for id '.$id,["message"=>'Event not found for id '.$id],Response::HTTP_NOT_FOUND);
        }

        return $twig_helper->getSlotsByEvents($event);


    }
}