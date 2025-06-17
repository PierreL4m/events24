<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class MaintenanceListener
{
   private $container, $maintenance, $ipAuthorized;
   public function __construct($maintenance, ContainerInterface $container,Environment $twig)
   {
       $this->container = $container;
       $this->maintenance = $maintenance["statut"];
       $this->ipAuthorized = $maintenance["ipAuthorized"];
       $this->twig = $twig;
   }
   public function onKernelRequest(RequestEvent $event)
   {
       // This will get the value of our maintenance parameter
       $maintenance = $this->maintenance ? $this->maintenance : false;
       $currentIP = (!empty($_SERVER) && isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
       // This will detect if we are in dev environment (app_dev.php)
       // $debug = in_array($this->container->get('kernel')->getEnvironment(), ['dev']);
       // If maintenance is active and in prod environment
       if ($maintenance AND !in_array($currentIP, $this->ipAuthorized)) {
           // We load our maintenance template
           $page = $this->twig->render('maintenance/maintenance.html.twig');
           // We send our response with a 503 response code (service unavailable)
           $event->setResponse(
               new Response(
                   $page,
                   Response::HTTP_SERVICE_UNAVAILABLE
               )
           );
           $event->stopPropagation();
       }
   }
}
