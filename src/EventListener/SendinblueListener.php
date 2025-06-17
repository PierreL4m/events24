<?php

namespace App\EventListener;

use App\Entity\BilanFileType;
use App\Entity\EmailType;
use App\Entity\Event;
use App\Entity\OrganizationType;
use App\Entity\Participation;
use App\Entity\Place;
use App\Entity\SectionType;
use App\Helper\GlobalHelper;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Psr\Log\LoggerInterface;
use App\Entity\CandidateUser;
use App\Entity\CandidateParticipation;
use App\Repository\CandidateParticipationRepository;

class SendinblueListener
{
    private $helper;   
    private $logger;
    
    /**
     * 
     * @var CandidateParticipationRepository
     */
    private $repo;
    
    /**
     * 
     * @var array
     */
    private $candidate_changed = null;
    
    /**
     * 
     * @var bool
     */
    private $candidate_created = false;
    
    /**
     *
     * @var CandidateUser
     */
    private $candidate = null;
    
    private static $liste_newsletter_id = 17;
    private static $liste_mailing_id = 16;
    
    private $api_endpoint = 'https://api.sendinblue.com/v3/contacts';
    
    private $api_key = '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';
    
    public function __construct(GlobalHelper $helper, LoggerInterface $logger, CandidateParticipationRepository $repo)
    {
        $this->helper = $helper;
        $this->logger = $logger;
        $this->repo = $repo;
    }
    public function postPersist(LifecycleEventArgs $args)
    {        
        $entity = $args->getObject();
        if($entity instanceof CandidateUser) {
            $this->candidate = $entity;
            $this->candidate_changed = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
            $this->candidate_created = true;
        }
        elseif ($entity instanceof CandidateParticipation) {
            $this->candidate = $entity->getCandidate();
        }
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {        

        $entity = $args->getObject();
        if($entity instanceof CandidateUser) {
            $this->candidate = $entity;
            $this->candidate_changed = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        }
        elseif ($entity instanceof CandidateParticipation) {
            $this->candidate = $entity->getCandidate();
        }
    }
    
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if($entity instanceof CandidateUser) {
            $this->delete($args);
        }
        elseif ($entity instanceof CandidateParticipation) {
            $this->candidate = $entity->getCandidate();
        }
    }
    
    public function postFlush() {
        if($this->candidate) {
            return $this->persistOrUpdate();
        }
    }
    
    private function delete(LifecycleEventArgs $args) {
        $obj = $args->getObject();
        if(!($obj instanceof CandidateUser)) {
            return true;
        }
        $mail = $obj->getEmail();
        $contact = $this->findContact($mail);
        if(!$contact) {
            return true;
        }
        
        // aucune liste ou uniquement des listes liees a events
        $listes = array(static::$liste_mailing_id, static::$liste_newsletter_id);
        if(!count($contact['listIds']) ||
            array_intersect($contact['listIds'], $listes) == $contact['listIds']
        ) {
            return $this->deleteContact($mail);
        }
        $array = $this->buildCandidatContact($obj, true);
        
        return $this->updateContact($mail, $array);
    }
    
    private function persistOrUpdate() {
        if(!$this->candidate) {
            return true;
        }
        
        // shortcuts
        $changed = $this->candidate_changed;
        $candidate = $this->candidate;
        if($changed) {
            if(!$this->candidate_created && !isset($changed['mailingRecall']) && !isset($changed['mailingEvents']) && !isset($changed['email'])) {
                return true;
            }
        }
        else {
            $changed = [];
        }
        
        $listes = array(static::$liste_newsletter_id, static::$liste_mailing_id);
        try {
            // FIXME : à l'avenir utiliser les utilitaires permettant de savoir quelle proprietes ont ete modifiees
            if(isset($changed['email'])) {
                $old_contact = $this->findContact($changed['email'][0]);
                // désactivons news et mailing sur l'ancien mail
                if($old_contact) {
                    // ancien contact sur aucune liste ou uniquement sur une ou des listes liées à events => supprimer
                    if(empty($old_contact['listIds']) || !count($old_contact['listIds']) ||
                        array_intersect($old_contact['listIds'], $listes) == $old_contact['listIds']
                    ) { 
                        $this->deleteContact($changed['email'][0]);
                    }
                    else {
                        $o = new CandidateUser();
                        $o->setMailingRecall(0);
                        $o->setMailingEvents(0);
                        $o->setLastname($candidate->getLastname());
                        $o->setFirstname($candidate->getFirstname());
                        $o->setEmail($changed['email'][0]);
                        $this->updateContact($changed['email'][0], $this->buildCandidatContact($o));
                    }
                }
                unset($o);
            }
            $contact = $this->findContact($candidate->getEmail());
            $array = $this->buildCandidatContact($candidate);
            
            if($contact && !$this->hasChanged($candidate, $contact, $array)) {
                return true;
            }
            
            // ni mailing, ni newsletter
            if(!in_array(static::$liste_newsletter_id, $array['listIds']) && !in_array(static::$liste_mailing_id, $array['listIds'])) {
                if(!$contact) {
                    return true;
                }
                // contact sur aucune liste ou uniquement sur la liste candidats => supprimer
                // ancien contact sur aucune liste ou uniquement sur une ou des listes liées à events => supprimer
                if(!count($contact['listIds']) ||
                    array_intersect($contact['listIds'], $listes) == $contact['listIds']
                ) {
                    return $this->deleteContact($candidate->getEmail());
                }
            }
            
            if($contact) {
                $res = $this->updateContact($candidate->getEmail(), $array);
            }
            else {
                $res = $this->createContact($candidate->getEmail(), $array);
            }
            return $res;
        }
        catch(\Exception $e) {
            die($e->getMessage().'<hr>'.$e->getTraceAsString());
            return false;
        }
    }
    
