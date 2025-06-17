<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Participations;
use App\Entity\EventJobs;
use App\Helper\TwigHelper;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetJoblinkByEvent extends AbstractController
{
    /**
     * @Route(
     *     name="get_joblink_event",
     *     path = "/joblinks/event/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_joblink_event"
     *     }
     * )
     */
    public function __invoke(Event $data)
    {
        if($data instanceof EventJobs) {
            $joblinks = $data->getJoblinks();
        }
        else {
            $joblinks = [];
        }

        // if (count($joblinks) == 0) {
        //     return View::create('Pas de joblinks pour cet événement', Response::HTTP_NOT_FOUND);
        // }

        return $joblinks;
    }
}