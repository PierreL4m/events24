<?php

namespace App\tests\Controller;

use App\Entity\EventType;
use App\Tests\AuthentificationHelper;

class EventTypeControllerTest extends AuthentificationHelper
{
    public function eventType($path, $name, $shortname)
    {
        //I request the page to add a event type and I check that it is valid
        $crawlerRequest = $this->client->request('GET', $path);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawlerRequest->selectButton('save')->form();

        $form['event_type[fullName]']->setValue($name);
        $form['event_type[shortName]']->setValue($shortname);
        $form['event_type[analyticsId]']->setValue("test");
        $form['event_type[mandatoryRegistration]']->select(0);
        $form['event_type[registrationType]']->select(1);
        $form['event_type[registrationValidation]']->select(1);
        $form['event_type[registrationJoblinks]']->select(1);
        $form['event_type[host]']->select(1);
        $form['event_type[displayParticipationContactInfo]']->select(0);
        $form['event_type[recruitmentOfficeAllowed]']->select(1);

        $this->client->submit($form);

        //I check that there is at least one entry that has a <p> that contains the name of the title I just added
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/event-type/')
        );
    }
    public function testNewEventType() 
    {
        $this->eventType('/admin/event-type/new', "Test de l'ajout d'un type event" . time(), "Test" . time());
    }
        
    /**
     * @depends testNewEventType
     */
    public function testEditEventType()
    {
        $eventType = $this->em->getRepository(EventType::class)->findOneByIdDesc();

        $this->eventType('/admin/event-type/edit/' . $eventType->getId(), "Test de la modification d'un type event" . time(), "TestModif" . time());
    }
        
    /**
     * @depends testEditEventType
     */      
    public function testDeleteEventType() 
    {
        //I get the last event type modify in the database with the previous function
        $eventType = $this->em->getRepository(EventType::class)->findOneByIdDesc();

        $this->client->request('GET', 'admin/event-type/delete/' . $eventType->getId());
        
        $this->client->followRedirect();
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
