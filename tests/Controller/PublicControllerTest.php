<?php

namespace App\Tests\Controller;

use App\Entity\Event;
use App\Entity\EventJobs;
use App\Entity\EventType;
use App\Tests\AuthentificationAndFilesHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\Slots;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Mime\Email;

class PublicControllerTest extends AuthentificationAndFilesHelper
{

    use MailerAssertionsTrait;

    public function testRegistration()
    {
        $this->goAnonymous();

        $asserts_first = ['#registration_lastname','#registration_firstname','#registration_email','#registration_phone'];
        $asserts_edit =  ['#registration_file'] ;
        $asserts_edit_spec =  ['#registration_sectors','#registration_mobility','#registration_degree','#registration_city_name'] ;
        //$asserts_spec = ['Vous devez choisir une option','secteur'] ;
        $asserts_spec = [];
        //$asserts_edit_spec = [];
        $asserts = array_merge($asserts_first,$asserts_edit,$asserts_spec);
        $last_events = $this->em->getRepository(Event::class)->findCurrentEvents();
        $time = time();
        $i = 0;
        
        do{
            if(!$last_events[$i]->ishas_slots()) {
                $slug = $last_events[$i]->getSlug();
                $event = $this->em->getRepository(Event::class)->findOneBySlug($slug);
            }
            $i++;
        }
        while ( (is_null($event) || ($event instanceof EventJobs && !empty($event->getRegistrationLimit()) && $event->getRegistrationLimit()->getTimestamp() < $time) || !$event->getType()->registrationValidationAuto()) && $i < count($last_events)-1 );


        if(is_null($event) || !$event->getType()->registrationValidationAuto()){
            throw new \Exception('cannot test simple event registration. there is no current simple event');
        }

        $this->setHost($event->getType()->getHost()->getName());

        $crawler = $this->client->request('GET', '/'.$slug);
        
//         die($this->client->getResponse()->getContent()."\n");
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $email = uniqid().'@l4m.fr';
        $pwd = 'Test1234' ;

        $form = $crawler->selectButton('register_action')->form();

        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        $form['registration[lastname]'] = 'lastname';
        // on "unset" '#registration_lastname' puisqu'on la renseigné
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_lastname');
        $form['registration[firstname]'] = 'firstname';
        // on "unset" '#registration_firstname' puisqu'on la renseigné
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_firstname');
        $form['registration[email]'] = $email;
        // on "unset" '#registration_email' puisqu'on la renseigné
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_email');
        $form['registration[phone]'] = "0696969696";
        // on "unset" '#registration_phone' puisqu'on la renseigné
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_phone');
        // $form['registration[mailingEvents]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        // $form['registration[mailingRecall]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        // $form['registration[phoneRecall]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'Vous devez choisir une option');

        // $form['registration[sectors][0]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'secteur');

        // $form['registration[city_name]'] = 'whatever';
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        // $form['registration[city_id]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_city_name');

        // $form['registration[plainPassword][first]'] = $pwd;
        // $form['registration[plainPassword][second]'] = $pwd."5";
        // array_push($asserts,'#registration_plainPassword_first');
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        // $form['registration[plainPassword][second]'] = $pwd;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_plainPassword_first');

        array_push($asserts,'#registration_plainPassword');
        $form['registration[plainPassword]'] = $pwd;
        // on "unset" '#registration_plainPassword' puisqu'on la renseigné
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_plainPassword');

        // les fichiers gif ne sont pas acceptés
        $form['registration[file]']->upload($this->gif_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        array_push($asserts,'#registration_email');
        $form['registration[email]'] = '';

        $form['registration[file]']->upload($this->pdf_file) ;
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_file');
        $form['registration[file]']->upload($this->docx_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        $form['registration[file]']->upload($this->odt_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        $form['registration[email]'] = $email;
        $form['registration[file]']->upload($this->pdf_file) ;
        //failure here sometimes
        $crawler = $this->checkRegistration($form, $event, $email);

        $this->assertEquals(1, $crawler->filter('#next_events')->count());
        $link = $crawler
            ->filter('#next_events') // find all element with id=next_events
            ->eq(0) // select the first element in the list
            ->link() // and select link
        ;

        $crawler = $this->client->click($link);
        $this->assertEquals("/", $this->client->getRequest()->getPathInfo());

        // new registration with registered candidate
//        $link = $crawler
//            ->filter('.img_container>a')
//            ->eq(2) // select the third element in the list (because first event is already registered and second may be registered if h48) # warning if EventType=Experts  and if date offline > date event
//           // ->link()
//        ;
//
//
//        $crawler = $this->client->click($link);
//        $this->assertEquals(1, $crawler->filter('h2:contains("Inscription")')->count());
//
//        foreach ($asserts_first as $assert) {
//            $this->assertEquals(0, $crawler->filter($assert)->count());
//        }
//
//        foreach ($asserts_edit as $assert) {
//            $this->assertEquals(1, $crawler->filter($assert)->count());
//        }
//
//        $this->assertEquals(1, $crawler->filter('#registration_file')->count());
//
//        $form = $crawler->selectButton('register_action')->form();
//        $this->checkRegistration($form,$event,$email);

        //edit profile
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $this->assertEquals(1, $crawler->filter('h1:contains("Éditer")')->count());

        $asserts = array_merge($asserts_edit,$asserts_edit_spec,$asserts_first);

        foreach ($asserts_edit as $assert) {
            $this->assertEquals(1, $crawler->filter($assert)->count());
        }

        $form = $crawler->selectButton('register_action')->form();
        $crawler = $this->client->submit($form);

        $errors = $this->checkFormError($crawler);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );

        //edit profil and change wrong cv
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $form = $crawler->selectButton('register_action')->form();
        $form['registration[file]']->upload($this->gif_file);
        $crawler = $this->client->submit($form);
        $errors = $this->checkFormError($crawler,true);
        $this->assertStringContainsString('#registration_file',$errors);
        $this->assertFalse($this->client->getResponse()->isRedirect());

        //submit with old cv
        $form = $crawler->selectButton('register_action')->form();
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );

        //edit profile and change cv
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $form = $crawler->selectButton('register_action')->form();
        $form['registration[file]']->upload($this->docx_file);
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );
        $crawler = $this->client->followRedirect();

        //delete participation
        $participation_label = $crawler
            ->filter('.event_title')
            ->eq(0)
            ->each(function (Crawler $node, $i) {
                return $node->text();
            })
        ;
        $participation_label = explode('-', $participation_label[0]);
        $participation_label = str_replace(" ", "", $participation_label[0]);

        $link = $crawler
            ->filter('.delete_participation')
            ->eq(0)
            ->link();
        $crawler = $this->client->click($link);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );
        $crawler = $this->client->followRedirect();
        //make sur participation label not in dom anymore
        //$this->assertEquals(0,$crawler->filter($participation_label)->count());

