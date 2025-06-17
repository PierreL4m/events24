<?php


namespace App\Controller\Api;

use App\Entity\City;
use App\Helper\ApiHelper;
use App\Repository\CityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Request;


class GetFilteredCities extends AbstractController
{
    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    public function __construct(ApiHelper $api_helper)

    {
        $this->api_helper = $api_helper;
    }

    /**
     * @Route(
     *     name="get_filtered_cities",
     *     path="/filtered/cities",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=City::class,
     *         "_api_collection_operation_name"="get_filtered_cities"
     *     }
     * )
     */
    public function __invoke(CityRepository $er, Request $request, ApiHelper $helper)
    {

        $filter = $request->get('filter');
        if (strlen($filter) < 2){
            return $this->api_helper->apiException('You have to request at least with two chars',['code' => Response::HTTP_BAD_REQUEST, "message"=>'You have to request at least with two chars'],Response::HTTP_BAD_REQUEST);
        }
        $cities = $er->findByChars($filter);
        return $cities;
    }



}