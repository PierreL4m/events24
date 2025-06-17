<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\Image;
use App\Helper\TwigHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class IndexAdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $current_events = $em->getRepository(Event::class)->findCurrentEvents();
        $nb = 3;
        $organizations = $em->getRepository(Organization::class)->findLast(3);
        $participations = $em->getRepository(Participation::class)->findLast(3);

        $past_events = $em->getRepository(Event::class)->findPastEvents($nb);

        return $this->render('index_admin/index.html.twig', array(
                'current_events' => $current_events,
                'organizations' => $organizations,
                'participations' => $participations,
                'past_events' => $past_events,
                'nb' => $nb
            )
        );
    }

    /**
     * @Route("/admin/download/{id}", name="file_download", methods="GET", requirements={"id"="\d+"})
     */
    public function download(Participation $participation, TwigHelper $helper)
    {
        /*
        <a href="{{asset(candidate.getCvPath)}}" target="_blank">
                        <i class="fa fa-download"></i>
                    </a>
         */
        ob_end_clean();
        if (!$participation->getLogo()) {
            throw new Exception('Cannot download a pathless file');
        }
        $path = '/uploads/'.$participation->getLogo()->getPath();
        return $this->file($this->getParameter('public_dir') . $path, $participation->getCompanyName().".png");
        dump($this->getParameter('public_dir').$path);
        die();
//        if ($helper->fileExists($path)) {
//        } else {
//            throw new NotFoundHttpException('Le fichier que vous souhaiter télécharger n\'existe plus');
//        }
    }
}
