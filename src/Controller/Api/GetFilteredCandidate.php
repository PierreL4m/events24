<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipation;
use App\Entity\Event;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\CandidateParticipationCommentRepository;
use App\Repository\CandidateParticipationRepository;
use EasyRdf\Literal\Integer;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\NotCandidateParticipationException;
use Symfony\Component\Form\FormInterface;

class GetFilteredCandidate extends AbstractController
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
     *     name="get_filtered_candidate",
     *     path = "/admin/show-candidates/{id}",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_filtered_candidate"
     *     }
     * )
     */
    public function __invoke(Event $data, CandidateParticipationRepository $cr, Request $request)
    {  $user = $this->getUser();
        if (!$user || !($user->getType() == 'Scan' || $user->getType() == 'L4M')) {
            if ($user){
                $type = $user->getType();
            }
            else{
                $type = 'anonymous';
            }
            return $this->api_helper->apiException("role '".$type."' cannot see candidates for an event",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
        $search = $request->get('search');
        return $cr->findByEventAndInputQuery($data, $search);
    }
}