<?php
namespace App\Command;

use App\Entity\Candidate;
use App\Entity\CandidateParticipation;
use App\Entity\CandidateUser;
use App\Entity\Event;
use App\Helper\MailerHelper;
use App\Helper\RenderHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\GlobalHelper;
use App\Helper\GlobalEmHelper;
use App\Entity\User;

class MigrateCandidatesSimpleToUser extends Command
{
    protected static $defaultName = 'migrate:candidatestouser';

    /**
     * 
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * 
     * @var GlobalEmHelper
     */
    private $helper;

    public function __construct(EntityManagerInterface $em, GlobalEmHelper $helper)
    {
        parent::__construct();
        $this->em = $em;
        $this->helper = $helper;
    }


    protected function configure()
    {
        $this
            ->setDescription('Automatically create persistent account for "old" candidates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        
        // load candidates
        /**
         * 
         * @var Candidate[] $candidates_simple
         */
        $candidates_simple = $this->em->getRepository(Candidate::class)->findBy([/*'email' => 'amli@lmwindpower.com'*/], ['email' => 'ASC', 'event' => 'ASC']);
        $repo_candidate = $this->em->getRepository(CandidateUser::class);
        $repo_user = $this->em->getRepository(User::class);
        $candidate = null;
        $create_candidate = false;
        $nb_participations = $global_nb_partitipcations = $nb_candidates = 0;
        foreach($candidates_simple as $candidate_simple) {
            // next candidate
            if(!$candidate || $candidate->getEmail() != $candidate_simple->getEmail()) {
                if($candidate) {
                    $nb_candidates++;
                    $this->em->persist($candidate);
                    $this->em->flush();
                    $io->text($nb_participations.' participations added for '.$candidate->getEmail());
                }
                
                $nb_participations = 0;
                if(!($candidate = $repo_candidate->findOneByEmail($candidate_simple->getEmail())) && !($candidate = $repo_candidate->findOneByEmailCanonical($candidate_simple->getEmail())) ) {
                    if($repo_user->findByEmail($candidate_simple->getEmail())) {
                        continue;
                    }
                    $io->text('Create '.$candidate_simple->getEmail());
                    $candidate = new CandidateUser();
                    $candidate->setEmail($candidate_simple->getEmail());
                    $candidate->setFirstname($candidate_simple->getFirstname());
                    $candidate->setLastname($candidate_simple->getLastname());
                    $create_candidate = true;
                }
            }
            
            
            if($create_candidate) {
                $candidate->setMailingEvents($candidate_simple->isMailingEvents());
                $candidate->setMailingRecall($candidate_simple->isMailingRecall());
                $candidate->setPhoneRecall($candidate_simple->isPhoneRecall());
                $candidate->setPhone($candidate_simple->getPhone());
                $this->helper->generateUsername($candidate, $this->em);
                $candidate->setEnabled(true);
                $candidate->setPassword(uniqid());
            }
            
            $participation = new CandidateParticipation();
            $participation->setEvent($candidate_simple->getEvent());
            $participation->setInvitationPath($candidate_simple->getInvitationPath());
            $candidate->addCandidateParticipation($participation);
            $global_nb_partitipcations++;
            $nb_participations++;
        }
        $io->success('all done : '.count($candidates_simple).' old users found, '.$nb_candidates.'accounts create, '.$global_nb_partitipcations.' participations created');
    }
}
