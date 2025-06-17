<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Tests\AuthentificationHelper;
use App\Entity\Event;
use App\Entity\EventType;

class RegisterControllerTest extends AuthentificationHelper
{


    public function testRegisterForm()
    {
        
        $event_type = $this->em->getRepository(EventType::class)->findOneBy(
            array('registrationValidation' => EventType::REGISTRATION_VALIDATION_AUTO)
        );
        
        /**
         *
         * @var Event $event
         */
        $event = $this->em->getRepository(Event::class)->getNextEventByType($event_type);
        
        $crawler = $this->client->request('GET', '/'.$event->getSlug());
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('register_action')->form();
        $last_name = uniqid();
        $first_name = uniqid();
        $local_file = __DIR__ . '/../files/pdf.pdf';

        $uploadedFile = new UploadedFile(
            $local_file,
            'large-ware-wario5e5917710c360.pdf',
            'pdf/pdf',
            null,
            true
        );
        $form['registration[lastname]'] = 'schuvey';
        $form['registration[firstname]'] = $first_name;
        $form['registration[email]']->setValue("pierreschuvey@gmail.com");
        $form['registration[phone]']->setValue("0676716137");
        $form['registration[file]'] = $local_file;
        $form['registration[slots]']->select($form['registration[slots]']->availableOptionValues()[0]);
        $form['registration[mailingRecall]']->select(1);
        $form['registration[mailingEvents]']->select(1);
        $form['registration[phoneRecall]']->select(1);

        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);
        die("STATUS ".$this->client->getResponse()->getStatusCode()."::".$this->client->getResponse()->headers->get('location')."\n");
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin-recruteurs/candidates-list/'.$event->getId())
        );
    }
}
