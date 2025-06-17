<?php

namespace App\Helper;

use App\Entity\BilanFile;
use App\Entity\BilanFileType;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\ClientUser;
use App\Entity\Email;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\Slots;
use App\Entity\ExposantScanUser;
use App\Entity\Participation;
use App\Entity\Partner;
use App\Entity\Place;
use App\Entity\Section;
use App\Entity\SectionType;
use App\Entity\SpecBase;
use App\Entity\Status;
use App\Entity\User;
use App\Helper\H48Helper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Entity\EventType;
use App\Entity\EventJobs;
use App\Entity\SectionJoblink;

class TwigHelper
{
    private $em;
    private $project_dir;
    private $router;
    private $h48_helper;

    public function __construct(EntityManagerInterface $em, $project_dir, RouterInterface $router, H48Helper $h48_helper)
    {
        $this->em = $em;
        $this->project_dir = $project_dir;
        $this->router = $router;
        $this->h48_helper = $h48_helper;
    }

    public function getReturnButton()
    {
        return '<button class="return_btn btn btn-primary" onclick="history.back(-1)"><i class="fa fa-arrow-left" aria-hidden="true"></i>Retour</button> ';
    }

    public function getAdminEventLink(Event $event)
    {
        $path = $this->router->generate('event_show', array('id' => $event->getId()));

        return  '<a href='.$path.'>'.$event->__toString().'</a>' ;
    }

    public function getActiveCities()
    {
        return $this->em->getRepository(Place::class)->getActives();
    }

    public function getLogoSvg(EventType $event_type)
    {
        return (file_exists($this->project_dir.'/public/images/logo_'.$event_type.'.svg') ? file_get_contents($this->project_dir.'/public/images/logo_'.$event_type.'.svg') : null);
    }

    public function getLogoPng(EventType $event_type)
    {
        return (file_exists($this->project_dir.'/public/images/logo_'.$event_type.'.png') ? '/images/logo_'.$event_type.'.png' : null);
    }

    public function getSVG()
    {
        return file_get_contents($this->project_dir.'/public/images/carte_france_site.svg');
    }

    public function getNextEvent($place_id)
    {
        $event = $this->em->getRepository(Event::class)->getNextEventByCity($place_id);

        return $event;
    }

    public function getRandomParticipations($event_id)
    {
        $participations = $this->em->getRepository(Participation::class)->getRandomByEvent($event_id);

        return $participations;
    }

    public function getParticipationById($participation_id)
    {
        $currentParticipation = $this->em->getRepository(Participation::class)->findById($participation_id);

        return $currentParticipation;
    }

    public function getSlotsByEvents($event)
    {
        $slots = $this->em->getRepository(Slots::class)->findByEvent($event->getId());
        return array_merge($slots);
    }

    public function getAvailablesSlotsByEvent($event)
    {
        $slots = $this->em->getRepository(Slots::class)->findAllNotFull($event);
        return array_merge($slots);
    }

    public function getOneSlot($slot)
    {
        $slot = $this->em->getRepository(Slots::class)->findOne($slot->getId());
        return array_merge($slot);
    }

    public function getOneParticipation($participation_id, $event_id)
    {

        $oneParticipation = $this->em->getRepository(Participation::class)->getOneParticipation($participation_id, $event_id);

        return $oneParticipation;
    }

