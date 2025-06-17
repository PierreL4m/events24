<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\JobRepository;

/**
 * @Route("/flux")
 */
class FluxController extends AbstractController
{
    /**
     * @Route("/jobl4m", defaults={"_format"="xml"}))
     */
    public function getFluxL4mJobs(Request $request, JobRepository $jr): Response
    {
        $jobs = $jr->getJobsFlux();
        $host = $request->getHost();
        $format = $request->getRequestFormat();
        return $this->render('flux/jobs.xml.twig', ['jobs' => $jobs, 'host' => $host]);
    }
}