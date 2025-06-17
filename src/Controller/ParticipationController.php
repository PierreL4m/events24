<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\ExposantUser;
use App\Entity\Participation;
use App\Entity\ParticipationCompanySimple;
use App\Entity\ParticipationFormationSimple;
use App\Entity\ParticipationJobs;
use App\Entity\ParticipationSite;
use App\Form\LoadParticipationType;
use App\Form\ParticipationList;
use App\Form\ParticipationType;
use App\Form\ParticipationTypeList;
use App\Form\ParticipationsStandNumbersType;
use App\Helper\GlobalEmHelper;
use App\Helper\GlobalHelper;
use App\Helper\MailerHelper;
use App\Helper\TwigHelper;
use App\Repository\JobRepository;
use App\Repository\ParticipationRepository;
use App\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Knp\Snappy\Pdf;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

/**
 * @Route("/admin/participation")
 */
class ParticipationController extends AbstractController
{
    /**
     * @Route("/{id}", name="participation_show", methods="GET")
     */
    public function show(Participation $participation, JobRepository $jobRepo): Response
    {
        $jobs = $jobRepo->getByParticipation($participation->getId());
        return $this->render('participation/show.html.twig', ['participation' => $participation, 'jobs' => $jobs]);
    }
    /**
     * @Route("/valid_bat/{id}", name="valid_bat", methods="GET")
     */
    public function validBat(Participation $participation, $anchor = null, $parentEvent = null, $childEvent = null, JobRepository $jobRepo): Response
    {
        $em = $this->getDoctrine()->getManager();
        $participation->setBatValid(new \Datetime());
        $em->persist($participation);
        $em->flush();
        if($participation->getEvent()->getParentEvent() != null){
            $parentEvent = $participation->getEvent()->getParentEvent();
            $childEvent = $participation->getEvent()->getParentEvent()->getChildEvents();
        }else{
            $childEvent = $participation->getEvent()->getChildEvents();
        }
        $jobs = $jobRepo->getByEvent($participation->getEvent());
        return $this->render('event/show.html.twig', ['event' => $participation->getEvent(), 'anchor' => $anchor, 'parentEvent' => $parentEvent, 'childEvent' => $childEvent, 'jobs' => $jobs]);
    }
    /**
     * @Route("/job/liste/{id}", name="exposant_jobs_list_show_admin")
     */
    public function showJobsListAction(Participation $participation, JobRepository $jr): Response
    {
        $jobs = $jr->findByParticipation($participation);
        return $this->render('job/show_list.html.twig', ['participation' => $participation, 'jobs'=>$jobs]);
    }
    /**
     * @Route("/{id}", name="participation_snappy", methods="GET")
     */
    public function showSnappy(Participation $participation): Response
    {
        return $this->render('snappy/participation.html.twig', ['participation' => $participation]);
    }

