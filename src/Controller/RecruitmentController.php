<?php

namespace App\Controller;

use App\Entity\Origin;
use App\Entity\Slots;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\EventJobs;
use App\Entity\RhUser;
use App\Entity\EventType;
use App\Entity\JoblinkSession;
use App\Form\RhCommentType;
use App\Form\ChangeStatusType;
use App\Form\TextFieldType;
use App\Form\SearchCandidateType;
use App\Repository\CandidateUserRepository;
use App\Repository\EventRepository;
use App\Repository\OriginRepository;
use App\Repository\ParticipationRepository;
use App\Repository\CandidateParticipationRepository;
use App\Repository\JoblinkSessionRepository;
use App\Repository\CandidateRepository;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Repository\SlotsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin-recruteurs")
 */
class RecruitmentController extends AbstractController
{

    /**
     * @Route("/infosByMail/{mail}", name="infos_by_", methods="POST", requirements={"id"="\d+"})
     */
    public function infosByMail($mail, GlobalHelper $helper)
    {
        $test = $this->getDoctrine()->getManager()->getRepository(User::class)->findByMail($mail);
        return $helper->handleResponse($test->getFirstname() . ',' . $test->getLastname() . ',' . $test->getEmail() . ',' . $test->getPhone());
    }

    /**
     * @Route("/validateOrigin/{id}", name="valider_origin", methods="POST", requirements={"id"="\d+"})
     */
    public function addOrigin(CandidateParticipation $id, Request $request, OriginRepository $or)
    {
        $submittedToken = $request->request->get('token');
        $origin = $or->find($request->get('origin_candidate'));
        $user = $id->getCandidate();
        $user->setOrigin($origin);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('candidates_list', ['id' => $id->getEvent()->getId()]);

    }
        /**
     * @Route("/changer-statut/{id}", name="recruitment_change_status")
     */
    public function changeStatus(CandidateParticipation $participation, Request $request, TwigHelper $twig_helper, MailerHelper $mailer_helper, RenderHelper $render_helper, SessionInterface $session)
    {
        $user = $this->getUser();
        if ($user instanceof RhUser &&
            (
                !($event = $participation->getEvent()) ||
                !($event->getType()->getRecruitmentOfficeAllowed()) ||
                $event->getType()->getRegistrationValidation() != EventType::REGISTRATION_VALIDATION_VIEWER_RH ||
                !$event->getRecruitmentOffices()->contains($user->getRecruitmentOffice())
            )
        ) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('candidates_list', ['id' => $event->getId(), 'page' => $session->get('event_candidates_last_page')]);
        }

        $form = $this->createForm(ChangeStatusType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$participation->getHandledBy() || $participation->getHandledBy()->getId() != $this->getUser()->getId()) {
                $participation->setHandledBy($this->getUser());
                $this->getDoctrine()->getManager()->persist($participation);
            }

            $status_slug = $participation->getStatus()->getSlug();

