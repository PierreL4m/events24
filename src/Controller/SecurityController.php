<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class SecurityController extends AbstractController
{

    /**
     * @Route("/auth/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
    
    /**
     * @Route("/auth/apilogin", name="json_login", methods={"GET|POST"})
     */
    public function apiLogin(): Response
    {
        $user = $this->getUser();
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
                ], 
                Response::HTTP_UNAUTHORIZED
            );
        }
        
        return $this->json(array('status' => 'loggedin'));
    }

   /**
    * @Route("/auth/login", name="app_login", methods={"GET|POST"})
    */
   public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
   {
       $error = $authenticationUtils->getLastAuthenticationError();
       $lastUsername = $authenticationUtils->getLastUsername();
       /*
       if($request->get('grant_type')) {
           die('pouzetkl');
           return new JsonResponse();
       }
       
       return new JsonResponse(array('ouaip' => $lastUsername, 'error' => $error));
       */
       return $this->render('jobflix/login.html.twig');
       
       die($error);
//
//        return $this->render('@EasyAdmin/page/login.html.twig', [
//            // parameters usually defined in Symfony login forms
//            'error' => $error,
//            'last_username' => $lastUsername,
//
//            // OPTIONAL parameters to customize the login form:
//
//            // the translation_domain to use (define this option only if you are
//            // rendering the login template in a regular Symfony controller; when
//            // rendering it from an EasyAdmin Dashboard this is automatically set to
//            // the same domain as the rest of the Dashboard)
//
//            // the title visible above the login form (define this option only if you are
//            // rendering the login template in a regular Symfony controller; when rendering
//            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
//            'page_title' => 'Jobflix',
//
//            // the string used to generate the CSRF token. If you don't define
//            // this parameter, the login form won't include a CSRF token
//
//            // the URL users are redirected to after the login (default: '/admin')
//
//            // the label displayed for the username form field (the |trans filter is applied to it)
//            'username_label' => 'Nom d\'utilisateur / Mail',
//
//            // the label displayed for the password form field (the |trans filter is applied to it)
//            'password_label' => 'Mot de passe',
//
//            // the label displayed for the Sign In form button (the |trans filter is applied to it)
//            'sign_in_label' => 'Connexion',
//        ]);
   }

    /**
     * @Route("/check/token", name="check_token")
     */
    public function checkToken()
    {
        $data = ['email'=>$this->getUser()->getEmail()];
        return new JsonResponse($data);
    }

    /**
     * @Route("/check/secure_area", name="secure_area")
     */
    public function indexAction()
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('public_index');
        }
        if($this->getUser()->hasRole('ROLE_ADMIN') || $this->getUser()->hasRole('ROLE_VIEWER') || $this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            return new RedirectResponse($this->generateUrl('admin_index'));
        }elseif($this->getUser()->hasRole('ROLE_CANDIDATE')){
            return $this->redirect($this->generateUrl('candidat_profile'));
        }elseif($this->getUser()->hasRole('ROLE_ORGANIZATION')|| $this->getUser()->hasRole('ROLE_EXPOSANT_SCAN')){
            return $this->redirect($this->generateUrl('exposant_index'));
        }elseif($this->getUser()->hasRole('ROLE_ONSITE')){
            return $this->redirect($this->generateUrl('on_site'));
        }elseif($this->getUser()->hasRole('ROLE_SCAN')){
            return $this->redirect($this->generateUrl('scan_index'));
        }
    }
}
