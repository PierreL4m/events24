<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateParticipationComment;
use App\Helper\FormHelper;
use App\Repository\ParticipationRepository ;
use App\Repository\CandidateParticipationCommentRepository ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\CustomApiException;
use ApiPlatform\Core\Util\RequestParser;
use App\Helper\ApiHelper;
use App\Form\Api\ScanType;

class PostScanExposant extends AbstractController
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
     *     name="post_scan_exposant",
     *     path="/exposant/scan/{id}",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=CandidateParticipation::class,
     *         "_api_collection_operation_name"="post_scan_exposant"
     *     }
     * )
     */
    public function __invoke(Request $request, CandidateParticipation $data, ApiHelper $helper, ParticipationRepository $er, CandidateParticipationCommentRepository $cr)
    {
        $user = $this->getUser();
        if($user->getType() != 'Exposant'){
            return $this->api_helper->apiException("role '".$user->getType()."' cannot see candidates for an event",['code' => Response::HTTP_FORBIDDEN, "message"=>"Forbidden"],Response::HTTP_FORBIDDEN);
        }
        
        $event_date = $data->getEvent()->getDate()->format('Ymd');
        $now = new \Datetime();

        if ($event_date != $now->format('Ymd')){
            return $this->api_helper->apiException("Le QR code ne correspond pas à l'événement du jour. Merci de demander au candidat de vous fournir le bon QR code",["message"=>"Le QR code ne correspond pas à l'événement du jour. Merci de demander au candidat de vous fournir le bon QR code"],Response::HTTP_BAD_REQUEST);
        }

        $organization_participation = $er->findByClientUserAndEvent($user, $data->getEvent());

        if (!$organization_participation){
            return $this->api_helper->apiException("Vous pouvez scanner un QR code uniquement le jour de l'événement auquel vous participez",["message"=>"Vous pouvez scanner un QR code uniquement le jour de l'événement auquel vous participez"],Response::HTTP_BAD_REQUEST);
        }

        $existing_note = $cr->findOneByParticipations($organization_participation, $data);

        if ($existing_note){
            return $existing_note;
        }

        $form = $this->createForm(ScanType::class);
        $form->submit($request->request->all());

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            /* FIXME : WTF ?!
            $scan = $form->get('scannedAt')->getData();
          	$scan = \DateTime::createFromFormat('dmYHi', $scan);
          	*/
            $comment = new CandidateParticipationComment();
            $comment->setScannedAt(new \DateTime());
            $comment->setCandidateParticipation($data);
            $comment->setOrganizationParticipation($organization_participation);
            $em->persist($comment);
            $em->flush();

            return $comment;

        }
        
        return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST);
    }
}