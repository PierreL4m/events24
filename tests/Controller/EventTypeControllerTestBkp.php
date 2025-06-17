<?php 

// namespace App\Tests\Controller;

// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


// class EventTypeControllerTest extends WebTestCase
// {
	// private $connection;
	// private $client;
	// private $container;

	// public function setUp()
	// {		

	// 	$this->container = static::createClient()->getContainer();
	// 	$this->client = $this->container->get('eight_points_guzzle.client.api');
	// 	$this->connection = $this->container->get('doctrine')->getManager()->getConnection();
	// }
	// public function checkObjectFields($object)
	// {
	// 	if (is_array($object)){
	// 		$this->assertArrayHasKey('id', $object);
	// 		$this->assertArrayHasKey('full_name', $object);
	// 		$this->assertArrayHasKey('short_name', $object);
	// 		$this->assertArrayHasKey('dns', $object);
	// 		$this->assertArrayHasKey('mandatory_registration', $object);
	// 		$this->assertArrayHasKey('places', $object);
	// 		$this->assertArrayHasKey('participation_types', $object);
	// 		$this->assertInternalType('array',$object['participation_types']);
	// 		$this->assertInternalType('array',$object['places']);


	// 	}
	// }

	// public function checkFormDatas($content,$full_name,$name,$dns)
	// {
	// 	$this->assertEquals(
	// 		$content['full_name'],
	// 		$full_name
	// 	);
	// 	$this->assertEquals(
	// 		$content['short_name'],
	// 		$name
	// 	);
	// 	$this->assertEquals(
	// 		$content['dns'],
	// 		$dns
	// 	);
	// 	$this->assertEquals(
	// 		$content['mandatory_registration'],
	// 		0
	// 	);
	// }
 //    public function testGetEventTypes()
 //    {
	// 	$client = $this->client;
 //        $response = $client->get('/api/event-types');

 //        $this->assertContains(		    
	// 	    'application/json', 
	// 	    $response->getHeader('Content-Type')
	// 	);

	// 	$this->assertEquals(
	// 	    200,
	// 	    $response->getStatusCode()
	// 	);

	// 	$content = json_decode($response->getBody()->getContents(),true);
		
	// 	$this->assertInternalType('array', $content);

	// 	 foreach ($content as $object) {			
	// 	 	$this->checkObjectFields($object);	
	// 	 }
 //    }

 //    public function testPostGetDeleteEventType()
 //    {
 //    	$full_name =  uniqid();
 //    	$name =  uniqid();
 //    	$dns =  uniqid();
 //        $datas = array(
 //     		'fullName' => $full_name,
 //     		'shortName' => $name,
 //     		'dns' => $dns,
 //     		'mandatoryRegistration' => 0
 //     	);

 //        $client = $this->client;
 //        $response = $client->post('/api/event-types', array('form_params' => $datas));
        
 //        $this->assertContains(		    
	// 	    'application/json', 
	// 	    $response->getHeader('Content-Type')
	// 	);

	// 	$this->assertEquals(
	// 	    201,
	// 	    $response->getStatusCode()
	// 	);

	// 	$content = json_decode($response->getBody()->getContents(),true);
		
	// 	$this->assertInternalType('array', $content);

		
	// 	$this->assertInternalType('array', $content);					
	// 	$this->checkObjectFields($content);	
	// 	$this->checkFormDatas($content,$full_name,$name,$dns);
		
	// 	$id = $content['id'];

	// 	//get test
	// 	$response = $client->get('/api/event-types/'.$id);

 //        $this->assertContains(		    
	// 	    'application/json', 
	// 	    $response->getHeader('Content-Type')
	// 	);

	// 	$this->assertEquals(
	// 	    200,
	// 	    $response->getStatusCode()
	// 	);

	// 	$content = json_decode($response->getBody()->getContents(),true);

	// 	$this->assertInternalType('array', $content);					
	// 	$this->checkObjectFields($content);	
	// 	$this->checkFormDatas($content,$full_name,$name,$dns);

	// 	//delete test
	// 	$response = $client->delete('/api/event-types/'.$id);
		
	// 	$this->assertEquals(
	// 	    204, 
	// 	    $response->getStatusCode()
	// 	);
		
	// 	$this->connection->exec('ALTER TABLE event_type AUTO_INCREMENT = 1;');
 //   }

//}