<?php

namespace App\Controller;

use App\Entity\Agenda;
use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\RhUser;
use App\Entity\Section;
use App\Entity\Accreditation;
use App\Form\AgendaType;
use App\Form\SearchCandidateType;
use App\Form\TextFieldType;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use App\Repository\AccreditationRepository;
use App\Repository\CandidateParticipationRepository;
use App\Repository\CandidateUserRepository;
use App\Repository\JoblinkSessionRepository;
use App\Repository\OrganizationRepository;
use App\Repository\OriginRepository;
use App\Repository\SlotsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Helper\ImageHelper;

/**
 * @Route("/admin")
 */
class AccreditationController extends AbstractController
{

    /**
     * @Route("/accreditations/{id}", name="accreditations_list", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function showCandidateUser(Event $event, AccreditationRepository $er, Request $request, RenderHelper $helper, OrganizationRepository $or): Response
    {
        $selectedStructure = null;
        if($request->query->get('filterStructure') != NULL && $request->query->get('filterStructure') != "null"){
            $selectedStructure = $er->findOneByParticipation($request->query->get('filterStructure'));
            $accreditations = $er->findByEvent($event);
            $accreditationsList = $er->findDistinctByEvent($event);
            $filteredAccreditations = $er->findByEventAndParticipation($event, $request->query->get('filterStructure'));
            $helper->generateAccreditation($accreditations[0]);

        }else{
            $accreditations = $er->findByEvent($event);
            $accreditationsList = $er->findDistinctByEvent($event);
            $filteredAccreditations = $er->findByEvent($event);
        }
        return $this->render('accreditations/participations.html.twig', [
            'event' => $event,
            'filteredAccreditations' => $filteredAccreditations,
            'accreditations' => $accreditations,
            'accreditationsList' => $accreditationsList,
            'selectedStructure' => $selectedStructure
        ]);
    }

    /**
     * @Route("/accreditations/{id}/regeneration", name="regeneration_accred", methods="GET|POST", requirements={"id" : "\d+"})
     */
    public function regenAccred(Accreditation $accred, AccreditationRepository $er, Request $request, RenderHelper $helper): Response
    {

        $helper->generateAccreditation($accred);
        $selectedStructure = null;
        if($request->query->get('filterStructure') != NULL && $request->query->get('filterStructure') != "null"){
            $selectedStructure = $er->findOneByParticipation($request->query->get('filterStructure'));
            $accreditations = $er->findByEvent($accred->getEvent());
            $accreditationsList = $er->findDistinctByEvent($accred->getEvent());
            $filteredAccreditations = $er->findByEventAndParticipation($accred->getEvent(), $request->query->get('filterStructure'));
            $helper->generateAccreditation($accreditations[0]);

        }else{
            $accreditations = $er->findByEvent($accred->getEvent());
            $accreditationsList = $er->findDistinctByEvent($accred->getEvent());
            $filteredAccreditations = $er->findByEvent($accred->getEvent());
        }
        return $this->render('accreditations/participations.html.twig', [
            'event' => $accred->getEvent(),
            'filteredAccreditations' => $filteredAccreditations,
            'accreditations' => $accreditations,
            'accreditationsList' => $accreditationsList,
            'selectedStructure' => $selectedStructure
        ]);
    }

    /**
     * @Route("/accreditations/resend-accreditation/{id}", name="candidate_user_resend_accreditation", methods="GET", requirements={"id" : "\d+"})
     */
    public function resendAccreditation(Accreditation $accreditation, TwigHelper $helper, RenderHelper $render_helper, MailerHelper $mailer_helper, Request $request): Response
    {
        $accreditation_path = $accreditation->getAccreditationPath();

        if (!$helper->fileExists($accreditation_path) || $request->get('regenerate') == 1) {
            $render_helper->generateAccreditation($accreditation);
        }
        $accreditation_path = $accreditation->getAccreditationPath();

        if ($helper->fileExists($accreditation_path)) {
            $mailer_helper->sendAccreditation($accreditation);
        }
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('info', 'L\'invitation a été renvoyé à ' . $accreditation->getEmail());

        return $this->redirect($request->headers->get('referer'));
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
     * @Route("/export-phone-accred/{id}", name="export-phone-accred")
     */
    public function exportPhoneAcred(Event $event, AccreditationRepository $ar): Response
    {
        ob_end_clean();
        $accreditations = $ar->findByEvent($event);


        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/accréditations_phone-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($accreditations as $accreditation) {
            $array = [$accreditation->getPhone() ];
            fputcsv($file, $this->convertToISOCharset($array));
        }

        fclose($file);

        return $this->file($file_name);
    }

    /**
     * @Route("/export-accred/{id}", name="export-accred")
     */
    public function exportAccred(Event $event, AccreditationRepository $ar): Response
    {
        ob_end_clean();
        $accreditations = $ar->findByEvent($event);


        $file_name = $this->getParameter('kernel.project_dir') . '/public/export/accréditations-' . $event->getSlug() . '.csv';
        $file = fopen($file_name, "w");

        foreach ($accreditations as $accreditation) {
            $array = [$accreditation->getLastName(), $accreditation->getFirstName(), $accreditation->getEmail(), $accreditation->getPhone(), $accreditation->getParticipation()->getCompanyName()  ];
            fputcsv($file, $this->convertToISOCharset($array));
        }

        fclose($file);

        return $this->file($file_name);
    }

}
