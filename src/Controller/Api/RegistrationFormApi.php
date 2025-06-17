<?php
namespace App\Controller\Api;

use App\Model\ApiForm;
use App\Entity\Event;
use App\Entity\CandidateUser;
use App\Form\Api\RegistrationType;
use App\Helper\FormHelper;
use App\Helper\ApiHelper;
use App\Helper\H48Helper;
use App\Repository\Eventrepository;
use App\Repository\SlotsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use League\Bundle\OAuth2ServerBundle\Security\User\NullUser;
use App\Entity\User;

class RegistrationFormApi extends AbstractController
{
     /**
     *
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     *
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $form_factory;

    public function __construct(FormFactoryInterface $form_factory, SessionInterface $session)
    {
        $this->form_factory = $form_factory;
        $this->session = $session;
    }
    /**
     * @Route(
     *     name="get_registration_form",
     *     path="/event/{id}/form",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="get_registration_form"
     *     }
     * )
     */
    public function __invoke(Event $data, ApiHelper $api_helper, SlotsRepository $sr,H48Helper $h48)
    {

        $user = $this->getUser();
        $context = 'api';
        // by default, a registration limit must prevent registration
        $can_register = true;
        if($data instanceof EventJobs && ($limit = $data->getRegistrationLimit()) && $limit->getTimestamp() < time()) {
            $can_register = false;
        }
        $candidate = $ro = null;
        $is_admin = $username = false;
        if($user && $user instanceof User) {
            if ($user instanceof CandidateUser){
                $candidate = $user;
                $username = true;
            }
            elseif ($context == 'api' && $user->getType() == 'Exposant'){
                return $this->api_helper->apiException("role '".$user->getType()."' cannot register to an event");
            }
        }
        
        if(!$candidate) {
            $candidate = new CandidateUser();   
        }
        
        //end user

        //create form
        $nbAvailableSlots = $sr->countSlotsInEventNotFull($data);
        $nbSlots = $sr->countSlotsInEvent($data);
        $second_event = null ;
        $nbAvailableSlotsSecond = null;
        if ($h48->is48($data) && $h48->getSecond48($data)){
            $second_event = $h48->getSecond48($data);
        }
        $form = $this->form_factory->create(
            RegistrationType::class,
            $candidate,
            array(
                'context' => $context,
                'event' => $data,
                'user' => $user,
                'password' => $this->session->get('plainPassword'),
                'cv' => $this->session->get('tmp_cv'),
                'second_event' => $second_event
            )
        );
        $apiForm = new ApiForm($form);
        return $apiForm;
    }
}