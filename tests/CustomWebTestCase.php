<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomWebTestCase extends WebTestCase {
    
    protected $client;
    
    public function setUp() : void
    {
        parent::setUp();
        
        // FIXME :
        $this->client = static::createClient([], $_SERVER);
        $this->client->enableProfiler();
    }
    
    protected function setHost($host) {
        $this->client->setServerParameter('HTTP_HOST', $host);
    }
}