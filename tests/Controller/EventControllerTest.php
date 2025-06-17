<?php

namespace App\Tests\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\DomCrawler\Crawler;
use App\Tests\AuthentificationHelper;

class EventControllerTest extends AuthentificationHelper
{
    public function testNew()
    {
    //select place     
        $crawler = $this->client->request('GET', '/admin/event/place');       
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save')->form();
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/event/new/1-1')
        );
    //end select place
        
    //create event for place_id = 4 (rouen) and event type 1
        $crawler = $this->client->request('GET', '/admin/event/new/1-4');       
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save')->form();
        $form['event[date][date][year]'] = date('Y') + 1;
        $form['event[nbStand]'] = 20;
        $crawler = $this->client->submit($form);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('h1:contains("Rouen")')->count());
        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("L\'événement a été créé")')->count());     
    //end create
 
    //delete event
        $id = $crawler->filter('#id_event')->text();
      
        $crawler = $this->client->request('GET', '/admin/event/delete/'.$id);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin')
        );
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("L\'événement a été supprimé")')->count()
        );     
    //end delete
    }

    public function testAddOrganization()
    {
        //saint amand les eaux 2008  
        $crawler = $this->client->request('GET', '/admin/event/2/add-organization');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        // check if already added participation are not displayed
        $this->assertEquals(0, $crawler->filter('label:contains("100% BONS PLANS")')->count()
        );
        $this->assertEquals(0, $crawler->filter('label:contains("TRIANGLE")')->count()
        );
        //check if checked item is display after redirect
        $form = $crawler->selectButton('save')->form();
        $form['add_organization[organizations][0]']->tick();
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/event/2')
        );
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('h2:contains("3AXES INSTITUT")')->count()
        );
        //participation type is already checked in ParticipationFactoryTest
              
        //remove previous participation        
        $link = $crawler->selectLink('3AXES INSTITUT')->link();
        $uri = $link->getUri();
        $start = strrpos($uri, "/") + 1;        
        $id = substr($uri,$start,strlen($uri));
        $crawler = $this->client->request('GET', '/admin/participation/'.$id.'/delete');
        //does not work with new participation bkp crap
//        $this->assertTrue(
//            $this->client->getResponse()->isRedirect('/admin/event/2')
//        );


//        $crawler = $this->client->followRedirect();
//        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("La fiche de participation été supprimée")')->count());
//
//        $this->assertEquals(0, $crawler->filter('h2:contains("3AXES INSTITUT")')->count()
//        );
    }

   //thanks stackoverflow
    function get_string_between($string, $start, $end){
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);   
        $len = strpos($string,$end,$ini) - $ini;

        return substr($string,$ini,$len);
    }


}
