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

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private UserPasswordHasherInterface $passwordHasher;
    private AccessTokenManagerInterface $accessTokenManager;
    private UserRepository $userRepository;


    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, AccessTokenManagerInterface $accessTokenManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->accessTokenManager = $accessTokenManager;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Token');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $apiToken = $request->headers->get('Token');
        $email = $request->headers->get('username');
        $password = $request->headers->get('password');
        $tokenParts = explode(".", $apiToken);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtPayload = json_decode($tokenPayload);
        $jti = $jwtPayload->jti;

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

//        return new Passport(new UserBadge($email), new PasswordCredentials($password));

        return new Passport(new UserBadge($email),
            new CustomCredentials(
                function ($credentials, $user) {
                    if(!$this->passwordHasher->isPasswordValid($user,$credentials['password'])){
                        throw new \Exception('Erreurs d\'identifiants');                    }
                    if(!($user->getEmail() === $this->accessTokenManager->find($credentials['jti'])->getUserIdentifier())){
                        throw new \Exception('Le token ne correspond pas à l\'utilisateur courant');
                    }
                    return true;
                },$credentials = ["mail"=> $email, "jti" => $jti, "password" => $password]
            )
        );
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