<?php

namespace App\Controller;
use App\Entity\Sector;
use App\Entity\SectorPic;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\EventJobs;
use App\Entity\EventSimple;
use App\Entity\ExposantScanUser;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\Place;
use App\Entity\RecruitmentOffice;
use App\Entity\RhUser;
use App\Factory\EventFactory;
use App\Factory\ParticipationFactory;
use App\Form\AddOrganizationType;
use App\Form\AddRoType;
use App\Form\ChangeManagerType;
use App\Form\ChoosePlaceType;
use App\Form\EventType;
use App\Form\RecruitmentOfficeType;
use App\Form\TextFieldType;
use App\Helper\GlobalEmHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\ParticipationHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateRepository;
use App\Repository\CandidateUserRepository;
use App\Repository\EventRepository;
use App\Repository\JobRepository;
use App\Repository\ParticipationRepository;
use App\Repository\SectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use App\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Form\ChooseEventTypePlaceType;
use App\Repository\SpecBaseRepository;
use Doctrine\ORM\EntityManager;
use App\Form\SearchFieldType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use function PHPUnit\Framework\isEmpty;

/**
 * @Route("/admin/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods="GET|POST")
     */
    public function index(EventRepository $er, Request $request, PaginatorInterface $paginator): Response
    {
        $form_search = $this->createForm(SearchFieldType::class, null, array('placeholder' => 'Rechercher un événement(Ville/Manager/Année/Type)'));
        $form_search->handleRequest($request);
        if ($form_search->isSubmitted() && $form_search->isValid()) {
            $search = $form_search->get('search')->getData();
            $query = $er->searchQuery($search);
        } else {
            $query = $er->findAllQuery();
        }


        $events = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );

        return $this->render('event/index.html.twig', ['events' => $events, 'form_search' => $form_search->createView()]);
    }

    /**
     * @Route("/ids", name="event_ids", methods="GET")
     */
    public function ids(EntityManagerInterface $em): Response
    {


        $query = $em->createQuery('SELECT DISTINCT e.id, e.date, p.name, p.city, t.fullName FROM \App\Entity\Event e JOIN e.place p JOIN e.type t ORDER BY e.date DESC, t.fullName, p.name');
        $results = $query->getResult();

        $events_ids = [];
        $annees = [];
        foreach ($results as $result) {
            $annee = $result['date']->format('Y');
            if (!in_array($annee, $annees)) {
                $annees[] = $annee;
            }
            $place = $result['name'] . '-' . $result['city'];
            $type = $result['fullName'];

            if (!isset($events_ids[$annee])) {
                $events_ids[$annee] = [];
            }
            if (!isset($events_ids[$annee][$type])) {
                $events_ids[$annee][$type] = [];
            }
            if (!isset($events_ids[$annee][$type][$place])) {
                $events_ids[$annee][$type][$place] = [];
            }
            $events_ids[$annee][$type][$place][] = ['date' => $result['date']->format('d/m/Y'), 'id' => '(' . $result['id'] . ')'];
        }

        return $this->render('event/ids.html.twig', ['ids' => $events_ids, 'annees' => $annees]);
    }

    /**
     * @Route("/place", name="event_place", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(ChooseEventTypePlaceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $type = $form->get('type')->getData();
            $place = $form->get('place')->getData();

            return $this->redirectToRoute('event_new', array('type' => $type->getId(), 'place' => $place->getId()));
        }

        return $this->render('event/choose_city.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{anchor}", name="event_show", methods="GET", defaults={"anchor" : null}, requirements={"id" : "\d+", "anchor" : "\d+|null"})
     */
    public function show(Event $event, EventRepository $er, $anchor = null, $parentEvent = null, $childEvent = null, JobRepository $jobRepo): Response
    {
        if($event->getParentEvent() != null){
            $parentEvent = $event->getParentEvent();
            $childEvent = $event->getParentEvent()->getChildEvents();
        }else{
            $childEvent = $event->getChildEvents();
        }
        $jobs = $jobRepo->getByEvent($event);
        return $this->render('event/show.html.twig', ['event' => $event, 'anchor' => $anchor, 'parentEvent' => $parentEvent, 'childEvent' => $childEvent, 'jobs' => $jobs]);
    }

    /**
     * @Route("/reload-scan/{id}/{event}", name="reload_scan", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function reloadScan(Organization $organization, Request $request, Event $event, GlobalEmHelper $globalEmHelper, $anchor = null): Response
    {
        $em = $this->getDoctrine()->getManager();
        $name = $organization->getName();

        if (strlen($name) >= 10) {
            $name = mb_substr($name, 0, 10);
        }

        $user = new ExposantScanUser();
        $user->setUsername($name);
        $name = $globalEmHelper->generateUsername($user, $em);
        $password = GlobalHelper::random_str(6);

        $user->setFirstname($name);
        $user->setUsername($name);
        $user->setLastname($name);
        $user->setEmail($name);
        $user->setPhone('0320202020');
        $user->setPlainPassword($password);
        $user->setSavedPlainPassword($password);
        $user->setEnabled(true);
        $user->addRole('ROLE_EXPOSANT_SCAN');
        $user->setOrganization($organization);

        $em->persist($user);
        $em->flush();

        return $this->render('event/show.html.twig', ['event' => $event, 'anchor' => $anchor]);
    }

    /**
     * @Route("/new/{type}-{place}", name="event_new", methods="GET|POST")
     */
    public function create(Request $request, \App\Entity\EventType $type, Place $place, GlobalHelper $helper, ImageHelper $image_helper, TwigHelper $twig_helper, H48Helper $h48_helper, SpecBaseRepository $repo): Response
    {
        $event = EventFactory::get($type);
        $event->setPlace($place);
        $event->setType($type);

        foreach ($type->getOrganizationTypes() as $type) {
            $event->addOrganizationType($type);
        }

        $event->setSpecBase($repo->findDefault());

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*
            TODO escape
            $ok5 = $image_helper->handleImage($event->getInvitation(),$form->get('invitation'),'invitation_models',1240,1754); //300dpipok
            */
            $ok1 = $image_helper->handleImage($em, $event->getBackFacebookFile(), $form->get('backFacebookFile'), 'back_facebook');
            $ok3 = $image_helper->handleImage($em, $event->getPubFile(), $form->get('pubFile'), 'pub'); //300dpi
            $ok4 = $image_helper->handleImage($em, $event->getlogoFile(), $form->get('logoFile'), 'logo'); //300dpi
            $ok5 = $image_helper->handleImage($em, $event->getbackBadgeFile(), $form->get('backBadgeFile'), 'background_badge');
            $ok7 = $image_helper->handleImage($em, $event->getbackTwitterFile(), $form->get('backTwitterFile'), 'back_twitter');
            $ok8 = $image_helper->handleImage($em, $event->getbackLinkedinFile(), $form->get('backLinkedinFile'), 'back_linkedin');
            $ok9 = $image_helper->handleImage($em, $event->getbackInstaFile(), $form->get('backInstaFile'), 'back_insta');
            $ok6 = $image_helper->handleImage($event->getBannerFile(), $form->get('bannerFile'), 'banners', 233, 233);
            $ok2 = $image_helper->handleImage($em, $event->getSocialLogoFile(), $form->get('socialLogoFile'), 'social_logos'); //8mb max
            $ok10 = $image_helper->handleImage($em, $event->getPubAccredFile(), $form->get('pubAccredFile'), 'pub'); //8mb max
            $ok11 = $image_helper->handleImage($em, $event->getBackSignatureFile(), $form->get('backSignatureFile'), 'back_signature');

            if ($ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6 && $ok7 && $ok8 && $ok9 && $ok11) {
                $event->setManager($this->getUser());
                $this->setSlug($event, $h48_helper, $helper);
                $this->handleCheckboxes($event, $request);
                $em->persist($event);

                $exists = $em->getRepository(Event::class)->findBySlug($event->getSlug());

                if (count($exists) > 0) {
                    $form->addError(new FormError ('L\'événement ' . $event->__toString() . ' existe déjà.'));
                } else {
                    //set up relevant previous datas form last event in same city
                    $last_event = $this->getDoctrine()->getManager()->getRepository(Event::class)->findLastSameCity($event);

                    if ($last_event) {
                        if (!$event->getBannerFile()) {
                            $image_helper->duplicateImage($event, $last_event, 'bannerFile');
                        }
                        if (!$event->getLogoFile()) {
                            $image_helper->duplicateImage($event, $last_event, 'logoFile');
                        }
                        if (!$event->getbackBadgeFile()) {
                            $image_helper->duplicateImage($event, $last_event, 'backBadgeFile');
                        }

                        foreach ($last_event->getPartners() as $partner) {
                            if (!$event->getPartners()->contains($partner)) {
                                $event->addPartner($partner);
                            }
                        }

                        foreach ($last_event->getSections() as $section) {
                            if ($section->getSectionType()->getAutomaticGeneration()) {
                                $new_section = clone $section;
                                $event->addSection($new_section);
                            } elseif ($section->getOnPublic()) {
                                $new_section = $section->simpleCopy();
                                $event->addSection($new_section);
                            }
                        }
                        if($form->has('sectorPic')){
                            $sectorPic = $form->get('sectorPic')->getData();
                            $pictoSectorEvent = new PictoSectorEvent;
                            foreach ($sectorPic as $pic) {
                                $pictoSectorEvent->setEvent($event);
                                $pictoSectorEvent->setSectorPic($pic);

                            }
                        }
                    }
                    $event->setParentEvent($form->get('parentEvent')->getData());
                    $em->flush();

                    $this->addFlash(
                        'success',
                        'L\'événement a été créé'
                    );

                    return $this->redirectToRoute('event_show', array('id' => $event->getId()));
                }
            }
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'place' => $place
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods="GET|POST")
     */
    public function edit(Request $request, Event $event, GlobalHelper $helper, ImageHelper $image_helper, TwigHelper $twig_helper, H48Helper $h48_helper, SpecBaseRepository $repo): Response
    {
        $event->setSpecBase($repo->findDefault());

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /*
            TODO escape
            $ok5 = $image_helper->handleImage($event->getInvitation(),$form->get('invitation'),'invitation_models',1240,1754); //300dpipok
            */
            $ok4 = $image_helper->handleImage($em, $event->getLogoFile(), $form->get('logoFile'), 'events_logos', $event->getLogoName()); //300dpi
            $ok6 = $image_helper->handleImage($em, $event->getBannerFile(), $form->get('bannerFile'), 'banners', $event->getBannerName(), 233, 233);
            $ok5 = $image_helper->handleImage($em, $event->getBackBadgeFile(), $form->get('backBadgeFile'), 'background_badge', $event->getBannerName());
            $ok1 = $image_helper->handleImage($em, $event->getBackFacebookFile(), $form->get('backFacebookFile'), 'back_facebook', $event->getBackfacebookName());
            $ok3 = $image_helper->handleImage($em, $event->getPubFile(), $form->get('pubFile'), 'pub',$event->getPubName()); //300dpi
            $ok7 = $image_helper->handleImage($em, $event->getBackTwitterFile(), $form->get('backTwitterFile'), 'back_twitter',$event->getBackTwitterName());
            $ok8 = $image_helper->handleImage($em, $event->getBackLinkedinFile(), $form->get('backLinkedinFile'), 'back_linkedin',$event->getBackLinkedinName());
            $ok9 = $image_helper->handleImage($em, $event->getBackInstaFile(), $form->get('backInstaFile'), 'back_insta',$event->getBackInstaName());
            $ok10 = $image_helper->handleImage($em, $event->getPubAccredFile(), $form->get('pubAccredFile'), 'pub',$event->getPubAccredName()); //8mb max
            $ok11 = $image_helper->handleImage($em, $event->getBackSignatureFile(), $form->get('backSignatureFile'), 'back_signature', $event->getBackSignatureName());

            if($ok11[1]){
                $event->setBackSignature($ok11[1]);
            }
            if($ok1[1]){
                $event->setBackFacebook($ok1[1]);
            }
            if($ok3[1]){
                $event->setPub($ok3[1]);
            }
            if($ok4[1]){
                $event->setLogo($ok4[1]);
            }
            if($ok5[1]){
                $event->setBackBadge($ok5[1]);
            }
            if($ok6[1]){
                $event->setBanner($ok6[1]);
            }
            if($ok7[1]){
                $event->setBacktwitter($ok7[1]);
            }
            if($ok8[1]){
                $event->setBackLinkedin($ok8[1]);
            }
            if($ok9[1]){
                $event->setBackInsta($ok9[1]);
            }
            if($ok10[1]){
                $event->setPubAccred($ok10[1]);
            }
            $ok2 = $image_helper->handleImage($em, $event->getSocialLogoFile(), $form->get('socialLogoFile'), 'social_logos'); //8mb max
            if ($ok1 && $ok2 && $ok3 && $ok4 && $ok5 && $ok6 && $ok7 && $ok8 && $ok9 && $ok10) {
                $this->setSlug($event, $h48_helper, $helper);
                $this->handleCheckboxes($event, $request);
                $em->persist($event);

                $exists = $em->getRepository(Event::class)->findBySlug($event->getSlug());

                if (count($exists) > 0) {
                    $event->setParentEvent($form->get('parentEvent')->getData());
                    $em->flush();
                    if($event->getLogo()){
                        $event->getLogo()->setOriginalPath("uploads/events_logos/".$event->getLogoName());
                        $event->getLogo()->setPath("uploads/events_logos/".$event->getLogoName());
                    }
                    if($event->getBanner()){
                        $event->getBanner()->setOriginalPath("uploads/banners/".$event->getBannerName());
                        $event->getBanner()->setPath("uploads/banners/".$event->getBannerName());
                    }
                    if($event->getBackBadge()){
                        $event->getBackBadge()->setOriginalPath("uploads/background_badge/".$event->getBackBadgeName());
                        $event->getBackBadge()->setOriginalPath("uploads/background_badge/".$event->getBackBadgeName());

                    }
                    if($event->getBackInsta()){
                        $event->getBackInsta()->setPath("uploads/backInsta/".$event->getBackInstaName());
                        $event->getBackInsta()->setOriginalPath("uploads/backInsta/".$event->getBackInstaName());

                    }
                    if($event->getBackTwitter()){
                        $event->getBackTwitter()->setOriginalPath("uploads/backTwitter/".$event->getBackTwitterName());
                        $event->getBackTwitter()->setPath("uploads/backTwitter/".$event->getBackTwitterName());
                    }
                    if($event->getBackFacebook()){
                        $event->getBackFacebook()->setOriginalPath("uploads/backFacebook/".$event->getBackFacebookName());
                        $event->getBackFacebook()->setPath("uploads/backFacebook/".$event->getBackFacebookName());

                    }
                    if($event->getBackSignature()){
                        $event->getBackSignature()->setOriginalPath("uploads/backSignature/".$event->getBackSignatureName());
                        $event->getBackSignature()->setPath("uploads/backSignature/".$event->getBackSignatureName());

                    }
                    if($event->getBackLinkedin()){
                        $event->getBackLinkedin()->setOriginalPath("uploads/backLinkedin/".$event->getBacklinkedinName());
                        $event->getBackLinkedin()->setPath("uploads/backLinkedin/".$event->getBacklinkedinName());
                    }
                    if($event->getPub()){
                        $event->getPub()->setOriginalPath("uploads/pub/".$event->getPubName());
                        $event->getPub()->setPath("uploads/pub/".$event->getPubName());
                    }
                    if($event->getPubAccred()){
                        $event->getPubAccred()->setOriginalPath("uploads/pub/".$event->getPubAccredName());
                        $event->getPubAccred()->setPath("uploads/pub/".$event->getPubAccredName());
                    }
                    if($form->has('sectorPic')){
                        $sectorPic = $form->get('sectorPic')->getData();
                        foreach ($sectorPic as $pic) {
                            $event->addSectorPic($pic);
                        }
                    }
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'L\'événement a été modifié'
                    );

                    return $this->redirectToRoute('event_show', array('id' => $event->getId()));
                }
            }
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="event_delete")
     */
    public function delete(Request $request, Event $event): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        //}
        //
        $this->addFlash('success', 'L\'événement a été supprimé');

        return $this->redirectToRoute('admin_index');
    }

    /**
     * @Route("/{id}/invitation/{mode}", name="event_invitation", methods="GET", requirements={"id": "\d+", "mode": "pdf|html"})
     */
    public function previewInvitation(Event $event, $mode, \Knp\Snappy\Pdf $snappy): Response
    {
        $participation = new CandidateParticipation();
        $participation->setEvent($event);
        $candidate = new CandidateUser();
        $candidate->setFirstname('Adèle');
        $candidate->setLastname('Catrem');
        $candidate->addCandidateParticipation($participation);
        $qrcode = [
            'lastName' => 'Catrem',
            'firstName' => 'Adèle',
            'userId' => 666,
            'participationId' => 666
        ];

        $base64 = base64_encode(json_encode($qrcode));
        $urlQRCode = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=".$base64;
        $participation->setQrCode($urlQRCode);

        $rendered = $this->render(
            'snappy/invitation_new.html.twig',
            array(
                'participation' => $participation
            )
        );
        if ($mode == 'html') {
            return $rendered;
        }

        $file_name = 'invitation_test.pdf';
        if (file_exists($file_name)) {
            @unlink($file_name);
        }
        $file_path = $this->getParameter('kernel.project_dir') . '/public/' . $file_name;

        //     dump($event->getType()->getShortName());
        $snappy->generateFromHtml($rendered, $file_path);
        return new BinaryFileResponse($file_path);
    }

    /**
     * @Route("/{id}/accreditation/{mode}", name="event_accreditation", methods="GET", requirements={"id": "\d+", "mode": "pdf|html"})
     */
    public function previewAccreditation(Event $event, $mode, \Knp\Snappy\Pdf $snappy,SectionRepository $section ): Response
    {
        $participation = new CandidateParticipation();
        $participation->setEvent($event);
        $candidate = new CandidateUser();
        $candidate->setFirstname('Adèle');
        $candidate->setLastname('Catrem');
        $candidate->addCandidateParticipation($participation);
        $qrcode = [
            'lastName' => 'Catrem',
            'firstName' => 'Adèle',
            'userId' => 666,
            'participationId' => 666
        ];

        $base64 = base64_encode(json_encode($qrcode));
        $urlQRCode = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=".$base64;
        $participation->setQrCode($urlQRCode);
        $sections = $section->findByEvent($event->getId());
        $rendered = $this->render(
            'snappy/accreditation_new.html.twig',
            array(
                'sections' => $sections,
                'event' => $event,
                'participation' => $participation
            )
        );
        if ($mode == 'html') {
            return $rendered;
        }

        $file_name = 'accreditation_test.pdf';
        if (file_exists($file_name)) {
            @unlink($file_name);
        }
        $file_path = $this->getParameter('kernel.project_dir') . '/public/' . $file_name;

        //     dump($event->getType()->getShortName());
        $snappy->generateFromHtml($rendered, $file_path);
        return new BinaryFileResponse($file_path);
    }

    /**
     * @Route("/{id}/add-organization", name="add_organization", methods="GET|POST")
     */
    public function addOrganization(Request $request, Event $event, ParticipationHelper $helper): Response
    {
        $form = $this->createForm(AddOrganizationType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $organizations = $form->get('organizations')->getData();
            $break = false;

            foreach ($organizations as $organization) {
                $ok = $helper->generateParticipation($event, $organization, $form);

                if (!$ok) {
                    $break = true;
                }
            }
            $em->flush();

            if (!$break) {
                return $this->redirectToRoute('event_show', array('id' => $event->getId()));
            }
        }

        return $this->render('event/addOrganization.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-manager/{id}", name="event_change_manager")
     */
    public function changeManager(Request $request, Event $event): Response
    {
        $form = $this->createForm(ChangeManagerType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Le responsable a été changé');

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        return $this->render('event/changeManager.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/see-recall/{id}", name="event_see_recall")
     */
    public function seeRecall(Event $event, GlobalEmHelper $helper): Response
    {
        $em = $this->getDoctrine()->getManager();

        $not_logged = $em->getRepository(Participation::class)->getNotLogged($event);
        $missing_datas = $em->getRepository(Participation::class)->getMissingDatas($event);

        return $this->render('event/seeRecall.html.twig', [
            'event' => $event,
            'not_logged' => $not_logged,
            'missing_datas' => $missing_datas,
        ]);
    }

    public function handleCheckboxes($event, $request)
    {
        $firstRecall = $request->get('firstRecall');
        if ($firstRecall == 'on') {
            $event->setFirstRecallDate(null);
        }

        $secondRecall = $request->get('secondRecall');
        if ($secondRecall == 'on') {
            $event->setSecondRecallDate(null);
        }

        $break = $request->get('break');
        if (!$break && $event instanceof EventSimple) {
            $event->setStartBreak(null);
            $event->setEndBreak(null);
        }
    }

    /**
     * @Route("/candidates/{id}", name="event_candidates", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function showCandidate(Event $event, CandidateRepository $er, Request $request, PaginatorInterface $paginator): Response

    {
        //old candidates
        $search = false;
        $form = $this->createForm(TextFieldType::class, null, array('placeholder' => 'Recherche par email/nom/prénom'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = true;
            $query = $er->findByEventAndInputQuery($event, $form->get('text')->getData());
        } else {
            $query = $er->findByEventQuery($event);
        }

        $paginator = $this->get('knp_paginator');
        $candidates = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        $new_candidates = count($this->getDoctrine()->getManager()->getRepository(CandidateUser::class)->findByEvent($event));

        return $this->render('event/candidates_old.html.twig', [
            'event' => $event,
            'candidates' => $candidates,
            'form' => $form->createView(),
            'search' => $search,
            'new_candidates' => $new_candidates
        ]);
    }

    /**
     * @Route("/delete-candidate-participation/{id}", name="admin_candidate_participation_delete", methods="GET")
     */
    public function deleteCandidateParticipation(CandidateParticipation $participation, H48Helper $h48_helper): Response
    {
        $em = $this->getDoctrine()->getManager();
        $event = $participation->getEvent();

        if ($h48_helper->is48($event)) {
            $second_participation = $h48_helper->getSecondParticipation($participation);

            if ($second_participation) {
                $em->remove($second_participation);
            }
        }

        $em->remove($participation);
        $em->flush();

        $this->addFlash('success', 'La participation a été annulée');

        return $this->redirectToRoute('candidates_list', ['id' => $participation->getEvent()->getId()]);
    }


    /**
     * @Route("/delete-candidate/{id}", name="admin_candidate_user_delete", methods="DELETE")
     */
    public function deleteCandidate(Request $request, CandidateUser $candidate): Response
    {

        if ($this->isCsrfTokenValid('delete' . $this->getUser()->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($candidate);
            $em->flush();

            return $this->redirectToRoute('admin_index');
        } else {
            throw new \Exception('CsrfToken invalide');
        }
    }

    /**
     * @Route("/candidates/print/{id}", name="event_print_candidates", methods="GET", requirements={"id" : "\d+"})
     */
    public function printCandidates(Event $event, CandidateUserRepository $er, Request $request): Response
    {

        //$candidates = $er->findByEvent($event);

        $confirmed = $er->findByEventAndStatus($event, 'confirmed', [], ['name']);
        $refused = $er->findByEventAndStatus($event, 'refused', [], ['name']);

        $candidates = ['Confirmés' => $confirmed, 'Refusés' => $refused];

        return $this->render('event/print_candidates.html.twig', ['event' => $event, 'candidates' => $candidates]);
    }

    public function setSlug(Event $event, H48Helper $helper, GlobalHelper $global_helper)
    {
        $formation = '';
        // FIXME / OLD
        // this specific code only prevents duplicate slugs in events, it has no other purpose because only one "super-event" appears in the events carousel
        // (and this is far too ugly, we should (must ?= find a generic way to "bind" X events together so that it makes a "super event")
        /*if ($helper->is48($event)) {
        $formation = '-formation';
      }*/

        $repo = $this->getDoctrine()->getManager()->getRepository(Event::class);
        $i = 0;
        do {
            $slug = $global_helper->generateSlug(/*$event->getType()->getShortName().'-'.*/ $event->getPlace()->getCity() . $formation . ($i > 0 ? "-$i" : '') . '-' . $event->getDate()->format('Y'));
            ++$i;
        } while (($e = $repo->findOneBySlug($slug)) && $e->getId() != $event->getId());

        $event->setSlug($slug);
    }

    /**
     * @Route("/voir-recruteurs/{id}", name="admin_candidate_seen_organization", methods="GET")
     */
    public function seenOrganization(CandidateParticipation $participation): Response
    {
        return $this->render('candidate_user/seen_organizations.html.twig', [
            'participation' => $participation
        ]);
    }

    /**
     * @Route("/exportCandidat/{id}", name="admin_export_exposant_stand", methods="GET")
     */
    public function exportExposantsStand(Event $event, ParticipationRepository $pr): Response
    {
        $participations = $pr->findAllEventsParticipations($event->getId());
        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/export_' . $event->getSlug() . uniqid() . '.csv';
        $file = fopen($file_name, "w+b");
        foreach ($participations as $participation) {
            fputcsv($file, [strtoupper($participation->getCompanyName()), $participation->getStandNumber()]);
        }
        fclose($file);
        return $this->file($file_name);
    }


    /**
     * @Route("/cancel_event/{id}", name="cancel_event", methods="GET")
     */
    public function cancelEvent(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $event->setis_cancel(1);
        $em->persist($event);
        $em->flush();
        $this->addFlash('success', 'L\'événement est annulé');
        return $this->redirectToRoute('event_show', array('id' => $event->getId()));
    }

    /**
     * @Route("/restart_event/{id}", name="restart_event", methods="GET")
     */
    public function restartEvent(Event $event)
    {
        $em = $this->getDoctrine()->getManager();
        $event->setis_cancel(0);
        $em->persist($event);
        $em->flush();
        $this->addFlash('success', 'L\'événement est relancé');
        return $this->redirectToRoute('event_show', array('id' => $event->getId()));
    }
}
