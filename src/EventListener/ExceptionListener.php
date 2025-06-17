<?php

namespace App\EventListener;

use App\Helper\MailerHelper;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ExceptionListener
{
    private $mailer;
    private $token_storage;

    public function __construct(MailerHelper $mailer, TokenStorageInterface $token_storage)
    {
        $this->mailer = $mailer;
        $this->token_storage = $token_storage;
    }
    public function onKernelException(ExceptionEvent $event)
    {

        if (strpos($event->getRequest()->getRequestUri(),'/api/') !== false){
            return new Response($event->getThrowable()->getMessage());
        }
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $message = $exception->getMessage();
        $readable = $error_code = $timeout = null;

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $message = $message."<br>".json_encode($exception->getHeaders());
            $error_code = $exception->getStatusCode();

        }
        elseif ($exception instanceof UniqueConstraintViolationException){
            $duplicate_msg = $exception->getPrevious()->errorInfo[2];

            $duplicate_msg = str_replace('Duplicate entry', 'Vous ne pouvez pas utiliser deux fois la valeur :', $duplicate_msg);

            $pos = strpos($duplicate_msg,'for');
            $readable = substr($duplicate_msg,0,$pos);

        }
        elseif ($exception instanceof ProcessTimedOutException){

            $error_code = 500;
//$event->getRequest()->getSchemeAndHttpHost();
            //die();
            if(strpos($message, 'convert') !== false){
                $readable = 'Nous sommes navrés mais nous n\'avons pas réussi à convertir automatiquement votre image dans un format web valide. Merci de réesayer avec une image au format JPG';
            }
        }
        else {
            $error_code = 500;
        }

        $excludes = [
            '/uploads/'
        ];

        $send = true;

        foreach ($excludes as $exclude) {
            if ($event->getRequest()->getMethod() == 'GET' && strpos($event->getRequest()->getRequestUri(), $exclude) !== false){
                $send = false;
            }
            elseif ($event->getRequest()->getMethod() == 'HEAD'){
                $send = false;
            }
            //elseif
            //request.headers.get('user-agent')}}
        }

        if ($send){
            if ($this->token_storage && $this->token_storage->getToken()){
                $user = $this->token_storage->getToken()->getUser();
            }
            else{
                $user = null;
            }
            /*$this->mailer->sendMail("monitoring@l4m.fr", "error admin events",'error',array(
                    'readable' => $readable,
                    'error' => $message,
                    'error_code' => $error_code,
                    'exception' => $exception,
                    'request' => $event->getRequest(),
                    'user' => $user
                )
            );*/
        }
        if ($timeout){
            //return new RedirectResponse('http://your.location.com');
        }
        $event->stopPropagation();

         // Customize your response object to display the exception details
        $response = new Response();

        $view = $this->mailer->templating->render(
                    'bundles/TwigBundle/Exception/error.html.twig', array(
                        'readable' => $readable,
                        'error' => $message,
                        'error_code' => $error_code,
                        'exception' => $exception,
                        'request' => $event->getRequest()
                    )
                );

        $response->setContent($view);
         // sends the modified response object to the event
        $event->setResponse($response);
    }
}
