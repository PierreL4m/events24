<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\ParticipationCompanySimple;
use App\Entity\ParticipationDefault;
use App\Entity\ParticipationFormationSimple;
use App\Helper\GlobalHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationRepository;
use App\Repository\CandidateRepository;
use App\Repository\CandidateUserRepository;
use App\Repository\EventRepository;
use App\Repository\ExposantUserRepository;
use App\Repository\JobRepository;
use App\Repository\OrganizationRepository;
use App\Repository\ParticipationRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamedResponse;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamer\ZipStreamer;
use function PHPUnit\Framework\isEmpty;
use PhpOffice\PhpWord\Shared\ZipArchive;

/**
 * @Route("/admin/export")
 */
class ExportController extends AbstractController
{
    /**
     * @Route("/", name="export_index")
     */
    public function index(EventRepository $er, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $er->findAllQuery();

        $events = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );
        return $this->render('export/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/event/{id}", name="export_event", requirements={"id"="\d+"})
     */
    public function export(Event $event, ParticipationRepository $er, JobRepository $jobRepo): Response
    {
        $participations = $er->findByEvent($event);
        $jobs = $jobRepo->getByEvent($event);
        return $this->render('export/export.html.twig', [
            'participations' => $participations,
            'jobs' => $jobs
        ]);

    }

    /**
     * @Route("/event/long/{id}", name="export_event_long", requirements={"id"="\d+"})
     */
    public function exportLong(Event $event, ParticipationRepository $er, JobRepository $jobRepo): Response
    {
        $participations = $er->findByEvent($event);
        $jobs = $jobRepo->getByEvent($event);
        return $this->render('export/exportLong.html.twig', [
            'participations' => $participations,
            'jobs' => $jobs
        ]);
    }

    /**
     * @Route("/hd/{id}", name="export_hd", requirements={"id"="\d+"})
     */
    public function exportHd(Event $event, GlobalHelper $helper): Response
    {
        ob_end_clean();
        $zipStreamer = new ZipStreamer("logo_hd_" . $event->getSlug() . '.zip');

        foreach ($event->getParticipations() as $participation) {
            $logo = $participation->getLogo();
            if ($logo && $logo->getPath() && file_exists('uploads/' . $logo->getPath())) {
                $zipStreamer->add(
                    'uploads/'.$logo->getPath(),
                    str_replace('/', '-', $participation->getCompanyName()).'.jpg'
                );
            }
        }
        return new ZipStreamedResponse($zipStreamer);
    }

    /**
     * @Route("/visuels/{id}", name="download_visuels", requirements={"id"="\d+"})
     */
    public function downloadVisuel(GlobalHelper $helper, event $event): Response
    {
        ob_end_clean();
        $zipStreamer = new ZipStreamer("logo_hd_" . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $event) . '.rar');

        foreach ($event->getParticipations() as $participation) {
                $zipStreamer->add(
                    'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getFacebookVisuel()),
                    str_replace('/', '-', $participation->getCompanyName()) . '/Twitter.jpg'
                );
                $zipStreamer->add(
                    'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getTwitterVisuel()),
                    str_replace('/', '-', $participation->getCompanyName()) . '/Facebook.jpg'
                );
                $zipStreamer->add(
                    'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getLinkedinVisuel()),
                    str_replace('/', '-', $participation->getCompanyName()) . '/Linkedin.jpg'
                );
                $zipStreamer->add(
                    'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getSignatureVisuel()),
                    str_replace('/', '-', $participation->getCompanyName()) . '/Signature.jpg'
                );
                if (!empty($participation->getInstaVisuel())) {
                    $zipStreamer->add(
                        'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getInstaVisuel()),
                        str_replace('/', '-', $participation->getCompanyName()) . '/Instagram.jpg'
                    );
                }
        }
        return new ZipStreamedResponse($zipStreamer);
    }

