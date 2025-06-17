<?php

namespace App\Controller;

use App\Entity\BilanFile;
use App\Entity\Event;
use App\Entity\Participation;
use App\Repository\BilanFileRepository;
use App\Repository\BilanFileTypeRepository;
use App\Repository\EventRepository;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class BilanController extends AbstractController
{
    /**
     * @Route("/admin/bilan", name="bilan_index", methods="GET")
     */
    public function events(EventRepository $er, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $er->findAllQuery();

        $events = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );

        return $this->render('bilan/index.html.twig', ['events' => $events]);
    }

    /**
     * @Route("/bilan/{id}", name="bilan_view_pdf", methods="GET", requirements={"id" = "\d+"})
     */
    public function downloadPDF(BilanFile $file): Response
    {
        return $this->file($this->getParameter('public_dir') . '/uploads/bilan/' . $file->getPath());
    }

    /**
     * @Route("/bilan/arnaud/{slug}", name="bilan_show_arnaud", methods="GET")
     */
    public function show(Event $event, BilanFileRepository $er)
    {
        $bilan_files = $er->findByEvent($event);

        return $this->render('bilan/show.html.twig', [
            'event' => $event,
            'bilan_files' => $bilan_files,
        ]);
    }

//    /**
//     * @Route("/bilan/picture/{id}", name="bilan_picture_participation", methods="GET")
//     */
//    public function showPictures(Participation $participation, BilanFileTypeRepository $btr): Response
//    {
//        $event = $participation->getEvent();
//        $bilan_file_types = $btr->findByEvent($event);
//
//        return $this->render('bilan/bilan_picture_participation.html.twig', [
//            'participation' => $participation,
//            'event' => $event,
//            'menu_items' => $bilan_file_types,
//
//        ]);
//    }

}
