<?php

namespace App\Controller;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Entity\ExposantScanUser;
use App\Entity\Participation;
use App\Entity\ParticipationJobs;
use App\Entity\User;
use App\Form\Api\CandidateParticipationCommentType;
use App\Form\SearchFieldType;
use App\Form\TextFieldType;
use App\Form\SearchCommentType;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\GlobalEmHelper;
use App\Helper\TwigHelper;
use App\Repository\CandidateParticipationCommentRepository;
use App\Repository\CandidateParticipationRepository;
use App\Repository\EventRepository;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use App\Repository\JobRepository;
use PhpOffice\PhpWord\PhpWord;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamedResponse;
use Wamania\ZipStreamedResponseBundle\Response\ZipStreamer\ZipStreamer;
use iio\libmergepdf\Merger;
use App\Entity\EventJobs;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin-exposant")
 */
class ExposantController extends AbstractController
{
    private $helper;

    public function __construct(TwigHelper $helper)
    {
        //parent::construct();
        $this->helper = $helper;
    }

    /**
     * @Route("", name="exposant_index")
     */
    public function index(ParticipationRepository $er, Request $request, JobRepository $jr ,PaginatorInterface $paginator): Response
    {
        if ($this->getUser() instanceof ExposantScanUser) {
            return $this->redirectToRoute('exposant_participation_list');
        }

        if (($this->helper->isAtLeastViewer($this->getUser())) && ($this->getUser()->getEmail() != 'webmaster@l4m.fr')) {
            return $this->redirectToRoute('admin_index');
        }

        $user = $this->getUser();
        $organization = $user->getOrganization();
        $last_participation = $er->getLastParticipationByOrgaOrUser($user, $organization);
        $passed_participations = $er->getLastParticipationByOrga($organization);
        $next_participations = $er->getNextParticipations($organization);
        if(!$last_participation){
            $participations = null;
        }
        else{
            $organization = $last_participation->getOrganization();
            $query = $er->getParticipationsQuery($organization);

            $jobs = $jr->getByOrganization($organization);
            $participations = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );
        }