    /**
     * @Route("/visuels_one/{id}", name="download_visuels_one", requirements={"id"="\d+"})
     */
    public function downloadVisuelOne(GlobalHelper $helper, participation $participation): Response
    {
        ob_end_clean();
        $zipStreamer = new ZipStreamer("logo_hd_" . str_replace('/', '-', $participation->getCompanyName()) . '.rar');

            $zipStreamer->add(
                'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getFacebookVisuel()),
                '/Twitter.jpg'
            );
            $zipStreamer->add(
                'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getTwitterVisuel()),
                '/Facebook.jpg'
            );
            $zipStreamer->add(
                'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getLinkedinVisuel()),
                '/Linkedin.jpg'
            );
            $zipStreamer->add(
                'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getSignatureVisuel()),
                '/Signature.jpg'
            );
            if (!empty($participation->getInstaVisuel())) {
                $zipStreamer->add(
                    'uploads/images/event/visuels_rs/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'], $participation->getEvent()) . '/' . str_replace(['ç', 'É', 'é', 'è', 'È','/', '&'], ['c','e','e','e','e','-','et'],$participation->getCompanyName()) . '/' . preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $participation->getInstaVisuel()),
                    '/Instagram.jpg'
                );
            }
        return new ZipStreamedResponse($zipStreamer);
    }

    /**
     * @Route("/gazette/{id}", name="export_gazette", requirements={"id"="\d+"})
     */
    public function exportGazette(Event $event, GlobalHelper $helper, TwigHelper $twig_helper, JobRepository $jr): Response
    {

        $em = $this->getDoctrine()->getManager();

        $file = $helper->generateSlug($event->getFullType().'-'.$event->getSlug()).'.docx';

        $phpWord = new PhpWord();
        /* Note: any element you append to a document must reside inside of a Section. */
        $section = $phpWord->addSection();
        $phpWord->addParagraphStyle('paragraph', array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));

        // Adding Text element with font customized using named font style...
        $fontStyleName = 'exoposantName';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('size' => 14, 'bold' => true)
        );

        $participations = $em->getRepository(Participation::class)->getOrderedByEvent($event->getId());
        //Adding Text element to the Section having font styled
        foreach ($participations as $p) {
            //exposant name
            $section->addText(htmlspecialchars($p->getCompanyName()),$fontStyleName);

            //image
//            if ($twig_helper->fileExists($p->getLogoSrc())){
//                $logo_absolute_path = $this->getParameter('public_dir').$p->getLogoSrc() ;
//                $size = getimagesize($logo_absolute_path);
//                $height = $size[1];
//
//
//                if ($height > 100) $height = 100;
//
//                $section->addImage($logo_absolute_path,array(
//                    'positioning'   => 'relative',
//                    'height' => $height,
//                    'wrappingStyle' => 'inline',
//                ));
//                if ($height < 30)
//                    $section->addTextBreak(1);
//                elseif($height < 50)
//                    $section->addTextBreak(2);
//                elseif($height < 70)
//                    $section->addTextBreak(3);
//                else
//                    $section->addTextBreak(4);
//            }
                $jobs = $jr->findByParticipation($p);
                if(empty($jobs)){
                    $section->addText('Présentation :','textstyle','paragraph');
                    $section->addText($this->convertToText($p->getPresentation()));
                }else{
                    $section->addText('Offres disponibles :','textstyle','paragraph');
                    $section->addText($this->convertToText(implode(",", $jobs)));
                }
        }
        $objWriter = IOFactory::createWriter($phpWord);
        $objWriter->save($file);

        $response= new Response;
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($file) . '";');
        $response->headers->set('Content-length', filesize($file));
        $response->setContent(file_get_contents($file));

        unlink($file);

        return $response;
    }

    public function convertToText($html)
    {
        $html = strip_tags($html);
        $html = str_replace(array("\n", "\r"), '', $html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');

        return (htmlspecialchars($html));
    }


    /**
     * @Route("/export-mailing-list", name="export_mailing_list")
     */
    public function indexMailing(EventRepository $er, Request $request): Response
    {
        $events = $er->findCurrentEvents();

        return $this->render('export/mailing.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/export-mailing/{id}", name="export_mailing")
     */
    public function exportMailing(Event $event, CandidateRepository $er): Response
    {
        $candidates = $er->findForMailingNormandie($event);


        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/candidats-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($candidates as $candidate) {
            $array = [$candidate['firstname'] . " " . $candidate['lastname'], $candidate['email']];
            fputcsv($file, $this->convertToISOCharset($array));
        }

        fclose($file);

        return $this->file($file_name);
    }

    public function convertToISOCharset($array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = convertToISOCharset($value);
            } else {
                $array[$key] = mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
            }
        }

        return $array;
    }


    /**
     * @Route("/export-sms/{id}", name="export_sms")
     */
    public function exportSMS(Event $event, CandidateRepository $er, CandidateUserRepository $cr): Response
    {
        $candidates = $er->findPhoneRecall($event); // old candidate entity
        $candidates_user = $cr->findPhoneRecall($event);


        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/rappel-sms-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($candidates as $candidate) {
            fputcsv($file, [$candidate['phone']]);
        }

        foreach ($candidates_user as $candidate) {
            fputcsv($file, [$candidate['phone']]);
        }

        fclose($file);

        return $this->file($file_name);
    }

    /**
     * @Route("/export-sms-urgence/{id}", name="export_sms_urgence")
     */
    public function exportSMSrugence(Event $event, CandidateParticipationRepository $er): Response
    {
        $candidates = $er->findConfirmed($event);


        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/rappel-sms-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($candidates as $candidate) {
            fputcsv($file, [$candidate['phone']]);
        }

        fclose($file);

        return $this->file($file_name);
    }

    /**
     * @Route("/export-username/{id}", name="export_export_username", methods="GET")
     */
    public function exportUsername(Event $event, OrganizationRepository $or, GlobalHelper $global_helper): Response
    {

        $organizations = $or->findByEvent($event);

        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/identifiants-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($organizations as $organization) {
            $user = $organization->getExposantScanUser();
            $array = [$global_helper->escapeFrenchChar($organization->getName()), $user->getUsername(), $user->getSavedPlainPassword()];
            fputcsv($file, $this->convertToISOCharset($array));
        }

        fclose($file);

        return $this->file($file_name);
    }

    /**
     * @Route("/export-mailing-exposantuser", name="export_mailing_exposantuser")
     */
    public function exportMailingClient(ExposantUserRepository $er): Response
    {
        $clients = $er->findForMailingClient();

        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/client-' . date('Ymd') . '.csv';
        $file = fopen($file_name, "w");

        foreach ($clients as $client) {
            $array = [$client['firstname'], $client['lastname'], $client['email']];
            fputcsv($file, $this->convertToISOCharset($array));
            $array_bis = [$client['name'], $client['bis_email']];
            fputcsv($file, $this->convertToISOCharset($array_bis));
        }

        fclose($file);

        return $this->file($file_name);
    }
}