            switch ($status_slug) {

                case 'confirmed':
                    $em = $this->getDoctrine()->getManager();
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $nbCandidate = $twig_helper->countCandidats($slotsId);
                        if ($nbCandidate == $slotsId->getMaxCandidats() - 1) {
                            $slotsId->setis_full(1);
                            $mailCom = $em->getRepository(User::class)->findMailCom();
                            foreach ($mailCom as $mailCom) {
                                $mailer_helper->sendMail(
                                    $mailCom['email'],
                                    'UN CRENEAU EST PLEIN !',
                                    'raw',
                                    array('body' => 'Le créneau ' . $slotsId->getName() . ' de l\'événement ' . $slotsId->getEvent()->getSlug() . ' est plein'),
                                    array("webmaster@l4m.fr" => "Back office événements L4M")
                                );
                            }
                        }
                    }
                    $render_helper->generateInvitation($participation);
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' a été confirmé. Il va recevoir son invitation par email.');
                    break;

                case 'refused':
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $slotsId->setis_full(0);
                    }
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' a été refusé. Il va recevoir un mail de refus.');
                    break;

                case 'refused_after_call':
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $slotsId->setis_full(0);
                    }
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' a été refusé. Il va recevoir un mail de refus "après appel"');
                    break;

                case 'waiting':
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $slotsId->setis_full(0);
                    }
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' a été mis sur la liste d\'attente. Il ne reçoit aucun mail. Vous pourrez le confirmer plus tard.');
                    break;

                case 'pending':
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $slotsId->setis_full(0);
                    }
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' est en cours de vérification. Il ne reçoit aucun mail. Vous pourrez le confirmer plus tard.');
                    break;

                case 'registered':
                    $slotsId = $participation->getSlot();
                    if (!empty($slotsId)) {
                        $slotsId->setis_full(0);
                    }
                    $this->addFlash('success', 'Le candidat ' . $participation->getCandidate() . ' a été remis en statut "inscrit"');
                    break;

                default:
                    throw new \Exception($status_slug . ' does not exist');
                    break;
            }

            $mailer_helper->sendChangeStatusMail($participation);
            $this->getDoctrine()->getManager()->flush();


            return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId(), 'page' => $session->get('event_candidates_last_page')));
        }

        return $this->render('recruitment/change_status.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/candidates-list/{id}", name="candidates_list", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function showCandidateUser(Event $event,OriginRepository $or, CandidateUserRepository $er, CandidateParticipationRepository $cer, Request $request, SessionInterface $session, JoblinkSessionRepository $joblinkSessionRepository, SlotsRepository $slotsRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && (!($event->getType()->getRecruitmentOfficeAllowed()) || !$event->getRecruitmentOffices()->contains($user->getRecruitmentOffice()))) {
            return $this->redirectToRoute('recruitement_index');
            $this->addFlash('danger', "Impossible d'accéder à ces données");
        }
        if ($user->hasRole('ROLE_RH')) {
            $form = $this->createForm(TextFieldType::class, null, array('required' => false, 'placeholder' => 'Recherche par email/nom/prénom'));
        } else {
            $form = $this->createForm(SearchCandidateType::class, null, [
                'event' => $event,
                'method' => 'GET'
            ]);
        }

        $form->handleRequest($request);

        $filter = array('status' => null, 'search' => null, 'rh' => null, 'rhSectors' => null);

        $user = $this->getUser();
        if ($user->hasRole('ROLE_RH')) {
            $filter['rh'] = $user;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('text')->getData())) {
                $filter['search'] = $form->get('text')->getData();
            }
            if ($this->isGranted('ROLE_VIEWER')) {
                if ($form->has('status') && !empty($form->get('status')->getData()) && count($form->get('status')->getData())) {
                    $filter['status'] = $form->get('status')->getData();
                }

                if ($form->has('rhSectors') && !empty($form->get('rhSectors')->getData()) && count(($d = $form->get('rhSectors')->getData()))) {
                    $filter['rh_sectors'] = $d;
                }

                if ($form->has('user') && !empty($form->get('user')->getData())) {
                    $filter['user'] = $form->get('user')->getData();
                }
                $filter['came'] = $form->get('came')->getData();
            }
        } else {
            $filter['status'] = $request->get('status');
        }


        if($request->get('mode') == 'export') {
            $candidates = $er->findByEvent($event, $filter);
            $file_name = $this->getParameter('kernel.project_dir'). '/public/export/export_'.$event->getSlug().uniqid().'.csv';
            $file = fopen($file_name,"w+b");
            foreach ($candidates as $candidate) {
                $dateInscription = $cer->findOneByCandidateAndEvent($candidate, $event)->getCreatedAt();
                $ifScannedAt = $cer->findOneByCandidateAndEvent($candidate, $event)->getScannedAt();
                if($ifScannedAt != NULL){
                    $scannedAt = date_format($cer->findOneByCandidateAndEvent($candidate, $event)->getScannedAt(), 'H:i:s');
                }else{
                    $scannedAt = NULL;
                }
                if($cer->findOneByCandidateAndEvent($candidate, $event)->getSlot() != NULL){
                    $beginSlot = date_format($cer->findOneByCandidateAndEvent($candidate, $event)->getSlot()->getBeginSlot(), 'H:i:s');
                    $endingSlot = date_format($cer->findOneByCandidateAndEvent($candidate, $event)->getSlot()->getEndingSlot(), 'H:i:s');
                    fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(),$scannedAt,$beginSlot,$endingSlot,$dateInscription->format('Y-m-d H:i:s'), $candidate->getOrigin()->getName()]);
                }
                else{
                    if($candidate->getOrigin() != NULL){
                        fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(),$scannedAt,$dateInscription->format('Y-m-d H:i:s'), $candidate->getOrigin()->getName()]);
                    }else{
                        fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(),$scannedAt,$dateInscription->format('Y-m-d H:i:s')]);
                    }
                }
            }

            fclose($file);
            return $this->file($file_name);
        }

        if($request->get('mode') == 'exportScanned') {
            $candidates = $er->findByEventAndScanned($event, $filter);
            $file_name = $this->getParameter('kernel.project_dir'). '/public/export/export_'.$event->getSlug().uniqid().'.csv';
            $file = fopen($file_name,"w+b");
            foreach ($candidates as $candidate) {
                if($candidate->getOrigin() != NULL){
                    fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), $candidate->getPhone(), $cer->findOneByCandidateAndEvent($candidate, $event)->getHandledBy(), $candidate->getOrigin()->getName()]);
                }else{
                    fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), $candidate->getPhone(), $cer->findOneByCandidateAndEvent($candidate, $event)->getHandledBy()]);
                }
            }
            fclose($file);
            return $this->file($file_name);
        }

        if($request->get('mode') == 'exportOrigin') {
            $candidates = $er->findWithOrigin($event);
            $file_name = $this->getParameter('kernel.project_dir'). '/public/export/export_'.$event->getSlug().uniqid().'.csv';
            $file = fopen($file_name,"w+b");
            foreach ($candidates as $candidate) {
                fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), $candidate->getPhone(), $candidate->getOrigin()->getName()]);
            }
            fclose($file);
            return $this->file($file_name);
        }

        $query = $er->findByEventQuery($event, $filter);
        $candidates = $paginator->paginate(
            $query, /* query NOT result */
            ($page = $request->query->getInt('page', 1))/*page number*/,
            50/*limit per page*/
        );

        $session->set('event_candidates_last_page', $page);

        $old_candidates = count($this->getDoctrine()->getManager()->getRepository(Candidate::class)->findByEvent($event));
        //$company = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findByCandidateParticipation($candidate->getId());

        $joblinkSessions = $joblinkSessionRepository->findByEvent($event->getId());
        $origins = $or->findAllQuery();
        return $this->render('recruitment/candidates.html.twig', [
            'event' => $event,
            'candidates' => $candidates,
            'form' => $form->createView(),
            'search' => $filter['search'],
            'old_candidates' => $old_candidates,
            'status' => $filter['status'],
            'registration_validation' => $event->getType()->getRegistrationValidation() != EventType::REGISTRATION_VALIDATION_AUTO,
            //Check that the registration type is by job(for escape recruit here)
            'registration_type' => $event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_JOB,
            //'company_name' => $company->getCompanyName(),
            'can_validate_registration' => ($this->isGranted('ROLE_VIEWER') || $event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH),
            'joblink_sessions' => $joblinkSessions,
            'registration_joblinks' => $event->getType()->getRegistrationJoblinks() == EventType::REGISTRATION_USE_JOBLINKS,
            'slots_session' => $slotsRepository->findByEvent($event->getId()),
            'origins' => $origins
        ]);
    }

    /**
     * @Route("/candidates-list-unslots/{id}", name="candidates_list_unslots", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function showCandidateUserUnslots(Event $event, CandidateUserRepository $er, CandidateParticipationRepository $cer, Request $request, SessionInterface $session, JoblinkSessionRepository $joblinkSessionRepository, SlotsRepository $slotsRepository): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && (!($event->getType()->getRecruitmentOfficeAllowed()) || !$event->getRecruitmentOffices()->contains($user->getRecruitmentOffice()))) {
            return $this->redirectToRoute('recruitement_index');
            $this->addFlash('danger', "Impossible d'accéder à ces données");
        }
        if ($user->hasRole('ROLE_RH')) {
            $form = $this->createForm(TextFieldType::class, null, array('required' => false, 'placeholder' => 'Recherche par email/nom/prénom'));
        } else {
            $form = $this->createForm(SearchCandidateType::class, null, [
                'event' => $event,
                'method' => 'GET'
            ]);
        }

        $form->handleRequest($request);

        $filter = array('status' => null, 'search' => null, 'rh' => null, 'rhSectors' => null);

        $user = $this->getUser();
        if ($user->hasRole('ROLE_RH')) {
            $filter['rh'] = $user;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('text')->getData())) {
                $filter['search'] = $form->get('text')->getData();
            }
            if ($this->isGranted('ROLE_VIEWER')) {
                if ($form->has('status') && !empty($form->get('status')->getData()) && count($form->get('status')->getData())) {
                    $filter['status'] = $form->get('status')->getData();
                }

                if ($form->has('rhSectors') && !empty($form->get('rhSectors')->getData()) && count(($d = $form->get('rhSectors')->getData()))) {
                    $filter['rh_sectors'] = $d;
                }

                if ($form->has('user') && !empty($form->get('user')->getData())) {
                    $filter['user'] = $form->get('user')->getData();
                }
                $filter['came'] = $form->get('came')->getData();
            }
        } else {
            $filter['status'] = $request->get('status');
        }


        if ($request->get('mode') == 'export') {
            $candidates = $er->findByEventUnslots($event, $filter);
            $file_name = $this->getParameter('kernel.project_dir') . '/public/export/export_' . $event->getSlug() . uniqid() . '.csv';
            $file = fopen($file_name, "w+b");
            foreach ($candidates as $candidate) {
                fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail()]);
            }

            fclose($file);
            return $this->file($file_name);
        }

        if ($request->get('mode') == 'exportScanned') {
            $candidates = $er->findByEventAndScanned($event, $filter);
            $file_name = $this->getParameter('kernel.project_dir') . '/public/export/export_' . $event->getSlug() . uniqid() . '.csv';
            $file = fopen($file_name, "w+b");


            foreach ($candidates as $candidate) {
                fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), $candidate->getPhone(), $cer->findOneByCandidateAndEvent($candidate, $event)->getHandledBy()]);
            }

            fclose($file);
            return $this->file($file_name);
        }

        $query = $er->findByEventUnslots($event, $filter);
        $paginator = $this->get('knp_paginator');
        $candidates = $paginator->paginate(
            $query, /* query NOT result */
            ($page = $request->query->getInt('page', 1))/*page number*/,
            50/*limit per page*/
        );

        $session->set('event_candidates_last_page', $page);

        $old_candidates = count($this->getDoctrine()->getManager()->getRepository(Candidate::class)->findByEvent($event));
        //$company = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findByCandidateParticipation($candidate->getId());

        $joblinkSessions = $joblinkSessionRepository->findByEvent($event->getId());

        return $this->render('recruitment/candidates.html.twig', [
            'event' => $event,
            'candidates' => $candidates,
            'form' => $form->createView(),
            'search' => $filter['search'],
            'old_candidates' => $old_candidates,
            'status' => $filter['status'],
            'registration_validation' => $event->getType()->getRegistrationValidation() != EventType::REGISTRATION_VALIDATION_AUTO,
            //Check that the registration type is by job(for escape recruit here)
            'registration_type' => $event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_JOB,
            //'company_name' => $company->getCompanyName(),
            'can_validate_registration' => ($this->isGranted('ROLE_VIEWER') || $event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH),
            'joblink_sessions' => $joblinkSessions,
            'registration_joblinks' => $event->getType()->getRegistrationJoblinks() == EventType::REGISTRATION_USE_JOBLINKS,
            'slots_session' => $slotsRepository->findByEvent($event->getId()),
            'unslot' => 1
        ]);
    }

    /**
     * @Route("/candidates-list/{id}/{id_slots}", name="candidates_list_by_slots", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function showCandidateUserBySlots(Event $event, Slots $id_slots, CandidateUserRepository $er, CandidateParticipationRepository $cer, Request $request, SessionInterface $session, JoblinkSessionRepository $joblinkSessionRepository, SlotsRepository $slotsRepository): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && (!($event->getType()->getRecruitmentOfficeAllowed()) || !$event->getRecruitmentOffices()->contains($user->getRecruitmentOffice()))) {
            return $this->redirectToRoute('recruitement_index');
            $this->addFlash('danger', "Impossible d'accéder à ces données");
        }
        if ($user->hasRole('ROLE_RH')) {
            $form = $this->createForm(TextFieldType::class, null, array('required' => false, 'placeholder' => 'Recherche par email/nom/prénom'));
        } else {
            $form = $this->createForm(SearchCandidateType::class, null, [
                'event' => $event,
                'method' => 'GET'
            ]);
        }

        $form->handleRequest($request);

        $filter = array('status' => null, 'search' => null, 'rh' => null, 'rhSectors' => null);

        $user = $this->getUser();
        if ($user->hasRole('ROLE_RH')) {
            $filter['rh'] = $user;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($form->get('text')->getData())) {
                $filter['search'] = $form->get('text')->getData();
            }
            if ($this->isGranted('ROLE_VIEWER')) {
                if ($form->has('status') && !empty($form->get('status')->getData()) && count($form->get('status')->getData())) {
                    $filter['status'] = $form->get('status')->getData();
                }

                if ($form->has('rhSectors') && !empty($form->get('rhSectors')->getData()) && count(($d = $form->get('rhSectors')->getData()))) {
                    $filter['rh_sectors'] = $d;
                }

                if ($form->has('user') && !empty($form->get('user')->getData())) {
                    $filter['user'] = $form->get('user')->getData();
                }
                $filter['came'] = $form->get('came')->getData();
            }
        } else {
            $filter['status'] = $request->get('status');
        }


        if ($request->get('mode') == 'export') {
            $candidates = $er->findByEventAndSlots($event, $id_slots, $filter);
            $file_name = $this->getParameter('kernel.project_dir') . '/public/export/export_' . $event->getSlug() . uniqid() . '.csv';
            $file = fopen($file_name, "w+b");
            foreach ($candidates as $candidate) {
                fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), date_format($id_slots->getBeginSlot(), 'H:i:s'), date_format($id_slots->getEndingSlot(), 'H:i:s')]);

            }

            fclose($file);
            return $this->file($file_name);
        }

        if ($request->get('mode') == 'exportScanned') {
            $candidates = $er->findByEventAndScanned($event, $filter);
            $file_name = $this->getParameter('kernel.project_dir') . '/public/export/export_' . $event->getSlug() . uniqid() . '.csv';
            $file = fopen($file_name, "w+b");


            foreach ($candidates as $candidate) {
                fputcsv($file, [$candidate->getLastname(), $candidate->getFirstname(), $candidate->getEmail(), $candidate->getPhone(), $cer->findOneByCandidateAndEvent($candidate, $event)->getHandledBy()]);
            }

            fclose($file);
            return $this->file($file_name);
        }

        $query = $er->findByEventQuery($event, $filter);
        $paginator = $this->get('knp_paginator');
        $candidates = $paginator->paginate(
            $query, /* query NOT result */
            ($page = $request->query->getInt('page', 1))/*page number*/,
            50/*limit per page*/
        );

        $session->set('event_candidates_last_page', $page);

        $old_candidates = count($this->getDoctrine()->getManager()->getRepository(Candidate::class)->findByEvent($event));
