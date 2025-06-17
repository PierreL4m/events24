<?php

namespace App\Tests\Controller;

use App\Entity\Place;
use App\Tests\AuthentificationHelper;

class PlaceControllerTest extends AuthentificationHelper
{
    public function place($path, $name, $adress, $cp)
    {
        //I request the page to add a place and I check that it is valid
        $crawlerRequest = $this->client->request('GET', $path);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawlerRequest->selectButton('save')->form();

        $form['place[name]'] = $name . time();
        $form['place[nameMobile]'] = "Test lieu" ;
        $form['place[address]'] = $adress . time();
        $form['place[cp]'] = $cp;
        $form['place[city]'] = "Test ville";
        $form['place[slug]'] = "Lille";
        $form['place[latitude]'] = "";
        $form['place[longitude]'] = "";
        $form['place[colors][0][name]'] = "color_1";
        $form['place[colors][0][code]'] = "#f47b21";

        $this->client->submit($form);
        $this->redirectAndAssertion('td:contains(' . $name . ')');
    }

    public function testIndexPlace()
    {
        $crawler = $this->client->request('GET', '/admin/place/');

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Lieux', $crawler->filter('h1')->text());
    }

    public function testNewPlace()
    {
        $this->place('/admin/place/new', "Test ajout lieu", '6 rue de la Loire', '59120');
    }

    /**
     * @depends testNewPlace
     */
    public function testEditPlace()
    {
        //I get the last place modify in the database with the previous function
        $place = $this->em->getRepository(Place::class)->findOneByIdDesc();

        $this->place('/admin/place/' . $place->getId() . '/edit', "Test de modification lieu", '6 rue de la Loire', '59120');
    }

    /**
     * @depends testNewPlace
     */
    public function testShowPlace()
    {
        //I get the last place add in the database with the previous function
        $place = $this->em->getRepository(Place::class)->findOneByIdDesc();

        $crawler = $this->client->request('GET', '/admin/place/' . $place->getId());

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($crawler->filter('#place_id')->text(), $place->getId());
    }

    /**
     * @depends testEditPlace
     */
    public function testDeletePlace()
    {
        //I get the last place modify in the database with the previous function
        $place = $this->em->getRepository(Place::class)->findOneByIdDesc();

        $this->client->request('GET', '/admin/place/delete/' . $place->getId());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("Le lieu a été supprimé")')->count());
    }

    public function redirectAndAssertion($result)
    {
        $crawler = $this->client->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter($result)->count());
    }
}
