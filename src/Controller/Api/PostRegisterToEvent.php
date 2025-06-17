<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipation;
use App\Entity\EventType;
use App\Entity\Status;
use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\Participation;
use App\Helper\FormHelper;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\CustomApiException;
use ApiPlatform\Core\Util\RequestParser;
use App\Helper\ApiHelper;

class PostRegisterToEvent extends AbstractController
{
    /**
     *
     * @var GlobalHelper
     */
    private GlobalHelper $helper;

    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    /**
     *
     * @var H48Helper
     */
    private H48Helper $h48_helper;

    /**
     *
     * @var RenderHelper
     */
    private RenderHelper $render_helper;

    /**
     *
     * @var MailerHelper
     */
    private MailerHelper $mailer_helper;

    /**
     *
     * @var TwigHelper
     */
    private TwigHelper $twig_helper;

    /**
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    public function __construct(GlobalHelper $helper, ApiHelper $api_helper, H48Helper $h48_helper, RenderHelper $render_helper, MailerHelper $mailer_helper, TwigHelper $twig_helper, EntityManagerInterface $em) {
        $this->helper = $helper;
        $this->api_helper = $api_helper;
        $this->h48_helper = $h48_helper;
        $this->render_helper = $render_helper;
        $this->mailer_helper = $mailer_helper;
        $this->twig_helper = $twig_helper;
        $this->em = $em;
    }
    /**
     * @Route(
     *     name="registration_event_candidate",
     *     path="/event/{id}/participation",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Event::class,
     *         "_api_collection_operation_name"="registration_event_candidate"
     *     }
     * )
     */
    public function __invoke(Event $id, Request $request)
    {
        $user = $this->getUser();
        $participation = new CandidateParticipation();
        $participation->setEvent($id);
        $user->addCandidateParticipation($participation);
        if ($this->h48_helper->is48($id) && $this->h48_helper->getSecond48($id)) {
            $second_event = $this->h48_helper->getSecond48($id);
            $second_participation = clone($participation);
            $second_participation->setEvent($second_event);
            $user->addCandidateParticipation($second_participation);
        }
        if($id->getType()->getId() != 2){
            $status = $this->em->getRepository(Status::class)->findOneBySlug('confirmed');
            $participation->setStatus($status);
        }

        $this->em->persist($user);
        $this->em->flush();

        if ($participation->getStatus()->getSlug() == 'confirmed') {
            $this->helper->generateQrCode($participation);
            $this->render_helper->generateInvitation($participation);
            $invitation_path = $participation->getInvitationPath();
            //h48
            if (isset($second_participation)) {
                $this->helper->generateQrCode($second_participation);
                $this->render_helper->generateInvitation($second_participation);
                //dump($participation);die();
                //send invitation
                $secondInvitation_path = $second_participation->getInvitationPath();
            }
            //end h48
            if ($this->twig_helper->fileExists($invitation_path)) {
                $this->mailer_helper->sendInvitation($participation);
            }

//            if ($this->twig_helper->fileExists($secondInvitation_path)) {
//                $this->mailer_helper->sendInvitation($second_participation);
//            }
            //end send invitation
        }
    }
}