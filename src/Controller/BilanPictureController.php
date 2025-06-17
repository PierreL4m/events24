<?php

namespace App\Controller;

use App\Entity\BilanPicture;
use App\Entity\Participation;
use App\Form\BilanPictureType;
use App\Repository\BilanPictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/bilan/picture")
 */
class BilanPictureController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="bilan_picture_new", methods="GET|POST", requirements={"id" = "\d+"})
     * @param Request $request
     * @param Participation $participation
     * @return Response
     */
    public function new(Request $request, Participation $participation): Response
    {
        $form = $this->createForm(BilanPictureType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($form->get('files')->getData() as $file) {
                $bilanPicture = new BilanPicture();
                $bilanPicture->setParticipation($participation);
                $bilanPicture->setFile($file);
                $em->persist($bilanPicture);
            }

            $em->flush();

            return $this->redirectToRoute('bilan_file_index', array('id' => $participation->getEvent()->getId()));
        }

        return $this->render('bilan_picture/new.html.twig', [
            'participation' => $participation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remove/{id}", name="bilan_picture_delete", methods="GET", requirements={"id" = "\d+"})
     */
    public function delete(Request $request, BilanPicture $bilanPicture): Response
    {
        $id = $bilanPicture->getParticipation()->getEvent()->getId();
        //if ($this->isCsrfTokenValid('delete'.$bilanPicture->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($bilanPicture);
        $em->flush();
        //}

        return $this->redirectToRoute('bilan_file_index', array('id' => $id));
    }
}
