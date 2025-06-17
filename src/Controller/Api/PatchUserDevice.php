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
use Doctrine\ORM\EntityManagerInterface;

class PatchUserDevice extends AbstractController
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
     *     name="patch_user_device",
     *     path="/user/device",
     *     methods={"PATCH"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="patch_user_device"
     *     }
     * )
     */
    public function __invoke(Request $request, EntityManagerInterface $em)
    {

        $user = $this->getUser();

    // anonymous users have no data to update
        if(!$user) {
            return new \stdClass();
        }

        $device = $request->get('device');

        if ($device){
            $user->setDevice($device);
        }

        $last_login = $request->get('last_login');

        if ($last_login){
            $last_login = \DateTime::createFromFormat('dmYHi', $last_login);
            $user->setLastLogin($last_login);
        }

        $em->flush();

        return $user;
}}