<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\PressFile;
use App\Form\PressFileType;
use App\Helper\GlobalHelper;
use App\Repository\PressFileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamedResponse;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamer\ZipStreamer;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamer\ZipStreamerFile;


/**
 * @Route("/admin/press")
 */
class PressFileController extends AbstractController
{
    /**
     * @Route("/{id}", name="press_file_index", methods="GET", requirements={"id" = "\d+"})
     */
    public function index(Event $event): Response
    {
        return $this->render('press_file/index.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/new/{id}", name="press_file_new", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function new(Event $event, Request $request): Response
    {
        $pressFile = new PressFile();
        $form = $this->createForm(PressFileType::class, $pressFile, array('required' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $pressFile->setEvent($event);
            $em->persist($pressFile);
            $em->flush();

            return $this->redirectToRoute('press_file_index', ['id' => $event->getId()]);
        }

        return $this->render('press_file/new.html.twig', [
            'press_file' => $pressFile,
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/download/{id}", name="press_file_download", methods="GET", requirements={"id" = "\d+"})
     */
    public function download(PressFile $pressFile): Response
    {
        return $this->file($pressFile->getPathSrc(), $pressFile->getName());
    }

    /**
     * @Route("/{id}/edit", name="press_file_edit", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, PressFile $pressFile): Response
    {
        $form = $this->createForm(PressFileType::class, $pressFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('press_file_index', ['id' => $pressFile->getEvent()->getId()]);
        }

        return $this->render('press_file/edit.html.twig', [
            'press_file' => $pressFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="press_file_delete", methods="GET")
     */
    public function delete(Request $request, PressFile $pressFile): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$pressFile->getId(), $request->request->get('_token'))) {
        $id = $pressFile->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $em->remove($pressFile);
        $em->flush();
        // }

        return $this->redirectToRoute('press_file_index', ['id' => $id]);
    }

    /**
     * @Route("/download-kit/{id}", name="press_kit_download", methods="GET", requirements={"id" = "\d+"})
     */
    public function downloadKit(Event $event, GlobalHelper $helper): Response
    {
        ob_end_clean();
        $zipStreamer = new ZipStreamer("kit_presse_" . $event->getSlug() . '.zip');

        foreach ($event->getPressFiles() as $press) {
            $extension = pathinfo($press->getPath(), PATHINFO_EXTENSION);
            $zipStreamer->add(
                $press->getPathSrc(),
                iconv('UTF-8', 'CP850', $press->getName() . '.' . $extension)
            );
        }
        return new ZipStreamedResponse($zipStreamer);
    }

}
