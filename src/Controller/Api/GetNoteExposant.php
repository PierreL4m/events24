<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipationComment;
use App\Repository\CandidateParticipationCommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;

class GetNoteExposant extends AbstractController
{
    /**
     * @Route(
     *     name="get_note_exposant",
     *     path = "/exposant/note/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipationComment::class,
     *         "_api_collection_operation_name"="get_note_exposant"
     *     }
     * )
     */
    public function __invoke(CandidateParticipationComment $data, CandidateParticipationCommentRepository $cpc)
    {
        $user = $this->getUser();
        
        if($user->getType() == 'Exposant') {
            $candidateComment = $cpc->findById($data);
            
            if($candidateComment->getOrganizationParticipation()->getOrganization()->getId() == $user->getOrganization()->getId()) {
                return $candidateComment;
            }else{
                throw new NotCandidateParticipationException(sprintf('La note "%s" n\'est pas lié à l\'utilisateur courant.', $data->getId()));
            }
            return $candidateComment;
        }
    }
}