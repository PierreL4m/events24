<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetMe extends AbstractController
{

    /**
     * @Route(
     *     name="get_me",
     *     path="/user/me",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="get_me"
     *     }
     * )
     */
    public function __invoke()
    {
        $user = $this->getUser();
        
        if($user instanceof User) {
            return $user;
        }
        
        return null;
    }
}