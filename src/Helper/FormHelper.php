<?php

namespace App\Helper;

use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\City;
use App\Entity\ClientUser;
use App\Entity\Event;
use App\Entity\EventSimple;
use App\Entity\L4MUser;
use App\Entity\OnsiteUser;
use App\Entity\Origin;
use App\Entity\ScanUser;
use App\Entity\Section;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\Slots;
use App\Form\Api\CandidateParticipationCommentType;
use App\Form\Api\RegistrationType;
use App\Form\PreRegisterType;
use App\Form\ContactType;
use App\Form\UserType;
use App\Helper\ApiHelper;
use App\Helper\GlobalHelper;
use App\Helper\RenderHelper;
use App\Helper\MailerHelper;
use App\Repository\OriginRepository;
use App\Repository\SlotsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Twig\Environment;
use App\Entity\RhUser;
use App\Entity\EventJobs;
use App\Entity\Candidate;
use App\Entity\EventType;
use App\Repository\CityRepository;
use Symfony\Component\Form\FormInterface;
use App\Exception\RegistrationFormException;
use function PHPUnit\Framework\isEmpty;
use App\Exception\CustomApiException;
use App\Exception\CustomApiFormException;
use Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\CandidateParticipationComment;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class FormHelper
{
    const EXTENSIONS = ["pdf", "doc", "docx", "odt"];
    const TMP_PATH = '/tmp';

    /**
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     *
     * @var GlobalHelper
     */
    private GlobalHelper $helper;
    
    /**
     * 
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $form_factory;
    /**
     *
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $token_storage;
    
    /**
     * 
     * @var ApiHelper
     */
    private ApiHelper $api_helper;
    
    /**
     * 
     * @var RenderHelper
     */
    private RenderHelper $render_helper;
    
    /**
     * 
     * @var MailerHelper
     */
    private MailerHelper $mailer_helper;
    
    /**
     * 
     * @var TwigHelper
     */
    private TwigHelper $twig_helper;
    private $project_dir;
    
    /**
     * 
     * @var H48Helper
     */
    private H48Helper $h48_helper;
    
    /**
     * 
     * @var RouterInterface
     */
    private RouterInterface $router;
    
    /**
     * 
     * @var SessionInterface
     */
    private SessionInterface $session;
    
    /**
     * 
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $event_dispatcher;
    

    /**
     * 
     * @var GlobalEmHelper
     */
    private GlobalEmHelper $em_helper;
    
    public function __construct(FormFactoryInterface $form_factory,TokenStorageInterface $token_storage, ApiHelper $api_helper, RenderHelper $render_helper, MailerHelper $mailer_helper, $project_dir, H48Helper $h48_helper, RouterInterface $router, SessionInterface $session, EventDispatcherInterface $event_dispatcher, GlobalEmHelper $em_helper)

    {
        $this->em = $em_helper->getEm();
        $this->templating = $render_helper->getTemplating();
        $this->helper = $render_helper->getGlobalHelper();
        $this->form_factory = $form_factory;
        $this->token_storage = $token_storage;
        $this->api_helper = $api_helper;
        $this->render_helper = $render_helper;
        $this->twig_helper = $render_helper->getTwigHelper();
        $this->mailer_helper = $mailer_helper;
        $this->project_dir = $project_dir;
        $this->h48_helper = $h48_helper;
        $this->router = $router;
        $this->session = $session;
        $this->event_dispatcher = $event_dispatcher;
        $this->em_helper = $em_helper;
    }

    private function registrationFormNotSubmittedOrNotValid(Event $event, Request $request, $context, $is_admin = false, $can_register = true, ?CandidateUser $candidate, ?FormInterface $form, int $nbSlots, int $nbAvailableSlots) {
        //form is not valid or not submitted
        switch($context) {
            case 'api':
                return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST);
            case 'admin':
                if($is_admin) {
                    return $this->templating->render('recruitment/add_candidate.html.twig', array(
                        'event' => $event,
                        'form' => ($can_register ? $form->createView() : null),
                        'candidate' => $candidate,
                        'cv' => $this->session->get('tmp_cv'),
                        'pwd_placeholder' => null,
                        'is_admin' => true
                    ));
                }
                $this->noCaseException($context);
                break;
            case 'onsite':
                $origins = $this->em->getRepository(Origin::class)->findAllQuery();
                return $this->templating->render('onsite_registration/form.html.twig', array(
                    'event' => $event,
                    'form' => ($can_register ? $form->createView() : null),
                    'candidate' => $candidate,
                    'cv' => $this->session->get('tmp_cv'),
                    'pwd_placeholder' => null,
                    'is_admin' => true,
                    'origins' => $origins
                ));
                $this->noCaseException($context);
                break;
            case 'web':
                $pwd_placeholder = null;
                //contact form
                $contact_form = $this->form_factory->create(ContactType::class);
                $contact_address = $contact_phone = null ;
                $contact_form->handleRequest($request);

                if ($contact_form->isSubmitted() && $contact_form->isValid()){
                    $datas = $contact_form->getData();
                    $this->mailer_helper->sendContactForm($datas,$event);
                    $contact_address = $datas['email'] ;
                    $contact_phone = $datas['phone'] ;
                }
                // end contact form
                // screwed h48
                if ($this->h48_helper->is48($event) && $this->h48_helper->getSecond48($event)){
                    $sections = $this->em->getRepository(Section::class)->findByEvents($event, $this->h48_helper->getSecond48($event));

                    $agendas = $this->em->getRepository(Section::class)->findAgendaByEvents($event, $this->h48_helper->getSecond48($event));
                    //screwed agendas
                    if (count($agendas) == 2){
                        $agenda_formation = $agendas[1];
                        //before info pratiques
                        $key = 0;

                        foreach ($sections as $section) {
                            if($section->getSectionType()->getSlug() == "infos"){
                                break;
                            }
                            $key++;
                        }
                        if($key != 0){
                            $sections = array_slice($sections, 0, $key, true) +
                                array("6.1" => $agenda_formation) +
                                array_slice($sections, $key, count($sections) - 1, true) ;
                        }
                    }
                }// end screwed h48
                else{
                    $sections = $this->em->getRepository(Section::class)->findByEvent($event->getId());
                }

                $type = $event->getType()->getShortName();

                $pwd_placeholder = null;

                if ($this->session->get('plainPassword')){
                    for ($i = 0; $i < strlen($this->session->get('plainPassword')); $i++) {
                        $pwd_placeholder = $pwd_placeholder.'*';
                    }
                }

                return $this->templating->render('public/event'.$type.'_new.html.twig', array(
                    'event' => $event,
                    'sections' => $sections,
                    'form' => ($can_register ? $form->createView() : null),
                    'contact_form' => $contact_form->createView(),
                    'candidate' => $candidate,
                    'contact_address' => $contact_address,
                    'contact_phone' => $contact_phone,
                    'cv' => $this->session->get('tmp_cv'),
                    'pwd_placeholder' => $pwd_placeholder,
                    'nbSlots' => $nbSlots,
                    'nbAvailableSlots' => $nbAvailableSlots
                ));
            default:
                $this->noCaseException($context);
                break;
        }
    }

    /**
     *
     * @param Event $event
     * @param Request $request
     * @param string $context ('api', 'web', 'admin')
     * @throws \Exception
     * @throws Exception
     * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\RedirectResponse|string
     */
    public function registerCandidate(Event $event, Request $request, $context, UserPasswordHasherInterface $passwordHasher)
    {


        //user
        $user = $this->getCurrentUser();

        if(!$user && $request->getMethod() == 'PATCH'){
            return $this->api_helper->apiException("patch cannot be used to register a new user");
        }

        // by default, a registration limit must prevent registration
        $can_register = true;
        if($event instanceof EventJobs && ($limit = $event->getRegistrationLimit()) && $limit->getTimestamp() < time()) {
            $can_register = false;
        }
        $candidate = $ro = null;
        $is_admin = $username = false;
        if($user) {
            $is_admin = (($user instanceof L4MUser || $user instanceof ClientUser || $user instanceof RhUser || $user instanceof OnsiteUser));
            if ($user instanceof CandidateUser){
                $candidate = $user;
                $username = true;
            }
            elseif ($context == 'api' && $user->getType() == 'Exposant'){
                return $this->api_helper->apiException("role '".$user->getType()."' cannot register to an event");
            }
            elseif($context == 'admin') {
                $can_register = true;
                if($user instanceof RhUser) {
                    $ro = $user->getRecruitmentOffice();
                    $events = $ro->getEvents();
                    if(!$events->contains($event)) {
                        return new RedirectResponse($this->router->generate('recruitement_index'));
                    }
                }
            }
        }
        if($is_admin) {
            // an admin can always add a participation
            $can_register = true;
        }

        if(!$can_register) {
            return $this->registrationFormNotSubmittedOrNotValid($event, $request, $context, $is_admin, $can_register, $candidate, null, 0, 0);
        }
        if($is_admin && !$candidate) {
            if(($r = $request->get('registration')) && !empty($r['email']) && ($m = $r['email']) && count(($cs = $this->em->getRepository(CandidateUser::class)->findByEmail($m))) > 0){
                $candidate = $cs[0];
            }
        }

        if(!$candidate) {
            $candidate = new CandidateUser();
        }
        //end user

        //create form
            $nbAvailableSlots = $this->em->getRepository(Slots::class)->countSlotsInEventNotFull($event);
            $nbSlots = $this->em->getRepository(Slots::class)->countSlotsInEvent($event);
            $second_event = null ;
            $nbAvailableSlotsSecond = null;
            if ($this->h48_helper->is48($event) && $this->h48_helper->getSecond48($event)){
                $second_event = $this->h48_helper->getSecond48($event);
                $nbAvailableSlotsSecond = $this->em->getRepository(Slots::class)->countSlotsInEventNotFull($second_event);
            }
            $form = $this->form_factory->create(
                RegistrationType::class,
                $candidate,
                array(
                    'context' => $context,
                    'event' => $event,
                    'user' => $user,
                    'password' => $this->session->get('plainPassword'),
                    'cv' => $this->session->get('tmp_cv'),
                    'slots'=> ($nbAvailableSlots || $nbAvailableSlotsSecond),
                    'second_event' => $second_event
                )
            );
        //end create form

        //handle request
        switch($context) {
            case 'api':
                $form->submit($request->request->all(), !$username);
                break;

            case 'web':
            case 'admin':
                $form->handleRequest($request);
                break;
            case 'onsite':
                $form->handleRequest($request);
                break;
            default:
                $this->noCaseException($context);
                break;
        }
        //end handle request

        $cv_backup = $candidate->getCv();

        if($candidate->isMailingEvents() == null){
            $candidate->setMailingEvents(false);
        }
        if ($context != 'api' && $form->isSubmitted()) {
            //save password
            if($form->has('plainPassword') && $form->get('plainPassword')->getData() ){
                $this->session->set('plainPassword', $form->get('plainPassword')->getData());
            }
            //end save password

            $fileSystem = new Filesystem();

            $cv = $candidate->getFile();
            // check octet stream and save tmp cv
            if ($cv){

                $this->checkMimeType($form);

                $file_name = $this->generateCvName($candidate);

                //if (!$candidate->getCv()){ //no cv path not working

                //create cache repo if not exists
                if (!$fileSystem->exists($this::TMP_PATH)){
                    try {
                        $fileSystem->mkdir(sys_get_temp_dir());
                    } catch (IOExceptionInterface $exception) {
                        throw new \Exception("An error occurred while creating your directory at ".$exception->getPath());
                    }
                }

                if (!$fileSystem->exists($this::TMP_PATH."/".$this->session->get('tmp_cv') && !$this->session->get('tmp_cv'))){
                    //weird !$this->session->get('tmp_cv')
                    try{

                        $cv->move(
                            $this::TMP_PATH,
                            $file_name
                        );

                    }catch (\Exception $e){
                        chmod("/tmp/cvs", 0770);
                        // changes the mod of the directory recursively
                        //$fileSystem->chmod('/tmp/cvs', 0770, 0770, true);

                        // changes the group of the directory recursively
                        $fileSystem->chgrp('/tmp/cvs', 'www-data', true);

                        try{
                            $cv->move(
                                $this::TMP_PATH,
                                $file_name
                            );
                        }catch (\Exception $e){
                            throw $e;
                        }
                    }

                    if ($fileSystem->exists($this::TMP_PATH."/".$file_name)){
                        $this->session->set('tmp_cv',$file_name);
                    }
                }
                //}
            }
            // end check octet stream and save tmp cv
            //check errors
            $candidate = $this->checkRegistrationFormErrors($username, $candidate, $form, $context, $is_admin);
            //end check errors
        }

        if(!$form->isSubmitted() || !$form->isValid()) {
            return $this->registrationFormNotSubmittedOrNotValid($event, $request, $context, $is_admin, $can_register, $candidate, $form, $nbSlots, $nbAvailableSlots);
        }

        // warn admins if chosen slot is full ?
        // FIXME : shouldn't the check be AFTER successfull registration ?
        if($nbAvailableSlots) {
            $slotsId = $form->get('slots')->getData();

            if($slotsId) {
                $nbCandidate = $this->twig_helper->countCandidats($slotsId);
                if($nbCandidate == $slotsId->getMaxCandidats() - 1 /* FIXME : why ? && ($event->getType() == "24" || $event->getType() == "48")*/){
                    $slotsId->setis_full(1);
                    $mailCom = $this->em->getRepository(User::class)->findMailCom();
                    foreach ($mailCom as $mailCom) {
                        $this->mailer_helper->sendMail(
                            $mailCom['email'],
                            'UN CRENEAU EST PLEIN !',
                            'raw',
                            array('body' => 'Le créneau '.$slotsId->getName().' de l\'événement '.$slotsId->getEvent()->getSlug() .' est plein'),
                            array("webmaster@l4m.fr" => "Back office événements L4M")
                        );
                    }
                }
            }
        }

        // check if already registered to this event
        if ($this->twig_helper->isAlreadyRegistered($candidate ,$event)){
            switch($context) {
                case 'api':
                    return $this->api_helper->apiException('Vous ne vouvez pas vous inscrire deux fois au même événement');
                   // throw new RegistrationFormException(sprintf([$form,'error' => 'Vous ne vouvez pas vous inscrire deux fois au même événement', 'user_id' => $user->getId(), 'event_id' => $event->getId()]));
                    break;
                case 'admin':
                    // already registered
                    if($is_admin) {
                        $this->session->getFlashBag()->add('success', 'Le candidat participe déjà à cet événement.');
                        return new RedirectResponse($this->router->generate('candidates_list', array('id' => $event->getId())));
                    }
                    $this->noCaseException($context);
                    break;
                case 'onsite':
                    // already registered
                    if($is_admin) {
                        $this->session->getFlashBag()->add('success', 'Le candidat participe déjà à cet événement.');
                        return new RedirectResponse($this->router->generate('on_site_registration', array('id' => $event->getId())));
                    }
                    $this->noCaseException($context);
                    break;
                case 'web':
                    // already registered
                    return new RedirectResponse($this->router->generate('candidate_user_profile'));
                    break;
                default:
                    $this->noCaseException($context);
                    break;
            }
        }

        if ($form->has('city_id')){
            $this->handleCity($candidate, $form->get('city_id')->getData());
        }
        elseif (!$candidate->getId()) {
            // set event place for candidate city for new candidate
            $city_name = strtolower($event->getPlace()->getCity());
            //avoid Arras (st ...)
            $pos = strpos($city_name, "(");
            if($pos !== false){
                $city_name = substr($city_name,0,$pos);
            }

            $cities = $this->em->getRepository(City::class)->findByName($city_name);

            if (count($cities) == 1){
                $candidate->setCity($cities[0]);
            }
            elseif(count($cities) > 1){
                foreach ($cities as $city){
                    if($city->getCountry()->getName() == 'France'){
                        $candidate->setCity($city);
                        break;
                    }
                }
            }

            //            	}

            // upload cv tmp
            $cv_name = $this->session->get('tmp_cv');
            if(is_string($cv_name)) { // there is a cv in session. either previous cv or current cv
                if ($fileSystem->exists($this::TMP_PATH."/".$cv_name) ){
                    $fileSystem->copy($this::TMP_PATH."/".$cv_name, $this->project_dir."/public/uploads/cvs/".$cv_name);

                    $fileSystem->remove($this::TMP_PATH."/".$cv_name);
                    $candidate->setCv($cv_name);

                    if($cv_backup){
                        $old_cv_absolute_path = $this->project_dir."/public/uploads/cvs/".$cv_backup;

                        if(file_exists($old_cv_absolute_path) && is_file($old_cv_absolute_path)){
                            unlink($old_cv_absolute_path);
                        }
                    }
                }
                elseif (!$fileSystem->exists($this::TMP_PATH."/".$cv_name)) {
                    $form->get('file')->addError(new FormError('Vous devez choisir un cv'));
                    $ok = false;
                    $this->session->remove('tmp_cv');
                }
            }
        }

        if ($context == 'api'){
            $candidate = $this->checkRegistrationFormErrors($username,$candidate,$form,$context, $is_admin);
            if(count($form->getErrors(true,false)) == 0){
                $success = $this->handleB64Upload($form->get('cv_file')->getData(), $candidate);

                if($success == false){
                    return $this->cvFileError();
                }
            }
        }

        if($candidate->getId()) {
            $this->em->merge($candidate);
        }

        if(count($form->getErrors(true,false))) {
            return $this->registrationFormNotSubmittedOrNotValid($event, $request, $context, $is_admin, $can_register, $candidate, $form, $nbSlots, $nbAvailableSlots);
        }

        // slots and no slot chosen
        if(($nbAvailableSlots > 0 || $nbAvailableSlotsSecond > 0) && $form->get('slots')->getData() == null && (!$nbAvailableSlotsSecond || $form->get('slots_second')->getData() == null)) {
            return;
        }


        $participation = new CandidateParticipation();
        $participation->setEvent($event);
        // slot if available / needed
        if ($nbAvailableSlots) {
            $slots = $form->get('slots')->getData();
            $participation->setSlot($slots);
        }

        // job if available / needed
        if ($event->getType()->getRegistrationType() == EventType::REGISTRATION_TYPE_JOB) {
            $job = $form->get('job');
            if ($job) {
                if (($j = $job->getData())) {
                    $participation->setJob($j);
                } elseif ($context == 'api') {
                    throw new \Exception('Unable to process ' . serialize($request->request->all()));
                }
            }
        }

        if ($is_admin) {
            $participation->setHandledBy($user);
        }


        // participation status

//        dump($event->getType());
//        die();
//        if ($event->getType()->registrationValidationAuto()) {
//            $status = $this->em->getRepository(Status::class)->findOneBySlug('confirmed');
//        } elseif ($is_admin) {
//            $s = $form->get('status');
//            if ($s) {
//                if (!($status = $s->getData())) {
//                    $status = $this->em->getRepository(Status::class)->findOneBySlug('confirmed');
//                }
//            }
//        } else {
//
//        }
        $status = $this->em->getRepository(Status::class)->findOneBySlug('registered');
        $participation->setStatus($status);

        // participation origin
        if ($context == 'api') {
            $from = 'appli';
        } elseif ($ro) {
            $from = 'ro';
        } else {
            $from = $request->get('from') ?: 'l4m';
        }
        $participation->setComesFrom($from);
        $candidate->addCandidateParticipation($participation);

        //second h48 event if available 
        if ($this->h48_helper->is48($event) && $this->h48_helper->getSecond48($event)) {
            $second_event = $this->h48_helper->getSecond48($event);
            $second_participation = clone($participation);
            $second_participation->setEvent($second_event);

            if($nbAvailableSlotsSecond) {
                $second_slots = $form->get('second_slots')->getData();
                $second_participation->setSlots($second_slots);
                $nbCandidate = $this->twig_helper->countCandidats($second_slots);
                if($nbCandidate == $second_slots->getMaxCandidats()){
                    $second_slots->setis_full(1);
                    $mailCom = $this->em->getRepository(User::class)->findMailCom();
                    foreach ($mailCom as $mailCom) {
                        $this->mailer_helper->sendMail(
                            $mailCom['email'],
                            'UN CRENEAU EST PLEIN !',
                            'raw',
                            array('body' => 'Le créneau '.$second_slots->getName().' de l\'événement '.$second_slots->getEvent()->getSlug() .' est plein'),
                            array("webmaster@l4m.fr" => "Back office événements L4M")
                        );
                    }
                }
            }

            $candidate->addCandidateParticipation($second_participation);
        }
        // end h48

        if (!$candidate->getId()) {
            // send pwd by email if a NEW candidate is registered by L4M, SCAN or RH
            $candidate->setEnabled(true);
            $candidate->setRoles(['ROLE_CANDIDATE']);
            if($request->request->get("registration_origin") != null){
                $origin = $this->em->getRepository(Origin::class)->findById($request->request->get("registration_origin"));
                $candidate->setOrigin($origin);

            }
            if ($is_admin) {
                $password = substr(hash('sha512', rand()), 0, 7);
                $password = "A" . $password;
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $candidate->setPassword($hashedPassword);
                $candidate->setEnabled(true);
                $this->mailer_helper->sendNewPassword($candidate, $password, $event);
            }
            //end send pwd
        }
        if($event->getType()->getId() != 2 || $context == 'admin'){
            $status = $this->em->getRepository(Status::class)->findOneBySlug('confirmed');
            $participation->setStatus($status);
        }
        $this->em->persist($candidate);
        $this->em->flush();
        $this->session->remove('tmp_cv');
        $this->session->remove('plainPassword');

        //handle invitation
        if ($participation->getStatus()->getSlug() == 'confirmed') {
            $this->helper->generateQrCode($participation);
            $this->render_helper->generateInvitation($participation);
            $invitation_path = $participation->getInvitationPath();
            //h48
            if (isset($second_participation)) {
                $this->helper->generateQrCode($second_participation);
                $this->render_helper->generateInvitation($second_participation);
                //dump($participation);die();
                //send invitation
                $secondInvitation_path = $second_participation->getInvitationPath();
            }
            //end h48
            if ($this->twig_helper->fileExists($invitation_path)) {
                $this->mailer_helper->sendInvitation($participation);
            }

            if ($this->twig_helper->fileExists($secondInvitation_path)) {
                $this->mailer_helper->sendInvitation($second_participation);
            }
            //end send invitation
        } elseif (!$is_admin) { //send registration success to candidate and registration info to recruitment office
            $this->mailer_helper->sendRegistrationMail($participation, $is_admin);
        }

        switch ($context) {
            case 'api':

//                throw new RegistrationFormException(sprintf('test'));
                if (!$user || !$user instanceof User) {
                    $user = $candidate;
                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->token_storage->setToken($token);
                    // $this->session->set('_security_main', serialize($token));
                    // Fire the login event manually
                    $event = new InteractiveLoginEvent($request, $token);
                    $this->event_dispatcher->dispatch($event, "security.interactive_login");
                }
                return $candidate;
                break;
            case 'admin':
                if ($is_admin) {
                    $this->session->getFlashBag()->add('success', 'Le candidat ' . $candidate . ' a été créé. Il va recevoir son invitation et son mot de passe par email.');
                    return new RedirectResponse($this->router->generate('candidates_list', array('id' => $event->getid())));
                }
                $this->noCaseException($context);
                break;
            case 'onsite':
                if ($is_admin) {
                    $this->session->getFlashBag()->add('success', 'Inscription validée, votre invitation vous a été transmise par mail, veuillez la présenter à l\'accueil du salon');
                    return new RedirectResponse($this->router->generate('on_site_registration', array('id' => $event->getid())));
                }
                $this->noCaseException($context);
                break;
            case 'web':
                //log in new candidate
                if (!$user || !$user instanceof User) {
                    $user = $candidate;
                    $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                    $this->token_storage->setToken($token);
                    // $this->session->set('_security_main', serialize($token));
                    // Fire the login event manually
                    $event = new InteractiveLoginEvent($request, $token);
                    $this->event_dispatcher->dispatch($event, "security.interactive_login");
                }
                //end log in

                $this->session->set('register_success', $participation->getId());
                if ($is_admin) {
                    return new RedirectResponse($this->router->generate('candidates_list', array('id' => $event->getid())));
                }
                return new RedirectResponse($this->router->generate('candidate_user_profile'));
                break;

            default:
                $this->noCaseException($context);
                break;

        }
    }
    public function editCandidateComment(Request $request, CandidateParticipationComment $comment, $context)
    {

        
        //warning case web is not used
        //handle in ExposantController.php
        $user = $this->token_storage->getToken()->getUser();

        if($user->getType() != 'Exposant'){
            switch ($context) {
                case 'api':
                    return $this->api_helper->apiException("role '".$user->getType()."' cannot edit candidate note");
                    break;
                case 'web':
                    throw new AccessDeniedException("role '".$user->getType()."' cannot edit candidate note");
                default:
                    $this->noCaseException();
                    break;
            }
        }
        $this->helper->checkCommentAccess($user, $comment, 'api');

        $form = $this->form_factory->create(CandidateParticipationCommentType::class, $comment, array('context' => $context));
        switch($context) {
            case 'api':
                $form->submit($request->request->all(), false);
                break;

            case 'web':
                $form->handleRequest($request);
                break;

            default:
                $this->noCaseException($context);
                break;

        }
        //if form is valid
        if ( ($context == 'api' && $form->isValid()) || ($context == 'web' && $form->isSubmitted() && $form->isValid()) ) {
            $this->em->flush();

            switch($context) {
                case 'api':
                    return $comment;
                    break;

                case 'web':
                    return $this->templating->render('exposant/candidate_profile.html.twig', array('form' => $form->createView(), 'comment' => $comment));
                    break;

                default:
                    $this->noCaseException($context);
                    break;
            }
        }

        switch($context) {
            case 'api':
                return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST);
                break;

            case 'web':
                return $this->templating->render('exposant/edit_candidate_participation_comment.html.twig', array('form' => $form->createView(), 'comment' => $comment));
                break;

            default:
                $this->noCaseException($context);
                break;
        }
    }

    public function editCandidateProfile(Request $request, $context, CandidateUser $candidate = null, CandidateParticipation $participation = null, Event $event = null)
    {

        $user = $this->token_storage->getToken()->getUser();

        switch($context) {
            case 'api':
                $candidate = $user;
                break;
            case 'admin':
                if($user->hasRole('ROLE_CANDIDATE')){
                    $this->noCaseException($context);
                }
                if(!$candidate) {
                    $this->noCaseException($context);
                }

                if($user->hasRole("ROLE_RH") && $user instanceof RhUser) {
                    if(empty($participation) ||
                        !($event = $participation->getEvent()) ||
                        ($event->getType()->registrationValidationAuto()) ||
                        !($user->getRecruitmentOffice()->getEvents()->contains($event))
                    ) {
                        $this->noCaseException($context);
                    }
                }
                break;
            case 'web':
                // FIXME : this feels very odd, using a twig helper in an other helper
                if($this->twig_helper->isAtLeastViewer($user)) {
                    if (!$candidate){
                        throw new \Exception('User '.$user->getType().' cannot edit his candidate profile');
                    }
                }
                else if($candidate){
                    throw new \Exception('User '.$user->getType().' cannot edit an other candidate profile');
                }
                else if($user->hasRole('ROLE_CANDIDATE')){
                    $candidate = $user;
                }
                break;

            default:
                $this->noCaseException($context);
        }
        $email_backup = $candidate->getEmail();
        $cv_backup = $candidate->getCv();

        if ($context == 'api'){
            $sectors_backup = clone($candidate->getSectors());
            $candidate->removeAllSectors();
        }

        $form = $this->form_factory->create(RegistrationType::class, $candidate, array('context' => $context,'edit_profile' => true, 'user' => $user));

        switch($context) {
            case 'api':
                $form->submit($request->request->all(),false);
                break;

            case 'web':
            case 'admin':
                $form->handleRequest($request);
                break;

            default:
                $this->noCaseException($context);
                break;
        }

        if ($context != 'api'){
            if ($form->get('file')->getData()){
                $this->checkMimeType($form);
            }
        }

//if form is valid
        if ( ($context == 'api' && $form->isValid())
            || ($context != 'api' && $form->isSubmitted() && $form->isValid())
        ) {
            if($email_backup != $candidate->getEmail() && count($this->em->getRepository(User::class)->findByEmail($candidate->getEmail())) > 0){
                $form->get('email')->addError(new FormError('Cette adresse email est déjà utilisée'));
            }
            else{
                if($context != 'api' && $form->has('city_id')){
                    $this->handleCity($candidate, $form->get('city_id')->getData());
                }
                else if($context == 'api'){
                    $success = $this->handleB64Upload($form->get('cv_file')->getData(), $candidate);

                    if(!$success){
                        return $this->cvFileError();
                    }

                    if(count($candidate->getSectors()) == 0){
                        foreach ($sectors_backup as $sector) {
                            $candidate->addSector($sector);
                        }
                    }
                    //else symfony handle new sectors

                }

                $cv = $candidate->getFile();

                if($cv){
                    $cv_name = $this->generateCvName($candidate);
                    $cv_path = $this->project_dir."/public/uploads/cvs/";
                    $cv->move($cv_path, $cv_name);
                    $candidate->setCv($cv_name);
                    $old_cv_absolute_path = $cv_path.$cv_backup;

                    if(file_exists($old_cv_absolute_path) && is_file($old_cv_absolute_path)){
                        unlink($old_cv_absolute_path);
                    }
                }
                //do this for all boolean problem with ChoiceType and PATCH should be fixed by custom datatransformer
                $array_functions = ['mailingEvents', 'mailingRecall', 'phoneRecall'];

                foreach ($array_functions as $function_name) {
                    $this->handleCheckbox($request,$function_name,$user);
                }

                if($participation  && ! $participation->getHandledBy() && $context == 'admin') {
                    $participation->setHandledBy($user);
                    if($participation->getStatus()->getSlug() == 'registered') {
                        $participation->setStatus($this->em->getRepository(Status::class)->findOneBySlug('pending'));
                    }
                    $this->em->persist($participation);
                }

                $this->em->flush();

                switch($context) {
                    case 'api':
                        return $candidate;
                    case 'admin':
                        if($participation) {
                            return new RedirectResponse($this->router->generate('candidates_list', ['id' => $participation->getEvent()->getId()]));
                        }
                        return new RedirectResponse($this->router->generate('admin_candidate_profile', ['id' => $candidate->getId()]));

                        break;
                    case 'web':
                        if($user->hasRole("ROLE_CANDIDATE")){
                            return new RedirectResponse($this->router->generate('candidate_user_profile'));
                        }
                        else{
                            return new RedirectResponse($this->router->generate('admin_candidate_profile', array('id' => $candidate->getId(), 'event' => $event->getId())));
                        }

                    default:
                        $this->noCaseException($context);
                        break;
                }

            }
        }
        switch($context) {
            case 'api':
                return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST, "Ce formulaire ne doit pas contenir des champs supplémentaires.");
            case 'admin':
                return $this->templating->render('recruitment/edit_candidate.html.twig', [
                    'candidate_user' => $candidate,
                    'form' => $form->createView(),
                    'is_admin' => true
                ]);
                break;
            case 'web':
                return $this->templating->render('candidate_user/edit.html.twig', [
                    'candidate_user' => $candidate,
                    'form' => $form->createView(),
                    'cities' => $this->em->getRepository(City::class)->findAll()
                ]);
            default:
                $this->noCaseException($context);
                break;
        }
    }

    public function handleCity($candidate,$city_id)
    {
        if ($city_id){
            $city = $this->em->getRepository(City::class)->find($city_id);
            $candidate->setCity($city);
        }
    }

    function noCaseException($case)
    {
        throw new \Exception('Case not found '.$case);
    }

    public function checkRegistrationFormErrors($username, CandidateUser $candidate, $form, $context, $is_admin = false)
    {
        // check if already have account
        if (!$is_admin && !$username && count(($cs = $this->em->getRepository(CandidateUser::class)->findByEmail($candidate->getEmail()))) > 0){
            $form->get('email')->addError(new FormError('Vous avez déjà un compte associé à l\'email '.$candidate->getEmail()));

        }
        elseif (!$username){
            $this->em_helper->generateUsername($candidate);
        }
        //end check if already have account

        //errors empty sectors / password / city_id
        // if (count($candidate->getSectors()) == 0){
        // 	$form->get('sectors')->addError(new FormError('Vous devez choisir au moins un secteur'));
        // }
        // if (!$candidate->getPlainPassword() && !$username){
        // 	$form->get('plainPassword')->get('first')->addError(new FormError('Vous devez choisir un mot de passe'));
        // }
        // if($context == 'web' && !$form->get('city_id')->getData() && $form->get('city_name')->getData()){
        // 	$form->get('city_name')->addError(new FormError('Vous devez choisir une ville dans la liste'));
        // }
        // if($context == 'api' && !$form->get('city')->getData()){
        // 	$form->get('city')->addError(new FormError('Vous devez choisir une ville dans la liste'));
        // }

        //if password save in session
        if ($this->session->get('plainPassword') && !$candidate->getPlainPassword()){
            $candidate->setPlainPassword($this->session->get('plainPassword'));
        }
        //end password save in session
        if (!$candidate->getPlainPassword() && !$username){
            if ($form->has('plainPassword') ){
                $form->get('plainPassword')->addError(new FormError('Vous devez choisir un mot de passe'));
            }
        }
        //end errors
        return $candidate;
    }

    public function handleB64Upload($cv_file, $candidate)
    {
        if ($cv_file){
            $full_data = $cv_file;

            if ($cv_file)

                $array = explode(',', $full_data);

            if (!array_key_exists(1, $array)){
                return false;
            }
            $data = base64_decode($array[1]);
            $extensions = array(
                "image/jpg" => ".jpg",
                "image/png" => ".png",
                "image/jpeg" => ".jpg",
                "application/pdf" => ".pdf",
                "application/msword" => ".doc",
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => ".docx",
                "application/vnd.oasis.opendocument.text" => ".odt",
            );

            $type = $this->helper->get_string_between($full_data,'data:',';base64');

            if (!array_key_exists($type, $extensions)){
                return false;
            }
            $extension = $extensions[$type];
            $filename = $this->helper->generateSlug($candidate->getLastName().$candidate->getFirstName().uniqid());
            $path = $this->project_dir.'/public/uploads/cvs/'.$filename.$extension;

            file_put_contents($path, $data);
            $candidate->setCv($filename.$extension);
        }
        return true;
    }

    public function deleteCandidate($context)
    {
        $user = $this->token_storage->getToken()->getUser();

        $this->em->remove($user);
        $this->em->flush();

        switch ($context) {
            case 'web':
                $this->session->getFlashBag()->add('danger', 'Votre compte a été supprimé');

                return new RedirectResponse($this->router->generate('public_index'));

            case 'api' :
                return $this->api_helper->apiException('Votre compte à bien été supprimé');
            default:
                $this->noCaseException();
                break;
        }
    }

    public function checkMimeType($form)
    {
        $cv = $form->get('file')->getData();
        $mime_type = $cv->getMimeType();
        if($mime_type == "application/octet-stream" || $mime_type == "application/zip"){
            if(!in_array($cv->guessClientExtension(), $this::EXTENSIONS) ){
                $form->get('file')->addError(new FormError('Votre fichier n\'est pas valide'));
            }
        }
    }

    public function cvFileError()
    {
        return $this->api_helper->apiException("cv file is not valid. Please send data:{mimetype};base64,{base64string}");
    }

