<?php
namespace App\Security;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use function PHPUnit\Framework\throwException;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use League\Bundle\OAuth2ServerBundle\Security\User\NullUser;

class ApiSessionAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private SessionInterface $session;
    private TokenStorageInterface $tokenStorage;
    private Security $security;

    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session, UserRepository $userRepository, Security $security)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        // return $request->cookies->has('authApiUser');
        // die('supports ?'.$request->getUri().$request->getMethod());
        return ((!$request->headers->has('Authorization') || false === stripos($request->headers->get('Authorization'), 'bearer')) && $request->cookies->has('_jobflix'));
    }

    public function authenticate(Request $request): PassportInterface
    {
        // current client MUST have the initial _jobflix cookie 
        $this->session->start();
        // si on a un cookie _jobflix identique à l'id de session alors on est bon
        // _jobflix cookie MUST match session 
        if(!$this->session->has('jobflix_session_identifier') || $request->cookies->get('_jobflix') != $this->session->get('jobflix_session_identifier')) {
            throw new CustomUserMessageAuthenticationException('No user found');
        }
        
        // let's check that the joblix_useridentifier in session can be loaded
        if(($identifier = $this->session->get('jobflix_useridentifier')) && ($user = $this->userRepository->loadUserByIdentifier($identifier))) {
            return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
        }
        
        // anonymous access
        $userLoader = function (string $userIdentifier): UserInterface {
            return new NullUser();
        };
        return new SelfValidatingPassport(new UserBadge('', $userLoader));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}