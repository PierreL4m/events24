<?php
namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\TwigHelper;
use App\Repository\JobRepository;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetNbJobsByContractAndParticipation extends AbstractController
{
    /**
     * @Route(
     *     name="get_nb_jobs",
     *     path = "/participation/nbjobs/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Participation::class,
     *         "_api_collection_operation_name"="get_jobs_participation"
     *     }
     * )
     */
    public function __invoke(JobRepository $jr, Participation $data)
    {
        $result = $jr->getNbJobByContractAndParticipation($data->getId());
        return $result;
    }
}