<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Partner;
use App\Entity\PartnerType as PartnerTypeEntity;
use App\Entity\SectionPartner;
use App\Helper\ImageHelper;
use App\Form\AddEventsToPartnerType;
use App\Form\AddPartnerType;
use App\Form\PartnerType;
use App\Helper\GlobalEmHelper;
use App\Repository\PartnerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/partner")
 */
class PartnerController extends AbstractController
{
    //partner show by event is handle in public controller show section //

    /**
     * @Route("/", name="partner_index", methods="GET")
     */
    public function index(PartnerRepository $partnerRepository): Response
    {
        return $this->render('partner/index.html.twig', ['partners' => $partnerRepository->findAll()]);
    }

    /**
     * @Route("/new/{id}/{section_id}", name="partner_new", methods="GET|POST", requirements={"id" = "\d+", "section_id" = "\d+"})
     * @ParamConverter("section", class="App\Entity\SectionPartner", options={"id" = "section_id"})
     * )
     */
    public function new(Request $request, Event $event, SectionPartner $section, ImageHelper $image_helper): Response
    {
        $partner = new Partner();
        $em = $this->getDoctrine()->getManager();
        $partner_type = $em->getRepository(PartnerTypeEntity::class)->findOneBySlug($section->getTypeSlug());


        $form = $this->createForm(PartnerType::class, $partner, array('required' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = $image_helper->handleImage($em, $partner->getLogoFile(), $form->get('logoFile'), 'partenaires', $partner->getLogoName(), 233, 233);
            if($ok[1]){
                $partner->setLogo($ok[1]);
            }
            if ($ok) {
                $partner->addEvent($event);
                if ($partner_type) {
                    $partner->setPartnerType($partner_type);
                }
                $em->persist($partner);
                $em->flush();
                $partner->getLogo()->setOriginalPath("partenaires/".$partner->getLogoName());
                $partner->getLogo()->setPath("partenaires/".$partner->getLogoName());
                $em->flush();

                $this->addFlash('success', 'Le partenaire a bien été créé');

                return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
            }
        }

        return $this->render('partner/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
            'event' => $event,
            'partner_type' => $partner_type
        ]);
    }

    /**
     * @Route("/{id}/edit/{section_id}", name="partner_edit", methods="GET|POST", requirements={"id" = "\d+","section_id" = "\d+|null"}, defaults={"section_id" = null})
     */
    public function edit(Request $request, Partner $partner, ImageHelper $image_helper, $section_id): Response
    {
        $form = $this->createForm(PartnerType::class, $partner);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {


            $ok = $image_helper->handleImage($em, $partner->getLogoFile(), $form->get('logoFile'), 'partenaires', 233, 233);
            if($ok[1]){
                $partner->setLogo($ok[1]);
            }
            if ($ok) {
                $this->getDoctrine()->getManager()->flush();
                $partner->getLogo()->setOriginalPath("partenaires/".$partner->getLogoName());
                $partner->getLogo()->setPath("partenaires/".$partner->getLogoName());
                $em->flush();
                $this->addFlash('success', 'Le partenaire a bien été modifié');

                $this->getDoctrine()->getManager()->flush();

                if ($section_id) {
                    return $this->redirectToRoute('section_show', ['id' => $section_id]);
                } else {
                    return $this->redirectToRoute('partner_index');
                }
            }
        }

        return $this->render('partner/edit.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="partner_delete",requirements={"id" = "\d+"})
     */
    public function delete(Partner $partner): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($partner);
        $em->flush();
        // }

        return $this->redirectToRoute('partner_index');
    }

    /**
     * @Route("remove-from-event/{id}/{event_id}/{section_id}", name="partner_remove_from_event", methods="GET", requirements={"id" = "\d+","event_id" = "\d+","section_id" = "\d+"})
     * @ParamConverter("event", class="App\Entity\Event", options={"id" = "event_id"})
     */
    public function removeFromEvent(Partner $partner, Event $event, $section_id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $event->removePartner($partner);
        $em->flush();

        return $this->redirectToRoute('section_show', array('id' => $section_id));
    }

    /**
     * @Route("/add-partners-to-event/{id}/{section_id}", name="partners_add", methods="GET|POST", requirements={"id" = "\d+", "section_id" = "\d+"})
     * @ParamConverter("section", class="App\Entity\SectionPartner", options={"id" = "section_id"})
     * )
     */
    public function addToEvent(Request $request, Event $event, SectionPartner $section): Response
    {
        $em = $this->getDoctrine()->getManager();
        $partner_type = $em->getRepository(PartnerTypeEntity::class)->findOneBySlug($section->getTypeSlug());
        // has to bkp other partner type entities
        // they are not in AddPartner type
        // so they are deleted on flush
        $partners_bkp = [];

        foreach ($event->getPartners() as $partner) {
            if ($partner->getPartnerType() != $partner_type) {
                array_push($partners_bkp, $partner);
            }
        }

        $form = $this->createForm(AddPartnerType::class, $event, ['partner_type_id' => $partner_type->getId()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($partners_bkp as $partner) {
                $event->addPartner($partner);
            }
            $em->flush();
            $this->addFlash('success', 'Les partenaires ont été ajoutés');

            return $this->redirectToRoute('section_show', ['id' => $section->getId()]);
        }

        return $this->render('partner/addPartnersToEvent.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'partner_type' => $partner_type
        ]);
    }

    /**
     * @Route("/add-events-to-partner/{id}", name="add_events_to_partner", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function addEvents(Request $request, Partner $partner, GlobalEmHelper $helper): Response
    {
        $original_entities = $helper->backupOriginalEntities($partner->getEvents());

        $form = $this->createForm(AddEventsToPartnerType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // old events are not displayed in form
            // have to add it back manually
            foreach ($original_entities as $event) {
                if ($event->getDate() <= new \DateTime) {
                    $partner->addEvent($event);
                }
            }
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Le partenaire a été ajouté aux événements');

            return $this->redirectToRoute('partner_index');
        }

        return $this->render('partner/addEventsToPartner.html.twig', [
            'form' => $form->createView(),
            'partner' => $partner
        ]);
    }
}