        $participation_today = $er->findTodayByClientUser($user);
        return $this->render('exposant/index.html.twig', ['next_participations' => $next_participations, 'passed_participations' => $passed_participations, 'participations' => $participations, 'participation_today' => $participation_today, 'jobs' => $jobs]);
    }

    /**
     * @Route("/liste-participations", name="exposant_participation_list", methods="GET")
     */
    public function list(ParticipationRepository $er, EventRepository $evr, Request $request, JobRepository $jr,PaginatorInterface $paginator): Response
    {

        $user = $this->getUser();
        $organization = $user->getOrganization();
        $last_participation = $er->getLastParticipationByOrgaOrUser($user, $organization);
        $passed_participations = $er->getLastParticipationByOrga($organization);
        $next_participations = $er->getNextParticipations($organization);
        if(!$last_participation){
            $participations = null;
        }
        else{
            $organization = $last_participation->getOrganization();
            $query = $er->getParticipationsQuery($organization);

            $jobs = $jr->getByOrganization($organization);
            $participations = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );
        }


        $participation_today = $er->findTodayByClientUser($user);
        return $this->render('exposant/index.html.twig', ['next_participations' => $next_participations, 'passed_participations' => $passed_participations, 'participations' => $participations, 'participation_today' => $participation_today, 'jobs' => $jobs]);
    }


    /**
     * @Route("/participation/voir/{id}", name="exposant_participation_show")
     */
    public function showParticipationAction(Participation $participation): Response
    {
        $this->canAccess($participation);
        $em = $this->getDoctrine()->getManager();
        $participation->getResponsable()->setLastLogin(new \Datetime());
        $em->persist($participation);
        $em->flush();

        return $this->render('participation/show.html.twig', ['participation' => $participation]);
    }

    /**
     * @Route("/participation/edit/{id}", name="exposant_participation_edit_redirect")
     */
    public function editParticipationRedirectAction(Participation $participation, Request $request): Response
    {
        return $this->redirectToRoute('exposant_participation_show', array('id' => $participation->getId()));

    }

    /**
     * @Route("/searchScan", name="exposant_scan", methods="POST")
     */
    public function searchScan(Request $request, CandidateParticipationRepository $cp, ParticipationRepository $pr, CandidateParticipationCommentRepository $cpr)
    {

        $participation = $pr->findById($request->request->get('id'));
        $candidate = $request->request->get('info');
        $candidateParticipation = $cp->findByEventAndEmailOrId($participation->getEvent(), $candidate);
        $candidateParticipationComment = $cpr->findOneByParticipations($participation, $candidateParticipation);

        $this->canAccess($participation);

        if (!$candidateParticipation) {
            $error = "Le candidat ne peut être scanné";
            return new JsonResponse(array('error' => $error));
        }elseif($candidateParticipationComment){
            $error = "Ce candidat a déjà été scanné";
            return new JsonResponse(array('error' => $error));
        }
        else
        {
            $candidateParticipation = $cp->findByEventAndEmailOrId($participation->getEvent(), $candidate);
            $em = $this->getDoctrine()->getManager();
            $comment = new CandidateParticipationComment();
            $comment->setScannedAt(new \Datetime);
            $comment->setCandidateParticipation($candidateParticipation);
            $comment->setOrganizationParticipation($participation);
            $em->persist($comment);
            $em->flush();
            return new Response($comment->getId());
        }
    }

    /**
     * @Route("/comment/add_search/{id}", name="add_candidate_comment", methods="POST")
     */
    public function addCommentSearch(Participation $organization_participation, Request $request, CandidateParticipationRepository $cpr, ParticipationRepository $pr): Response
    {
        $participation = $pr->findById($request->request->get('id'));
        $candidate = $request->request->get('info');
        $candidateParticipation = $cpr->findByEventAndEmailOrId($participation->getEvent(), $candidate);
        //warnign duplicate of controller/api/organizationcontroller
        $em = $this->getDoctrine()->getManager();
        $comment = new CandidateParticipationComment();
        $comment->setScannedAt(new \Datetime);
        $comment->setCandidateParticipation($candidateParticipation);
        $comment->setOrganizationParticipation($organization_participation);
        $em->persist($comment);
        $em->flush();

        $id = $comment->getId();

        return new Response($comment->getId());

    }

    /**
     * @Route("/participation/editer/{id}", name="exposant_participation_edit")
     */
    public function editParticipationAction(Participation $participation, Request $request): Response
    {
        $this->canAccess($participation);
        $response = $this->forward('App\Controller\ParticipationController::edit', array(
            'participation' => $participation
        ));

        if ($request->getMethod() == 'POST') {
            $url = $this->generateUrl(
                'exposant_participation_show',
                array('id' => $participation->getId())
            );
            if ($response instanceof RedirectResponse) {
                $response->setTargetUrl($url);
            }

        }

        return $response;
    }

    /**
     * @Route("/liste-participations/{id}", name="validate_participation", methods="GET")
     */
    public function validateParticipation(Participation $participation, GlobalEmHelper $em_helper): Response
    {
        $participation->setRecall(null);
        $em_helper->setTimestamp($participation,$this->getUser());
        $participation->setRecall(false);
        return $this->redirectToRoute('exposant_participation_list');
    }

    /**
     * @Route("/contact", name="exposant_contact")
     */
    public function contactAction(ParticipationRepository $er, UserRepository $ur)
    {
        $last_participation = $er->getLastParticipation($this->getUser());

        if ($last_participation) {
            $user = $last_participation->getEvent()->getManager();
        } else {
            $user = $ur->findOneByEmail('morgane.hutin@l4m.fr');
        }

        return $this->render('exposant/contact.html.twig', array('user' => $user));
    }

    public function canAccess(Participation $participation)
    {
        $organization = $participation->getOrganization();
        $last_organization = $this->helper->getOrganization($this->getUser());

        if (!$last_organization || $organization->getId() != $last_organization->getId()) {
            throw new AccessDeniedException('Vous ne pouvez pas accèder aux données de ' . $organization->getName() . ' En cas de problème, n\'hésiter pas à contacter l\'équipe L4M');
        }
    }

    /**
     * @Route("/voir-candidats/{id}", name="exposant_show_candidates")
     */
    public function showCandidatesAction(Request $request, Participation $participation, CandidateParticipationCommentRepository $cr, ParticipationRepository $er): Response
    {
        $this->canAccess($participation);

        $form = $this->createForm(SearchCommentType::class, array('attr' => ['class' => 'searchCandidatScanned'],'placeholder' => "Nom / Prénom"));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $param = $form->get('searchComment')->getData()['search'];
        } else {
            $param = null;
        }
        $filter = $request->get('filter');
        $comments = $cr->findByEventAndExposant($participation->getEvent(), $this->getUser(), $filter, $param);

        $participation_today = $er->findTodayByClientUser($this->getUser());

        return $this->render(
            'exposant/candidats_list.html.twig',
            [
                'comments' => $comments,
                'participation' => $participation,
                'form' => $form->createView(),
                'filter' => $filter,
                'participation_today' => $participation_today
            ]
        );
    }

    /**
     * @Route("/voir-scan/{id}", name="exposant_show_scan")
     */
    public function showScanAction(Request $request, Participation $participation, CandidateParticipationCommentRepository $cr, ParticipationRepository $er): Response
    {
        $this->canAccess($participation);

        $form = $this->createForm(SearchFieldType::class, null, array('placeholder' => "Nom, Prénom"));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $param = $form->get('search')->getData();
        } else {
            $param = null;
        }
        $filter = $request->get('filter');

        $comments = $cr->findByEventAndExposant($participation->getEvent(), $this->getUser(), $filter, $param);

        $participation_today = $er->findTodayByClientUser($this->getUser());

        return $this->render(
            'exposant/candidates.html.twig',
            [
                'comments' => $comments,
                'participation' => $participation,
                'form' => $form->createView(),
                'filter' => $filter,
                'participation_today' => $participation_today
            ]
        );
    }

    /**
     * @Route("/voir-candidat/{id}", name="exposant_show_candidate")
     */
    public function showCandidateAction(CandidateParticipationComment $comment): Response
    {
        $this->canAccess($comment->getOrganizationParticipation());
        return $this->render('exposant/candidate_profile.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/voir-candidat-export/{id}", name="exposant_show_candidate_exported")
     */
    public function showExportedCandidateAction(CandidateParticipationComment $comment): Response
    {
        $this->canAccess($comment->getOrganizationParticipation());

        return $this->render('exposant/candidate_exported_profile.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/editer-commentaire", name="exposant_comment_edit", methods="POST")
     */
    public function editCandidateCommentAction(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $comment_text = $request->request->get('comment');
            $comment = $em->getRepository(CandidateParticipationComment::class)->find($request->request->get('id'));
            $comment->setComment($comment_text);
            $em->flush();

            return new JsonResponse(array($request->request->get('comment')), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/update-icon", name="exposant_favorite_edit", methods="POST")
     */
    public function editFavoriteAction(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $value = $request->request->get('value');
            $comment = $em->getRepository(CandidateParticipationComment::class)->find($request->request->get('id'));
            switch ($value) {
                case "favorite":
                    $comment->setFavorite(true);
                    $comment->setLike(0);
                    break;
                default:
                    $comment->setFavorite(null); //null is important to keep candidate sorting
                    $comment->setLike($value);
                    break;
            }
            $em->flush();

            return new JsonResponse($request->request->get('value'), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/scanner/{id}", name="exposant_scan_old", methods={"POST","GET"})
     */
    public function scanAction(Request $request, ParticipationRepository $er, CandidateParticipationCommentRepository $cr, CandidateParticipationRepository $car, Participation $organization_participation)
    {
        $date = $organization_participation->getEvent()->getDate();

        if ($date->format('Ymd') != date('Ymd')) {
            $this->addFlash('danger', "Vous pouvez scanner un QR code uniquement le jour de l'événement auquel vous participez");

            return $this->redirectToRoute('exposant_index');
        }

        $form = $this->createForm(TextFieldType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $participation = $car->find($form->get('text')->getData());

            if (!$participation) {
                $this->addFlash('danger', "Candidat non trouvé");

                return $this->render('exposant/scan.html.twig', array('form' => $form->createView(), 'participation' => $organization_participation));
            }

            if ($participation->getEvent() != $organization_participation->getEvent()) {
                $this->addFlash('danger', "Le n° de QR code ne correspond pas à l'événement du jour. Merci de demander le bon n° de QR Code au candidat.");

                return $this->render('exposant/scan.html.twig', array('form' => $form->createView(), 'participation' => $organization_participation));
            }

            $existing_note = $cr->findOneByParticipations($organization_participation, $participation);

            if ($existing_note) {
                $id = $existing_note->getId();

                return $this->redirectToRoute('exposant_show_candidate', ['id' => $id]);
            }


            //warnign duplicate of controller/api/organizationcontroller
            $em = $this->getDoctrine()->getManager();
            $comment = new CandidateParticipationComment();
            $comment->setScannedAt(new \Datetime);
            $comment->setCandidateParticipation($participation);
            $comment->setOrganizationParticipation($organization_participation);
            $em->persist($comment);
            $em->flush();

            $id = $comment->getId();

            return $this->redirectToRoute('exposant_show_candidate', ['id' => $id]);
        }
        return $this->render('exposant/scan.html.twig', array('form' => $form->createView(), 'participation' => $organization_participation));
    }


    /**
     * @Route("/addScan/{id}", name="add_candidate_comment_scan", methods="POST")
     */
    public function addComment(Participation $organization_participation, Request $request,CandidateParticipationCommentRepository $cpr, CandidateParticipationRepository $cp, ParticipationRepository $pr)
    {

        $data = json_decode($request->getContent());
        $participation = $pr->findById($organization_participation->getId());
        $candidate = $data->participationId;
        $candidateParticipation = $cp->findByEventAndEmailOrId($participation->getEvent(), $candidate);
        if(!$candidateParticipation){
            $error = "Le candidat n'est pas inscrit à ce salon";
            return new JsonResponse(array('error' => $error));
        }
        $candidateParticipationComment = $cpr->findOneByParticipations($participation, $candidateParticipation);

        $this->canAccess($participation);

        if (!$candidateParticipation) {
            $error = "Le candidat ne peut être scanné";
            return new JsonResponse(array('error' => $error));
        }elseif($candidateParticipationComment){
            $error = "Ce candidat a déjà été scanné";
            return new JsonResponse(array('error' => $error));
        }
        else
        {
            $candidateparticipation = $cp->findOneById($data->participationId);
            //warnign duplicate of controller/api/organizationcontroller
            $em = $this->getDoctrine()->getManager();
            $comment = new CandidateParticipationComment();
            $comment->setScannedAt(new \Datetime);
            $comment->setCandidateParticipation($candidateparticipation);
            $comment->setOrganizationParticipation($organization_participation);
            $em->persist($comment);
            $em->flush();
            return new Response($comment->getId());
        }


    }

    /**
     * @Route("/export-candidats/{id}", name="exposant_export_candidates")
     */
    public function exportCandidatesAction(Participation $participation, TwigHelper $twig_helper, GlobalHelper $helper, \Knp\Snappy\Pdf $snappy): Response
    {
        $this->canAccess($participation);

        ob_end_clean();
        $zipStreamer = new ZipStreamer("Liste des candidats scannes lors du salon.rar");

        foreach ($participation->getCandidateComments() as $comment) {
            $rendered = $this->render('exposant/candidate_exported_profile.html.twig', [
                'comment' => $comment
            ]);
            $file_name = 'fiche_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getFirstName()).'_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getLastName()).'.pdf';
            if (file_exists($file_name)) {
                @unlink($file_name);
            }
            $file_path = $this->getParameter('kernel.project_dir') . '/public/commentaires/' . $file_name;
            if (!file_exists($file_path)) {
                $pdf = $snappy->generateFromHtml($rendered, $file_path);
                $candidate = $comment->getCandidateParticipation()->getCandidate();
                if ($candidate->getCV() && $twig_helper->fileExists(substr($candidate->getCvPath(), 1))) {
                    $cv_path = substr($candidate->getCvPath(), 1);
                    $zipStreamer->add(
                        $cv_path,
                        $candidate . '/CV_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getCv())
                    );
                    $zipStreamer->add(
                        $file_path,
                        $candidate . '/Notes_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getFirstName()).'_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getLastName()).'.pdf'
                    );
                }
            }else{
                $candidate = $comment->getCandidateParticipation()->getCandidate();
                if ($candidate->getCV() && $twig_helper->fileExists(substr($candidate->getCvPath(), 1))) {
                    $cv_path = substr($candidate->getCvPath(), 1);
                    $zipStreamer->add(
                        $cv_path,
                        $candidate . '/CV_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getCv())
                    );
                    $zipStreamer->add(
                        $file_path,
                        $candidate . '/Notes_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getFirstName()).'_'.str_replace(['ë', 'ï' , 'ä', 'é', 'è', ' ','-','É','È','Ï','Ë','Ä'], ['e', 'i', 'a','e','è', '','','E','E','I','E','A'],$comment->getCandidateParticipation()->getCandidate()->getLastName()).'.pdf'
                    );
                }
            }

        }
        return new ZipStreamedResponse($zipStreamer);
        /*$this->canAccess($participation);

        $zipStreamer = new ZipStreamer("candidats_".$participation->getEvent()->getSlug().'_'.$participation->getId().'.zip');

        // $file_name = "candidats_".$participation->getEvent()->getSlug().'_'.$participation->getId().'.pdf';


        foreach ($participation->getCandidateComments() as $comment) {
            $candidate = $comment->getCandidateParticipation()->getCandidate();
            //generate pdf with comment
            if ($candidate->getCv()){
                $file_name = $candidate->getCv()."-notes".'.pdf';
            }
            else{
                $file_name = $helper->generateSlug($candidate);
            }
            $public_dir = $this->getParameter('public_dir');
            $file_path = $public_dir.'/generated/'.$file_name ;

            if(file_exists($file_path)){
                unlink($file_path);
            }

            $this->get('knp_snappy.pdf')->generateFromHtml(
                $this->renderView(
                    'snappy/candidate_profile.html.twig',
                    array(
                        'comment' => $comment,
                        'event' => $participation->getEvent(),
                        'candidate' => $candidate
                    )
                ),
                $file_path
            );


            //to do merge pdf comment and cv
            //to do convert any type of file to pdf first
            // $merger = new Merger;
            // $merger->addFile($file_path);
            // $merger->addFile($public_dir.$candidate->getCvPath());
            // $createdPdf = $merger->merge();
            // file_put_contents($file_path,$createdPdf);

            $zipStreamer->add(
              'generated/'.$file_name,
              $file_name
            );

            if($candidate->getCV() && $twig_helper->fileExists(substr($candidate->getCvPath(), 1))){
                $cv_path = substr($candidate->getCvPath(), 1);
                $zipStreamer->add(
                  $cv_path,
                  $candidate->getCv()
                );
            }
          }

        return new ZipStreamedResponse($zipStreamer);*/
    }


    /**
     * @Route("/ajax-get-max-jobs", name="participation_get_max_jobs")
     */
    public function getMaxJobs(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {

            $participation = $this->getDoctrine()->getManager()->getRepository(Participation::class)->find($request->request->get('id'));

            if ($participation instanceof ParticipationJobs) {
                $max = $participation->getMaxJobs();
            } else {
                $max = false;
            }


            return new JsonResponse(array('max' => $max), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/visuels_one/{id}", name="download_visuels_one_exposant", requirements={"id"="\d+"})
     */
    public function downloadVisuelOne(GlobalHelper $helper, participation $participation): Response
    {
        ob_end_clean();
        $zipStreamer = new ZipStreamer("logo_hd_" . str_replace(['/', 'ç', 'É', 'é', 'è', 'È','/', '&'], ['-', 'c','e','e','e','e','-','et'], $participation->getCompanyName()) . '.rar');

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

}