    // TODO : replace this with AuthorizationChecker isGranted('ROLE_VIEWER') which is far better bacause it handles roles hierarchy
    // plus : it seems weird to have a method in a TwigHelper that gets called in other "non-Twig" helpers
    public function isAtLeastViewer(?User $user)
    {
        if(!$user) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN') || $user->hasRole('ROLE_VIEWER')){
            return true;
        }
        return false;
    }

    public function getOrganization(ClientUser $user)
    {
        if($user instanceof ExposantScanUser){
            return $user->getOrganization();
        }
        $last_participation = $this->em->getRepository(Participation::class)->getLastParticipation($user);

        if(!$last_participation){
            return null;
        }

        return $last_participation->getOrganization();
    }

    public function getOrderedParticipations(Event $event)
    {
        return $this->em->getRepository(Participation::class)->getOrderedByEvent($event->getId());
    }
    public function responsableConnectedAfterGeneration(Participation $participation)
    {
        if ($participation->getTimestamp()){
            $created = $participation->getTimestamp()->getCreated();
            $last_login = $participation->getResponsable()->getLastLogin();
            if ($last_login < $created){
                return false;
            }
        }
        return true;
    }

    public function getMissingResponsables(Event $event)
    {
        return $this->em->getRepository(Participation::class)->getMissingResponsables($event);
    }

    public function hasEmail(Event $event, $slug)
    {
        $email = $this->em->getRepository(Email::class)->findByEventAndSlug($event,$slug);

        if(count($email) > 0){
            return $email[0];
        }
        return false;
    }

    public function getEmailTypeBySlug($slug)
    {
        $email_type = $this->em->getRepository(EmailType::class)->findOneBySlug($slug);

        if ($email_type){
            return $email_type->getLabel();
        }
        else{
            throw new \Exception('No email type associated with slug : '.$slug);
        }
    }

    public function missingTechGuide(Event $event)
    {
        $p = $this->em->getRepository(Participation::class)->findMissingTechGuide($event);

        if ($p){
            return true;
        }
        return false;
    }

    public function missingStandNumber(Event $event)
    {
        $p = $this->em->getRepository(Participation::class)->findMissingStandNumber($event);

        if ($p){
            return true;
        }
        return false;
    }

    public function canSendMail(Participation $p, $var)
    {
        switch ($var) {
            case 'Tech':
                if ($this->canSendMailToMe($p,$var) && $p->getResponsable()){
                    return true;
                }
                return false;

            case 'Bat':
                if ($this->canSendMailToMe($p,$var) && $p->getResponsable()){
                    return true;
                }
                return false;

            default:
                throw new \Exception('function canSendMail does not handle var = '.$var);
        }
    }

    public function canSendMailToMe(Participation $p,$var)
    {
        switch ($var) {
            case 'Tech':
                if ($this->canCreateAck($p) && $p->getStandType() && $p->getStandType()->getTechGuide() && $p->getEvent()->getSpecificationPath() ){
                    return true;
                }
                return false;

            case 'Bat':
                if ($p->getEvent()->getBatDate() && count($p->getBats()) > 0 ){
                    return true;
                }
                return false;

            default:
                throw new \Exception('function canSendMailToMe does not handle var = '.$var);
                break;
        }

    }

    public function canCreateAck(Participation $participation)
    {
        if (is_null($participation->getStandNumber()) || is_null($participation->getEvent()->getAckDate())){
            return false;
        }

        return true;
    }

    public function canPrintBadges(Event $event)
    {
        foreach ($event->getParticipations() as $p) {
            if (is_null($p->getStandType()) || is_null($p->getBadge())){
                return false;
            }
        }
        return true;
    }

    public function getNbRow(Event $event)
    {
        $i = 1;

        foreach ($event->getParticipations() as $participation) {
            $i = $participation->getStandSize() <= 5 ? $i+2 : $i+4;
        }

        return floor($i/3);
    }
    public function getNbPages(Event $event)
    {
        $row = $this->getNbRow($event);

        return ceil($row/7);
    }

    public function getSectionTemplate(Section $section)
    {
        $reflexion_class = new \ReflectionClass($section);

        return $reflexion_class->getShortName();
    }

    public function canDeleteUser(User $user)
    {
        return false;

        if (method_exists('getEvents', $user) && count($user->getEvents()) > 0) {
            return false;
        }
        elseif (method_exists('getRecruitmentOffice', $user) && $user->getRecruitmentOffice()) {
            return false;
        }
        if (count($user->getParticipations()) > 0){
            return false;
        }


        // elseif($user instanceof CandidateUser ){
        //     return false;
        // }

        return true;
    }

    public function findSectionBySlugAndEvent($slug, Event $event)
    {
        $section = $this->em->getRepository(Section::class)->findByStSlugAndEvent($event, $slug);

        return $section;
    }

    public function specBaseExists(Event $event)
    {
        $specBase = $event->getSpecBase();

        if (!$specBase){
            return false;
        }
        $path = $this->project_dir.'/public/uploads/spec_base/'.$specBase->getPath();

        if(file_exists($path) && is_file($path)){
            return true;
        }
        return false;
    }

    public function getOrderedEventsByPartner(Partner $partner)
    {
        return $this->em->getRepository(Event::class)->getOrderedByPartner($partner);
    }

    public function fileExists($web_path){

        if (!$web_path){
            return false;
        }

        if($web_path[0] != '/'){
            $web_path = '/'.$web_path;
        }

        $path = $this->project_dir.'/public'.$web_path;

        if(file_exists($path) && is_file($path)){
            return true;
        }
        return false;
    }

    public function getYoutubeLink($url)
    {
        if (strpos($url, 'watch') !== false){
            parse_str( parse_url( $url, PHP_URL_QUERY ), $results );
            $url = $results['v'];
        }
        elseif (strpos($url, 'youtu.be') !== false){
            $pos = strrpos($url, '/');
            $url = substr($url, $pos + 1);
        }
        else{
            return $url ;
        }
        $theURL = "https://www.youtube.com/embed/".$url;
        $headers = get_headers($theURL);

        if(substr($headers[0], 9, 3) == "200"){
            return $theURL;
        }
        return $url;
    }

    public function escapeExtension($web_path, $revert = null)
    {
        if (!$web_path){
            return ;
        }
        $char1 = '.';
        $char2 = '-';

        if($revert){
            $char1 = '-';
            $char2 = '.';
        }
        $pos = strrpos($web_path, $char1);

        $web_path[$pos] = $char2 ;

        return $web_path;
    }

    public function getIFrame($url,$width=640,$height=360)
    {
        $start = strpos($url, 'src=')+5;
        $src = substr($url,$start);
        $end = strpos($src, '"');

        if($end === false){
            $end = strpos($src, "'");
        }
        $src = substr($src,0,$end);

        $iframe = "<iframe width='".$width."'' height='".$height."' src='".$src."' ></iframe>";

        return $iframe;
    }

    public function getBilanMenuItems(Event $event)
    {
        return $this->em->getRepository(BilanFileType::class)->findByEvent($event);
    }
    public function getBilanSections(Event $event)
    {
        return $this->em->getRepository(Section::class)->findByEventForBilan($event);
    }



    public function getCandidateParticipationByEvent(CandidateUser $candidate, Event $event)
    {
        $participation = $this->em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEvent($candidate, $event);

        return $participation;
    }

    public function getCandidateParticipationByEventAndSlots(CandidateUser $candidate, Event $event, slots $slots)
    {
        $participation = $this->em->getRepository(CandidateParticipation::class)->findOneByCandidateAndEventAndSlots($candidate, $event, $slots);

        return $participation;
    }

    public function getPreviewLink(Event $event,$class=null)
    {
        if($this->h48_helper->is48($event) && ($main = $this->h48_helper->getMain48($event))) {
            $event = $main;
        }

        $path = $this->router->generate('public_event', array('slug' => $event->getSlug()));

        return  '<a href='.$path.' class="btn btn-primary '.$class.'" target="_blank">Preview</a>' ;
    }

    public function getPublicEventLink(Event $event,$label="Site web de l'événement", $class=null)
    {
        if($this->h48_helper->is48($event) && ($main = $this->h48_helper->getMain48($event))) {
            $event = $main;
        }

        $path = $this->router->generate('public_event', array('slug' => $event->getSlug()));

        return  '<a href='.$path.' class="'.$class.'" target="_blank">'.$label.'</a>' ;
    }

    public function isAlreadyRegistered(User $candidate = null, Event $event)
    {
        if (!$candidate || !$candidate instanceof CandidateUser){
            return false;
        }
        foreach ($candidate->getCandidateParticipations() as $p) {
            if($p->getEvent() == $event){
                return true;
            }
        }
        return false;
    }

    public function registrationLimitOutdated(Event $event)
    {
        $time = time();
        if($event->getDate()->format('Y-m-d') < date('Y-m-d') || $event instanceof EventJobs && $event->getRegistrationLimit() && $event->getRegistrationLimit()->getTimestamp() < $time) {
            return true;
        }
        return false;
    }

    public function findOldParticipationsFromDays($candidate,$days=90)
    {
        return $this->em->getRepository(CandidateParticipation::class)->findOldFromDays($candidate,$days);
    }
    public function findFutureParticipationsFromDays($candidate,$days=90)
    {
        return $this->em->getRepository(CandidateParticipation::class)->findFuture($candidate,$days);
    }

    public function displayCandidates($participation)
    {
        $date_event = $participation->getEvent()->getDate();
        $date = clone($date_event);
        $now = new \Datetime();

        if ($date > $now){
            return false;
        }

        $date->add(new \DateInterval('P90D'));

        if ($date < $now){
            return false;
        }

        $candidates = count($participation->getCandidateComments());

        if($candidates > 0){

            return true;
        }

        return false;
    }

    public function countHaveCome(Event $event)
    {
        $count = 0;
        foreach ($event->getCandidateParticipations() as $c_participation) {
            if($c_participation->getScannedAt()){
                $count++;
            }
        }
        return $count;
    }

    public function countCandidats(Slots $slots)
    {
        $count = $this->em->getRepository(CandidateParticipation::class)->findNumberBySlots($slots);
        return $count;
    }
    public function countSlotsInEvent(Event $event)
    {
        $countSlots = $this->em->getRepository(Slots::class)->countSlotsInEvent($event);
        return $countSlots;
    }
    public function countUnslots(Event $event)
    {
        $unslots = $this->em->getRepository(CandidateParticipation::class)->findUnslots($event);
        return $unslots;
    }
    public function getEventPartners(Event $event,SectionType $section_type)
    {
        return $this->em->getRepository(Partner::class)->findByEventAndSectionType($event,$section_type->getSlug());
    }

    public function getStatus()
    {
        return $this->em->getRepository(Status::class)->findAll();
    }

    public function registrationNeedsValidation(EventType $type) {
        return ($type->getRegistrationValidation() != EventType::REGISTRATION_VALIDATION_AUTO);
    }

    public function hasSectionJoblink(Event $event) {
        return $this->em->getRepository(SectionJoblink::class)->findBy(['event' => $event, 'onPublic' => true]);
    }
}
