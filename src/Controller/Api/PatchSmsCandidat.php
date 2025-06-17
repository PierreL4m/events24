<?php
namespace App\Controller\Api;
use App\Entity\CandidateUser;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PatchSmsCandidat extends AbstractController
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
     *     name="working_candidate",
     *     path="/candidate/sms/update/{id}",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="working_candidate"
     *     }
     * )
     */
    public function __invoke(CandidateUser $id, Request $request, FormHelper $form_helper)
    {
        $test = json_decode($request->getContent());
        $id->setPhoneRecall($test->sms);
        return $id;
    }
}