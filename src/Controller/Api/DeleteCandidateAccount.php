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

class DeleteCandidateAccount extends AbstractController
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
     *     name="delete_candidate_account",
     *     path="/candidate/remove-account",
     *     methods={"DELETE"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="delete_candidate_account"
     *     }
     * )
     */
    public function __invoke(ApiHelper $api_helper) : CandidateUser
    {
        $user = $this->getUser();
        if (!$user){
            return $this->api_helper->apiException('Un utilisateur anonyme ne peut pas supprimer son profil');
        }
        elseif (!$user->hasRole('ROLE_CANDIDATE')){
            return $this->api_helper->apiException("Forbbiden",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
        return $user;
    }
}