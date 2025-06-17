<?php

namespace App\Controller;

use App\Entity\Bat;
use App\Entity\Event;
use App\Entity\Participation;
use App\Form\BatParticipationType;
use App\Form\BatType;
use App\Form\ParticipationsNoBatType;
use App\Helper\MailerHelper;
use App\Repository\BatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bat")
 */
class BatController extends AbstractController
{
    /**
     * @Route("/{id}", name="bat_index", methods="GET",requirements={"id" = "\d+"})
     */
    public function index(BatRepository $batRepository, Event $event): Response
    {
        return $this->render('bat/index.html.twig', [
            'bats' => $batRepository->findByEvent($event->getId()),
            'event' => $event
        ]);
    }

    /**
     * @Route("/new/{id}", name="bat_new", methods="GET|POST",requirements={"id" = "\d+"})
     */
    public function new(Request $request, Event $event): Response
    {
        $bat = new Bat();
        $form = $this->createForm(BatType::class, $bat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $bat->setEvent($event);
            $em->persist($bat);
            $em->flush();

            return $this->redirectToRoute('bat_index', array('id' => $event->getId()));
        }

        return $this->render('bat/new.html.twig', [
            'bat' => $bat,
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bat_edit", methods="GET|POST",requirements={"id"="\d+"})
     */
    public function edit(Request $request, Bat $bat): Response
    {
        $form = $this->createForm(BatType::class, $bat, ['required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bat_index', ['id' => $bat->getEvent()->getId()]);
        }

        return $this->render('bat/edit.html.twig', [
            'bat' => $bat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remove/{id}", name="bat_remove", methods="GET",requirements={"id"="\d+"})
     */
    public function delete(Bat $bat): Response
    {
        $id = $bat->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($bat);
        $em->flush();

        return $this->redirectToRoute('bat_index', ['id' => $id]);
    }

    /**
     * @Route("/download/{id}", name="bat_download", methods="GET",requirements={"id"="\d+"})
     */
    public function download(Bat $bat): Response
    {
        return $this->file($bat->getPathSrc());
    }

    /**
     * @Route("/{id}/add-organizations", name="bat_add_organizations", methods="GET|POST",requirements={"id"="\d+"})
     */
    public function addOrganizations(Request $request, Bat $bat): Response
    {
        $form = $this->createForm(BatParticipationType::class, $bat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bat_index', ['id' => $bat->getEvent()->getId()]);
        }

        return $this->render('bat/add_organizations.html.twig', [
            'bat' => $bat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/date", name="bat_date", methods="POST")
     */
    public function batDate(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $date = $request->request->get('date');
            $event = $em->getRepository(Event::class)->find($request->request->get('id'));
            $datetime = \DateTime::createFromFormat('d/m/Y', $date);
            $event->setBatDate($datetime);
            $em->flush();

            return new JsonResponse(array('date' => $date), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }


    /**
     * @Route("/nosend-bat/{id}", name="nosend_bat", methods="GET|POST", requirements={"id"="\d+"})
     */
    public function noSendBat(Event $event, Request $request): Response
    {
        $form = $this->createForm(ParticipationsNoBatType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Les exposants sélectionnés ont été retirés de l\'envoi');

            return $this->redirectToRoute('bat_index', array('id' => $event->getId()));
        }

        return $this->render('bat/noSend.html.twig', array('form' => $form->createView(), 'event' => $event));
    }
}
