<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthentificationTest extends WebTestCase
{
    public function testAuth()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin');
        $client->getResponse()->isRedirect('/auth/login');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/admin-exposant');
        $client->getResponse()->isRedirect('/auth/login');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }
}
