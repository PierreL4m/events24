<?php

namespace App\Controller;

use App\Entity\BilanFile;
use App\Entity\BilanFileType;
use App\Entity\Event;
use App\Form\BilanFileTypeForm;
use App\Repository\BilanFileRepository;
use App\Repository\BilanFileTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bilan/file")
 */
class BilanFileController extends AbstractController
{
    /**
     * @Route("/{id}", name="bilan_file_index", methods="GET", requirements={"id"="\d+"})
     */
    public function index(BilanFileRepository $bilanFileRepository, BilanFileTypeRepository $bilanFileTypeRepository, Event $event): Response
    {
        return $this->render('bilan_file/index.html.twig', [
            'bilan_files' => $bilanFileRepository->findByEvent($event),
            'event' => $event,
            'bilan_file_types' => $bilanFileTypeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}/{type_id}", name="bilan_file_new", methods="GET|POST", requirements={"id"="\d+", "type_id"="\d+"})
     * @Entity("type", expr="repository.find(type_id)")
     */
    public function new(Request $request, Event $event, BilanFileType $type): Response
    {
        $bilanFile = new BilanFile();
        $bilanFile->setBilanFileType($type);
        $bilanFile->setEvent($event);
        $form = $this->createForm(BilanFileTypeForm::class, $bilanFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bilanFile);
            $em->flush();

            return $this->redirectToRoute('bilan_file_index', array('id' => $event->getId()));
        }

        return $this->render('bilan_file/new.html.twig', [
            'bilan_file' => $bilanFile,
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/{id}/edit", name="bilan_file_edit", methods="GET|POST")
     */
    public function edit(Request $request, BilanFile $bilanFile): Response
    {
        $form = $this->createForm(BilanFileTypeForm::class, $bilanFile, ['required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bilan_file_index', ['id' => $bilanFile->getEvent()->getId()]);
        }

        return $this->render('bilan_file/edit.html.twig', [
            'bilan_file' => $bilanFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="bilan_file_delete", methods="GET", requirements={"id"="\d+"})
     */
    public function delete(Request $request, BilanFile $bilanFile): Response
    {
        $id = $bilanFile->getEvent()->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($bilanFile);
        $em->flush();

        return $this->redirectToRoute('bilan_file_index', ['id' => $id]);
    }
}
