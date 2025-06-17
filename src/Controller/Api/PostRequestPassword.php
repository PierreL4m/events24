<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\RequestPasswordHelper;
use App\Helper\ApiHelper;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use League\Bundle\OAuth2ServerBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

class PostRequestPassword extends AbstractController
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
     *     name="request_password",
     *     path="/request/password",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=CandidateUSer::class,
     *         "_api_collection_operation_name"="request_password"
     *     }
     * )
     */
    public function __invoke(Request $request, RequestPasswordHelper $request_helper, MailerInterface $mailer)
    {
        return $request_helper->request($this->api_helper->getAdaptedRequest($request), $mailer);
    }
}