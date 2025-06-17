<?php

namespace App\Controller;

use App\Controller\TechFileController;
use App\Entity\Email;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\Participation;
use App\Entity\Partner;
use App\Form\MailType;
use App\Form\MailTypeCandidats;
use App\Helper\GlobalHelper;
use App\Helper\MailerHelper;
use App\Helper\TwigHelper;
use App\Repository\EventRepository;
use Docx_reader\Docx_reader;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Mailer controller.
 *
 * @Route("/admin/email")
 */
class MailerController extends AbstractController
{
    /**
     * @Route("/send_candidats/{id}/{slug}", name="emails_send_candidats", methods="GET|POST")
     */
    public function sendMailCandidats(Request $request, Event $event, $slug, MailerHelper $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        //$check_email = $em->getRepository(Email::class)->findByEventAndSlug($event,$slug);

        $email_type = $em->getRepository(EmailType::class)->findOneBySlug($slug);

        if (!$email_type) {
            throw new \Exception('No email type found for slug ' . $slug);
        }

        // if($check_email){
        //     return $this->redirectToRoute('email_list', array('id' => $event->getId(), 'slug' => $slug));
        // }

        $email = new Email();
        $email->setEmailType($email_type);
        $email->setEvent($event);

        $form = $this->createForm(MailTypeCandidats::class, $email);
        $form->handleRequest($request);

        switch ($slug) {
            case 'partners':
            case 'organizations':
                $user = $this->getUser();
                break;

            default:
                $user = $this->getUser();
                break;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $email->setSent(new \DateTime());
            $em->persist($email);
            $em->flush();

            $subject = $email->getSubject();
            $template_params = array('body' => $email->getBody(), 'user' => $user);
            $from = array($user->getEmail() => $user);
            $template_name = 'raw';
            $attachment = $email->getAttachmentWebPath();
            $participations = $form->get('recipients')->getData();// participation array
            foreach ($participations as $p) {
                $nbSending++;
                $ccs = null;
                $to = $p->getCandidate()->getEmail();
                $mailer->sendMail($to, $subject, $template_name, $template_params, $from, $ccs, null, $attachment);
            }

            $this->addFlash('success', 'Les emails ont été envoyés');


            return $this->redirectToRoute('email_show', array('id' => $email->getId()));
        }
        $nbSending = 0;
        $participations = $form->get('recipients');
        foreach ($participations as $p) {
            $nbSending++;
        }
        return $this->render('mailer/new_candidate.html.twig', array(
            'form' => $form->createView(),
            'email' => $email,
            'user' => $user,
            'nbSending' => $nbSending
        ));
    }

    /**
     * @Route("/send/{id}/{slug}", name="emails_send", methods="GET|POST", requirements={"slug"="organizations|partners|m-1|online|bilan|d-3|Candidats"})
     */
    public function sendMail(Request $request, Event $event, $slug, MailerHelper $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        //$check_email = $em->getRepository(Email::class)->findByEventAndSlug($event,$slug);

        $email_type = $em->getRepository(EmailType::class)->findOneBySlug($slug);

        if (!$email_type) {
            throw new \Exception('No email type found for slug ' . $slug);
        }

        // if($check_email){
        //     return $this->redirectToRoute('email_list', array('id' => $event->getId(), 'slug' => $slug));
        // }

        $email = new Email();
        $email->setEmailType($email_type);
        $email->setEvent($event);

        $form = $this->createForm(MailType::class, $email);
        $form->handleRequest($request);

        switch ($slug) {
            case 'partners':
            case 'organizations':
                $user = $this->getUser();
                break;

            default:
                $user = $this->getUser();
                break;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $email->setSent(new \DateTime());
            $em->persist($email);
            $em->flush();

            $subject = $email->getSubject();
            $template_params = array('body' => $email->getBody(), 'user' => $user);
            $from = $user->getEmail();
            $template_name = 'raw';
            $attachment = $email->getAttachmentWebPath();
            $participations = $form->get('recipients')->getData();// participation array
            $mailer->sendMail($user->getEmail(), $subject, $template_name, $template_params, $from, null, null, $attachment);

            foreach ($participations as $p) {

                $ccs = null;

                if ($p instanceof Partner) {
                    $to = $p->getEmail();
                } else {
                    $responsable = $p->getResponsable();
                    $to = $responsable->getEmail();

                    if (method_exists($responsable, 'getResponsableBises') && count($responsable->getResponsableBises()) > 0) {
                        $ccs = array();
                        foreach ($responsable->getResponsableBises() as $bis) {
                            array_push($ccs, $bis->getEmail());
                        }
                    }

                }
                $mailer->sendMail($to, $subject, $template_name, $template_params, $from, $ccs, null, $attachment);
            }

            $extra_mails = $form->get('extra_mails')->getData();
            if (isset($extra_mails) && !empty($extra_mails)) {
                $extraMailArray = explode(";", $extra_mails[0]);
                foreach ($extraMailArray as $mail) {
                    $to = $mail;
                    $mailer->sendMail($to, $subject, $template_name, $template_params, $from, null, null, $attachment);
                }
            }

            $this->addFlash('success', 'Les emails ont été envoyés');


            return $this->redirectToRoute('email_show', array('id' => $email->getId()));
        }
        return $this->render('mailer/new.html.twig', array(
            'form' => $form->createView(),
            'email' => $email,
            'user' => $user
        ));
    }

