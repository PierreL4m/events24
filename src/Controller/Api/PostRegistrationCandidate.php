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

class PostRegistrationCandidate extends AbstractController
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
     *     name="registration_candidate",
     *     path="/event/{id}/registration",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=CandidateUser::class,
     *         "_api_collection_operation_name"="registration_candidate"
     *     }
     * )
     */
    public function __invoke(CandidateUser $data, $id, Request $request, FormHelper $form_helper, EventRepository $er, UserPasswordHasherInterface $passwordHasher)
    {
        $event = $er->findOneById($id);
        return $form_helper->registerCandidate($event, $this->api_helper->getAdaptedRequest($request), 'api', $passwordHasher);
    }
}