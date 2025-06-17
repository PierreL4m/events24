<?php

namespace App\Controller;

use App\Entity\CandidateUser;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\RhUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RedirectLoginController extends AbstractController
{
    /**
     * @Route("/redirect/login", name="redirect_login")
     */
    public function index()
    {
    	$user = $this->getUser();

    	if(!$user){
    		return $this->redirectToRoute('public_index');
    	}
    	if ($user instanceof L4MUser){
    		return $this->redirectToRoute('admin_index');
    	}
    	else if ($user instanceof ExposantUser ) {
    		return $this->redirectToRoute('exposant_index');
    	}
        else if ($user instanceof ExposantScanUser) {
            return $this->redirectToRoute('exposant_participation_list');
        }
    	else if ($user instanceof CandidateUser) {
    		return $this->redirectToRoute('candidate_user_profile');
    	}
    	else if ($user instanceof RhUser) {
    	    return $this->redirectToRoute('recruitement_index');
    	}

    	return $this->redirectToRoute('public_index');
    }
}
