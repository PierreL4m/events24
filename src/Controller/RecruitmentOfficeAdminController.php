<?php

namespace App\Controller;

use App\Controller\UserController;
use App\Entity\Event;
use App\Entity\RecruitmentOffice;
use App\Entity\RhUser;
use App\Form\AddRoType;
use App\Form\RecruitmentOfficeType;
use App\Helper\GlobalEmHelper;
use App\Helper\MailerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RhUserType;
use App\Form\UserType;

/**
 * @Route("/admin/recruitment-office")
 */
class RecruitmentOfficeAdminController extends AbstractController
{
	private $em_helper;
	private $mailer_helper;

	public function __construct(GlobalEmHelper $em_helper, MailerHelper $mailer_helper)
	{
		//parent::__construct();
		$this->em_helper = $em_helper;
		$this->mailer_helper = $mailer_helper;
	}
    /**
     * @Route("/show/{id}", name="recruitment_office_show")
     */
    public function show(RecruitmentOffice $ro)
    {
        return $this->render('recruitment_office_admin/show.html.twig', [
            'ro' => $ro,
        ]);
    }

    /**
     * @Route("/add-ro/{id}", name="recruitment_office_admin_add_ro")
     */
    public function addOrCreateRo(Request $request, Event $event): Response
    {
        $em = $this->getDoctrine()->getManager();
        // for pre-existing Recruitment Office
        $form = $this->createForm(AddRoType::class, $event);
        $form->handleRequest($request);
           
        if ($form->isSubmitted() && $form->isValid()) {
           
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Le(s) cabinet(s) de recrutement a(ont) été ajouté(s)');            

            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }

        $ro = new RecruitmentOffice();
        $rh = new RhUser();
        $rh->setRoles(['ROLE_RH']);
        $ro->addRh($rh);
        
        $form_new = $this->createForm(RecruitmentOfficeType::class, $ro);
        $form_new->handleRequest($request);

        if ($form_new->isSubmitted() && $form_new->isValid()) {
           	$this->addRhUserToRo($rh,$ro,$event);
            return $this->redirectToRoute('event_show', array('id' => $event->getId()));
        }
        
        return $this->render('recruitment_office_admin/add_ro.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'form_new' => $form_new->createView()
        ]);
    }

    public function addRhUserToRo(RhUser $rh, RecruitmentOffice $ro, Event $event = null)
    {
        $password = UserController::setPassword($rh);
        $this->em_helper->generateUsername($rh);
        if($event) {
            $ro->addEvent($event);
        }        
        $em = $this->getDoctrine()->getManager();
        $rh->setRoles(['ROLE_RH']);
        $em->persist($rh);
        
        $this->mailer_helper->sendNewPassword($rh,$password);
        $em->persist($ro);
        $em->flush();
        $this->addFlash('success', 'Le cabinet de recrutement a été créé et ajouté à l\'événement. L\'utilisateur '.$rh.' a reçu ses identifiants par email.');            
    }

    /**
     * @Route("/add-rh/{id}", name="recruitment_office_admin_add_rh")
     */
    public function addRhRo(Request $request, RecruitmentOffice $ro): Response
    {
        $em = $this->getDoctrine()->getManager();
        $rh = new RhUser();
        $rh->setRecruitmentOffice($ro);
       
        $form = $this->createForm(UserType::class, $rh);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {           
            $this->addRhUserToRo($rh,$ro);
            return $this->redirectToRoute('recruitment_office_show', array('id' => $ro->getId()));
        }
        
        return $this->render('recruitment_office_admin/add_rh.html.twig', [
            'ro' => $ro,
            'form' => $form->createView()
        ]);
    }
}
