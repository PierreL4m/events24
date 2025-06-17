<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Helper\ApiHelper;
use App\Repository\EventRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetParticipatedEventExposant extends AbstractController
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
     *     name="get_event_exposant",
     *     path = "/exposant/event",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_event_exposant"
     *     }
     * )
     */
    public function __invoke(ApiHelper $api_helper, EventRepository $er)
    {
        $user = $this->getUser();

        $type = $user->getType();
        switch($type) {
            case 'Scan':
            case 'L4M' :
                $events = $er->findLastThreeMonth();
                break;
            case 'Exposant' :
                $events = $er->findLastThreeMonthByUser($user);
                break;
            default : return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }

        return $events;
    }
}