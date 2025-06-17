<?php

namespace App\Controller;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\User;
use App\Entity\clientUser;
use App\Repository\CandidateParticipationRepository;
use App\Repository\CandidateUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

 /**
 * @Route("/admin/stat")
 */
class StatController extends AbstractController
{
    /**
     * @Route("/app/{id}", name="app_stats")
     */
    public function appStats(Event $event)
    {
    	$em = $this->getDoctrine()->getManager();

    	
    	$event_candidate_dwd = null;
    	$scanned_once = null;
    	$candidate_ios = null;
        $candidate_android = null;
        $had_scan_once = null;
        $participation_with_dwd = null;

        $candidate_comments = $em->getRepository(CandidateParticipationComment::class)->findByEvent($event);

        return $this->render('stat/app.html.twig', [
            'event' => $event,
            'total_candidate_dwd' => $em->getRepository(User::class)->countAppDwd('candidate'),
            'total_exposant_dwd' => $em->getRepository(User::class)->countAppDwd('client'),
            'total_candidate_ios' => $em->getRepository(User::class)->countAppDwd('candidate','ios'),
            'total_candidate_android' => $em->getRepository(User::class)->countAppDwd('candidate','android'),
            'event_candidate_dwd' => $event_candidate_dwd,
            'scanned_once' => $scanned_once,
            'candidate_ios' => $candidate_ios,
            'candidate_android' => $candidate_android,
            'had_scan_once' => $had_scan_once,
            'exposant_ios' => $em->getRepository(User::class)->countAppDwd('client','ios'),
            'exposant_android' => $em->getRepository(User::class)->countAppDwd('client','android'),
            'participations' => $em->getRepository(Participation::class)->getOrderedByEvent($event->getId()),
            'candidate_comments' => count($candidate_comments),
            'participation_has_pub' => $em->getRepository(Participation::class)->findWithPubByEvent($event),

        ]);
    }

    /**
     * @Route("/experts/{id}", name="experts_stats")
     */
    public function expertsStats(Event $event, CandidateUserRepository $cr)    {
        $em = $this->getDoctrine()->getManager();

        $total_confirmed = count($cr->findByEventAndStatus($event,'confirmed'));
        $total_confirmed_ro = count($cr->findByEvent($event,['status' => 'confirmed', 'ro' => true]));

        $total_came = count($cr->findByEvent($event,['came' => true]));
        $total_came_ro = count($cr->findByEvent($event,['came' => true,'ro' => true]));

        $total_confirmed_by_sectors = null;
        $total_confirmed_by_sectors_by_l4m = null;
        $total_confirmed_by_sectors_by_ro = null;
        $total_confirmed_by_hours = null;
        $total_came_by_hours = null;

        $candidate_participations = $cr->findByEvent($event);
        //warning
        //if you change $results label, update js
        $results = [
            'Total candidats' => count($candidate_participations),
            'Total candidats via site et appli' => count($candidate_participations) - $total_confirmed_ro,
            'Refusés' => count($cr->findByEventAndStatus($event,'refused')) + count($cr->findByEventAndStatus($event,'refused_after_call')) ,
            'Confirmés par l4m' => $total_confirmed - $total_confirmed_ro,
            'Confirmés par le cabinet de recrutement' => $total_confirmed_ro,
            'Total confirmés' => $total_confirmed,
            'Venus l4m' =>  $total_came - $total_came_ro,
            'Venus cabinet de recrutement' => $total_came_ro,
            'Total venus' => $total_came,
            'Confirmés linkedin' => count($cr->findByEvent($event,['status' => 'confirmed', 'from' => 'linkedin'])),
            'Venus linkedin' => count($cr->findByEvent($event,['came' => 'true', 'from' => 'linkedin']))

        ];

        $admin_sectors = $event->getSectors();
        $sectors_datas = [];
        $sectors_stats = [];

        foreach ($admin_sectors as $sector){

            $mini_array = [
                'name' => $sector->getName(),
                'id' => $sector->getId()
            ];
            $total_came_by_sector = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'came' =>true]));
            $total = count($cr->findByEvent($event,['rh_sectors' => $sector->getId()]));
            $mini_array['Total inscriptions'] = $total;
            $mini_array['Total confirmés'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'status' =>"confirmed"]));
            $mini_array['Confirmés rh'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'status' =>"confirmed", "ro" => true]));
            $mini_array['Confirmés l4m'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'status' =>"confirmed", "l4m" => true]));
            $mini_array['Refusés'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'status' =>"refused"]));
            $mini_array['Venus rh'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'came' =>true, "ro" => true]));
            $mini_array['Venus l4m'] = count($cr->findByEvent($event,['rh_sectors' => $sector->getId(), 'came' =>true, "l4m" => true]));
            $mini_array['Total venus'] = $total_came_by_sector;

            $stats_array = [
                $sector->getName(),
                $total_came_by_sector
            ];
            array_push($sectors_stats,$stats_array);
            array_push($sectors_datas,$mini_array);
        }

        #'total_confirmed_by_hour' => null,
        #'total_came_by_hour' => null

        return $this->render('stat/experts.html.twig', [
            'event' => $event,
            'results' => $results,
            'sectors_datas' => $sectors_datas,
            'sectors_stats' => $sectors_stats
        ]);
    }
}
