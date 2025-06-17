<?php
namespace App\Controller\Api;
use App\Entity\CandidateUser;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Repository\CityRepository;
use App\Repository\DegreeRepository;
use App\Repository\MobilityRepository;
use App\Repository\SectorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PatchInfosCandidat extends AbstractController
{
    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    public function __construct(ApiHelper $api_helper) {
        $this->api_helper = $api_helper;
    }

    /**
     * @Route(
     *     name="infos_candidate",
     *     path="/candidate/infos/update/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="infos_candidate"
     *     }
     * )
     */
    public function __invoke(CandidateUser $id, Request $request, FormHelper $form_helper, DegreeRepository $dr, MobilityRepository $mr, CityRepository $cr, SectorRepository $sr)
    {
        $test = json_decode($request->getContent());
        $diplome = $dr->findOne($test->diplome);
        $mobility = $mr->findOne($test->mobility);
        $city = $cr->findOne($test->city);
        $id->setEmail($test->mail);
        $id->setPhone($test->phone);
        $id->setDegree($diplome);
        $id->setMobility($mobility);
        $id->setCity($city);
        $id->setWantedJob($test->sector[0]);
        return $id;
    }
}