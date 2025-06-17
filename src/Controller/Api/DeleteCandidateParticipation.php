<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\CandidateParticipation;
use App\Helper\FormHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\H48Helper;

class DeleteCandidateParticipation extends AbstractController
{
    /**
     * @Route(
     *     name="delete_candidate_participation",
     *     path="/candidate/participation/{id}",
     *     methods={"DELETE"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipation::class,
     *         "_api_collection_operation_name"="delete_candidate_participation"
     *     }
     * )
     */
    public function __invoke(CandidateParticipation $data, EntityManagerInterface $em, H48Helper $h48_helper) 
    {
        /**
         * 
         * @var CandidateUser $user
         */
        $user = $this->getUser();
        if($data->getCandidate()->getId() != $user->getId()) {
            throw new \Exception('Unknown participation');
        }
        $event = $data->getEvent();
        if($h48_helper->is48($event)){
            $second_participation = $h48_helper->getSecondParticipation($data);
            if($second_participation){
                $em->remove($second_participation);
            }
        }
        
        $em->remove($data);
        $em->flush();
        return $data;
    }
}