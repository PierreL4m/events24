<?php

namespace App\Controller;

use App\Entity\CandidateParticipationComment;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Repository\CandidateParticipationCommentRepository;
use App\Repository\CandidateParticipationRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DoctrineExtensions\Query\Mysql\Year;

class ScanController extends AbstractController
{
    /**
     * @Route("/scan/today", name="scan_index")
     */
    public function index(EventRepository $er)
    {
        $current_events = $er->findTodayEvent();
        return $this->render('scan_scan/index.html.twig', array(
                'current_events' => $current_events
            )
        );
    }

    /**
     * @Route("/scan/scanned", name="scan_candidate")
     */
    public function scanCandidate(Request $request, CandidateParticipationRepository $cpr)
    {
        $data = json_decode($request->getContent());
        $candidateparticipation = $cpr->findOneById($data->participationId);
        $em = $this->getDoctrine()->getManager();
        $candidateparticipation->setScannedAt(new \Datetime);
        $em->persist($candidateparticipation);
        $em->flush();
        $this->addFlash('success', 'Validé');
        return $this->render('scan_scan/index.html.twig');
    }

    /**
     * @Route("/scan/searchScan", name="exposant_scan", methods="POST")
     */
    public function searchScan(Request $request, CandidateParticipationRepository $cp, ParticipationRepository $pr, CandidateParticipationCommentRepository $cpr)
    {
        $candidateParticipation = $cp->findOneById($request->request->get('info'));

        if (!$candidateParticipation) {
            $error = "Le candidat ne peut être scanné";
            return new JsonResponse(array('error' => $error));
        }elseif($candidateParticipation->getScannedAt() !== null){
            $error = "Ce candidat a déjà été scanné";
            return new JsonResponse(array('error' => $error));
        }
        else
        {
            $candidateParticipation = $cp->findByEventAndEmailOrId($candidateParticipation->getEvent(), $request->request->get('info'));
            $em = $this->getDoctrine()->getManager();
            $candidateParticipation->setScannedAt(new \Datetime);
            $em->persist($candidateParticipation);
            $em->flush();
            return new Response($candidateParticipation->getId());
        }
    }
}
