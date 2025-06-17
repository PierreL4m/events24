<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

class PatchStatsPub extends AbstractController
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
     *     name="patch_stat_pub",
     *     path="/stats/pub/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=Participation::class,
     *         "_api_collection_operation_name"="patch_stat_pub"
     *     }
     * )
     */
    public function __invoke(Participation $data)
    {

        $data->setPubCount($data->getPubCount() + 1);
        $this->getDoctrine()->getManager()->flush();

        return $data;
    }
}