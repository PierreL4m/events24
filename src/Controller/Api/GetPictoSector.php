<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\PictoSectorEvent;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\EventRepository;
use App\Repository\SectorPicRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetPictoSector extends AbstractController
{

    /**
     * @Route(
     *     name="get_picto",
     *     path="/pictoSector/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_picto"
     *     }
     * )
     */
    public function __invoke(Event $data, ApiHelper $api_helper, SectorPicRepository $spr)
    {
        $pictoSectors = $spr->findByEvent($data);
        return $pictoSectors;
    }
}