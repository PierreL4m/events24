<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipationComment;
use App\Helper\MailerHelper;
use App\Repository\CandidateParticipationCommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;

class GetSendProfile extends AbstractController
{
    /**
     * @Route(
     *     name="get_send_profile",
     *     path = "/exposant/send-profile/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipationComment::class,
     *         "_api_collection_operation_name"="get_send_profile"
     *     }
     * )
     */
    public function __invoke(CandidateParticipationComment $data, MailerHelper $helper, CandidateParticipationCommentRepository $cpc)
    {
        $user = $this->getUser();
        $candidateComment = $cpc->findById($data);
        
        if($user->getType() == 'Exposant' && $candidateComment->getOrganizationParticipation()->getOrganization()->getId() == $user->getOrganization()->getId()){
            $helper->sendProfile($data, $user);
            return true;
        }else{
            throw new NotCandidateParticipationException(sprintf('La note "%s" n\'est pas lié à l\'utilisateur courant.', $data->getId()));
        }
    }
}