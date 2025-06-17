<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Repository\EventRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;

class GetIncomingEvents extends AbstractController
{
    /**
     * @Route(
     *     name="get_incoming_events",
     *     path = "/events",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_incoming_events"
     *     }
     * )
     */
    public function __invoke(EventRepository $er)
    {
        $events = $er->findHomePageEvents($_SERVER['HTTP_HOST']);
        return  $events;
    }
}