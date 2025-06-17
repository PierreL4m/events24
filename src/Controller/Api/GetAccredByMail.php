<?php


namespace App\Controller\Api;

use App\Entity\Accreditation;
use App\Entity\User;
use App\Repository\AccreditationRepository;
use App\Repository\EventRepository;
use App\Repository\SlotsRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Request;


class GetAccredByMail extends AbstractController
{
    /**
     * @Route(
     *     name="get_accreditation_mail",
     *     path="/accredByMail/{data}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Accreditation::class,
     *         "_api_collection_operation_name"="get_accreditation_mail"
     *     }
     * )
     */
    public function __invoke(String $data, AccreditationRepository $ar, EventRepository $er, Request $request)
    {
        $event = $er->findOneById($request->query->get('event'));
        $accreditation = $ar->findByMail($data, $event);
        return $accreditation;

    }



}