<?php

namespace App\Controller;

use App\Entity\ExposantScanUser;
use App\Entity\Organization;
use App\Entity\Participation;
use App\Entity\Event;
use App\Form\OrganizationType;
use App\Form\SearchFieldType;
use App\Helper\GlobalEmHelper;
use App\Helper\GlobalHelper;
use App\Helper\ParticipationHelper;
use App\Repository\OrganizationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/admin/organization")
 */
class OrganizationController extends AbstractController
{
    /**
     * @Route("/", name="organization_index", methods="GET|POST")
     */
    public function index(OrganizationRepository $organizationRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $form_search = $this->createForm(SearchFieldType::class, null, array('placeholder' => 'Rechercher un exposant'));
        $form_search->handleRequest($request);

        if ($form_search->isSubmitted() && $form_search->isValid()) {
            $search = $form_search->get('search')->getData();
            $query = $organizationRepository->searchQuery($search);
        } else {
            $query = $organizationRepository->findAllQuery();
        }

        $organizations = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            50/*limit per page*/
        );

        return $this->render('organization/index.html.twig', ['organizations' => $organizations, 'form_search' => $form_search->createView()]);
    }

    public function checkType($form)
    {
        $types = $form->get('organizationTypes')->getData();

        if (count($types) == 0) {
            $form->get('organizationTypes')->addError(new FormError('Vous devez renseigner le type d\'exposant'));

            return false;
        }
        return true;
    }

    /**
     * @Route("/new", name="organization_new", methods="GET|POST")
     */
    public function new(Request $request, ParticipationHelper $helper, UserPasswordHasherInterface $passwordHasher, GlobalEmHelper $globalEmHelper): Response
    {
        $organization = new Organization();
        $form = $this->createForm(OrganizationType::class, $organization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            if (!$organization->getInternalName()) {
                $organization->setInternalName($organization->getName());
            }

            if ($this->checkType($form)) {
                $events = $form->get('events')->getData();

                foreach ($events as $event) {
                    $ok = $helper->generateParticipation($event, $organization, $form);
                }

                $em->persist($organization);
                $em->flush();

                //stupid fix should be handled in eventlistener

                if (!$organization->getExposantScanUser()) {

                    $name = $organization->getName();

                    if (strlen($name) >= 10) {
                        $name = mb_substr($name, 0, 10);
                    }

                    $user = new ExposantScanUser();
                    $user->setUsername($name);
                    $name = $globalEmHelper->generateUsername($user, $em);
                    $password = GlobalHelper::random_str(6);

                    $user->setFirstname($name);
                    $user->setUsername($name);
                    $user->setLastname($name);
                    $user->setEmail($name);
                    $user->setPhone('0320202020');
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $password
                    );
                    $user->setPassword($hashedPassword);
                    $user->setPlainPassword($password);
                    $user->setEnabled(true);
                    $user->setRoles(['ROLE_EXPOSANT_SCAN']);
                    $user->setOrganization($organization);

                    $em->persist($user);
                    $em->flush();

                }
                $this->addFlash(
                    'success',
                    'L\'exposant a été créé'
                );

                return $this->redirectToRoute('organization_show', array('id' => $organization->getId()));
            }
        }

        return $this->render('organization/new.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_show", methods="GET", requirements={"id" = "\d+"})
     */
    public function show(Organization $organization, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findByOrganizationQuery($organization);

        $participations = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return $this->render('organization/show.html.twig', ['organization' => $organization, 'participations' => $participations]);
    }


    /**
     * @Route("/{id}/edit", name="organization_edit", methods="GET|POST", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, Organization $organization, GlobalEmHelper $globalEmHelper): Response
    {
        $form = $this->createForm(OrganizationType::class, $organization, array('edit' => true));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();

            if (!$organization->getExposantScanUser()) {

                $name = $organization->getName();

                if (strlen($name) >= 10) {
                    $name = mb_substr($name, 0, 10);
                }

                $user = new ExposantScanUser();
                $user->setUsername($name);
                $name = $globalEmHelper->generateUsername($user, $em);
                $password = GlobalHelper::random_str(6);

                $user->setFirstname($name);
                $user->setUsername($name);
                $user->setLastname($name);
                $user->setEmail($name);
                $user->setPhone('0320202020');
                $user->setPlainPassword($password);
                $user->setSavedPlainPassword($password);
                $user->setEnabled(true);
                $user->addRole('ROLE_EXPOSANT_SCAN');
                $user->setOrganization($organization);

                $em->persist($user);
                $em->flush();

            }

            $this->addFlash(
                'success',
                'L\'exposant a été modifié'
            );

            return $this->redirectToRoute('organization_show', ['id' => $organization->getId()]);
        }

        return $this->render('organization/edit.html.twig', [
            'organization' => $organization,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="organization_delete", methods="DELETE", requirements={"id" = "\d+"})
     */
    public function delete(Request $request, Organization $organization): Response
    {
        if ($this->isCsrfTokenValid('delete' . $organization->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $exposant_scan = $organization->getExposantScanUser();
            $em->remove($organization);
            $em->flush();
        }

        return $this->redirectToRoute('organization_index');
    }
}
