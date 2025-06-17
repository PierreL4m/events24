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
use App\Helper\MailerHelper;

class PostContact extends AbstractController
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

    /**
     *
     * @var MailerHelper
     */
    private MailerHelper $mailer_helper;


    public function __construct(FormHelper $helper, ApiHelper $api_helper, MailerHelper $mailer_helper) {
        $this->helper = $helper;
        $this->api_helper = $api_helper;
        $this->mailer_helper = $mailer_helper;
    }
    /**
     * @Route(
     *     name="contact",
     *     path="/event/{id}/contact",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="contact"
     *     }
     * )
     */
    public function __invoke(Event $data, Request $request)
    {
            $datas = json_decode($request->getContent());
            $infos = (array) $datas;
            $this->mailer_helper->sendContactForm($infos,$data);
            $contact_address = json_decode($request->getContent())->email ;
            $contact_phone = json_decode($request->getContent())->phone ;
            return $infos;
    }
}