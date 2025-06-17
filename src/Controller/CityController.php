<?php

namespace App\Controller;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends AbstractController
{

    /**
     * @Route("/autocomplete", name="city_search")
     */
    public function autoComplete(Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest()) {
            $input = $request->get('data');
            //$reset = $request->get('reset');
            $cache = new FilesystemAdapter();

            //  if ($reset){
            $cache->delete('city_search');
            //}

            // if(ctype_digit($input)){
            //      $cache->set('cp_search',true);
            // }
            // else{
            // 	 $cache->set('cp_search',false);
            // }
            $citySearch = $cache->getItem('city_search');
            if (!$citySearch->isHit('city_search')){

                if ($citySearch->get()){

                    $cities = $em->getRepository(City::class)->findByCp($input);
                }
                else{
                    $cities = $em->getRepository(City::class)->findByCharsForAjax($input);
                }
            }
            else{
                $cached = json_decode($citySearch->get('city_search'));
                $cities = array();

                if ($citySearch->get('cp_search')){
                    $key = 'cp';
                }
                else{
                    $key = 'name';
                }

                foreach ($cached as $city) {
                    if(stristr($city->$key,$input)){
                        array_push($cities,$city);
                    }
                }
            }
//$cache->set('city_search', json_encode($cities));

            return new JsonResponse(array('cities' => $cities),200);
        }
        else{
            throw new NotFoundHttpException();
        }
    }
    /**
     * @Route("/candidate/editer-info/{id}", name="admin_candidate_user_edit", methods="GET|POST", requirements={"id" = "\d+"})
     */

}
