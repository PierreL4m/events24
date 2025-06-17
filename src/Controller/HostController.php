<?php

namespace App\Controller;

use App\Entity\EventType;
use App\Form\EventTypeType;
use App\Repository\EventTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\HostRepository;
use App\Entity\Host;
use App\Form\HostType;

/**
 * Host controller.
 * @Route("/admin/host")
 */
class HostController extends AbstractController
{
    /**
     * Lists all host entities
     * @Route("/", name="host_index")
     * @Template()
     */
    public function indexAction(HostRepository $er): Response
    {
        $hosts = $er->findAll();

        return $this->render('host/index.html.twig', ['hosts' => $hosts]);
    }

    /**
     * @Route("/new", name="host_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $host = new Host();
        $form = $this->createForm(HostType::class, $host);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($host);
            $em->flush();

            $this->addFlash(
                'success',
                'Le host a été créé'
            );

            return $this->redirectToRoute('host_index');
        }

        return $this->render('host/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="host_edit", requirements={"id" = "\d+"}, methods="GET|POST")
     */
    public function editAction(Request $request, Host $host)
    {
        $form = $this->createForm(HostType::class, $host);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Le host a été modifié'
            );
            return $this->redirectToRoute('host_index');
        }

        return $this->render('host/edit.html.twig', [
            'host' => $host,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/delete/{id}", name="host_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction(Host $host)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($host);
        $em->flush();

        $this->addFlash(
            'success',
            'Le host a été supprimé'
        );

        return $this->redirectToRoute('host_index');
    }

}
