<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Doctrine\ORM\Tools\SchemaTool;

class AuthentificationHelper extends CustomWebTestCase
{
    protected $cookie;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function setUp() : void
    {
        parent::setUp();

        $this->logIn();

        // $kernel = self::bootKernel();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    private function logIn() :void
    {
        $session = $this->client->getContainer()->get('session');

        $userManager = $this->client->getContainer()->get('security.user_providers');
        $user = $userManager->loadUserByIdentifier('france');
        $this->client->loginUser($user);
        /*
        $firewall = 'firewalls.main';
        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_SUPER_ADMIN'));

        $token_storage = $this->client->getContainer()->get('security.token_storage');
        $token_storage->setToken($token);

        // $session->set('_security_'.$firewall, serialize($token));
        // $session->save();

        $this->cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($this->cookie);*/
    }

    private function logInRh()
    {
        // $client = static::createClient();
        // $client->enableProfiler();
        // $session = $client->getContainer()->get('session');

        // $userManager = $client->getContainer()->get('fos_user.user_manager');

        // $user=$userManager->findUserByUsername('france');


        // $firewall = 'main';
        // $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_SUPER_ADMIN'));

        // $session->set('_security_'.$firewall, serialize($token));
        // $session->save();

        // $this->cookie = new Cookie($session->getName(), $session->getId());
        // $client->getCookieJar()->set($this->cookie);

        // return $client;

    }

    protected function goAnonymous() :void
    {
        $session = $this->client->getContainer()->get('session');
        $session->remove('_security_main');
        $session->save();
        $this->client->getCookieJar()->clear();
    }

    protected function checkFormError(Crawler $crawler,$error=null)
    {
        $text = null;
        $has_error = false;
//         $e = new \Exception();
//         echo "EXPECT ".serialize($error)."\n".$e->getTraceAsString()."\n-------\n";
        if (($sub = $crawler->filter('.form-error-message'))->count() > 0){
            $has_error = true;
            $text = $sub->each(function (Crawler $node, $i) {
                $error = $node->ancestors()->ancestors()->ancestors()->attr('for');
//                 echo "ERR $error ".$node->text()."\n";
                return '. Form field #'.$error." : ".$node->text();
            });
        }
//         else {
//             echo "\nNOERR\n";
//         }

        if (is_array($text)){
            $text = implode($text);
        }

        if($error){
            $this->assertTrue(
                $has_error,
                "On devrait avoir au moins une erreur"
            );
        }
        else{
            $this->assertFalse(
                $has_error,
                "On ne devrait pas avoir d'erreur"
            );
        }

//         echo "TEXTE ERR $text\n";

        return $text;
    }
    protected function dump($debug,$die=null)
    {
        if (is_object($debug)) {
            echo('class : '.get_class($debug));
            var_dump(get_object_vars($debug));
        }
        else{
            var_dump($debug);
        }
        if($die){
            die();
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() : void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
