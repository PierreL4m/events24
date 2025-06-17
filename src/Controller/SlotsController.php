<?php


namespace App\Controller;

use App\Entity\Section;
use App\Entity\Slots;
use App\Entity\Event;
use App\Form\SectionType;
use App\Form\SlotsType;
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
class SlotsController extends AbstractController
{
    /**
     * @Route("/{id}", name="slots_index", requirements={"id"="\d+"}, methods="GET")
     */
    public function index(SlotsRepository $slotsRepository, Event $event, CandidateParticipationRepository $participationRepository): Response

    {
        return $this->render('slots/index.html.twig', ['slots' => $slotsRepository->findByEvent($event->getId()), 'event' => $event]);
    }


    /**
     * @Route("/send_free_slots/{id}", name="send_free_slots", methods="GET")
     */
    public function sendFreeSlots(Request $request, Event $event, TwigHelper $twig_helper, CandidateUserRepository $er, MailerHelper $mailerHelper, SlotsRepository $slotsRepository): Response
    {
        $candidates = $er->findByEventUnslots($event);
        $host = $request->getHost();
        foreach ($candidates as $candidate) {
            $mailerHelper->sendFreeSlots($candidate, $event, $host);
        }
        return $this->redirectToRoute('slots_index', ['id' => $event->getId()]);
    }

    /**
     * @Route("/add/{id}", name="slots_add", requirements={"id"="\d+"} , methods="GET|POST")
     */
    public function add(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SlotsType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $beginDate = $form->get('beginSlot')->getData();
            $endingSlot = $form->get('endingSlot')->getData();
            $maxCandidats = $form->get('maxCandidats')->getData();
            $class = '\App\Entity\\Slots';
            $slot = new $class;
            $slot->setBeginSlot($beginDate);
            $slot->setEndingSlot($endingSlot);
            $slot->setMaxCandidats($maxCandidats);
            $slot->setEvent($event);
            $slot->setis_full(0);
            $slot->setName($beginDate->format('H') . 'h' . $beginDate->format('i') . ' - ' . $endingSlot->format('H') . 'h' . $endingSlot->format('i'));
            $event->sethas_slots(1);
            $em->persist($slot);

            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Le creneau est ajouté');

            return $this->redirectToRoute('slots_index', ['id' => $event->getId()]);
        }

        return $this->render('slots/add.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="slots_edit", methods="GET|POST")
     */
    public function edit(Request $request, Slots $slots): Response
    {
        $form = $this->createForm(SlotsType::class, $slots);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slots->setis_full(0);
            $beginDate = $form->get('beginSlot')->getData();
            $endingSlot = $form->get('endingSlot')->getData();
            $slots->setName($beginDate->format('H') . 'h' . $beginDate->format('i') . ' - ' . $endingSlot->format('H') . 'h' . $endingSlot->format('i'));
            $em = $this->getDoctrine()->getManager();

            $em->persist($slots);
            $em->flush();
            return $this->redirectToRoute('slots_index', ['id' => $slots->getEvent()->getId()]);
        }

        return $this->render('slots/edit.html.twig', [
            'slots' => $slots,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/{id_event}", name="slots_delete", methods="GET")
     */
    public function delete(Request $request, Slots $slots, TwigHelper $twig_helper): Response
    {


        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new \AccessDeniedException("Vous n'avez pas accès à cette page");
        }
        $nbSlots = $twig_helper->countSlotsInEvent($slots->getEvent());
        if ($nbSlots == 1) {
            $event = $slots->getEvent();
            $event->sethas_slots(0);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($slots);
        $em->flush();

        $this->addFlash('notice', 'Le créneaux a été supprimé');
        return $this->redirectToRoute('slots_index', ['id' => $slots->getEvent()->getId()]);
    }


}
