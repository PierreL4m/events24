<?php
namespace App\Controller\Api;

use App\Entity\CandidateParticipation;
use App\Entity\Status;
use App\Helper\GlobalHelper;
use App\Helper\H48Helper;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use App\Helper\TwigHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\ApiHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Exception\CustomApiException;
use ApiPlatform\Core\Util\RequestParser;
use App\Entity\Accreditation;
use App\Entity\Participation;

class PostAddAccred extends AbstractController
{
    /**
    *
    * @var ApiHelper
    */
    private ApiHelper $api_helper;

    /**
     *
     * @var RenderHelper
     */
    private RenderHelper $render_helper;

    /**
     *
     * @var TwigHelper
     */
    private TwigHelper $twig_helper;

    /**
     *
     * @var MailerHelper
     */
    private MailerHelper $mailer_helper;

    public function __construct(ApiHelper $api_helper, RenderHelper $render_helper, TwigHelper $twig_helper, MailerHelper $mailer_helper) {
        $this->api_helper = $api_helper;
        $this->render_helper = $render_helper;
        $this->twig_helper = $twig_helper;
        $this->mailer_helper = $mailer_helper;
    }
    /**
     * @Route(
     *     name="accreditation_add",
     *     path="/accreditation/add/{id}",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Participation::class,
     *         "_api_collection_operation_name"="accreditation_add"
     *     }
     * )
     */
    public function __invoke(Participation $id, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $datas = json_decode($request->getContent());
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        $errors = [];
        if($datas->firstname == null){
            $errors['firstnameError'] = "Merci de renseigner un Prénom";
        }
        if($datas->lastname == null){
            $errors['lastnameError'] = 'Merci de renseigner un Nom';
        }
        if($datas->phone == null){
            $errors['phoneError'] =  'Merci de renseigner un numéro de téléphone';
        }
        if(strlen($datas->phone) != 10){
            $errors['phonelenError'] =  'Votre numéro de téléphone doit comporter 10 caractères';
        }
        if($datas->email == null){
            $errors['mailError'] =  'Merci de renseigner un mail';
        }

        if(!preg_match($pattern, $datas->email) ){
            $errors['mailErrorFormat'] =  'Merci de renseigner un mail valide';
        }
        if($errors != null){
            return $errors;
        }
        $accreditation = new Accreditation;
        $accreditation->setFirstname($datas->firstname);
        $accreditation->setLastname($datas->lastname);
        $accreditation->setPhone($datas->phone);
        $accreditation->setEmail($datas->email);
        $accreditation->setParticipation($id);
        $accreditation->setEvent($id->getEvent());
        $em->persist($accreditation);
        $em->flush();


        $this->render_helper->generateAccreditation($accreditation);
        if ($this->twig_helper->fileExists($accreditation->getAccreditationPath())) {
            $this->mailer_helper->sendAccreditation($accreditation);
        }
        return $accreditation;
    }
}