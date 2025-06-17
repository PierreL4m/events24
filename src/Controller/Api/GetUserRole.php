<?php


namespace App\Controller\Api;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ApiPlatform\Core\Annotation\ApiResource;


class GetUserRole extends AbstractController
{
    /**
     * @Route(
     *     name="get_user_role",
     *     path="/user",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="get_user_role"
     *     }
     * )
     */
    public function __invoke()
    {
        $roles = $this->getUser()->getRoles();
        if(!is_array($roles) || !$roles){
            $role = null;
        }
        else{
            if (in_array('ROLE_SUPER_ADMIN',$roles) || in_array('ROLE_ADMIN',$roles) || in_array('ROLE_VIEWER',$roles)){
                $role ='l4m';
            }
            elseif(in_array('ROLE_CANDIDATE',$roles)){
                $role = 'candidate';
            }
            elseif(in_array('ROLE_SCAN',$roles)){
                $role = 'scan';
            }
            elseif(in_array('ROLE_ORGANIZATION',$roles) || in_array('ROLE_EXPOSANT_SCAN',$roles)){
                $role = 'exposant';
            }
            elseif(in_array('ROLE_RH',$roles)){
                $role = 'rh';
            }
            else{
                $role = json_encode($roles);
            }
        }
        return $this->json(['role' => $role]);
    }

    

}