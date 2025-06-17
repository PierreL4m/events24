<?php

namespace App\Tests\Controller;

use App\Tests\AuthentificationHelper;
use Symfony\Component\Mime\Email;

class UserControllerTest extends AuthentificationHelper
{
    public function testNewL4MUser()
    {
        $crawler = $this->client->request('GET', '/admin/user/new/l4m');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save')->form();
        $last_name = uniqid();
        $first_name = uniqid();
        $form['user[lastname]'] = $last_name;
        $form['user[firstname]'] = $first_name;
        $email = uniqid().'@l4m.fr' ;
        $form['user[email]'] =  $email;
        $form['user[phone]'] = '0102003099';
        $crawler = $this->client->submit($form);
        $this->checkFormError($crawler);

        $this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/user/l4m')
         );
        // checks that an email was sent
        $this->assertEmailCount(1);
        /**
         *
         * @var Email $message
         */
        $message = $this->getMailerMessage();
        
        // Asserting email data
        $this->assertInstanceOf(Email::class, $message);
        $this->assertSame('Votre mot de passe pour le back office événements L4M', $message->getSubject());
        
        $from = $message->getFrom();
        $this->assertEquals(1,count($from));
        $this->assertSame('evenements@l4m.fr', $from[0]->getAddress());
        
        //$this->assertSame('email_l4m@l4m.fr', key($message->getTo()));
        $this->assertEmailHtmlBodyContains($message, 'Voici vos identifiants pour accèder au back office des événements L4M à l\'adresse suivante :');

        $crawler = $this->client->request('GET', '/admin/user/deletebyemail/'.$email);

        $this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/user')
         );

    }
    public function testNewExposantUser()
    {
  		$crawler = $this->client->request('GET', '/admin/user/new/exposant/2');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save')->form();
        $form['user[lastname]'] = 'last_name_test';
        $form['user[firstname]'] = 'first_name_test';
        $email = uniqid().'@l4m.fr' ;
        $form['user[email]'] = $email;
        $form['user[phone]'] = '0102003099';

        // gets the raw values
		$values = $form->getPhpValues();
		// adds fields to the raw values
		$values['user']['responsableBises'][0]['email'] = 'test_email@l4m.fr';
		// submits the form with the existing and new values
		$crawler = $this->client->request($form->getMethod(), $form->getUri(), $values,
		    $form->getPhpFiles());
        $this->checkFormError($crawler);
        
        $this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/participation/2')
         );

        // checks that an email was sent
        $this->assertEmailCount(2);
        /**
         *
         * @var Email[] $message
         */
        $messages = $this->getMailerMessages();
        $message = $messages[0];
        // Asserting email data
        $this->assertInstanceOf(Email::class, $message);
        $this->assertEmailHtmlBodyContains($message, 'Vous pouvez éditer dès à présent votre fiche exposant pour l\'événement L4M');
        $this->assertEmailHtmlBodyContains($message, 'Mot de passe ');
        $this->assertStringContainsString('Complétez dès maintenant votre fiche exposant pour l\'événement L4M', $message->getSubject());
        
        $message = $messages[1];
        // Asserting email data
        $this->assertInstanceOf(Email::class, $message);
        $this->assertSame('evenements@l4m.fr', $message->getFrom()[0]->getAddress());
        $this->assertEmailHtmlBodyContains($message, $email);
        $this->assertEmailHtmlBodyContains($message, 'Mot de passe ');
        $this->assertStringContainsString('Nouveau mot de passe admin events', $message->getSubject());
        $this->assertEmailHtmlBodyContains($message, 'Voici le nouveau mot de passe de First_name_test LAST_NAME_TEST');
        
        
//         $message = $collectedMessages[0];
        //$this->assertSame('email@l4m.fr', key($message->getTo()));
//         $this->assertStringContainsString(
//             'Vous pouvez éditer dès à présent votre fiche exposant pour l\'événement L4M',
//             $message->getBody()
//         );
        // this is a real new user so his password is sent to admin
//         $this->assertStringContainsString(
//             'Mot de passe ',
//             $message->getBody()
//             );
        
        // $this->assertSame(2, $mailCollector->getMessageCount());
        // $collectedMessages = $mailCollector->getMessages();
//         $message = $collectedMessages[0];
        // Asserting email data
//         $this->assertInstanceOf('Swift_Message', $message);
//         $this->assertStringContainsString('Complétez dès maintenant votre fiche exposant pour l\'événement L4M', $message->getSubject());
//         //$this->assertSame('email@l4m.fr', key($message->getTo()));
//         $this->assertStringContainsString(
//             'Vous pouvez éditer dès à présent votre fiche exposant pour l\'événement L4M',
//             $message->getBody()
//         );
//          // this is a real new user so his password is sent to admin
//         $this->assertStringContainsString(
//             'Mot de passe ',
//             $message->getBody()
//         );
//         $message = $collectedMessages[1];

        // Asserting email data
