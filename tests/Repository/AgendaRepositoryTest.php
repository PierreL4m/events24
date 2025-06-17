<?php

//namespace App\Tests\Repository;
//
//use App\Entity\Agenda;
//use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
//
//class AgendaRepositoryTest extends KernelTestCase 
//{
//    /**
//     * @var \Doctrine\ORM\EntityManager
//     */
//    private $entityManager;
//    
//    /**
//     * {@inheritDoc}
//     */
//    protected function setUp() :void
//    {
//        $kernel = self::bootKernel();
//
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//    }
//    
//    public function testFindLastAgendaInTable() 
//    {
//        $lastAgenda = $this->entityManager->getRepository(Agenda::class)->findOneByIdDesc();
//        
//    }
//    
//    /**
//     * {@inheritDoc}
//     */
//    protected function tearDown() :void
//    {
//        parent::tearDown();
//
//        $this->entityManager->close();
//        $this->entityManager = null; // avoid memory leaks
//    }
//}

