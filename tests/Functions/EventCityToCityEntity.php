<?php

namespace App\Tests\Functions;

use App\Entity\City;
use App\Entity\Place;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EventCityToCityEntity extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp() :void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testPlaceToCity()
    {
        $places =  $this->em->getRepository(Place::class)->findAll();
        $places_nb = count($places);
        $cities_nb = array();
        
        foreach ($places as $place) {
            $city = $place->getCity();
            $pos = strpos($city, "(");

            if($pos !== false){
                $city = substr($city,0,$pos);
            }

            $cities = $this->em->getRepository(City::class)->findByName($city);

            $this->assertCount(1, $cities);
            array_push($cities_nb, $cities[0]->getName());
        }

        $this->assertEquals($places_nb,count($cities_nb));
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