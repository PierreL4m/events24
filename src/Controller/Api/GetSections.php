<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class GetSections extends AbstractController
{

    /**
     * @Route(
     *     name="get_sections",
     *     path="/sections/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_concept_section"
     *     }
     * )
     */
    public function __invoke(Event $data, ApiHelper $api_helper, SectionRepository $sr)
    {
        $section = $sr->findByEvent($data);
        return $section;
    }
}