<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Participations;
use App\Helper\TwigHelper;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetExposantsList extends AbstractController
{
    /**
     * @Route(
     *     name="get_exposant_list",
     *     path = "/participations/event/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_exposant_list"
     *     }
     * )
     */
    public function __invoke(ParticipationRepository $pr, Event $id, TwigHelper $twig_helper)
    {
        $participations = $pr->getRandomByEvent($id->getId());
        return $participations;
    }
}