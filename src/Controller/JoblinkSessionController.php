<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Joblink;
use App\Entity\JoblinkSession;
use App\Entity\Section;
use App\Form\AddParticipationToJoblinkType;
use App\Form\JoblinkSessionType;
use App\Repository\JoblinkSessionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * @Route("/joblink/session")
 */
class JoblinkSessionController extends AbstractController
{

    /**
     * @Route("/add-organization/{id}/{event_id}", name="joblink_add_participation", methods="GET|POST")
     * @ParamConverter("event", class="App\Entity\Event", options={"id" = "event_id"})
     */
    public function new(Request $request, Joblink $joblink, Event $event): Response
    {
        $form = $this->createForm(AddParticipationToJoblinkType::class, $joblink, ['event_id' => $event->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participations = $form->get('participations')->getData();
            $em = $this->getDoctrine()->getManager();

            foreach ($participations as $participation) {
                $joblink_session = new JoblinkSession();
                $joblink_session->setJoblink($joblink);
                $joblink_session->setParticipation($participation);
                $em->persist($joblink_session);
            }

            $em->flush();

            $section = $em->getRepository(Section::class)->findByStSlugAndEvent($event, 'joblinks');

            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        return $this->render('joblink_session/add_participations.html.twig', [
            'joblink' => $joblink,
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/set-hours/{id}", name="joblink_session_set_hours", methods="GET|POST")
     */
    public function setHours(Request $request, JoblinkSession $joblinkSession): Response
    {

        $form = $this->createForm(JoblinkSessionType::class, $joblinkSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($joblinkSession);
            $em->flush();

            $section = $em->getRepository(Section::class)->findByStSlugAndEvent($joblinkSession->getParticipation()->getEvent(), 'joblinks');

            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        return $this->render('joblink_session/set_hours.html.twig', [
            'joblink_session' => $joblinkSession,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/remove/{id}", name="joblink_session_remove", methods="GET")
     */
    public function delete(Request $request, JoblinkSession $joblinkSession): Response
    {
        $em = $this->getDoctrine()->getManager();

        $section = $em->getRepository(Section::class)->findByStSlugAndEvent($joblinkSession->getParticipation()->getEvent(), 'joblinks');
        //if ($this->isCsrfTokenValid('delete'.$joblinkSession->getId(), $request->request->get('_token'))) {

        $em->remove($joblinkSession);
        $em->flush();
        //}
        $this->addFlash('success', 'La session joblink a été supprimée');

        return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
    }
}
