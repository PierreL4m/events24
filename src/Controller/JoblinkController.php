<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Joblink;
use App\Entity\Section;
use App\Entity\EventJobs;
use App\Form\JoblinkType;
use App\Repository\JoblinkRepository;
use App\Repository\EventJobsRepository;
use App\Helper\ImageHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/joblink")
 */
class JoblinkController extends AbstractController
{
    /**
     * @Route("/", name="joblink_index", methods="GET")
     * @param JoblinkRepository $joblinkRepository
     * @return Response
     */
    public function index(JoblinkRepository $joblinkRepository, EventJobsRepository $eventJobsRepository): Response
    {
        $joblinks = $joblinkRepository->findAll();
        foreach ($joblinks as $key => $joblink) {
            $eventJobs = $eventJobsRepository->findAll();
            foreach ($eventJobs as $key => $eventJob) {
                $eventId = $eventJob->getId();
                $eventSlug = $eventJob->getSlug();
            }
        }

        return $this->render('joblink/index.html.twig', [
            'joblinks' => $joblinks,
            'event_id' => $eventId,
            'event_slug' => $eventSlug
        ]);
    }

    /**
     * @Route("/new/{id}", name="joblink_new", methods="GET|POST")
     */
    public function new(Event $event, Request $request, ImageHelper $image_helper): Response
    {
        $joblink = new Joblink();
        $joblink->addEvent($event);
        $form = $this->createForm(JoblinkType::class, $joblink);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ok = $image_helper->handleImage($joblink->getLogoFile(), $form->get('logoFile'), 'joblinks', 233, 233);

            if ($ok) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($joblink);
                $em->flush();

                $section = $em->getRepository(Section::class)->findByStSlugAndEvent($event, 'joblinks');

                return $this->redirectToRoute('section_show', array('id' => $section->getId()));
            }
        }

        return $this->render('joblink/new.html.twig', [
            'joblink' => $joblink,
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="joblink_show", methods="GET")
     */
    public function show(Joblink $joblink, Event $event, EventJobsRepository $eventJobsRepository): Response
    {
        $eventJobs = $eventJobsRepository->findAll();

        return $this->render('joblink/show.html.twig', [
            'joblink' => $joblink,
            'event' => $event,
            'event_jobs' => $eventJobs
        ]);
    }

    /**
     * @Route("/edit/{id}/{event_id}", name="joblink_edit", methods="GET|POST")
     * @ParamConverter("event", class="App\Entity\Event", options={"id" = "event_id"})
     */
    public function edit(Request $request, Joblink $joblink, Event $event, ImageHelper $image_helper): Response
    {

        $form = $this->createForm(JoblinkType::class, $joblink);
        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository(Section::class)->findByStSlugAndEvent($event, 'joblinks');

        if (!$section) {
            throw new \Exception('section_type_slug = joblinks for event_id = ' . $event->getId() . ' not found');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $image_helper->handleImage($joblink->getLogoFile(), $form->get('logoFile'), 'joblinks', 233, 233);

            if ($ok) {
                $em->flush();

                return $this->redirectToRoute('section_show', array('id' => $section->getId()));
            }

        }

        return $this->render('joblink/edit.html.twig', [
            'joblink' => $joblink,
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/{event_id}", name="joblink_remove_from_event",requirements={"id" = "\d+"})
     * @ParamConverter("event", class="App\Entity\Event", options={"id" = "event_id"})
     */
    public function delete(Joblink $joblink, Event $event): Response
    {

        // to do when joblink unique
        // $event->removeJoblink($joblink);
        $em = $this->getDoctrine()->getManager();
        $em->remove($joblink);
        $em->flush();

        return $this->redirectToRoute('joblink_index');
    }
}
