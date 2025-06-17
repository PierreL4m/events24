<?php
namespace App\Controller;

use App\Entity\KeyNumbers;
use App\Entity\Event;
use App\Form\KeyNumbersType;
use App\Repository\SlotsRepository;
use App\Repository\CandidateParticipationRepository;
use App\Repository\CandidateUserRepository;
use App\Helper\TwigHelper;
use App\Helper\MailerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/slots")
 */
class KeyNumberController extends AbstractController
{


    /**
     * @Route("/chiffres_cles/add/{id}", name="key_number_add", requirements={"id"="\d+"} , methods="GET|POST")
     */
    public function add(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(KeyNumbersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nbExposants = $form->get('exposants')->getData();
            $nbOffres = $form->get('offres')->getData();
            $nbCandidats = $form->get('candidats')->getData();
            $nbEntretiens = $form->get('entretiens')->getData();
            $class = '\App\Entity\\KeyNumbers';
            $keyNumbers = new $class;
            $keyNumbers->setEntretiens($nbEntretiens);
            $keyNumbers->setExposants($nbExposants);
            $keyNumbers->setOffres($nbOffres);
            $keyNumbers->setCandidats($nbCandidats);
            $em->persist($keyNumbers);

            $event->setKeyNumbers($keyNumbers);
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Les chiffres clés ont été modifié');

            return $this->redirectToRoute('section_index', ['id' => $event->getId()]);
        }

        return $this->render('key_numbers/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

}