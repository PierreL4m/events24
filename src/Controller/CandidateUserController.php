<?php

namespace App\Controller;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\RhUser;
use App\Entity\Status;
use App\Entity\User;
use App\Form\Api\RegistrationType;
use App\Form\CandidateUserType;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationRepository;
use App\Repository\CandidateUserRepository;
use App\Repository\CityRepository;
use App\Repository\SlotsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/espace-candidat")
 */
class CandidateUserController extends AbstractController
{
    /**
     * @Route("", name="candidate_user_profile", methods="GET")
     */
    public function show(SessionInterface $session, CandidateParticipationRepository $repo, GlobalHelper $helper): Response
    {
        $register_success_event = null;
        if(($pid = $session->get('register_success'))) {
            $participation = $repo->findOneById($pid);
            if($participation) {
                $register_success_event = $participation->getEvent();
            }
            $session->remove('register_success');
        }
        $candidate = $this->getUser();
      return $this->render('candidate_user/profile.html.twig', [
          'candidate' => $candidate,
          'percentageProfilInfos'=> $helper->progressProfilCandidate($candidate),
          'register_success_event' => $register_success_event
      ]);
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
     * @Route("/editer-info", name="candidate_user_edit", methods="GET|POST")
     */
    public function edit(Request $request, FormHelper $form_helper, GlobalHelper $helper): Response
    {
      return $helper->handleResponse($form_helper->editCandidateProfile($request, 'web'));
    }

    /**
     * @Route("/{id}", name="slots_index_candidate",  methods="GET")
     */
    public function index_candidat(SlotsRepository $slotsRepository, CandidateParticipation $candidateParticipation, CandidateParticipationRepository $participationRepository): Response
    {

        return $this->render('candidate_user/edit_slot.html.twig', ['slots_session' => $slotsRepository->findAllNotFull($candidateParticipation->getEvent()), 'participation'=>$candidateParticipation]);
    }


    /**
     * @Route("/edit_slot_candidate/{id}", name="edit_slot_candidate", methods={"GET", "POST"}, requirements={"id" : "\d+"})
     */
    public function editCandidateSlots(Request $request, CandidateParticipation $candidateParticipation, SlotsRepository $slots_repo, GlobalHelper $helper, TwigHelper $twig_helper, RenderHelper $render_helper, MailerHelper $mailer_helper): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('edit-slots-session', $submittedToken)) {
            $slotsSession = $slots_repo->findOneBy(['id' => $request->get('slots')]);

            if($request->get('submitAction') == 'enregistrer' && empty($slotsSession)) {
                $this->addFlash('danger', 'Vous n\'avez pas choisi de session pour ce candidat');
            }
            elseif ($request->get('submitAction') == 'supprimer' && empty($slotsSession)) {
                $this->addFlash('danger', 'Il n\'y a pas de session a supprimer concernant ce candidat');
            }

            if($request->get('submitAction') == 'enregistrer' && !empty($slotsSession)) {
                $candidateParticipation->getSlot()->setis_full(0);
                $candidateParticipation->setSlot($slotsSession);
          			$nbCandidate = $twig_helper->countCandidats($candidateParticipation->getSlot());
          			if($nbCandidate >= $slotsSession->getMaxCandidats() - 1){
          				$slotsSession->setis_full(1);
          			}else{
                  $slotsSession->setis_full(0);
                }
                $this->addFlash('success', 'Le candidat a bien été inscrit à cette session');
            }
            elseif ($request->get('submitAction') == 'supprimer' && !empty($slotsSession)) {
              $candidateParticipation->getSlot()->setis_full(0);
                $candidateParticipation->removeSlots($slotsSession);
                $this->addFlash('success', 'Le créneau a bien été modifier');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($candidateParticipation);
            $em->flush();
        }

        $render_helper->generateInvitation($candidateParticipation);
        $invitation_path = $candidateParticipation->getInvitationPath();

        if ($twig_helper->fileExists($invitation_path)){
            $mailer_helper->sendInvitation($candidateParticipation);
        }

        return $this->render('candidate_user/profile.html.twig', [
            'candidate' => $candidateParticipation->getCandidate(),
            'percentageProfilInfos'=> $helper->progressProfilCandidate($candidateParticipation->getCandidate())
        ]);
    }

    /**
     * @Route("/supprimer-mon-compte", name="candidate_user_delete", methods="DELETE")
     */
    public function delete(Request $request, FormHelper $form_helper, GlobalHelper $helper): Response
    {
      $candidateUser = $this->getUser();
      return $helper->handleResponse($form_helper->deleteCandidate('web'));
    }

   /**
     * @Route("/supprimer-ma-participation/{id}", name="candidate_participation_delete", methods="GET")
     */
    public function deleteParticipation(Request $request, CandidateParticipation $participation, H48Helper $h48_helper): Response
    {
      if ($this->getUser()->getCandidateParticipations()->contains($participation)){
        $em = $this->getDoctrine()->getManager();

        $event = $participation->getEvent();

        if($h48_helper->is48($event)){
          $second_participation = $h48_helper->getSecondParticipation($participation);

          if($second_participation){
            $em->remove($second_participation);
          }
        }


        $em->remove($participation);
        $em->flush();

        $this->addFlash('success', 'Votre participation a été annulée');

        return $this->redirectToRoute('candidate_user_profile');
      }
      else{
        throw new AccessDeniedException('CandidateUser id='.$this->getUser()->getId().' cannot delete CandidateParticipation id='.$participation->getId());
      }
    }

    /**
     * @Route("/voir-recruteurs/{id}", name="candidate_seen_organization", methods="GET")
     */
    public function seenOrganization(CandidateParticipation $participation): Response
    {
        return $this->render('candidate_user/seen_organizations.html.twig', [
          'participation' => $participation
      ]);
    }

    // /**
    //  * @Route("/add-job/{id}", name="candidate_add_job", methods={"GET", "POST"})
    //  */
    // public function candidateAddJob(CandidateParticipation $candidateParticipation)
    // {
    //   $candidateParticipation->setJob($job);

    //   $em = $this->getDoctrine()->getManager();
    //   $em->persist($candidateParticipation);
    //   $em->flush();

    //   $this->addFlash('success', 'Votre choix a été enregistré');

    //   return $this->redirectToRoute('public_index');

    // }
}
