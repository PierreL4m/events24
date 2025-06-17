<?php

namespace App\Controller;

use App\Controller\Fos\ResettingController;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Entity\ExposantScanUser;
use App\Entity\ExposantUser;
use App\Entity\L4MUser;
use App\Entity\OnsiteUser;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\RecruitmentOffice;
use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\AdminType;
use App\Form\EmailFieldType;
use App\Form\ExposantScanUserType;
use App\Form\SearchFieldType;
use App\Form\UserListType;
use App\Form\UserType;
use App\Helper\FormHelper;
use App\Helper\GlobalEmHelper;
use App\Helper\GlobalHelper;
use App\Helper\MailerHelper;
use App\Repository\CandidateRepository;
use App\Repository\UserRepository;
use App\Repository\CityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\RhUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    private $helper;
    private $global_helper;

    public function __construct(GlobalHelper $helper, GlobalEmHelper $global_helper)
    {
        $this->helper = $helper;
        $this->global_helper = $global_helper;
    }

    /**
     * @Route("/{type}", name="user_index", methods="GET|POST", requirements={"type" = "l4m|L4M|exposant|rh|candidate|scan"})
     */
    public function index(UserRepository $er, Request $request, $type = null, PaginatorInterface $paginator): Response
    {
        $form_search = $this->createForm(SearchFieldType::class, null, array('placeholder' => 'Rechercher un utilisateur (Nom/Prénom/Email/Téléphone)'));
        $form_search->handleRequest($request);

        if ($form_search->isSubmitted() && $form_search->isValid()) {
            $search = $form_search->get('search')->getData();
            $query = $er->searchQuery($search);
        } else {
            $query = $er->findAllQuery($type);
        }

        $users = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            100/*limit per page*/
        );

        return $this->render('user/index.html.twig', ['users' => $users, 'form_search' => $form_search->createView()]);
    }

    /**
     * @Route("/new/{type}/{id}/{ro_id}", name="user_new", methods="GET|POST", defaults={"id"=null, "ro_id"=null})
     * @ParamConverter("ro", class="App\Entity\RecruitmentOffice", options={"id" = "ro_id"})
     */
    public function new(Request $request, $type, Participation $participation = null, RecruitmentOffice $ro = null, MailerHelper $mailer_helper, GlobalEmHelper $em_helper): Response
    {
        // l4m, exposant, rh
        $user = UserFactory::get($type);
        if ($user instanceof L4MUser == false && $user instanceof OnsiteUser == false && $user instanceof RhUser == false) {
            $user->addOrganization($participation->getOrganization());
        }
        if ($participation !== null) {
            $organization = $participation->getOrganization();
            $form_list = $this->createForm(UserListType::class, $organization);
            $form_list->handleRequest($request);
            if ($form_list->isSubmitted() && $form_list->isValid()) {
                $user_id = $form_list->get('users')->getViewData();
                return $this->redirectToRoute('user_assign', array('p_id' => $participation->getId(), 'id' => $user_id));
            }
            $form_list_view = $form_list->createView();
            if ($ro) {
                $user->setRo($ro);
            }
        }
        // to get static method ::
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //this is not supposed to happend because email @assert unique
            $exist = $this->existingUserAndSameOrganization($user, $participation);

            if ($exist) {
                $this->addFlash('danger', 'L\'utilisateur existe déjà');
                return $this->redirectToSameRoute($request);
            }
            $this->setRole($user);
            $password = $this->setPassword($user);
            $em_helper->generateUsername($user);

            if ($participation) {
                if (method_exists($user, 'addParticipation')) {
                    $user->addParticipation($participation);
                    $mailer_helper->sendParticipationMail($user, $participation, $password);
                    $participation->setFillSent(new \Datetime());
                    $em->persist($participation);
                } else {
                    throw new \Exception("Vous ne pouvez pas ajouter de participation à l'utilisateur " . $user->__toString() . " de type " . strtolower($user->getType()), 1);
                }
            } else {
                $mailer_helper->sendNewPassword($user, $password);
            }
            $mailer_helper->sendPasswordToAdmins($user, $password);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'L\'utilisateur a été créé');

            if ($participation) {
                return $this->redirectToRoute('participation_show', array('id' => $participation->getId()));
            } elseif ($ro) {
                return $this->redirectToRoute('recruitmentOffice_show', array('id' => $recruitmentOffice->getId()));
            }

            return $this->redirectToRoute('user_index', array('type' => 'l4m'));
        }
        if (isset($form_list_view)) {
            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'participation' => $participation,
                'form_list' => $form_list_view
            ]);
        } else {
            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'participation' => $participation
            ]);
        }

    }

    /**
     * @Route("/{id}/assign/{p_id}", name="user_assign", methods="GET|POST")
     * @ParamConverter("participation", class="App\Entity\Participation", options={"id" = "p_id"})
     */
    public function assign(Request $request, User $user, Participation $participation, MailerHelper $mailer_helper): Response
    {

        if (!method_exists($user, 'addParticipation')) {
            throw new \Exception("Vous ne pouvez pas ajouter de participation à l'utilisateur " . $user->__toString() . " de type " . $user->getType(), 1);
        }
        $user->addParticipation($participation);
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', $user->__toString() . ' est le nouveau responsable pour cette participation');

        return $this->redirectToRoute('participation_show', array('id' => $participation->getId()));
    }

    /**
     * @Route("/{id}/edit/{p_id}", name="user_edit", methods="GET|POST", defaults={"p_id"=null}))
     * @ParamConverter("participation", class="App\Entity\Participation", options={"id" = "p_id"})
     */
    public function edit(Request $request, User $user, Participation $participation = null, GlobalEmHelper $em_helper): Response
    {

        $first_name = $user->getFirstname();
        $last_name = $user->getLastname();
        $email = $user->getEmail();
        $em = $this->getDoctrine()->getManager();

        if (method_exists($user, 'addResponsableBis')) {
            $original_entities = $em_helper->backupOriginalEntities($user->getResponsableBises());
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        //edit form
        if ($form->isSubmitted() && $form->isValid()) {
            if ($email != $user->getEmail()) {
                $exist = $this->existingUserAndSameOrganization($user, $participation);

                if (is_string($exist)) {
                    return $this->redirectToSameRoute($request);
                }
            } else {
                if ($user->getFirstname() != $first_name || $last_name != $user->getLastname()) {
                    $em_helper->generateUsername($user);
                }
                if (method_exists($user, 'addResponsableBis')) {
                    $em_helper->removeRelation($original_entities, $user, $user->getResponsableBises(), 'removeResponsableBis', true);

                }
                $this->addFlash('success', 'L\'utilisateur a été édité');
            }
            $em->flush();

            switch (get_class($user)) {
                case ExposantUser::class:
                    if ($participation) {
                        return $this->redirectToRoute('participation_show', array('id' => $participation->getId()));
                    }
                    break;
            }

            return $this->redirectToRoute('user_index', array('type' => strtolower($user->getType())));
        }
        //end edit form
        $form_list_view = null;
        $form_search_view = null;

        if ($user instanceof ExposantUser && $participation) {

            $organization = $participation->getOrganization();
            $form_list = $this->createForm(UserListType::class, $organization);
            $form_list->handleRequest($request);

            if ($form_list->isSubmitted() && $form_list->isValid()) {
                $user_id = $form_list->get('users')->getViewData();

                return $this->redirectToRoute('user_assign', array('p_id' => $participation->getId(), 'id' => $user_id));
            }

            $form_list_view = $form_list->createView();

            $form_search = $this->createForm(EmailFieldType::class);
            $form_search->handleRequest($request);

            if ($form_search->isSubmitted() && $form_search->isValid()) {

                $email = $form_search->get('email')->getData();
                $user = $em->getRepository(User::class)->findOneByEmail($email);

                if (!$user) {
                    $this->addFlash('danger', 'Aucun compte associé à l\'email ' . $email . '. Merci de créer un nouvel utilisateur');
                } elseif (!$user instanceof ExposantUser) {
                    $this->addFlash('danger', 'Vous ne pouvez pas ajouter une participation à un utilisateur de type : ' . $user->getType() . '. Merci de lui créer un nouveau compte avec une autre adresse email');

                    return $this->redirectToRoute('user_new', array('type' => 'exposant', 'id' => $participation->getId()));
                } else {
                    $exist = $this->existingUserAndSameOrganization($user, $participation);

                    if ($exist === true) {
                        return $this->redirectToRoute('user_assign', array('p_id' => $participation->getId(), 'id' => $user->getId()));
                    }
                }
                return $this->redirectToRoute('user_new', array('type' => 'exposant', 'id' => $participation->getId()));
            }

            $form_search_view = $form_search->createView();
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form_list' => $form_list_view,
            'form_search' => $form_search_view
        ]);
    }

    /**
     * @Route("/disable/{id}", name="user_disable", methods="GET")
     */
    public function disable(Request $request, User $user): Response
    {
        // if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
        $em = $this->getDoctrine()->getManager();
        $user->setEnabled(false);
        $em->persist($user);
        $em->flush();

        $this->addFlash('notice', 'L\'utilisateur a été désactivé');
        //}

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/reset-login", name="reset_login", methods="POST")
     */
    public function resetLogin(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);
            $user->setLastLogin(null);
            $em->persist($user);
            $em->flush();
            return new JsonResponse(array('id' => $id), 200);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods="GET")
     */
    public function delete(Request $request, User $user): Response
    {
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN') && !$this->getUser()->hasRole('ROLE_ADMIN')) {
            throw new \AccessDeniedException("Vous n'avez pas accès à cette page");
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush($user);
        $this->addFlash('notice', 'L\'utilisateur a été supprimé');

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/deletebyemail/{email}", name="user_delete_by_email", methods="GET")
     * @Entity("post", expr="repository.findOneByEmail(email)")
     */
    public function deleteByEmail(Request $request, User $user): Response
    {

        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            throw new \AccessDeniedException("Vous n'avez pas accès à cette page");

        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);

        $this->addFlash('notice', 'L\'utilisateur a été supprimé');

        return $this->redirectToRoute('user_index');
    }

    public function setPassword($user)
    {
        $password = substr(hash('sha512', rand()), 0, 7);
        $password = "A" . $password;
        $user->setPlainPassword($password);
        $user->setEnabled(true);

        return $password;
    }

    public function setRole(UserInterface $user)
    {
        switch (get_class($user)) {
            case L4MUser::class:
                $user->setRoles(['ROLE_ADMIN']);
                break;
            case OnsiteUser::class:
                $user->addRole('ROLE_ONSITE');
                break;
            case RhUser::class:
                $user->addRole('ROLE_RH');
                break;
            default:
                $user->addRole('ROLE_ORGANIZATION');
                break;
        }
    }

    public function existingUserAndSameOrganization(User $user, Participation $participation = null)
    {
        $em = $this->getDoctrine()->getManager();
        $exist = $em->getRepository(User::class)->findOneByEmail($user->getEmail());

        if ($exist && !$participation) {
            return true;
        }

        if ($exist) {
            if (method_exists($exist, 'addParticipation')) {
                //check if same participation
                $last_participation = $this->getDoctrine()->getManager()->getRepository(Participation::class)->getLastParticipation($exist);

                if ($last_participation) {
                    $last_organization = $last_participation->getOrganization();
                    $organization = $participation->getOrganization();

                    if ($last_organization->getId() != $organization->getId()) {
                        $this->addFlash('danger', 'Vous ne pouvez pas ajouter une participation de ' . $last_organization->getName() . ' à un responsable de ' . $organization->getName());
                        return 'error_organization';
                    }
                }
                $exist->addParticipation($participation);
            }

            // if same organization : update user
            $exist->setFirstname($user->getFirstname());
            $exist->setLastname($user->getLastname());
            $exist->setPhone($user->getPhone());
            $exist->setPosition($user->getPosition());
            $em->persist($exist);
            $em->detach($user);

            return true;

        }

        return false;
    }

    public function redirectToSameRoute(Request $request)
    {
        $route = $request->get('_route');
        $route_params = $request->get('_route_params');

        return $this->redirectToRoute($route, $route_params);
    }

    /**
     * @Route("/candidate/{event}/{id}", name="admin_candidate_profile", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function showProfile(Event $event, CandidateUser $candidate, Request $request, GlobalHelper $helper): Response

    {

        return $this->render('candidate_user/profile.html.twig', [
            'candidate' => $candidate,
            'percentageProfilInfos' => $helper->progressProfilCandidate($candidate),
            'eventId' => $event->getId()


        ]);
    }

    /**
     * @Route("/candidate/editer-info/{event}/{id}", name="admin_candidate_user_edit", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function editCandidateProfile(Event $event, Request $request, FormHelper $form_helper, GlobalHelper $helper, CandidateUser $candidate): Response
    {

        return $helper->handleResponse($form_helper->editCandidateProfile($request, 'web', $candidate, null, $event));
    }

    /**
     * @Route("/exposant-scan/edit-password-scan/{id}", name="exposant_scan_user_edit", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function editExposantScanUser(Request $request, ExposantScanUser $user, UserPasswordHasherInterface $passwordHasher, GlobalHelper $helper): Response
    {
        $form = $this->createForm(ExposantScanUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ok = true;

            if (!$user->getUsername()) {
                $form->get('username')->addError(new FormError('L \'identifiant ne peut pas être nul'));
                $ok = false;
            }
            if (!$user->getSavedPlainPassword()) {
                $form->get('savedPlainPassword')->addError(new FormError('Le mot de passe ne peut pas être nul'));
                $ok = false;
            }
            $pwd = $user->getSavedPlainPassword();

            if (strlen($pwd) < 6) {
                $form->get('savedPlainPassword')->addError(new FormError('Le mot de passe doit contenir au moins 6 caractères'));
                $ok = false;
            }
            if (!preg_match("/([A-Z][a-z])|([a-z][A-Z])/", $pwd)) {
                $form->get('savedPlainPassword')->addError(new FormError('Le mot de passe doit contenir au moins une majuscule et une minuscule'));
                $ok = false;
            }

            if ($ok) {
                $user->setUsername($helper->generateSlugUnderscore($user->getUsername()));
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    str_replace(' ', '-', $user->getSavedPlainPassword())
                );
                $user->setPassword($hashedPassword);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash(
                    'success',
                    'L\'identifiant de connexion a été modifié'
                );

                return $this->redirectToRoute('exposant_scan_user_edit', ['id' => $user->getId()]);
            }
        }

        return $this->render('user/edit_exposant_scan.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
