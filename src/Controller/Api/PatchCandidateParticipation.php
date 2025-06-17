<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class PatchCandidateParticipation extends AbstractController
{
    /**
     * @Route(
     *     name="patch_candidate_participation",
     *     path="/candidate/participation/edit/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="patch_candidate_participation"
     *     }
     * )
     */
    public function __invoke()
    {
        $user = $this->getUser();
        return $user;
    }
}