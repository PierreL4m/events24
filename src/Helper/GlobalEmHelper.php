<?php

namespace App\Helper;

use App\Entity\Event;
use App\Entity\ExposantScanUser;
use App\Entity\L4MUser;
use App\Entity\Participation;
use App\Entity\Timestamp;
use App\Entity\User;
use App\Helper\GlobalHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class GlobalEmHelper
{
    /**
     * 
     * @var EntityManagerInterface
     */
	private $em;
	
	/**
	 * 
	 * @var GlobalHelper
	 */
	private $global_helper;
	
	
	public function __construct(EntityManagerInterface $em, GlobalHelper $global_helper)
	{
		$this->em = $em;
		$this->global_helper = $global_helper;
	}

	public function getEm()
	{
		return $this->em;
	}
	// this function removes only the relationship, removes the entity if remove set to true
	public function removeRelation($original_entities, $main_entity,$current_entities, $function_name, $remove=false)
	{		 
        foreach ($original_entities as $entity) {
            if (false === $current_entities->contains($entity)) {
            	$main_entity->$function_name($entity);

            	if($remove){
            		$this->em->remove($entity);
            	}
            }
        }
	}

	public function backupOriginalEntities($entities)
	{
		$original_entities = new ArrayCollection();

        // Create an ArrayCollection of the current objects in the database
        foreach ($entities as $entity) {
            $original_entities->add($entity);
        }

        return $original_entities;
	}
	
	public function generateRandomPassword(User $user, $em=null)
	{
	    return GlobalHelper::random_str(6);
	}

	//generate unique username
    public function generateUsername(User $user, $em=null)
    {   
        if(!$em){
            $em = $this->em;
        }
        
        if ($user instanceof ExposantScanUser){
            $username = $user->getUsername();
        }
        else{
            if (get_class($user) == L4MUser::class){
                $last_name = '';
            }
            else{
                $last_name = '.'.$user->getLastName();
            }
            $username = $user->getFirstName().$last_name;
        }

        $username = $this->global_helper->escapeFrenchChar($username);
        $username = $this->global_helper->escapeOnlySpaces($username);

        $exist = $em->getRepository(User::class)->findOneByUsername($username);
       
        $i=1;

        if($exist){
            
            while($exist){
                $new_username = $username.$i;
                $exist = $em->getRepository(User::class)->findOneByUsername($new_username);
                $i++;
            }
        }
        else{
        	$new_username = $username;
        }

        $user->setUsername($new_username);

        return $new_username;
    }

    public function setTimestamp(Object $entity,User $user)
    {
        if (!method_exists($entity, "setTimestamp")) {
            throw new \Exception('no timestamp method for '.get_class($entity));
        }
        if (!$entity->getTimestamp()){
            $timestamp = new Timestamp();
        }
        else{
            $timestamp = $entity->getTimestamp();
        }
        $timestamp->setUpdated(new \DateTime());
        $timestamp->setUpdatedBy($user);
        $entity->setTimestamp($timestamp);
        $this->em->persist($timestamp);
        $this->em->flush();
    }
}