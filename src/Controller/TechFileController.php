<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Form\ParticipationsNoTechsType;
use App\Helper\GlobalHelper;
use App\Helper\MailerHelper;
use App\Helper\TwigHelper;
use App\Repository\OrganizationRepository;
use App\Repository\ParticipationRepository;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamedResponse;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamer\ZipStreamer;

/**
 * @Route("/admin/tech-file")
 */
class TechFileController extends AbstractController
{
    /**
     * @Route("/{id}", name="tech_file_index", requirements={"id" = "\d+"})
     */
    public function index(Event $event): Response
    {
        return $this->render('tech_file/index.html.twig', [
            'event' => $event,
        ]);
    }


    /**
     * @Route("/ack", name="tech_file_ack", methods="POST")
     */
    public function ack(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $date = $request->request->get('date');
            $event = $em->getRepository(Event::class)->find($request->request->get('id'));
            $datetime = \DateTime::createFromFormat('d/m/Y', $date);
            $event->setAckDate($datetime);
            $em->flush();

            foreach ($event->getParticipations() as $p) {
                if ($p->getAckPath()) {
                    $this->createAck($p, true, true);
                }
            }
            $em->flush();

            return new JsonResponse(array('date' => $date), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/send-network-pub/{id}_{participation}", name="network_pub", methods="GET")
     */
    public function sendNetworkPub($participation, Request $request, MailerHelper $mailer_helper, Event $event): Response
    {

        $id = $event->getId();
        $participation = explode(',', $participation);
        foreach ($participation as $p) {
            $this->sentFill($p, $mailer_helper);
        }
        return $this->redirectToRoute('tech_file_index', array('id' => $id));
    }

    public function sentFill($id, $mailer_helper)
    {
        $em = $this->getDoctrine()->getManager();
        $participation = $em->getRepository(Participation::class)->find($id);
        $user = $participation->getResponsable();

        if (!$user) {
            $this->addFlash('notice', 'Pas de responsable pour ' . $participation);
            return;
        }
        $mailer_helper->sendNetworkFiles($user, $participation);
    }

    /**
     * @Route("/download-ack/{id}", name="ack_download", methods="GET", requirements={"id"="\d+"})
     */
    public function downloadAck(Participation $participation): Response
    {
        if (!$participation->getAckPath()) {
            $this->createAck($participation);
        }

        $path = $this->get('kernel')->getProjectDir() . '/public/uploads/ack/' . $participation->getAckPath();

        return $this->file($path);
    }

    //this is a duplicate of MailerController
    public function createAck(Participation $participation, $loop = null, $force = null)
    {
        if (TwigHelper::canCreateAck($participation)) {

            if (!$participation->getAckPath() || $force) {
                $helper = new GlobalHelper();
                $file_name = 'accuse_de_reception_' . $helper->generateSlug($participation->getCompanyName()) . uniqid() . '.pdf';

                $file_path = $this->get('kernel')->getProjectDir() . '/public/uploads/ack/' . $file_name;


                $this->get('knp_snappy.pdf')->generateFromHtml(
                    $this->renderView(
                        'tech_file/ack.html.twig',
                        array(
                            'participation' => $participation
                        )
                    ),
                    $file_path
                );

                $participation->setAckPath($file_name);

                if (!$loop) {
                    $this->getDoctrine()->getManager()->flush();
                }
            }
        } else {
            $this->addFlash('danger', 'L\'accusé de récéption n\'a pas pu être créé. Merci de vérifier que la participation a un numéro de stand ainsi qu\'une superficie de stand et que la date de retour de l\'accusé de récéption est définie');
        }
    }

    /**
     * @Route("/nosend-tech-files/{id}", name="nosend_tech_files", methods="GET|POST", requirements={"id"="\d+"})
     */
    public function noSendTechFiles(Event $event, Request $request): Response
    {
        $form = $this->createForm(ParticipationsNoTechsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Les exposants sélectionnés ont été retirés de l\'envoi');

            return $this->redirectToRoute('tech_file_index', array('id' => $event->getId()));
        }

        return $this->render('tech_file/noTechGuide.html.twig', array('form' => $form->createView(), 'event' => $event));
    }

    /**
     * @Route("/generate-badge", name="generate_badge", methods="POST")
     */
    public function generateBadge(Request $request, ParticipationRepository $pr): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $em = $this->getDoctrine()->getManager();
            $participation = $pr->find($id);

            if (!$participation) {
                throw new NotFoundHttpException('Participation not found. id=' . $id);
            }

            $this->generateSingleBadge($participation);
            $em->flush();

            return new JsonResponse(array('id' => $id, 'src' => $participation->getBadgeSrc()), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function generateSingleBadge(Participation $participation)
    {
        if ($participation->getEvent()->getbackBadgeName()) {
            $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdt = 130;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logo = GlobalHelper::resize($public_dir.'/uploads/'.$participation->getLogo()->getPath(), $wdt, $wdt);
            } else {
                $logo = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdt, $wdt);
            }
            $badge = $public_dir.'uploads/background_badge/'.$participation->getEvent()->getbackBadgeName();
            $imagine = new Imagine();
            $image = $imagine->open($badge);
            if ($logo != null) {
                $standNb = $participation->getStandNumber();
                $image->paste($logo, new Point(50, 59));
                // Afficher le numéro de stand sur le badge
                /* $CenteredTextPosition = new \Imagine\Image\Point( 300, 5);
                $Palette = new \Imagine\Image\Palette\RGB();
                $TextColor = $Palette->color('ffffff', 100);
                $TextFont = new \Imagine\Gd\Font( 'eurostib-webfont.eot' , 15,  $TextColor);
                $image->draw()->text('Stand '.$standNb, $TextFont, $CenteredTextPosition); */
            } else {
                $image->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }
            $name = uniqid() . '.jpg';
            $path = $public_dir . 'badges/' . $name;
            $options = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 500,
                'resolution-y' => 500,
            );
            $image->save($path, $options);
            $participation->setBadge($name);
        }
        //to do visuel per events (Facebook)
        if ($participation->getEvent()->getBackSignatureName()) {
            $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdtSignature = 100;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logoSignature = GlobalHelper::resize($public_dir.'uploads/'.$participation->getLogo()->getPath(), $wdtSignature, $wdtSignature);
            } else {
                $logoSignature = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdtSignature, $wdtSignature);
            }
            $visuSignature = $public_dir . 'uploads/backSignature/' . $participation->getEvent()->getBackSignatureName();
            $imagine = new Imagine();
            $imageSignature = $imagine->open($visuSignature);
            if ($logoSignature != null) {
                $imageSignature->paste($logoSignature, new Point(180, 45));
            } else {
                $imageSignature->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }

            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()), 0777);
            }
            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()), 0777);
            }
            $nameSignature = preg_replace('/[\x00-\x1F\x80-\xFF]/', '','signature_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '.jpg');
            $pathRs = $public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . $nameSignature;
            $optionsSignature = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 300,
                'resolution-y' => 300,
            );
            $imageSignature->save($pathRs, $optionsSignature);
            $participation->setSignatureVisuel($nameSignature);
        }

        if ($participation->getEvent()->getBackFacebookName()) {
          $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdtFacebook = 150;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logoFacebook = GlobalHelper::resize($public_dir.'uploads/'.$participation->getLogo()->getPath(), $wdtFacebook, $wdtFacebook);
            } else {
                $logoFacebook = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdtFacebook, $wdtFacebook);
            }
            $visuFacebook = $public_dir . 'uploads/backFacebook/' . $participation->getEvent()->getBackFacebookName();
            $imagine = new Imagine();
            $imagefacebook = $imagine->open($visuFacebook);
            if ($logoFacebook != null) {
                $imagefacebook->paste($logoFacebook, new Point(355, 95));
            } else {
                $imagefacebook->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }

            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()), 0777);
            }
            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()), 0777);
            }
            $nameFacebook = preg_replace('/[\x00-\x1F\x80-\xFF]/', '','facebook_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '.jpg');
            $pathRs = $public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . $nameFacebook;
            $optionsFacebook = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 300,
                'resolution-y' => 300,
            );
            $imagefacebook->save($pathRs, $optionsFacebook);
            $participation->setFacebookVisuel($nameFacebook);
        }

        //to do visuel per events (Insta)
        if ($participation->getEvent()->getBackInstaName()) {
          $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdtInsta = 300;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logoInsta = GlobalHelper::resize($public_dir.'uploads/'.$participation->getLogo()->getPath(), $wdtInsta, $wdtInsta);
            } else {
                $logoInsta = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdtInsta, $wdtInsta);
            }
            $visuInsta = $public_dir.$participation->getEvent()->getBackInsta()->getPath();
            $imagine = new Imagine();
            $imageInsta = $imagine->open($visuInsta);
            if ($logoInsta != null) {
                $imageInsta->paste($logoInsta, new Point(615, 240));
            } else {
                $imageInsta->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }

            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()), 0777);
            }
            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()), 0777);
            }
            $nameInsta = preg_replace('/[\x00-\x1F\x80-\xFF]/', '','instagram_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '.jpg');
            $pathRs = $public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . $nameInsta;
            $optionsInsta = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 300,
                'resolution-y' => 300,
            );
            $imageInsta->save($pathRs, $optionsInsta);
            $participation->setInstaVisuel($nameInsta);
        }

        //to do visuel per events (Twitter)
        if ($participation->getEvent()->getBackTwitterName()) {
          $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdtTwitter = 170;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logoTwitter = GlobalHelper::resize($public_dir.'uploads/'.$participation->getLogo()->getPath(), $wdtTwitter, $wdtTwitter);
            } else {
                $logoTwitter = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdtTwitter, $wdtTwitter);
            }
            $visuTwitter = $public_dir . 'uploads/backTwitter/' . $participation->getEvent()->getBackTwitterName();
            $imagine = new Imagine();
            $imageTwitter = $imagine->open($visuTwitter);
            if ($logoTwitter != null) {
                $imageTwitter->paste($logoTwitter, new Point(503, 201));
            } else {
                $imageTwitter->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }

            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()), 0777);
            }
            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()), 0777);
            }
            $nameTwitter = preg_replace('/[\x00-\x1F\x80-\xFF]/', '','twitter_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '.jpg');
            $pathRs = $public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . $nameTwitter;
            $optionsTwitter = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 300,
                'resolution-y' => 300,
            );
            $imageTwitter->save($pathRs, $optionsTwitter);
            $participation->setTwitterVisuel($nameTwitter);
        }

        //to do visuel per events (Linkedin)
        if ($participation->getEvent()->getBackLinkedinName()) {
          $public_dir = $this->getParameter('kernel.project_dir') . '/public/';
            $wdtLinkedin = 180;
            if (file_exists($public_dir.'uploads/'.$participation->getLogo()->getPath())) {
                $logoLinkedin = GlobalHelper::resize($public_dir.'uploads/'.$participation->getLogo()->getPath(), $wdtLinkedin, $wdtLinkedin);
            } else {
                $logoLinkedin = GlobalHelper::resize('/home/samba/admin_events/public_html/master/admin_events/vendor/fbenoit/image-bundle/Fbenoit/ImageBundle/Entity/../../../../../../public/uploads/logo/noLogo.jpg', $wdtLinkedin, $wdtLinkedin);
            }
            $visuLinkedin = $public_dir . 'uploads/backLinkedin/' . $participation->getEvent()->getBackLinkedinName();
            $imagine = new Imagine();
            $imageLinkedin = $imagine->open($visuLinkedin);
            if ($logoLinkedin != null) {
                $imageLinkedin->paste($logoLinkedin, new Point(143, 166));
            } else {
                $imageLinkedin->paste('public\uploads\logo\03.jpg', new Point(50, 59));
            }

            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()), 0777);
            }
            if (!is_dir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()))) {
                mkdir($public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()), 0777);
            }
            $nameLinkedin = preg_replace('/[\x00-\x1F\x80-\xFF]/', '','linkedin_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '_' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '.jpg');
            $pathRs = $public_dir . 'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . $nameLinkedin;
            $optionsLinkedin = array(
                'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
                'resolution-x' => 300,
                'resolution-y' => 300,
            );
            $imageLinkedin->save($pathRs, $optionsLinkedin);
            $participation->setLinkedinVisuel($nameLinkedin);
        }
    }

    /**
     * @Route("/generate-badges/{id}", name="generate_badges", methods="GET", requirements={"id"="\d+"})
     */
    public function generateBadges(Event $event): Response
    {

        foreach ($event->getParticipations() as $p) {
            $this->generateSingleBadge($p);
        }
        $event->setrs_ready(1);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('tech_file_index', array('id' => $event->getId(), 'anchor' => 'badges'));
    }

    /**
     * @Route("/print-badges/{id}", name="print_badges", methods="GET", requirements={"id"="\d+"})
     */
    public function printBadges(Event $event, TwigHelper $helper): Response
    {
        //pattern size
        $width = 407;
        $height = 250;
        $blank_height = 25.4;

        $imagine = new Imagine();
        $pattern_width = 3 * $width;
        $row = $helper->getNbRow($event) + 2;
        $page = $helper->getNbPages($event);
        $pattern_height = ($row) * $height + $blank_height * ($page);
        if($pattern_height > 16000){
            $pattern = $imagine->create(new Box($pattern_width, 15999));
        }else{
            $pattern = $imagine->create(new Box($pattern_width, $pattern_height));
        }

        $x = 0;
        $y = 0;
        $nb_row = 1;
        //create blank img
        $size = new Box($pattern_width, $blank_height);
        $blank = $imagine->create($size);
        $public_dir = $this->getParameter('public_dir');

        $count = 0;
        foreach ($event->getParticipations() as $participation) {
            if (!$participation->getBadge()) {
                throw new \Exception('Cannot generate print badge file with no badge for participation id=' . $participation->getId());
            }

            //if ($count < 40){
            $badge = $imagine->open($public_dir . $participation->getBadgeSrc());
            if ($participation->getStandSize() >= 15) {
                $nb = 8;
            } elseif ($participation->getStandSize() <= 5) {
                $nb = 2;
            } else {
                $nb = 4;
            }

            for ($i = 0; $i < $nb; $i++) {
                $pattern->paste($badge, new Point($x, $y));
                $x = $x + $width;

                if ($x + $width > $pattern_width) {
                    $x = 0;
                    $y = $y + $height;

                    if ($nb_row % 7 == 0) {
                        $pattern->paste($blank, new Point(0, $y));
                        $y = $y + $blank_height;
                    }
                    $nb_row = $nb_row + 1;
                }
            }
            //}
            //$count++;
        }
        ob_end_clean();
        $zipStreamer = new ZipStreamer("badge_" . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $event) . '.zip');


        $name = time() . '.png';
        $path = $public_dir . '/badges/' . $name;

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 120,
            'resolution-y' => 120,
            'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        );
        $pattern->save($path, $options);

        $zipStreamer->add(
            $path,
            'badge.jpg'
        );
        return new ZipStreamedResponse($zipStreamer);
    }

    /**
     * @Route("/print-selected-badges/{event}_{participations}_{nbBadge}", name="print_selected_badges", methods="GET|POST", requirements={"id"="\d+"})
     */
    public function printSelectedBadges($participations, $nbBadge, Participation $participation, Event $event, TwigHelper $helper): Response
    {
        $em = $this->getDoctrine()->getManager();
        $participations = explode(',', $participations);
        $allParticipartions = array();
        foreach ($participations as $participations) {
            $participation = $em->getRepository(Participation::class)->getOneParticipation($participations);
            array_push($allParticipartions, $participation);
        }

        //pattern size
        $width = 411;
        $height = 250;
        $blank_height = 83;

        $imagine = new Imagine();
        $pattern_width = 3 * $width;
        $row = $helper->getNbRow($event) + 10;
        $page = $helper->getNbPages($event);
        $pattern_height = ($row) * $height + $blank_height * ($page);
        if($pattern_height > 16000){
            $pattern = $imagine->create(new Box($pattern_width, 15999));
        }else{
            $pattern = $imagine->create(new Box($pattern_width, $pattern_height));
        }
        $x = 0;
        $y = 0;
        $nb_row = 1;
        //create blank img
        $size = new Box($pattern_width, $blank_height);
        $blank = $imagine->create($size);
        $public_dir = $this->getParameter('public_dir');
        $count = 0;
        foreach ($allParticipartions as $participation) {
            if ($participation[0]->getStandSize() >= 15) {
                $nb = 8;
            } elseif ($participation[0]->getStandSize() <= 5) {
                $nb = 2;
            } else {
                $nb = 4;
            }
            if (!$participation[0]->getBadge()) {
                throw new \Exception('Cannot generate print badge file with no badge for participation id=' . $participation->getId());
            }

            //if ($count < 40){
            $badge = $imagine->open($public_dir . $participation[0]->getBadgeSrc());

            for ($i = 0; $i < $nb; $i++) {
                $pattern->paste($badge, new Point($x, $y));
                $x = $x + $width;

                if ($x + $width > $pattern_width) {
                    $x = 0;
                    $y = $y + $height;

                    if ($nb_row % 7 == 0) {
                        $pattern->paste($blank, new Point(0, $y));
                        $y = $y + $blank_height;
                    }
                    $nb_row = $nb_row + 1;
                }
            }
            //}
            //$count++;
        }
        ob_end_clean();
        $zipStreamer = new ZipStreamer("badge_" . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $event) . '.zip');
        $name = time() . '.jpg';
        $path = $public_dir . '/badges/' . $name;

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 120,
            'resolution-y' => 120,
            'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        );
        $pattern->save($path, $options);

        $zipStreamer->add(
            $path,
            'badge.jpg'
        );
        return new ZipStreamedResponse($zipStreamer);
    }

    /**
     * @Route("/print-one-badge/{id}_{nbBadges}", name="print_one_badge", methods="GET|POST", requirements={"id"="\d+"})
     */
    public function printOneBadge($nbBadges, Participation $participation, TwigHelper $helper): Response
    {
        ob_end_clean();
        //pattern size
        $width = 411;
        $height = 250;
        $blank_height = 83;

        $imagine = new Imagine();
        $pattern_width = 3 * $width;
        $row = 10;
        $page = 1;
        $pattern_height = ($row) * $height + $blank_height * ($page);
        $pattern = $imagine->create(new Box($pattern_width, $pattern_height));

        $x = 0;
        $y = 0;
        $nb_row = 1;
        //create blank img
        $size = new Box($pattern_width, $blank_height);
        $blank = $imagine->create($size);
        $public_dir = $this->getParameter('public_dir');

        $count = 0;

        if (!$participation->getBadge()) {
            throw new \Exception('Cannot generate print badge file with no badge for participation id=' . $participation->getId());
        }

        //if ($count < 40){
        $badge = $imagine->open($public_dir . $participation->getBadgeSrc());
        $nb = $nbBadges;

        for ($i = 0; $i < $nb; $i++) {
            $pattern->paste($badge, new Point($x, $y));
            $x = $x + $width;

            if ($x + $width > $pattern_width) {
                $x = 0;
                $y = $y + $height;

                if ($nb_row % 7 == 0) {
                    $pattern->paste($blank, new Point(0, $y));
                    $y = $y + $blank_height;
                }
                $nb_row = $nb_row + 1;
            }
        }
        //}
        //$count++;

        $name = time() . '.jpg';
        $path = $public_dir . '/badges/' . $name;

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 120,
            'resolution-y' => 120,
            'resampling-filter' => ImageInterface::FILTER_LANCZOS,
        );
        $pattern->save($path, $options);
        return $this->file($path);
    }
}