//$company = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findByCandidateParticipation($candidate->getId());

        $joblinkSessions = $joblinkSessionRepository->findByEvent($event->getId());

        return $this->render('recruitment/candidates.html.twig', [
            'event' => $event,
            'slots' => $id_slots,
            'candidates' => $candidates,
            'form' => $form->createView(),
            'search' => $filter['search'],
            'old_candidates' => $old_candidates,
            'status' => $filter['status'],
            'registration_validation' => $event->getType()->getRegistrationValidation() != EventType::REGISTRATION_VALIDATION_AUTO,
            //Check that the registration type is by job(for escape recruit here)
            'registration_type' => $event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_JOB,
            //'company_name' => $company->getCompanyName(),
            'can_validate_registration' => ($this->isGranted('ROLE_VIEWER') || $event->getType()->getRegistrationValidation() == EventType::REGISTRATION_VALIDATION_VIEWER_RH),
            'joblink_sessions' => $joblinkSessions,
            'registration_joblinks' => $event->getType()->getRegistrationJoblinks() == EventType::REGISTRATION_USE_JOBLINKS,
            'slots_session' => $slotsRepository->findByEvent($event->getId())
        ]);
    }

    /**
     * @Route("/candidate-joblink-session/{id}", name="candidate_joblinkSession", methods={"GET", "POST"}, requirements={"id" : "\d+"})
     */
    public function editCandidateToJoblinkSession(Request $request, CandidateParticipation $candidateParticipation, JoblinkSessionRepository $session_repo): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('edit-joblink-session', $submittedToken)) {
            $joblinkSession = $session_repo->findOneBy(['id' => $request->get('joblink_session')]);

            if ($request->get('submitAction') == 'enregistrer' && empty($joblinkSession)) {
                $this->addFlash('danger', 'Vous n\'avez pas choisi de session pour ce candidat');
            } elseif ($request->get('submitAction') == 'supprimer' && empty($joblinkSession)) {
                $this->addFlash('danger', 'Il n\'y a pas de session a supprimer concernant ce candidat');
            }

            if ($request->get('submitAction') == 'enregistrer' && !empty($joblinkSession)) {
                if (!empty($candidateParticipation->getJoblinkSessions())) {
                    foreach ($candidateParticipation->getJoblinkSessions() as $j) {
                        $candidateParticipation->removeJoblinkSession($j);
                    }
                }
                $candidateParticipation->addJoblinkSession($joblinkSession);
                $this->addFlash('success', 'Le candidat a bien été inscrit à cette session');
            } elseif ($request->get('submitAction') == 'supprimer' && !empty($joblinkSession)) {
                $candidateParticipation->removeJoblinkSession($joblinkSession);
                $this->addFlash('success', 'Le candidat a bien été supprimé de cette session');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($candidateParticipation);
            $em->flush();
        }
        return $this->redirectToRoute('candidates_list', array('id' => $candidateParticipation->getEvent()->getId()));
    }

    /**
     * @Route("/candidate-slots/{id}", name="candidate_slots", methods={"GET", "POST"}, requirements={"id" : "\d+"})
     */
    public function editCandidateSlots(Request $request, CandidateParticipation $candidateParticipation, SlotsRepository $slots_repo, RenderHelper $render_helper, TwigHelper $helper, MailerHelper $mailer_helper): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('edit-slots-session', $submittedToken)) {
            $slotsSession = $slots_repo->findOneBy(['id' => $request->get('slots')]);

            if ($request->get('submitAction') == 'enregistrer' && empty($slotsSession)) {
                $this->addFlash('danger', 'Vous n\'avez pas choisi de session pour ce candidat');
            } elseif ($request->get('submitAction') == 'supprimer' && empty($slotsSession)) {
                $this->addFlash('danger', 'Il n\'y a pas de session a supprimer concernant ce candidat');
            }

            if ($request->get('submitAction') == 'enregistrer' && !empty($slotsSession)) {
                if (!empty($candidateParticipation->getSlot())) {
                    $candidateParticipation->getSlot()->setis_full(0);
                }
                $candidateParticipation->setSlot($slotsSession);
                $nbCandidate = $helper->countCandidats($candidateParticipation->getSlot());
                if ($nbCandidate >= $slotsSession->getMaxCandidats() - 1) {
                    $slotsSession->setis_full(1);
                    $em = $this->getDoctrine()->getManager();
                    $mailCom = $em->getRepository(User::class)->findMailCom();
                    foreach ($mailCom as $mailCom) {
                        $mailer_helper->sendMail(
                            $mailCom['email'],
                            'UN CRENEAU EST PLEIN !',
                            'raw',
                            array('body' => 'Le créneau ' . $slotsSession->getName() . ' de l\'événement ' . $slotsSession->getEvent()->getSlug() . ' est plein'),
                            array("webmaster@l4m.fr" => "Back office événements L4M")
                        );
                    }

                } else {
                    $slotsSession->setis_full(0);
                }
                $this->addFlash('success', 'Le candidat a bien été inscrit à cette session');
            } elseif ($request->get('submitAction') == 'supprimer' && !empty($slotsSession)) {
                $candidateParticipation->getSlot()->setis_full(0);
                $candidateParticipation->removeSlot($slotsSession);
                $this->addFlash('success', 'Le candidat a bien été supprimé de cette session');
            }

            $render_helper->generateInvitation($candidateParticipation);
            $invitation_path = $candidateParticipation->getInvitationPath();

            /*if ($helper->fileExists($invitation_path)){
                $mailer_helper->sendInvitation($candidateParticipation);
            }*/

            $em = $this->getDoctrine()->getManager();
            $em->persist($candidateParticipation);
            $em->flush();
        }
        return $this->redirectToRoute('candidates_list', array('id' => $candidateParticipation->getEvent()->getId()));
    }

    /**
     * @Route("/valider-venue/{id}", name="valider_venue", methods="GET", requirements={"id" : "\d+"})
     */
    public function validationVenue(CandidateParticipation $participation, TwigHelper $helper): Response
    {
        $em = $this->getDoctrine()->getManager();
        $participation->setScannedAt(new \Datetime);
        $em->persist($participation);
        $em->flush();
        return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
    }

    /**
     * @Route("/candidate-user/cv/{id}", name="candidate_user_download_cv", methods="GET", requirements={"id" : "\d+"})
     */
    public function downloadCvUser(CandidateParticipation $participation, TwigHelper $helper): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        $cv_path = $participation->getCandidate()->getCvPath();
        if (!$helper->fileExists($cv_path)) {
            throw new NotFoundHttpException('Le CV du candidat ' . $participation->getCandidate() . ' n\'existe pas.(' . $cv_path . ')');
        }

        // there's no point handling statuses if event type has auto registration validation (ie participations are never only 'registered')
        if (!$participation->getEvent()->getType()->registrationValidationAuto()) {
            // status becomes 'pending' to show that someone is handling this participation
            if ($participation->getStatus()->getSlug() == 'registered') {
                $status = $this->getDoctrine()->getRepository(Status::class)->findOneBySlug('pending');
                if ($status) {
                    $participation->setStatus($status);
                }
            }

            if (!($h = $participation->getHandledBy())) {
                $participation->setHandledBy($user);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($participation);
            $em->flush();
        }

        return $this->file($this->getParameter('public_dir') . $cv_path);

    }

    /**
     * @Route("/candidate-user/invitation/{id}", name="candidate_user_download_invitation", methods="GET", requirements={"id" : "\d+"})
     */
    public function downloadInvitationUser(CandidateParticipation $participation, TwigHelper $helper): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        $invitation_path = $participation->getInvitationPath();

        if ($helper->fileExists($invitation_path)) {
            return $this->file($this->getParameter('public_dir') . $invitation_path);
        }

        throw new NotFoundHttpException('L\'invitation du candidat ' . $participation->getCandidate() . ' n\'existe pas.(' . $invitation_path . ')');
    }

    /**
     * @Route("/candidate-user/regenAllInvit/{id}", name="regen_all", methods="GET", requirements={"id" : "\d+"})
     */
    public function regenAllInvit(Event $event, RenderHelper $render_helper, CandidateParticipationRepository $er): Response
    {
        $filter = array('status' => null, 'search' => null, 'rh' => null, 'rhSectors' => null);
        $candidates = $er->findByEvent($event);
        foreach ($candidates as $participation) {
            $render_helper->generateInvitation($participation);
        }
    }

    /**
     * @Route("/candidate-user/resend-invitation/{id}", name="candidate_user_resend_invitation", methods="GET", requirements={"id" : "\d+"})
     */
    public function resendCandidateUserInvitation(CandidateParticipation $participation, TwigHelper $helper, RenderHelper $render_helper, MailerHelper $mailer_helper, Request $request): Response
    {

        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        $invitation_path = $participation->getInvitationPath();

        if (!$helper->fileExists($invitation_path) || $request->get('regenerate') == 1) {
            $render_helper->generateInvitation($participation);
        }
        $invitation_path = $participation->getInvitationPath();

        if ($helper->fileExists($invitation_path)) {
            $mailer_helper->sendInvitation($participation);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('info', 'L\'invitation a été renvoyé à ' . $participation->getCandidate());

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/candidate-user/regenerate-invitation/{id}", name="candidate_user_regenerate_invitation", methods="GET", requirements={"id" : "\d+"})
     */
    public function regenerateCandidateUserInvitation(CandidateParticipation $participation, TwigHelper $helper, RenderHelper $render_helper, Request $request): Response
    {

        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        $render_helper->generateInvitation($participation);
        $invitation_path = $participation->getInvitationPath();

        if ($helper->fileExists($invitation_path)) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('info', 'L\'invitation a été regénérée pour ' . $participation->getCandidate());
        } else {
            $this->addFlash('danger', 'L\'invitation n\'a pas été regénérée pour ' . $participation->getCandidate());
        }


        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/candidate-rh-comment/{id}", name="candidate_rh_comment", methods="GET", requirements={"id" : "\d+"})
     */
    public function showRhComment(CandidateParticipation $participation): Response
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        return $this->render('recruitment/rh_comment.html.twig', [
            'participation' => $participation
        ]);
    }

    /**
     * @Route("/edit-candidate-rh-comment/{id}", name="candidate_rh_comment_edit")
     */
    public function editRhComment(CandidateParticipation $participation, Request $request)
    {
        $user = $this->getUser();
        if ($user instanceof RhUser && $participation->getHandledBy()->getRecruitmentOffice()->getId() != $user->getRecruitmentOffice()->getId()) {
            $this->addFlash('danger', "Impossible d'accéder à ces données");
            return $this->redirectToRoute('recruitement_index');
        }

        $form = $this->createForm(RhCommentType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
        }
        return $this->render('recruitment/rh_comment.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/add-candidate/{id}", name="event_add_candidate")
     */
    public function addCandidate(FormHelper $form_helper, Request $request, Event $event, GlobalHelper $helper,UserPasswordHasherInterface $passwordHasher)
    {

        return $helper->handleResponse($form_helper->registerCandidate($event, $request, 'admin',$passwordHasher));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
        }
        return $this->render('recruitment/rh_comment.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/edit-candidate/{event}/{id}", name="event_edit_candidate_participation")
     */
    public function editCandidateParticipation(Event $event, CandidateParticipation $participation, FormHelper $form_helper, Request $request, GlobalHelper $helper)
    {

        return $helper->handleResponse($form_helper->editCandidateProfile($event, $request, 'admin', $participation->getCandidate(), $participation));


        $form = $this->createForm(RhCommentType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
        }
        return $this->render('recruitment/rh_comment.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-candidate/{id}", name="event_delete_candidate_participation")
     */
    public function deleteCandidateParticipation(CandidateParticipation $participation, EntityManagerInterface $em)
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
        }

        try {
            $em->remove($participation);
            $em->flush();
            $this->addFlash('success', 'La participation a bien été supprimée.');
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Désolé, une erreur est survenue lors de la suppression.');
        }

        return $this->redirectToRoute('candidates_list', array('id' => $participation->getEvent()->getId()));
    }

    /**
     * @Route("/recall-candidates/{id}", name="recall_candidates", methods="GET")
     */
    public function recallCandidates(Request $request, MailerHelper $mailer_helper, Event $event): Response
    {
        $id = $event->getId();

        foreach ($event->getCandidateParticipations() as $cp) {
            $this->sentFill($cp->getId(), $mailer_helper);
        }
        $mailer_helper->sendMail(
            $this->getUser()->getEmail(),
            'Les mails "rappel de la participation" ont été envoyés',
            'raw',
            array('body' => 'Les mails "rappel de participation à l\'événement ' . $event->__toString()),
            array("webmaster@l4m.fr" => "Back office événements L4M")
        );

        return $this->redirectToRoute('candidates_list', array('id' => $id));
    }

    /**
     * @Route("/recall-candidates-unslots/{id}", name="recall_candidates_unslots", methods="GET")
     */
    public function recallCandidatesUnslots(Request $request, MailerHelper $mailer_helper, Event $event): Response
    {
        $id = $event->getId();

        foreach ($event->getCandidateParticipations() as $cp) {
            $this->sentFillUnslots($cp->getId(), $mailer_helper);
        }
        $mailer_helper->sendMail(
            $this->getUser()->getEmail(),
            'Les mails "relance d\'inscription" ont été envoyés',
            'raw',
            array('body' => 'Les mails "relance d\'inscription à un créneau pour l\événement ' . $event->__toString()),
            array("webmaster@l4m.fr" => "Back office événements L4M")
        );

        return $this->redirectToRoute('candidates_list_unslots', array('id' => $id));
    }

    public function sentFill($id, $mailer_helper)
    {
        $em = $this->getDoctrine()->getManager();
        $candidateParticipation = $em->getRepository(CandidateParticipation::class)->find($id);
        $user = $candidateParticipation->getCandidate();

        if ($candidateParticipation->getSlot() == null) {
            $this->addFlash('notice', 'Le candidat n\'est pas inscrit à un créneau');
            return;
        }
        $mailer_helper->sendRecallCandidate($user, $candidateParticipation);
    }

    public function sentFillUnslots($id, $mailer_helper)
    {
        $em = $this->getDoctrine()->getManager();
        $candidateParticipation = $em->getRepository(CandidateParticipation::class)->find($id);
        $user = $candidateParticipation->getCandidate();

        if ($candidateParticipation->getSlot() != null) {
            $this->addFlash('notice', 'Le candidat est inscrit à un créneau');
            return;
        }
        $mailer_helper->sendRecallCandidate($user, $candidateParticipation);
    }
}
