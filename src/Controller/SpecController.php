<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\SpecBase;
use App\Form\LastPageType;
use App\Form\SpecBaseType;
use App\Helper\GlobalHelper;
use App\Helper\TwigHelper;
use App\Repository\SpecBaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use App\Repository\EventRepository;

/**
 * @Route("/admin/spec-base")
 */
class SpecController extends AbstractController
{

    // public function new(Request $request): Response
    // {
    //     /**
    //  * @Route("/new", name="spec_base_new", methods="GET|POST")
    //  */
    //     $specBase = new SpecBase();
    //     $form = $this->createForm(SpecBaseType::class, $specBase);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($specBase);
    //         $em->flush();

    //         return $this->redirectToRoute('spec_base_index');
    //     }

    //     return $this->render('spec_base/new.html.twig', [
    //         'spec_base' => $specBase,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}/edit", name="event_spec_base_edit", methods="GET|POST")
     */
    public function edit(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $spec_repo = $em->getRepository(SpecBase::class);
        $specBase = $event->getSpecBase();
        if (!$specBase) {
            $specBase = $spec_repo->findDefault();
            if (!$specBase) {
                $specBase = new SpecBase();
                $specBase->setUseDefault(true);
                $specBase->setName('Cahier des charges par défaut');
            }
        }

        $previous_name = $specBase->getName();

        $form = $this->createForm(SpecBaseType::class, $specBase, ['event' => $event]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->has('useDefault') && ($d = $form->get('useDefault')) && $d->getData()) {
                $new = $em->getRepository(SpecBase::class)->findDefault();
            } else {
                $new = $specBase;
                if ($specBase->isUseDefault()) {
                    if (!$form->get('editDefault')->getData()) {
                        if ($previous_name == $specBase->getName()) {
                            $new_name = 'Cahier des charges ' . $event->__toString();
                            if (($byname = $spec_repo->findOneByName($new_name))) {
                                $new = $byname;
                            } else {
                                $new = new SpecBase();
                                $new->setName($new_name);
                            }
                        } else {
                            $new = new SpecBase();
                            $new->setName($specBase->getName());
                        }
                        $new->setUseDefault(null);
                        $new->setUpdatedAt(new \DateTime);
                    }
                }
            }
            $new->setFile($form->get('file')->getData());
            $event->setSpecificationPath(null);
            $event->setSpecBase($new);
            $em->persist($event);
            $em->persist($new);
            $em->flush();

            $this->addFlash('message', "La base de cahier des charges a bien été modifiée");
            $this->addFlash('danger', "MAIS VOUS DEVEZ RECHARGER LA DERNIERE PAGE SIGNÉE POUR TERMINER LA MISE A JOUR");

            return $this->redirectToRoute('tech_file_index', ['id' => $event->getId()]);
        }

        return $this->render('spec_base/edit.html.twig', [
            'spec_base' => $specBase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit", name="spec_base_edit", methods="GET|POST")
     */
    public function editGlobal(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $specBase = $em->getRepository(SpecBase::class)->findDefault();

        if (!$specBase) {
            $specBase = new SpecBase();
            $specBase->setDefault(true);
            $specBase->setName('Cahier des charges par défaut');
        }

        $form = $this->createForm(SpecBaseType::class, $specBase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($specBase);

            $em->flush();

            return $this->redirectToRoute('tech_file_index', ['id' => $event->getId()]);
        }

        return $this->render('spec_base/edit.html.twig', [
            'spec_base' => $specBase,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/load-last-page", name="spec_last_page", methods="GET|POST")
     */
    public function loadLastPage(Request $request, Event $event, EventRepository $er, GlobalHelper $helper): Response
    {

        $form = $this->createForm(LastPageType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $base = $event->getSpecBase();

            if (!$base) {
                throw new \Exception('Spec base not found');
            }
            $uploadedFile = $form->get('last_page')->getData();
            $public_dir = $this->getParameter('kernel.project_dir') . '/public';
            $base_path = $public_dir . "/uploads/spec_base/" . $base->getPath();

            $i = 0;
            do {
                $spec_name = 'cahier-des-charges_' . $helper->escapeSpaces($event) . ($i > 0 ? $i : '') . '.pdf';
                $i++;
            } while ($tmp = $er->findOneBySpecificationPath($spec_name));

            $spec_path = $public_dir . "/uploads/spec/" . $spec_name;
            $merger = new Merger;
            $merger->addFile($base_path);
            $merger->addFile($uploadedFile->getRealPath());
            $createdPdf = $merger->merge();
            file_put_contents($spec_path, $createdPdf);
            $event->setSpecificationPath($spec_name);


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tech_file_index', ['id' => $event->getId()]);
        }

        return $this->render('spec_base/load_last_page.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

    /**
     * @Route("/{id}/download-base/", name="download_spec_base", methods="GET")
     */
    public function downloadBase(SpecBase $spec_base): Response
    {
        $base_path = $this->get('kernel')->getProjectDir() . '/public/uploads/spec_base/' . $spec_base->getPath();

        return $this->file($base_path);
    }

    /**
     * Downloads full spec tech doc (ie base + signed last page)
     * @Route("/{id}/download", name="download_spec", methods="GET", requirements={"id" = "\d+"})
     */
    public function download(Event $event): Response
    {
        $path = $this->get('kernel')->getProjectDir() . '/public/uploads/spec/' . $event->getSpecificationPath();

        return $this->file($path);
    }
}
