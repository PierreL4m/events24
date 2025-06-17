<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipationComment;
use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Repository\CandidateParticipationCommentRepository;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;

class PatchNote extends AbstractController
{
    /**
     * @Route(
     *     name="patch_note",
     *     path="/exposant/note/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipationComment::class,
     *         "_api_collection_operation_name"="patch_note"
     *     }
     * )
     */
    public function __invoke(Request $request, CandidateParticipationComment $comment, FormHelper $form_helper)
    {
        return $form_helper->editCandidateComment($request, $comment, 'api');
    }
}