    private function buildCandidatContact(CandidateUser $candidate, $delete = false) {
        $tab = [
            'email' => $candidate->getEmail(),
            'attributes' =>  [
                //'EMAIL' => $candidat->candidat_mail
            ]
        ];
        
        $tab['attributes']['NOM'] = $candidate->getLastname();
        $tab['attributes']['PRENOM'] = $candidate->getFirstname();
        $tab['SMS'] = $candidate->getMobile();
        
        $list_ids = $unlink_list_ids = [];
        
        if($delete /* TODO || !$candidate->getMailable()*/) {
            $unlink_list_ids[] = static::$liste_mailing_id;
            $unlink_list_ids[] = static::$liste_newsletter_id;
        }
        else {
            if($candidate->isMailingEvents()) {
                $list_ids[] = static::$liste_mailing_id;
            }
            else {
                $unlink_list_ids[] = static::$liste_mailing_id;
            }
            if($candidate->isMailingRecall()) {
                $list_ids[] = static::$liste_newsletter_id;
            }
            else {
                $unlink_list_ids[] = static::$liste_newsletter_id;
            }
        }
        
        $tab['listIds'] = $list_ids;
        $tab['unlinkListIds'] = $unlink_list_ids;
        
        // events
        $events = $events_confirmed = $events_came = '';
        $participations = $candidate->getCandidateParticipations();
        foreach($participations as $participation) {
            $events .= '('.$participation->getEvent()->getId().')';
            if($participation->getStatus() && $participation->getStatus()->getSlug() == 'confirmed') {
                $events_confirmed .= '('.$participation->getEvent()->getId().')';
            }
            if($participation->getScannedAt()) {
                $events_came .= '('.$participation->getEvent()->getId().')';
            }
        }

        $tab['attributes']['EVENEMENTS'] = $events;
        $tab['attributes']['EVENEMENTS_CONFIRMES'] = $events_confirmed;
        $tab['attributes']['EVENEMENTS_VENUS'] = $events_came;
        $last = $this->repo->findLastByCandidate($candidate);
        if($last) {
            $tab['attributes']['VILLE'] = $last->getEvent()->getPlace()->getSlug();
        }
        return $tab;
    }
    
    private function createCurlRessource($uri = '', $headers = []) {
        if(false === ($ch = curl_init($this->api_endpoint.($uri != '' ? '/'.urlencode($uri) : '')))) {
            return false;
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(
            array(
                'accept: application/json',
                'api-key: '.$this->api_key
            ),
            $headers
            ));
        return $ch;
    }
    
    /**
     * 
     * @param CandidateUser $obj
     * @param array $contact (as retrieved from sendinblue)
     * @param array $array (generatedà
     * @return boolean
     */
    private function hasChanged(CandidateUser $obj, $contact, $array) {
        if(!isset($contact['attributes']['EVENEMENTS'])) {
            $contact['attributes']['EVENEMENTS'] = '';
        }
        if(!isset($contact['attributes']['EVENEMENTS_CONFIRMES'])) {
            $contact['attributes']['EVENEMENTS_CONFIRMES'] = '';
        }
        if(!isset($contact['attributes']['EVENEMENTS_VENUS'])) {
            $contact['attributes']['EVENEMENTS_VENUS'] = '';
        }
        
        return $contact['email'] != $array['email']
        || $contact['attributes']['NOM'] != $array['attributes']['NOM']
        || $contact['attributes']['PRENOM'] != $array['attributes']['PRENOM']
        || $contact['attributes']['EVENEMENTS'] != $array['attributes']['EVENEMENTS']
        || $contact['attributes']['EVENEMENTS_CONFIRMES'] != $array['attributes']['EVENEMENTS_CONFIRMES']
        || $contact['attributes']['EVENEMENTS_VENUS'] != $array['attributes']['EVENEMENTS_VENUS']
        || $contact['listIds'] != $array['listIds']
        ;
    }
    
    private function findContact($mail) {
        $ch = $this->createCurlRessource($mail);
        if(false === ($result = curl_exec($ch))) {
            return false;
        }
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case '200': return json_decode($result, true);
            default : return false;
        }
    }
    
    public function updateContact($mail, $contact) {
        $ch = $this->createCurlRessource($mail, ['content-type: application/json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, (is_array($contact) ? json_encode($contact) : $contact));
        
        if(false === ($result = curl_exec($ch))) {
            return false;
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case '200':
            case '204':
                return json_decode($result, true);
            default :
                // 	            die($httpcode.' => '.$result.' , '.$json);
                return false;
        }
    }
    
    public function createContact($mail, $contact) {
        $ch = $this->createCurlRessource('', ['content-type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, (is_array($contact) ? json_encode($contact) : $contact));
        if(false === ($result = curl_exec($ch))) {
            return false;
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        switch($httpcode) {
            case '200':
            case '204':
                return json_decode($result, true);
            default : return false;
        }
    }
    
    public function deleteContact($mail) {
        $ch = $this->createCurlRessource($mail, ['content-type: application/json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        if(false === ($result = curl_exec($ch))) {
            return false;
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        switch($httpcode) {
            case '200':
            case '204':
                return json_decode($result, true);
            default : return false;
        }
    }
}