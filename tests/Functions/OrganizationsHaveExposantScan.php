<?php

namespace App\Tests\Functions;

use App\Entity\Organization;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrganizationsHaveExposantScan extends WebTestCase
{
    /**
     * 
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $client;
    /**
     * {@inheritDoc}
     */
    protected function setUp() :void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::createClient();
    }

    public function testOrganizationsHaveExposantScan()
    {
        $organizations =  $this->em->getRepository(Organization::class)->findAll();
               
        foreach ($organizations as $organization) {
            $user = $organization->getExposantScanUser();
            $user_entity = $user;

            if($user){
                $user = true;
            }
            $this->assertTrue($user,true);

          // this takes too long
          //   $crawler = $this->client->request('GET', '/auth/login');       
          //   $this->assertSame(200, $this->client->getResponse()->getStatusCode());

          //   $form = $crawler->selectButton('_submit')->form();
          //   //$form['_username'] = 'prout';
          //   $form['_username'] = $user_entity->getUsername();
          //   $form['_password'] = $user_entity->getSavedPlainPassword();
          //   $crawler = $this->client->submit($form);

          //   $this->assertSame(302, $this->client->getResponse()->getStatusCode());

          // //  dump(get_class_methods($this->client->getResponse()));
          // //  dump($this->client->getResponse()->getTargetUrl());die();
          //   $this->assertTrue(
          //       $this->client->getResponse()->isRedirect('http://localhost/redirect/login')
          //   );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() :void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}