//         $this->assertInstanceOf('Swift_Message', $message);
//         $this->assertSame('Nouveau mot de passe admin events', $message->getSubject());
//         $this->assertSame('evenements@l4m.fr', key($message->getFrom()));
//         $this->assertStringContainsString(
//             'Voici le nouveau mot de passe de First_name_test LAST_NAME_TEST',
//             $message->getBody()
//         );
//         $this->assertStringContainsString(
//             'Mot de passe',
//             $message->getBody()
//         );
//         $this->assertStringContainsString(
//             $email,
//             $message->getBody()
//         );

        $crawler = $this->client->followRedirect();

    }

    public function testNewCandidatUserWithoutSlot()
    {
        $crawler = $this->client->request('GET', '/admin-recruteurs/add-candidate/107');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('valid')->form();
        $form['registration[lastname]'] = 'Schuvey';
        $form['registration[firstname]'] = 'Pierre';
        $form['registration[email]'] = 'webmaster@l4m.fr';
        $form['registration[rhSectors]'][0]->tick();
        $form['registration[phone]'] = '0676716137';
        $form['registration[file]'] = __FILE__;
        //$form['registration[slots]'] = 11;
        $form['registration[mailingRecall]'] = 1;
        $form['registration[mailingEvents]'] = 1;
        $form['registration[phoneRecall]'] = 1;

        $this->client->submit($form);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->client->getResponse()->isRedirect('/admin-recruteurs/add-candidate/107');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

    }

    public function testNewExistingExposantUser()
    {
        $crawler = $this->client->request('GET', '/admin/user/new/exposant/2');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

// this is not a real new user
        $form = $crawler->selectButton('save')->form();
        $form['user[lastname]'] = 'last_name_test_new';
        $form['user[firstname]'] = 'first_name_test_new';
        $email = uniqid().'@l4m.fr' ;
        $form['user[email]'] = 'webmaster@l4m.fr';
        $form['user[phone]'] = '0102003090';

        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/user/new/exposant/2')
        );
        $crawler = $this->client->followRedirect();
        $this->assertTrue(
            $crawler->filter('.alert-danger')->count() > 0
        );
        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("L\'utilisateur existe déjà")')->count()
        );

    }

    public function testChooseExistingResponsable()
    {
        $crawler = $this->client->request('GET', '/admin/user/88/edit/50');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save_list')->form();
        $form['user_list[users]']->select(652);
        $crawler = $this->client->submit($form);

		$this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/user/652/assign/50')
        );
        $crawler = $this->client->followRedirect();

		$this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/participation/50')
         );
		$crawler = $this->client->followRedirect();

       	$this->assertEquals(1, $crawler->filter('.user_container:contains(Jordan CANDELIER)')->count());

       	$crawler = $this->client->request('GET', '/admin/user/88/assign/50');

       	$this->assertTrue(
         	$this->client->getResponse()->isRedirect('/admin/participation/50')
        );
    }


    //test edit but exists

    public function testChooseByEmail()
    {
  		// new email => redirect to new form with notice
        $crawler = $this->client->request('GET', '/admin/user/88/edit/50');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save_search')->form();
        $form['email_field[email]'] = 'azerty@l4m.fr' ;
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/user/new/exposant/50')
        );
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("azerty@l4m.fr")')->count());

        //user is not exposant user nor l4m user
        $crawler = $this->client->request('GET', '/admin/user/88/edit/50');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save_search')->form();
        $form['email_field[email]'] = 'webmaster@l4m.fr' ;
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/user/new/exposant/50')
        );
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("Vous ne pouvez pas ajouter une participation à un utilisateur de type")')->count());

        //exposant user but not same organiszation
        $crawler = $this->client->request('GET', '/admin/user/88/edit/50');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save_search')->form();
        $form['email_field[email]'] = 'bonsplansdk@gmail.com' ;
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/user/new/exposant/50')
        );
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("Vous ne pouvez pas ajouter une participation de ")')->count());
        $this->assertEquals(1, $crawler->filter('#flash_notice:contains("à un responsable de ")')->count());

        //exposant user and same organization
        $crawler = $this->client->request('GET', '/admin/user/88/edit/50');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('save_search')->form();
        $form['email_field[email]'] = 'jordan.candelier@intradef.gouv.fr' ;
        $crawler = $this->client->submit($form);

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/user/652/assign/50')
        );

        $crawler = $this->client->followRedirect();
        // $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // // checks that an email was sent
        // $this->assertSame(1, $mailCollector->getMessageCount());

        // $collectedMessages = $mailCollector->getMessages();
        // $message = $collectedMessages[0];

        // // Asserting email data
        // $this->assertInstanceOf('Swift_Message', $message);
        // $this->assertContains('Complétez dès maintenant votre fiche exposant pour l\'événement L4M', $message->getSubject());
        // $this->assertSame('jordan.candelier@intradef.gouv.fr', key($message->getTo()));
        // $this->assertContains(
        //     'Vous pouvez éditer dès à présent votre fiche exposant pour l\'événement L4M',
        //     $message->getBody()
        // );
        // $this->assertContains(
        //     'Rappel de vos identifiants :',
        //     $message->getBody()
        // );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/participation/50')
         );
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1, $crawler->filter('.user_container:contains(Jordan CANDELIER)')->count());

        $crawler = $this->client->request('GET', '/admin/user/88/assign/50');

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/admin/participation/50')
        );
    }

}