        $form = $crawler->selectButton('delete_account')->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals("/espace-candidat/supprimer-mon-compte", $this->client->getRequest()->getPathInfo());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/')
        );
    }

    public function testRegistrationSlots()
    {
        $this->goAnonymous();

        $asserts_first = ['#registration_lastname','#registration_firstname','#registration_email','#registration_phone'];
        $asserts_edit =  ['#registration_file'] ;
        $asserts_edit_spec =  ['#registration_sectors','#registration_mobility','#registration_degree','#registration_city_name'] ;
        //$asserts_spec = ['Vous devez choisir une option','secteur'] ;
        $asserts_spec = [];
        //$asserts_edit_spec = [];
        $asserts = array_merge($asserts_first,$asserts_edit,$asserts_spec);


        $last_events = $this->em->getRepository(Event::class)->findCurrentEventsWithslots();
        $time = time();
        $i = 0;

        do{
            $slug = $last_events[$i]->getSlug();
            /**
             *
             * @var Event $event
             */
            $event = $this->em->getRepository(Event::class)->findOneBySlug($slug);
            $i++;
        }
        while ( (is_null($event) || ($event instanceof EventJobs && !empty($event->getRegistrationLimit()) && $event->getRegistrationLimit()->getTimestamp() < $time) || !$event->getType()->registrationValidationAuto()) && $i < count($last_events)-1 );

        if(is_null($event) || !$event->getType()->registrationValidationAuto()){
            throw new \Exception('cannot test simple event registration. there is no current simple event');
        }

        $this->setHost($event->getType()->getHost());

        $crawler = $this->client->request('GET', '/'.$slug);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $email = uniqid().'@l4m.fr';
        $pwd = 'Test1234' ;

        $form = $crawler->selectButton('register_action')->form();

        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        $form['registration[lastname]'] = 'lastname';
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_lastname');
        $form['registration[firstname]'] = 'firstname';
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_firstname');
        $form['registration[email]'] = $email;
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_email');
        $form['registration[phone]'] = "0696969696";
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_phone');
        // $form['registration[mailingEvents]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        // $form['registration[mailingRecall]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        // $form['registration[phoneRecall]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'Vous devez choisir une option');

        // $form['registration[sectors][0]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'secteur');

        // $form['registration[city_name]'] = 'whatever';
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        // $form['registration[city_id]'] = 1;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true,'#registration_city_name');

        // $form['registration[plainPassword][first]'] = $pwd;
        // $form['registration[plainPassword][second]'] = $pwd."5";
        // array_push($asserts,'#registration_plainPassword_first');
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        // $form['registration[plainPassword][second]'] = $pwd;
        // $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_plainPassword_first');

        array_push($asserts,'#registration_plainPassword');
        $form['registration[plainPassword]'] = $pwd;
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_plainPassword');

        // les fichiers gif ne sont pas acceptés
        $form['registration[file]']->upload($this->gif_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        array_push($asserts,'#registration_email');
        $form['registration[email]'] = '';

        $form['registration[file]']->upload($this->pdf_file) ;
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true, '#registration_file');
        $form['registration[file]']->upload($this->docx_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);
        $form['registration[file]']->upload($this->odt_file);
        $this->submitFormAndCheckRegistrationErrors($asserts, $form, true);

        $form['registration[email]'] = $email;
        $form['registration[file]']->upload($this->pdf_file) ;

        $slots = $this->em->getRepository(Slots::class)->findAllNotFull($event);
        $form['registration[slots]'] = array_values($slots)[0]->getId();

        $crawler = $this->checkRegistration($form,$event,$email);

        $this->assertEquals(1, $crawler->filter('#next_events')->count(), $crawler->html());
        $link = $crawler
            ->filter('#next_events') // find all element with id=next_events
            ->eq(0) // select the first element in the list
            ->link() // and select link
        ;

        $crawler = $this->client->click($link);
        $this->assertEquals("/", $this->client->getRequest()->getPathInfo());

        // new registration with registered candidate
//        $link = $crawler
//            ->filter('.img_container>a')
//            ->eq(2) // select the third element in the list (because first event is already registered and second may be registered if h48) # warning if EventType=Experts  and if date offline > date event
//           // ->link()
//        ;
//
//
//        $crawler = $this->client->click($link);
//        $this->assertEquals(1, $crawler->filter('h2:contains("Inscription")')->count());
//
//        foreach ($asserts_first as $assert) {
//            $this->assertEquals(0, $crawler->filter($assert)->count());
//        }
//
//        foreach ($asserts_edit as $assert) {
//            $this->assertEquals(1, $crawler->filter($assert)->count());
//        }
//
//        $this->assertEquals(1, $crawler->filter('#registration_file')->count());
//
//        $form = $crawler->selectButton('register_action')->form();
//        $this->checkRegistration($form,$event,$email);

        //edit profile
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $this->assertEquals(1, $crawler->filter('h1:contains("Éditer")')->count());

        $asserts = array_merge($asserts_edit,$asserts_edit_spec,$asserts_first);

        foreach ($asserts_edit as $assert) {
            $this->assertEquals(1, $crawler->filter($assert)->count());
        }

        $form = $crawler->selectButton('register_action')->form();
        $crawler = $this->client->submit($form);

        $errors = $this->checkFormError($crawler);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );

        //edit profil and change wrong file type
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $form = $crawler->selectButton('register_action')->form();
        $form['registration[file]']->upload($this->gif_file);
        $crawler = $this->client->submit($form);
        $errors = $this->checkFormError($crawler,true);
        $this->assertStringContainsString('#registration_file',$errors);
        $this->assertFalse($this->client->getResponse()->isRedirect());

        //submit with old cv
        $form = $crawler->selectButton('register_action')->form();
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );

        //edit profile and change cv
        $crawler = $this->client->request('GET', '/espace-candidat/editer-info');
        $form = $crawler->selectButton('register_action')->form();
        $form['registration[file]']->upload($this->docx_file);
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );
        $crawler = $this->client->followRedirect();


        //delete participation
        $participation_label = $crawler
            ->filter('.event_title')
            ->eq(0)
            ->each(function (Crawler $node, $i) {
                return $node->text();
            })
        ;
        $participation_label = explode('-', $participation_label[0]);
        $participation_label = str_replace(" ", "", $participation_label[0]);

        $link = $crawler
            ->filter('.delete_participation')
            ->eq(0)
            ->link();
        $crawler = $this->client->click($link);
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat')
        );
        $crawler = $this->client->followRedirect();
        //make sur participation label not in dom anymore
        //$this->assertEquals(0,$crawler->filter($participation_label)->count());

        $form = $crawler->selectButton('delete_account')->form();
        $crawler = $this->client->submit($form);
        $this->assertEquals("/espace-candidat/supprimer-mon-compte", $this->client->getRequest()->getPathInfo());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/')
        );
    }

    public function testSectionExperts()
    {
        //failure on fixtures load
        $this->goAnonymous();
        /**
         *
         * @var Event $event_experts
         */
        $event_experts = $this->em->getRepository(Event::class)->findOneBySlug('experts-lille-test'); // Le Slug recherché était inexistant

        if (!$event_experts){
            throw new \Exception('test section experts : event expert not found ');
        }

        $this->setHost($event_experts->getType()->getHost()->getName());

        $slug = '/'.$event_experts->getSlug();

        $crawler = $this->client->request('GET', $slug);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('h2:contains("Secteurs")')->count());
        $this->assertEquals(1, $crawler->filter('h2:contains("Inscription")')->count());
        $this->assertStringContainsString("Banque / finance", $crawler->filter('#sectors_container')->text());
        $this->assertStringContainsString("Administratif / gestion", $crawler->filter('#sectors_container')->text());
    }

    public function testContact()
    {
        $last_events = $this->em->getRepository(Event::class)->findCurrentEvents();
        /**
         *
         * @var Event $event
         */
        $event = $last_events[0];

        $this->setHost($event->getType()->getHost()->getName());
        echo "\nHOST".$event->getType()->getHost()->getName()."/".$event->getSlug()."\n";
        $crawler = $this->client->request('GET', $event->getSlug());
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertNotEmpty($crawler->html());
//         echo $crawler->html()."\n";
        $email = uniqid().'@l4m.fr';
        $phone = "0303030303";
        $form = $crawler->selectButton('contact_action')->form();
        $form['contact[name]'] = 'lastname';
        $form['contact[firstName]'] = 'firstname';
        $form['contact[email]'] = $email;
        $form['contact[phone]'] = $phone;
        $form['contact[message]'] = 'blablabla zozfnezfmn';
        $crawler = $this->client->submit($form);

        $this->checkFormError($crawler);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertEmailCount(1);
        /**
         *
         * @var Email $message
         */
        $message = $this->getMailerMessage();
        // Asserting email data
        $this->assertInstanceOf(Email::class, $message);
        $this->assertEmailHtmlBodyContains($message, 'firstname LASTNAME');
        $this->assertEmailHtmlBodyContains($message, $email);
        $this->assertStringContainsString('Demande d\'informations', $message->getSubject());

        $this->assertEquals(1, $crawler->filter('#contact_send:contains("Votre demande de contact a été envoyée.")')->count());
        $this->assertEquals(1, $crawler->filter('#contact_send:contains("'.$email.'")')->count());
        $this->assertEquals(1, $crawler->filter('#contact_send:contains("'.$phone.'")')->count());
    }

    public function submitFormAndCheckRegistrationErrors(&$asserts, $form, $has_error=false, $unset=null)
    {
        //warning tmp file is lost after first submission
        $crawler = $this->client->submit($form);
        $errors = $this->checkFormError($crawler,$has_error);
        if($unset){
            $key = array_search($unset, $asserts) ;

            if ($key !== false) {
                unset($asserts[$key]);
            }
        }

        foreach ($asserts as $assert) {
            $this->assertStringContainsString(
                $assert,
                $errors
            );
        }
        return $errors;
    }

    public function checkRegistration($form,$event,$email)
    {
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);

        // echo $this->client->getResponse()->getContent()."\n";

        // there was a failure here then no failure wtf?
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/espace-candidat'),
            $this->client->getResponse()->getContent()
        );

        if($event instanceof EventJobs){
            $this->assertEmailCount(2);
            $message = $this->getMailerMessage();
            $this->assertEmailHtmlBodyContains($message, 'Bonjour Firstname LASTNAME');
            $this->assertEmailHasHeader($message, 'subject');
            // Asserting email data
            $this->assertInstanceOf(Email::class, $message);
            $this->assertStringContainsString('Votre inscription à l\'événement', $message->getSubject());
        }
        else {
            $this->assertEmailCount(1);
            $message = $this->getMailerMessage();
            $this->assertEmailHtmlBodyContains($message, 'Bonjour Firstname LASTNAME');
            $this->assertEmailHasHeader($message, 'subject');
            // Asserting email data
            $this->assertInstanceOf(Email::class, $message);
            $this->assertStringContainsString('Votre invitation à l\'événement', $message->getSubject());
        }

        $tos = $message->getTo();
        $this->assertEquals(1,count($tos));
        $this->assertSame($email, $tos[0]->getAddress());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('h1:contains("Mon profil")')->count());

        return $crawler;
    }
}
