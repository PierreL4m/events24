<?php

namespace App\Command;

use App\Entity\Place;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCoordinatesCommand extends Command
{
    protected static $defaultName = 'fix:coordinates';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('set missing coordinates')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $places = $this->em->getRepository(Place::class)->findAll();

        foreach ($places as $place) {
            if ($place->getLatitude() == 0){
                $io->text($place);
                $this->setCoordinates($place);
                $io->text($place->getLatitude());
            }
        }
        $this->em->flush();
        $io->success('Coordinates set');
    }


    public function setCoordinates($entity)
    {
       
        $address = $entity->getName()."+".$entity->getAddress()."+".$entity->getCp()."+".$entity->getCity();
        $address = str_replace(" ", "%20", $address); // replace all the white space with "%20" sign to match with mapbox search pattern
 
        $url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$address.'.json?limit=1&access_token=pk.eyJ1IjoiZnJhbmNlLWJlbm9pdC1sNG0iLCJhIjoiY2pub3J6cHZxMXlxdDNwczVxcWloMWZvZyJ9.ni7lkWwRO2g1rX2MQhLLfw';
        
        $response = file_get_contents($url);
        $json = json_decode($response,TRUE); //generate array object from the response from the web
        
      
        if (array_key_exists(0, $json['features'])){
            $coordinates = $json['features'][0]['center'];
            $entity->setLatitude($coordinates[1]);
            $entity->setLongitude($coordinates[0]);
        }
        else{
            $entity->setLatitude(0);
            $entity->setLongitude(0);
        }
    }
}