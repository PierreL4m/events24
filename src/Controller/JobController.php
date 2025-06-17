<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Participation;
use App\Form\ImportJobType;
use App\Form\JobType;
use App\Repository\JobRepository;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/job")
 */
class JobController extends AbstractController
{
    /**
     * @Route("/", name="job_index", methods="GET")
     */
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', ['jobs' => $jobRepository->findAll()]);
    }

    /**
     * @Route("/new/{id}", name="job_new", methods="GET|POST")
     */
    public function new(Participation $participation, Request $request, CityRepository $cr): Response
    {

        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(JobType::class);
        $form->handleRequest($request);
        $city = $cr->findById($form->get('city_id')->getData());
        if ($form->isSubmitted() && $form->isValid()) {
            $class = '\App\Entity\\Job';
            $job = new $class;
            $job->setName($form->get('name')->getData());
            $job->setPresentation($form->get('presentation')->getData());
            $job->setJobType($form->get('jobType')->getData());
            $job->setContractType($form->get('contractType')->getData());
            $job->setParticipation($participation);
            $job->setOfferType($form->get('offerType')->getData());
            $job->setTimeContract($form->get('timeContract')->getData());
            $job->setCity($city);
            $job->setOrganization($participation->getOrganization());
            $em->persist($job);

            $em->persist($participation);
            $em->flush();
            $this->addFlash('success', 'L\'offre à été ajouté');

            if($this->getUser()->hasRole('ROLE_ORGANIZATION'))
            {
                return $this->redirectToRoute('exposant_jobs_list_show', ['id' => $participation->getId()]);
            }else{
                return $this->redirectToRoute('exposant_jobs_list_show_admin', ['id' => $participation->getId()]);
            }
        }

        return $this->render('job/new.html.twig', [
            'participation' => $participation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/liste/{id}", name="exposant_jobs_list_show")
     */
    public function showJobsListAction(Participation $participation, JobRepository $jr): Response
    {
        $jobs = $jr->findByParticipation($participation);
        return $this->render('job/show_list.html.twig', ['participation' => $participation, 'jobs' => $jobs]);
    }

    /**
     * @Route("/voir/{id}", name="job_show", methods="GET")
     */
    public function show(Job $job): Response
    {
        return $this->render('job/show.html.twig', ['job' => $job]);
    }

    /**
     * @Route("/edit/{id}", name="job_edit", methods="GET|POST")
     */
    public function edit(Request $request, Job $job, CityRepository $cr): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);
        $city = $cr->findById($form->get('city_id')->getData());
        if ($form->isSubmitted() && $form->isValid()) {
            $job->setName($form->get('name')->getData());
            $job->setPresentation($form->get('presentation')->getData());
            $job->setJobType($form->get('jobType')->getData());
            $job->setContractType($form->get('contractType')->getData());
            $job->setOfferType($form->get('offerType')->getData());
            $job->setTimeContract($form->get('timeContract')->getData());
            $job->setCity($city);
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();
            return $this->render('job/show.html.twig', ['job' => $job]);
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}/{id_participation}", name="job_delete", methods="GET")
     */
    public function delete(Request $request, Job $job, JobRepository $jr): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($job);
        $em->flush();
        $this->addFlash('notice', 'L\'offre a été supprimée');
        $jobs = $jr->findByParticipation($job->getParticipation());
        return $this->render('job/show_list.html.twig', ['participation' => $job->getParticipation(), 'jobs' => $jobs]);
    }

    /**
     * @Route("/import/{id}", name="job_import", methods="GET|POST")
     */
    public function importJob(Request $request, Participation $participation): Response
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ImportJobType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $selected_jobs = $form->get('jobs')->getData();
            foreach ($selected_jobs as $selected_job) {
                $class = '\App\Entity\Job';
                $job = new $class;
                $job->setName($selected_job->getName());
                $job->setParticipation($participation);
                $job->setPresentation($selected_job->getPresentation());
                $job->setContractType($selected_job->getContractType());
                $job->setJobType($selected_job->getJobType());
                $job->setOfferType($selected_job->getOfferType());
                $job->setTimeContract($selected_job->getTimeContract());
                $job->setCity($selected_job->getCity());
                $job->setOrganization($selected_job->getOrganization());
                $em->persist($job);
            }
            $em->flush();
            $this->addFlash('success', 'Les offres ont été ajoutées');
            if($this->getUser()->hasRole('ROLE_ORGANIZATION'))
            {
                return $this->redirectToRoute('exposant_jobs_list_show', ['id' => $participation->getId()]);
            }else{
                return $this->redirectToRoute('exposant_jobs_list_show_admin', ['id' => $participation->getId()]);
            }
        }

        return $this->render('job/import_job.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
