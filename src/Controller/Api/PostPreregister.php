<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\CustomApiException;
use ApiPlatform\Core\Util\RequestParser;
use App\Helper\ApiHelper;

class PostPreregister extends AbstractController
{
    /**
     *
     * @var FormHelper
     */
    private FormHelper $helper;

    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    public function __construct(FormHelper $helper, ApiHelper $api_helper) {
        $this->helper = $helper;
        $this->api_helper = $api_helper;
    }
    /**
     * @Route(
     *     name="preregistration",
     *     path="/event/{id}/preregistration",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="preregistration"
     *     }
     * )
     */
    public function __invoke(Event $data, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mail = json_decode($request->getContent())->email[0];
        $class = '\App\Entity\\RecallSubscribe';
        $recall = new $class;
        $recall->setEmail($mail);
        $recall->setEvent($data);
        $em->persist($recall);
        $em->persist($data);
        $em->flush();
        return $recall;
    }
}