    /**
     * @Route("/", name="arnaud_index", methods="GET")
     */
    public function index(EventRepository $er, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $er->findAllQuery();

        $events = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $email_types = $this->getDoctrine()->getManager()->getRepository(EmailType::class)->findAll();

        return $this->render('mailer/index.html.twig', ['events' => $events, 'email_types' => $email_types]);
    }

    /**
     * @Route("/show/{id}", name="email_show", methods="GET", requirements={"id":"\d+"})
     */
    public function show(Email $email): Response
    {

        return $this->render('mailer/show.html.twig', ['event' => $email->getEvent(), 'email' => $email]);
    }

    /**
     * @Route("/list/{id}/{slug}", name="email_list", methods="GET", requirements={"id":"\d+"})
     */
    public function showList(Event $event, $slug): Response
    {
        $em = $this->getDoctrine()->getManager();
        $emails = $em->getRepository(Email::class)->findByEventAndSlug($event, $slug);

        if (!$emails) {
            throw new \Exception('No mail found for event id ' . $id . ' and slug ' . $slug);
        }

        return $this->render('mailer/list.html.twig', ['event' => $event, 'emails' => $emails, 'slug' => $slug]);
    }


    /**
     * @Route("/attachment/{id}", name="attachment_show", methods="GET")
     */
    public function fileAction(Email $email)
    {
        $public_dir = $this->getParameter('kernel.project_dir') . '/public';
        $path = $public_dir . $email->getAttachmentWebPath();

        $formats = array('application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//to do handle ,'application/vnd.oasis.opendocument.text'
        if (in_array($email->getAttachmentType(), $formats)) {

            $doc = new Docx_reader();
            $doc->setFile($path);

            if (!$doc->get_errors()) {
                $html = $doc->to_html();
                $plain_text = $doc->to_plain_text();

                echo $html;
            } else {
                echo implode(', ', $doc->get_errors());
                die();
            }

        } else {
            $response = new BinaryFileResponse($path);
            $response->headers->set('Content-Type', $email->getAttachmentType());

            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE, //use ResponseHeaderBag::DISPOSITION_ATTACHMENT to save as an attachement
                $email->getAttachmentPath()
            );

            return $response;
        }
    }

    /**
     * @Route("/attachment-download/{id}", name="attachment_download", methods="GET")
     */
    public function downloadFileAction(Email $email)
    {
        $public_dir = $this->get('kernel')->getProjectDir() . '/public';
        $path = $public_dir . $email->getAttachmentWebPath();

        return $this->file($path);

    }

    /**
     * @Route("/send-files", name="send_files", methods="POST")
     */
    public function sendFiles(Request $request, MailerHelper $mailer_helper, Pdf $pdf): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('id');
            $email = $request->request->get('email');
            $type = $request->request->get('type');
            $participation = $em->getRepository(Participation::class)->find($id);

            if (!$participation) {
                return new JsonResponse('Participation not found. id=' . $id, 500);
            }

            switch ($type) {
                case 'Bat':
                    if (!$participation->getEvent()->getBatDate()) {
                        return new JsonResponse('Cannot send bat because no bat date defined. Participation id=' . $id, 500);
                    }
                    break;

                case 'Tech':
                    if (!$participation->getAckPath()) {
                        $this->createAck($participation, null, null, $pdf);
                    }
                    break;

                default:
                    return new JsonResponse('Cannot send mail with type=' . $type, 500);
            }

            $mailer_helper->sendFiles($participation, $type, $email);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(array('id' => $id, 'email' => $email), 200);
        } else {
            throw new NotFoundHttpException();
        }

    }

    /**
     * @Route("/send-all-files/{id}/{type}", name="send_all_files", methods="GET", requirements={"id"="\d+", "type"="Bat|Tech"})
     */
    public function sendAllFiles(Event $event, MailerHelper $mailer_helper, $type, TechFileController $tech_controller, Pdf $pdf): Response
    {
        foreach ($event->getParticipations() as $p) {
            switch ($type) {
                case 'Bat':
                    if (!$event->getBatDate()) {
                        throw new \Exception('Cannot send bat because no bat date defined. Event id=' . $event->getId());
                    }
                    if (!$p->isNoBat() && !$p->getBatSent()) {
                        $mailer_helper->sendFiles($p, $type);
                    }
                    $success = "Les BAT ";
                    $route = 'bat_index';

                    break;

                case 'Tech':
                    if (!$p->getAckPath()) {
                        $this->createAck($p, null, null, $pdf);
                    }
                    if (!$p->getTechGuideSent() && !$p->isNoTechGuide()) {
                        $mailer_helper->sendFiles($p, $type);
                    }
                    $success = "Les guides techniques ";
                    $route = 'tech_file_index';

                    break;

                default:
                    throw new \Exception('Cannot send mail with type=' . $type);
            }

        }
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $success . 'ont été envoyés');

        return $this->redirectToRoute($route, array('id' => $event->getId()));
    }

    //this is a duplicate of TechFileController
    public function createAck(Participation $participation, $loop = null, $force = null, Pdf $pdf)
    {

        if (TwigHelper::canCreateAck($participation)) {

            if (!$participation->getAckPath() || $force) {
                $helper = new GlobalHelper();
                $file_name = 'accuse_de_reception_' . $helper->generateSlug($participation->getCompanyName()) . uniqid() . '.pdf';

                $file_path = $this->getParameter('kernel.project_dir') . '/public/uploads/ack/' . $file_name;


                $pdf->generateFromHtml(
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
}
