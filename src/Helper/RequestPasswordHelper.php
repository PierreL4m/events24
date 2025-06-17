<?php

namespace App\Helper;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Helper\ApiHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Symfony\Component\Mime\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


class RequestPasswordHelper
{
    use ResetPasswordControllerTrait;

    /**
     *
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $form_factory;

    /**
     *
     * @var ApiHelper
     */
    private ApiHelper $api_helper;

    private $resetPasswordHelper;
    private $entityManager;

    public function __construct(FormFactoryInterface $form_factory,ResetPasswordHelperInterface $resetPasswordHelper, EntityManagerInterface $entityManager, ApiHelper $api_helper)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->entityManager = $entityManager;
        $this->form_factory = $form_factory;
        $this->api_helper = $api_helper;
    }

    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->form_factory->create(ResetPasswordRequestFormType::class, ['context' => 'api']);
        $form->submit($request->request->all());

        if ($form->isSubmitted()) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'email' => $form->get('email')->getData(),
            ]);

            // Do not reveal whether a user account was found or not.
            if (!$user) {
                $form->get('email')->addError(new FormError('Pas d\'utilisateur avec cet email'));
                return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST);
            }


            try {
                $resetToken = $this->resetPasswordHelper->generateResetToken($user);
            } catch (ResetPasswordExceptionInterface $e) {
                // If you want to tell the user why a reset email was not sent, uncomment
                // the lines below and change the redirect to 'app_forgot_password_request'.
                // Caution: This may reveal if a user is registered or not.
                //
                // $this->addFlash('reset_password_error', sprintf(
                //     'There was a problem handling your password reset request - %s',
                //     $e->getReason()
                // ));
                die($e->getReason());
                $form->get('email')->addError(new FormError('Vous avez déjà demandé un nouveau mot de passe. N\'oubliez pas de vérifier dans vos spam !'));
                return $this->api_helper->formException($form, Response::HTTP_BAD_REQUEST , []);
            }

            $email = (new TemplatedEmail())
                ->from(new Address('pierre.schuvey@l4m.fr', 'L4M'))
                ->to($user->getEmail())
                ->subject('Réinitialisation de votre mot de passe pour les salons emploi L4M')
                ->htmlTemplate('reset_password/email.html.twig')
                ->context([
                    'resetToken' => $resetToken,
                ])
            ;

            $mailer->send($email);

            $response = new JsonResponse("Resetting email sent");
            return $response;
        }

        return $this->api_helper->apiException("patch cannot be used to register a new user");
    }
}
