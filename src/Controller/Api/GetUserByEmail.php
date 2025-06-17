<?php


namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\SlotsRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ApiPlatform\Core\Annotation\ApiResource;


class GetUserByEmail extends AbstractController
{
    /**
     * @Route(
     *     name="get_user_mail",
     *     path="/userByMail/{data}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="get_user_mail"
     *     }
     * )
     */
    public function __invoke(String $data, UserRepository $ur)
    {
        $user = $ur->findByMail($data);
        return $user;

    }



}