//this function should be replace by datatransformer. no time for now
    public function handleCheckbox($request,$function_name,$entity)
    {
        $request_value = $request->get($function_name);

        if($request_value !== null){
            $setter = 'set'.$function_name;
            $getter = 'is'.$function_name;
            $entity->$setter($request_value);
        }
    }

    public function generateCvName($candidate)
    {
        $cv_name = $candidate->getFile()->getClientOriginalName();
        $pos = strrpos($cv_name, '.');
        $extension = substr($cv_name, $pos);

        return $this->helper->generateSlug($candidate).uniqid().$extension;
    }

    /**
     *
     * @param Event $event
     * @param Request $request
     * @param string $context ('api', 'web', 'admin')
     * @throws \Exception
     * @throws Exception
     * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\RedirectResponse|string
     */
    public function waitingEvent(Event $event, Request $request, $context)
    {
        //contact form
        $contact_form = $this->form_factory->create(ContactType::class);
        $contact_address = $contact_phone = null ;
        $contact_form->handleRequest($request);

        if ($contact_form->isSubmitted() && $contact_form->isValid()){
            $datas = $contact_form->getData();
            $this->mailer_helper->sendContactForm($datas,$event);
            $contact_address = $datas['email'] ;
            $contact_phone = $datas['phone'] ;
        }
        //user
        $user = $this->getCurrentUser();

        if(!$user && $request->getMethod() == 'PATCH'){
            return $this->api_helper->apiException("patch cannot be use to register a new user");
        }

        // by default, a registration limit must prevent registration
        $can_register = true;
        if($event instanceof EventJobs && ($limit = $event->getRegistrationLimit()) && $limit->getTimestamp() < time()) {
            $can_register = false;
        }
        $candidate = $ro = null;
        $is_admin = $username = false;
        if($user) {
            $is_admin = (($user instanceof L4MUser || $user instanceof ClientUser || $user instanceof RhUser));
            if ($user instanceof CandidateUser){
                $candidate = $user;
                $username = true;
            }
            elseif ($context == 'api' && $user->getType() == 'Exposant'){
                return $this->api_helper->apiException("role '".$user->getType()."' cannot register to an event");
            }
            elseif($context == 'admin') {
                $can_register = true;
                if($user instanceof RhUser) {
                    $ro = $user->getRecruitmentOffice();
                    $events = $ro->getEvents();
                    if(!$events->contains($event)) {
                        return new RedirectResponse($this->router->generate('recruitement_index'));
                    }
                }
            }
        }
        $can_register = true;
        if($can_register) {
            if($is_admin && !$candidate) {
                if(($r = $request->get('registration')) && !empty($r['email']) && ($m = $r['email']) && count(($cs = $this->em->getRepository(CandidateUser::class)->findByEmail($m))) > 0){
                    $candidate = $cs[0];
                }
            }

            if(!$candidate) {
                $candidate = new CandidateUser();
            }
            //end user

            //create form
            $form = $this->form_factory->create(
                PreRegisterType::class,
                $candidate,
                array(
                    'context' => $context,
                    'event' => $event,
                    'user' => $user,
                    'password' =>
                        $this->session->get('plainPassword')
                )
            );
            //end create form

            //handle request
            switch($context) {
                case 'api':
                    $form->submit($request->request->all(),!$username);
                    break;

                case 'web':
                case 'admin':
                    $form->handleRequest($request);
                    break;

                default:
                    $this->noCaseException($context);
                    break;
            }

            if($candidate->isMailingEvents() == null){
                $candidate->setMailingEvents(false);
            }

            if ($context != 'api' && $form->isSubmitted()){
                //save password
                if($form->has('plainPassword') && $form->get('plainPassword')->getData() ){
                    $this->session->set('plainPassword', $form->get('plainPassword')->getData());
                }
                //check errors
                $candidate = $this->checkRegistrationFormErrors($username,$candidate,$form, $context, $is_admin);
                //end check errors
            }
            //if form is valid
            if ( ($context == 'api' && $form->isValid())
                || ($context != 'api' && $form->isSubmitted() && $form->isValid())
            ) {
                $slotsId = $form->get('slots')->getData();
                $nbCandidate = $this->twig_helper->countCandidats($slotsId);
                if($nbCandidate == $slotsId->getMaxCandidats() - 1 && $event_type == "24"){
                    $slotsId->setis_full(1);
                    $mailCom = $this->em->getRepository(User::class)->findMailCom();
                    foreach ($mailCom as $mailCom) {
                        $this->mailer_helper->sendMail(
                            $mailCom['email'],
                            'UN CRENEAU EST PLEIN !',
                            'raw',
                            array('body' => 'Le créneau '.$slotsId->getName().' de l\'événement '.$slotsId->getEvent()->getSlug() .' est plein'),
                            array("webmaster@l4m.fr" => "Back office événements L4M")
                        );
                    }
                }
                // check if already registered to this event
                if ($this->twig_helper->isAlreadyRegistered($candidate ,$event)){
                    switch($context) {
                        case 'api':
                            break;

                        case 'admin':
                            // already registered
                            if($is_admin) {
                                $this->session->getFlashBag()->add('success', 'Le candidat participe déjà à cet événement.');
                                return new RedirectResponse($this->router->generate('candidates_list', array('id' => $event->getId())));
                            }
                            $this->noCaseException($context);
                            break;

                        case 'web':
                            // already registered
                            return new RedirectResponse($this->router->generate('candidate_user_profile'));
                            break;
                        default:
                            $this->noCaseException($context);
                            break;
                    }
                }

                if($candidate->getId()) {
                    $this->em->merge($candidate);
                }

                if(count($form->getErrors(true,false)) == 0){
                    $participation = new CandidateParticipation();
                    $candidate->setRoles(['ROLE_CANDIDATE']);
                    $candidate->setEnabled(true);
                    $this->em->persist($candidate);
                    $this->em->flush();
                    $this->session->remove('plainPassword');

                    //log in new candidate
                    if (!$user  || !$user instanceof User){
                        $user = $candidate;
                        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                        $this->token_storage->setToken($token);
                        $this->session->set('_security_main', serialize($token));
                        // Fire the login event manually
                        $event = new InteractiveLoginEvent($request, $token);
                        $this->event_dispatcher->dispatch("security.interactive_login", $event);
                    }
                    return new RedirectResponse($this->router->generate('candidate_user_profile'));
                }
            }
        }
        if ($this->h48_helper->is48($event) && $this->h48_helper->getSecond48($event)){
            $sections = $this->em->getRepository(Section::class)->findByEvent($event, $this->h48_helper->getSecond48($event));
        }// end screwed h48
        else{
            $sections = $this->em->getRepository(Section::class)->findByEvent($event->getId());
        }
        return $this->templating->render('public/waiting_event.html.twig', array(
                'event' => $event,
                'sections' => $sections,
                'form' => ($can_register ? $form->createView() : null),
                'contact_form' => $contact_form->createView()
            )
        );
    }

//    /**
//     */
//    private function getCurrentUser() : ?User {
//        $token = $this->token_storage->getToken();
//        if(isEmpty($token)){
//            return null;
//        }
//        $user = $token->getUser();
//        if(isEmpty($user)){
//            return null;
//        }
//        if($user instanceof User){
//            return $user;
//        }
//        return null;
//    }

    /**
     */
    private function getCurrentUser() : ?User {
        $token = $this->token_storage->getToken();
        return ($token && $token->getUser() instanceof User ? $token->getUser() : null);
    }
}
