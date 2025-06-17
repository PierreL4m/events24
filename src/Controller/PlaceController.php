<?php

namespace App\Controller;

use App\Entity\Color;
use App\Entity\Place;
use App\Form\PlaceType;
use App\Helper\GlobalEmHelper;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/place")
 */
class PlaceController extends AbstractController
{
    /**
     * @Route("/", name="place_index", methods="GET")
     */
    public function index(PlaceRepository $placeRepository): Response
    {
        return $this->render('place/index.html.twig', ['places' => $placeRepository->findAll()]);
    }

    /**
     * @Route("/new", name="place_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $place = new Place();
        $color = new Color();
        $place->addColor($color);

        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $place->setActive(true);
            $this->handleColors($place);

            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'Le lieu a été créé');

            return $this->redirectToRoute('place_show', array('id' => $place->getId()));
        }

        return $this->render('place/new.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="place_show", methods="GET")
     */
    public function show(Place $place): Response
    {
        return $this->render('place/show.html.twig', ['place' => $place]);
    }

    /**
     * @Route("/{id}/edit", name="place_edit", methods="GET|POST")
     */
    public function edit(Request $request, Place $place, GlobalEmHelper $em_helper): Response
    {
        $original_entities = $em_helper->backupOriginalEntities($place->getColors());

        $form = $this->createForm(PlaceType::class, $place);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em_helper->removeRelation($original_entities, $place, $place->getColors(), 'removeColor', true);
            $this->handleColors($place);
            $this->getDoctrine()->getManager()->persist($place);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Le lieu a été modifié'
            );

            return $this->redirectToRoute('place_show', array('id' => $place->getId()));
        }

        return $this->render('place/edit.html.twig', [
            'place' => $place,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="place_delete")
     */
    public function delete(Request $request, Place $place): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($place);
        $em->flush();
        //}

        $this->addFlash(
            'success',
            'Le lieu a été supprimé'
        );

        return $this->redirectToRoute('place_index');
    }

    private function handleColors($place)
    {
        $i = 1;
        foreach ($place->getColors() as $color) {
            //check if color does not already exist contains
            if (!$color->getName()) {
                $color->setName('color_' . $i);
            }
            $this->getDoctrine()->getManager()->persist($color);
            $i++;
        }
    }
}