    /**
     * @Route("/edit_form/{id}", name="edit-form_participation", methods="GET|POST")
     */
    public function editForm(Request $request, Participation $participation, ImageHelper $image_helper, GlobalEmHelper $em_helper): Response
    {

        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        $form_load = $this->createForm(LoadParticipationType::class, $participation);
        $form_load->handleRequest($request);
        return $this->render('participation/form_edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="participation_edit", methods="GET|POST")
     */
    public function edit(Request $request, Participation $participation, ImageHelper $image_helper, GlobalEmHelper $em_helper): Response
    {


        if (count($participation->getSites()) == 0) {
            $participation->addSite(new ParticipationSite());
        }

        $original_entities = $em_helper->backupOriginalEntities($participation->getSites());

        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ok = $image_helper->handleImage($em, $participation->getLogoFile(), $form->get('logoFile'), 'logos', 233, 233);
            if($ok[1] != null){
                $participation->setLogo($ok[1]);
            }
            $image = $participation->getLogo();
            if ($ok) {
                $em = $this->getDoctrine()->getManager();
                $em_helper->removeRelation($original_entities, $participation, $participation->getSites(), 'removeSite', true);

                foreach ($participation->getSites() as $site) {
                    $site->setParticipation($participation);
                }
                $participation->setRecall(null);
                $em_helper->setTimestamp($participation, $this->getUser());
                $em->flush();
                if($ok[1] != null) {
                    $participation->getLogo()->setPath("logo/" . $participation->getLogoName());
                }
                $em->flush();
                $this->addFlash(
                    'success',
                    'La fiche de participation été modifiée'
                );
                $participation->setRecall(false);
                return $this->redirectToRoute('participation_show', ['id' => $participation->getId()]);
            }
        }

        //form load loads only participation with same type
        $form_load = $this->createForm(LoadParticipationType::class, $participation);
        $form_load->handleRequest($request);

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form->createView(),
            'form_load' => $form_load->createView()
        ]);
    }

    /**
     * Generate and save a PDF
     *
     * @Route("/participation/{id}/pdf", name="participation_pdf")
     */
    public function pdfAction(Participation $participation, Request $request, Pdf $snappy)
    {
        $organizationParticipation = $participation->getOrganization();
        $path = $request->server->get('DOCUMENT_ROOT');    // C:/wamp64/www/
        $path = rtrim($path, "/");                         // C:/wamp64/www

        $html = $this->renderView('snappy/participation.html.twig', array('participation' => $participation));


        $output = $path . $request->server->get('BASE');        // C:/wamp64/www/project/web
        $output .= '/fiche_exposant/contract-' . $participation->getId() . '.pdf';
        if (file_exists($output)) {
          return new PdfResponse(
            $snappy->getOutputFromHtml($html),
            'contract-' . $participation->getId() . '.pdf'
          );
        } else {
          return new PdfResponse(
            $snappy->getOutputFromHtml($html),
            'contract-' . $participation->getId() . '.pdf'
          );
        }

        // Generate PDF file

// Création des headers, pour indiquer au navigateur qu'il s'agit d'un fichier à télécharger
        header('Content-Transfer-Encoding: binary'); //Transfert en binaire (fichier)
        header('Content-Disposition: attachment; filename="Fiche - ' . $participation->getId() . '.pdf"'); //Nom du fichier
        header('Content-Length: ' . filesize($output)); //Taille du fichier

        //Envoi du fichier dont le chemin est passé en paramètre
        readfile($output);
        // Message + redirection
        $this->addFlash('success', 'The PDF file has been saved.');
        return $this->redirectToRoute('participation_show', array('id' => $participation->getId()));
    }

    /**
     * @Route("/{id}/delete", name="participation_delete")
     */
    public function delete(Request $request, Participation $participation, TwigHelper $helper, MailerHelper $mailer_helper): Response
    {
//        if (!$helper->isAtLeastViewer($this->getUser())){
//            throw new \AccessDeniedException('Vous n\'êtes pas autorisé à accèder à cette page.');
//        }


        if ($participation->getEvent()) {
            $event_id = $participation->getEvent()->getId();
        }
        //if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
        //if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($participation);
        $em->flush();
        //}
        //
        $this->addFlash(
            'success',
            'La fiche de participation été supprimée'
        );
//        }
//        else{
        $e = new \Exception();
        $mailer_helper->sendMail(
            'webmaster@l4m.fr',
            $this->getUser() . ' tried to delete participation',
            'raw',
            array('body' =>
                $this->getUser() . ' wanted to delete participation_id = ' . $participation->getId() . ' companyName = ' . $participation->getCompanyName() . ' event = ' . $participation->getEvent()
                . '<hr>Trace : ' . nl2br($e->getTraceAsString())
            )
        );

//        }
        if ($participation->getEvent()) {
            return $this->redirectToRoute('event_show', array('id' => $event_id));
        }

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/send-fill", name="participation_send_fill", methods="POST")
     */
    public function sendFillAjax(Request $request, MailerHelper $mailer_helper): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $this->sentFill($id, $mailer_helper);

            return new JsonResponse(array('id' => $id), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/send-fill-to-me", name="participation_send_fill_to_me", methods="POST")
     */
    public function sendFillToMeAjax(Request $request, MailerHelper $mailer_helper): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $participation = $em->getRepository(Participation::class)->find($id);
            $user = $participation->getResponsable();
            $mailer_helper->sendParticipationMail($user, $participation, null, $this->getUser()->getEmail());

            return new JsonResponse(array('id' => $id), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/send-all-fill/{id}", name="participation_send_fills", methods="GET")
     */
    public function sendFillAll(Request $request, MailerHelper $mailer_helper, Event $event): Response
    {
        $id = $event->getId();

        foreach ($event->getParticipations() as $p) {
            if (!$p->getFillSent()) {
                $this->sentFill($p->getId(), $mailer_helper);
            }
        }
        $mailer_helper->sendMail(
            $this->getUser()->getEmail(),
            'Les mails "complétez vos fiches exposants" ont été envoyés',
            'raw',
            array('body' => 'Les mails "complèter vos fiches exposants" ont été envoyés pour l\'événement ' . $event->__toString()),
            array("webmaster@l4m.fr" => "Back office événements L4M")
        );

        return $this->redirectToRoute('event_show', array('id' => $id));
    }

    /**
     * @Route("/recall-offer/{id}", name="recall_offers", methods="GET")
     */
    public function sendRecallOffers(Request $request, MailerHelper $mailer_helper, Participation $participation): Response
    {
        $id = $participation->getId();
        $this->sentFillOffers($id, $mailer_helper);
        $year = date_format($participation->getEvent()->getDate(), 'Y');
        $mailer_helper->sendMail(
            $this->getUser()->getEmail(),
            'Le mail "renseignez vos offres" à été envoyé à '.$participation->getCompanyName().' pour l\'événement de '.$participation->getEvent()->getPlace().' '. $year,
            'raw',
            array('body' => 'Le mail "renseignez vos offres" à été envoyé à '.$participation->getCompanyName().' pour l\'événement de '.$participation->getEvent()->getPlace().' '. $year),
            "webmaster@l4m.fr"
        );

        return $this->redirectToRoute('event_show', array('id' => $participation->getEvent()->getId()));
    }

    /**
     * @Route("/stand-numbers/{id}/{tech}", name="participation_stand_numerotation", requirements={"id" = "\d+", "tech" = "null|1"}, defaults={"tech" = null}, methods="GET|POST")
     */
    public function standNumerotation(Request $request, Event $event, $tech): Response
    {
        $form = $this->createForm(ParticipationsStandNumbersType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'success',
                'Les stands ont été numérotés'
            );
            if ($tech) {
                return $this->redirectToRoute('tech_file_index', ['id' => $event->getId()]);
            }
            return $this->redirectToRoute('event_show', ['id' => $event->getId()]);
        }

        return $this->render('participation/standNumerotation.html.twig', [
            'event' => $event,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/redo-recall", name="participation_redo_recall", methods="POST")
     */
    public function redoRecall(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $participation = $em->getRepository(Participation::class)->find($id);
            $participation->setRecall(true);
            $em->persist($participation);
            $em->flush();

            return new JsonResponse(array('id' => $id), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }
    public function sentFillOffers($id, $mailer_helper)
    {
        $em = $this->getDoctrine()->getManager();
        $participation = $em->getRepository(Participation::class)->find($id);
        $user = $participation->getResponsable();

        if(!$user){
            $this->addFlash('notice','Pas de responsable pour '.$participation);
            return;
        }
        $mailer_helper->sendOfferMail($user,$participation);
    }

    public function sentFill($id, $mailer_helper)
    {
        $em = $this->getDoctrine()->getManager();
        $participation = $em->getRepository(Participation::class)->find($id);
        $user = $participation->getResponsable();

        if (!$user) {
            $this->addFlash('notice', 'Pas de responsable pour ' . $participation);
            return;
        }
        $mailer_helper->sendParticipationMail($user, $participation);
        $participation->setFillSent(new \Datetime());
        $em->persist($participation);
        $em->flush();
    }

    /**
     * @Route("/change-type/{id}", name="participation_change_type", requirements={"id" = "\d+"},methods="GET|POST")
     */
    public function changeType(Request $request, Participation $participation): Response
    {
        $form = $this->createForm(ParticipationTypeList::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $class = '\App\Entity\\' . $form->get('participation_type')->getData();
            $new_p = new $class;
            $new_p->copy($participation);
            $new_p->setEvent($participation->getEvent());

            if (method_exists($participation, 'getDescription') &&
                method_exists($new_p, 'setDescription')) {
                $new_p->setDescription($participation->getDescription());
            }
            //to do handle specific cases
            //ParticipationJobs to company simple
            $em = $this->getDoctrine()->getManager();
            $em->persist($new_p);
            $participation->setEvent(null); //in case someone does something wrong, save the participation
            $em->flush();

            $this->addFlash(
                'success',
                'La participation est maintenant de type ' . $new_p->getType()
            );

            return $this->redirectToRoute('event_show', ['id' => $new_p->getEvent()->getId()]);
        }

        return $this->render('participation/changeType.html.twig', [
            'participation' => $participation,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/increment-max-jobs", name="participation_increment_max_jobs", methods="POST")
     */
    public function incrementMaxJobs(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $max = $request->request->get('max');
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager();
            $participation = $em->getRepository(Participation::class)->find($request->request->get('id'));
            $participation->setMaxJobs($max);
            $em->flush();

            return new JsonResponse(array('id' => $id, 'max' => $max), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

}
