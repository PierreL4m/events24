<?php
namespace App\Tests\Behat;

use App\Entity\CandidateUser;
use App\Entity\ExposantScanUser;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Context\RestContext;
use Behatch\HttpCall\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


/**
 * Defines application features from the specific context.
 */
class ApiContext extends RestContext
{
    private $session;
    private $base_url;
    private $json_context;
    /**
     *
     * @var DoctrineContext
     */
    private $doctrine_context;

    public function __construct(Request $request, SessionInterface $session, $base_url)
    {
        parent::__construct($request);
        $this->session = $session;
        $this->base_url = $base_url;
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->json_context = $environment->getContext('Behatch\Context\JsonContext');
        $this->doctrine_context = $environment->getContext('App\Tests\Behat\DoctrineContext');
        $this->mailer_context = $environment->getContext('App\Tests\Behat\SymfonyMailerContext');
        #$this->mink_context = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /** @BeforeScenario @login */
    public function login()
    {
        $this->iAddHeaderEqualTo('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiI0NjVmZDU5N2E2NmNhNTFiM2NhZTE1MGVkNzEyZGIyYiIsImp0aSI6Ijc1YzA5ODBjYTdlMGJjYzMxOGY1ZGFhM2JkMWQyNWU0YmMxMTU3NzA5NDgwNjY3MTU1N2UwZTdhN2U4ODJlYzQzZjQ0YjQ1Zjk5ZTllNGE1IiwiaWF0IjoxNjY2NzEwODU1LjE5MzEyNSwibmJmIjoxNjY2NzEwODU1LjE5MzEzNiwiZXhwIjoxNzU2MzY0OTEzLCJzdWIiOiIiLCJzY29wZXMiOlsiU1VQRVJfVVNFUiJdfQ.KM1DYC_KI6tYXhUm8CdBuapXWA5-FIILcEM5YuPFu1gd_0pzYMm54Y7x7GV2s8c6T463a8q4Sj4KdJBa6mXB72X6JPGlcKAYlwGWVtWsZQdja8rEQnzFoTixY9E8Rh9RyRA7fjvU2spd3KIQxFuxSUazxFAEK0cjIHMQqulfDqlMPwgKdIBImaL0o-PpPsEuJN0Y5-gXO8Py_0WBMHHv4fDYF1S_5VDHAQbHlBD_pRxgnehL5167PbVmgceb6ZcWCogvdwdN837MVKX0tS1NVdS4un1UtPYN0t4qy6TiRyZN8WdJedjkNH83kxwVpseq2_jy4PQrqEOC0lZecwi32w");
        
    }

    /**
     * @BeforeScenario @loginScan
     */
    public function loginScan()
    {
        $this->iAddHeaderEqualTo('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiI0NjVmZDU5N2E2NmNhNTFiM2NhZTE1MGVkNzEyZGIyYiIsImp0aSI6Ijg1OTRiYzUzMzk5NGM1OTJkMjE4Y2Q0N2IxYTY0ZDRhYTBlZDBjZWFjOTc2MzYwNzA2YWJjZGE2ZDhiMDhkMzEyYjU1ZmI5YjUyZTNhZTA3IiwiaWF0IjoxNjY2Nzg4MjAwLjAzMjA5MiwibmJmIjoxNjY2Nzg4MjAwLjAzMjEwMywiZXhwIjoxNzU2MzY0OTEzLCJzdWIiOiJzY2FuQGw0bS5mciIsInNjb3BlcyI6WyJST0xFX1NDQU4iLCJST0xFX1VTRVIiXX0.SZxvikZM7MOq-NTHBD0DZOfQ-v58S4KTv_vUpEqLxnN-7BIiurHQCZiuKeN7frywdWiD7rqD6oK4z56aU6M7yV8NSUE87zO6mVIDxBGX1fhBLUaB1zIHjQ_JnuFwp_RQAxOKDGByxjt0zyyzhkfZ-MVI4gm3hhFFpXUhIpr-4KYuTCY23xDS8t2F_1YuOhsvKMBjC9fGw0Ow8Tv3VeWBgXhrMBwWzIENvHuZtkAPKXEcjkXP2tDVTUbBadRwjH5zmuBK1CcAYjNyIK4QJfMXzzQ1V_YIcQHuasyMns2QuMM16CoGFUXBzq6GuKocfFOF9sLWNIWrIN6uFCnYqSOmmQ");
    }

    /**
     * @BeforeScenario @loginExposant
     */
    public function loginExposant()
    {
        $this->iAddHeaderEqualTo('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiI0NjVmZDU5N2E2NmNhNTFiM2NhZTE1MGVkNzEyZGIyYiIsImp0aSI6IjllMWM3ZjlhNzdkNzZhOTZkYzQzNjhkYzVkZTIwMjBlMDcyMWVkODI3NGUwYWU3OThmNDg3ODMwNzBhOWM2OWNmZThkZTYzMTgzZTAxZjNlIiwiaWF0IjoxNjY2NzkwMzc3Ljg5NTc0LCJuYmYiOjE2NjY3OTAzNzcuODk1NzQ1LCJleHAiOjE3NTYzNjQ5MTMsInN1YiI6ImV4cG9zYW50QGw0bS5mciIsInNjb3BlcyI6WyJST0xFX09SR0FOSVpBVElPTiJdfQ.ZEwbNaMsDbxJAp__RAHA8ImBqiaDSSiyPoCLIQeJggf7at2_AAuq02nhRofutLF7DvV_HF_O-fSuqlOSnx_7KdWbr4ICh6PiGPPexV1J7McYVYkXWWqULA9hVelE0q0IRp7XDmi2tSmd7ylX2HnxW3n24bp2HWJQv7I13tyZP8gZkyiq6a24Hc58M5RpvYiGBQKagroWt2ZCTk8lzgDF5sYKX1F16TOqCF8HbAxX0v3nr-QDH_9LCMlOxxd_oBT6vJNufgOClP5LSMhM2P9WY1s3n1g3M5SMY49jdp3ohta8xpuA6w9r_kVZ02YClwM7oKyhUvAEK4fUQbk7kSBcHw");
    }

    /**
     * @BeforeScenario @loginExposantScanUser
     */
    public function loginExposantScan()
    {
        $this->iAddHeaderEqualTo('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiI0NjVmZDU5N2E2NmNhNTFiM2NhZTE1MGVkNzEyZGIyYiIsImp0aSI6IjUxN2I4ZjllYWMwYTcyMDFiYzc2ZGZiMGNlZmUyZWQ4NDZiY2E4N2JmYTQwODZlZGY4YTMwNDQ4NmMwM2NlMzBjYmJiY2Y2NzUxYWI1NGVkIiwiaWF0IjoxNjY2NzkyODMyLjkwMTExOSwibmJmIjoxNjY2NzkyODMyLjkwMTEzMSwiZXhwIjoxNzU2MzY0OTEzLCJzdWIiOiJ0ZXN0X29yZ2FuaXphdGlvbiIsInNjb3BlcyI6WyJFWFBPU0FOVF9TQ0FOIl19.ECYvlvsEGLMvAwRe6hcS_hVDgH9rYojgwyt-kC9TLBtk15OugzD3pIVTQhTT2Zz7h_I2J_ek2Bqpkyv7Pl-L-D4lPpyQUa2DsKKJaa89X-Vi1tWIMg0MpLQxiRJTd8ruSVS_OGDqIgLJ_dtnyLzjZ2L6cnFsmlXMNjkDriEV6vKRoqBAR3MRJ4pWD_SZbyt-8XGQ0KeP3tZEMFvOrYzivRdy_9oQyMoXKhv5PBJS0H1v8-0UWjSRWEeplkknOVTKiS4ZbVPtXbPb_ZngQyOm9ddb7LP_hFH0_X6qfT_txYrGc0QGzQJwsL6n2FkmL7WtF8qYFuWDaXjn67WSqYM5LA");
    }


    /**
     * @BeforeScenario @loginL4M
     */
    public function loginL4M(BeforeScenarioScope $scope)
    {
        $this->iAddHeaderEqualTo('Authorization', "Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJhdWQiOiI0NjVmZDU5N2E2NmNhNTFiM2NhZTE1MGVkNzEyZGIyYiIsImp0aSI6ImQyMmJmYzlkOTkxMmNmYmE4ZjUwMDA5Mzk1NGExN2IzNzIwMGRmZWY2ZDQwNGJhMWRjNzZhMjI2YjdhYjg2NzNmOWNiMWEwZGU5NzVlNDIwIiwiaWF0IjoxNjY2ODYxODExLjg0NDM2OSwibmJmIjoxNjY2ODYxODExLjg0NDM4MSwiZXhwIjoxNzU2MzY0OTEzLCJzdWIiOiJsNG10ZXN0QGw0bS5mciIsInNjb3BlcyI6WyJTVVBFUl9VU0VSIl19.ApDhBWxZvA2XIJ_Bxfnk_LiZsJropd9XVF6B9BUuAMq3vPZqtCnw0YIehG8t_UMVnECQo1xZv3U_8GIn8jm-co0C9utyF9RWhawSHgIhF77MtON1WHIHD5wNwzh4z7WI-MjlI9Y7Or0UH1c18p9eojKRLogzJacb0BYTpCp9O7r1DxK10Rz0zGmKvKPCMiTXzg2_-7m31brb0V5leUj3UgVNYcxjpk1lVdi06HZ6sPgojMXOKmlkUCpUa4acA06EPirEN0tboCw2U-0qnwsN4VZmFcUBdc9g_rUKWQ2GepV7nOrhLcUPTO9TUAmSPLTVPF5itIx7uTg54UsT0j_2dQ");
    }

    /**
     * @BeforeScenario @loadFixtures
     */
    public function loadFixtures(BeforeScenarioScope $scope)
    {
        $executionFilePath = __DIR__ . "/../../.behat_fixtures";
        if(!file_exists($executionFilePath)
            || !($time = (int)file_get_contents($executionFilePath))
            || date('Y-m-d', $time) != date('Y-m-d')) {
        
            print(__DIR__) . PHP_EOL;
    
            $command = array(__DIR__ . "/../../bin/console", 'doctrine:fixtures:load', "--env={$_ENV['APP_ENV']}",  '-n', '--append');
    
            $process = new Process($command);
            $process->setTimeout(300);
            $process->run();
    
            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            
            file_put_contents($executionFilePath, time());
        }
    }

    /**
     * @AfterScenario
     * @logout
     */
    public function logout(AfterScenarioScope $scope)
    {
        $this->iAddHeaderEqualTo('Authorization', '');
    }

    /**
     * @param $string
     *
     * @When I describe :string
     */
    public function describe($string)
    {
        echo(" ");
    }

    /**
     * @param $string
     *
     * @Then I set bearer from response
     */
    public function ISetBearerFromResponse()
    {
        $content = json_decode($this->request->getContent());
        $session = $this->session;
        var_dump($content);
        var_dump($content->access_token);
        $session->set('CandidateToken', $content->access_token);
    }

    /**
     * @param $string
     *
     * @Then I dump the response
     */
    public function iDumpTheResponse()
    {
        //  dump(json_decode($this->request->getContent())->error->exception);die();
        dump($this->request->getContent());
        die();
    }

    /**
     * @When I authorize with my bearer
     */
    public function iAuthorizeWithMyBearer()
    {
        $session = $this->session;
        $session->get('CandidateToken');
        echo "-----Auth avec [".$session->get('CandidateToken')."]\n";
        $this->iAddHeaderEqualTo('Authorization', "Bearer " . $session->get('CandidateToken'));
    }

    public function responseContains($stringNode, $first_node = null)
    {
        $expectedResponse = (array)json_decode($stringNode->getRaw());
        $response = (array)json_decode($this->request->getContent());

        if ($first_node) {
            $response = (array)$response[0];
        }
        $error = null;

        foreach ($expectedResponse as $key => $expected_value) {
            if (!array_key_exists($key, $response)) {
                $error = 'expected key "' . $key . '"" does not exists in response';
                break;
            } elseif (!is_object($response[$key]) && !is_array($expected_value)) {
                if ($response[$key] != $expected_value) {
                    $error = 'expected "' . $key . ' => ' . $expected_value . '" does not match response "' . $response[$key] . '"';
                    break;
                }
            } elseif (is_object($response[$key])) {
                if ($response[$key]->id != $expected_value) {
                    $error = 'expected "' . $key . '[id] => ' . $expected_value . '" does not match response "' . $response[$key]->id . '"';
                    break;
                }
            } elseif (is_array($response[$key]) && is_array($expected_value)) {
                $i = 0;
                foreach ($response[$key] as $val) {
                    if(is_object($val)) {
                        if ($val->id != $expected_value[$i]) {
                            $error = 'expected "' . $key . '[id] => ' . $expected_value[$i] . '" does not match response "' . $val->id . '"';
                            break;
                        }
                    }
                    else {
                        if($val != $expected_value[$i]) {
                            $error = 'expected "' . $key . '[id] => ' . $expected_value[$i] . '" does not match response "' . $val . '"';
                            break;
                        }
                    }
                    $i++;
                }

            } else {
                $error = 'expected : key :' . $key . ' value ' . var_dump($expected_value) . ' not verified';
            }

        }

        if ($error != null) {
            var_dump($response); echo "\n";
            throw new \RuntimeException($error);
        }
    }

    /**
     * @param PyStringNode $stringNode
     *
     * @Then the response body contains JSON:
     */
    public function theResponseBodyContainJson(PyStringNode $stringNode)
    {
        $this->responseContains($stringNode);
    }

    /**
     * @Then I store participation_id
     */
    public function iStoreParticipationId()
    {
        $content = json_decode($this->request->getContent());
        $session = $this->session;
        var_dump($content);
        $session->set('participation_id', $content->candidate_participations[0]->id);
    }

    /**
     * @When I send the request to cancel participation_id
     */
    public function iSendTheRequestToCancelParticipationId()
    {
        if ($this->session->get('participation_id')) {
            $this->iSendARequestTo('DELETE', '/api/candidate/participation/' . $this->session->get('participation_id'));
        } else {
            throw new \Exception('Previous participation has not been saved');
        }
    }

    /**
     * @Then the child node :child_node should not contains :value_in_session
     */
    public function theChildNodeCandidateParticipationsShouldNotContainsDeletedParticipation($child_node, $value_in_session)
    {

        if ($this->session->get($value_in_session)) {
            $content = json_decode($this->request->getContent());
            $contains = false;

            foreach ($content->$child_node as $element) {
                if ($element->id == $this->session->get($value_in_session)) {
                    $contains = true;
                }
            }

            if ($contains == true) {
                throw new \Exception('child node ' . $child_node . ' contains session->' . $value_in_session);
            }
        } else {
            throw new \Exception('Previous participation has not been saved');
        }
    }

    /**
     * @Then the first response body node contains JSON:
     */
    public function firstResponseBodyNodeContainsJson(PyStringNode $stringNode)
    {
        $this->responseContains($stringNode, true);
    }

    /**
     * @Then the JSON response should have :arg1 elements
     */
    public function theJsonResponseShouldHaveElements($arg1)
    {
        $content = json_decode($this->request->getContent());
        $real_size = sizeof((array)$content);
        if ($arg1 != $real_size) {
            throw new \Exception('The json response has ' . $real_size . ' elements. (given ' . $count . ')');
        }
    }

    public function jsonNodeExist($name)
    {
        $content = (array)json_decode($this->request->getContent());
        $first_node = array($content[0]);

        if (!property_exists($content[0], $name)) {
            throw new \Exception('node ' . $name . ' does not exist');
        }
    }

    public function jsonNodesShouldExist($string, $list = null)
    {

        $node_names = explode(",", $string);
        $content = json_decode($this->request->getContent());
        // var_dump($content);echo "\n";
        if ($list) {
            $first_node = $content[0];
        } else {
            $first_node = $content;
        }
        // dump($first_node);
        foreach ($node_names as $value) {
            if (!property_exists($first_node, $value)) {
                throw new \Exception('node ' . $value . ' does not exists');
            }
        }
    }

    /**
     * @Then the JSON nodes :string should exist in first node
     */
    public function theJsonNodesShouldExistInFirstNode($string)
    {
        $this->jsonNodesShouldExist($string, true);
    }
    
    /**
     * @Then the JSON nodes :string should exist in :child
     */
    public function theJsonNodesShouldExistInChild($string, $child)
    {
        // TODO
        $this->jsonNodesShouldExist($string, true);
    }

    /**
     * @Then the JSON nodes :string should exist
     */
    public function theJsonNodesShouldExist($string)
    {
        $this->jsonNodesShouldExist($string);
    }

    /**
     * @Then the JSON node :name should exist in first node
     */
    public function theJsonNodeShouldExistInList($name)
    {
        $this->jsonNodeExist($name);
    }

    public function sendRequestToShowCandidatesWithEventInFixtures($search = null)
    {
        //
        return $this->request->send(
            "GET",
            $this->base_url . "/api/admin/show-candidates/" . $this->session->get('event_id'),
            ['search' => $search]
        );
    }

    /**
     * @When I send a GET request to show candidates with event in fixtures
     */
    public function iSendAGetRequestToShowCandidatesWithEventInFixtures()
    {
        return $this->sendRequestToShowCandidatesWithEventInFixtures();
    }


    /**
     * @Then the response body should contains the candidate in fixtures
     */
    public function theResponseBodyShouldContainsTheCandidateInFixtures()
    {
        $content = (array)json_decode($this->request->getContent());


        $candidate = null;
        foreach($content as $c) {
            $candidate_json = $c->candidate;
            if($candidate_json->email == 'candidate@l4m.fr') {
                $candidate = $candidate_json;
                break;
            }
        }
        $this->assertTrue(!empty($candidate), 'Fixture candidate not found');
        $this->assertEquals('candidate@l4m.fr', $candidate->email);
        $this->assertEquals('Candidate', $candidate->firstname);
        $this->assertEquals('CANDIDATE', $candidate->lastname);
    }

    /**
     * @When I send a GET request to search candidate with event in fixtures with search :value
     */
    public function iSendAGetRequestToSearchCandidateWithEventInFixturesWithSearch($value)
    {
        $this->sendRequestToShowCandidatesWithEventInFixtures($value);
    }

    /**
     *
     * @When I try to register to event simple with body:
     * @param PyStringNode $body
     * @param array $files
     */
    public function iTryToRegisterToEventSimple(PyStringNode $body = null, $files = []) {
        // send a "POST" request to "/api/event/70/registration" with body:
        return $this->iSendARequestTo(
            'POST', 
            "/api/event/".$this->doctrine_context->getEventSimple()->getId()."/registration", 
            $body, 
            $files
        );
    }

    /**
     *
     * @When I try to register to event jobs with body:
     * @param PyStringNode $body
     * @param array $files
     */
    public function iTryToRegisterToEventJobs(PyStringNode $body = null, $files = []) {
        // send a "POST" request to "/api/event/70/registration" with body:
        return $this->iSendARequestTo('POST', "/api/event/".$this->doctrine_context->getEventJobs()->getId()."/registration", $body, $files);
    }

    /**
     *
     * @When I try to register to event jobs with registration type job
     * @param PyStringNode $body
     * @param array $files
     */
    public function iTryToRegisterToEventJobsWithRegistrationTypeJob($files = []) {
        $event = $this->doctrine_context->getEventJobsWithRegistrationTypeJob();
        $job = $this->doctrine_context->getEventRandomJob($event);
        $body = json_encode([
            "firstname" => "test",
            "lastname" => "test",
            "email" => "testtest@l4m.fr",
            "phone" => "0698929728",
            "plainPassword" => "Test1234",
            "mailingEvents" => false,
            "mailingRecall" => false,
            "phoneRecall" => true,
            "job" => $job->getId(),
            "cv_file" => "data:image/png;base64,AAAFBfj42Pj4"
        ]);

        // send a "POST" request to "/api/event/70/registration" with body:
        return $this->request->send(
            "POST",
            "/api/event/".$event->getId()."/registration",
            [],
            [],
            $body
        );
    }

    /**
     *
     * @When I try to register to event jobs without auto validation and without job registration with body:
     * @param PyStringNode $body
     * @param array $files
     */
    public function iTryToRegisterToEventJobsWithoutAutoValidationAndWithoutJobRegistration(PyStringNode $body = null, $files = []) {
        // send a "POST" request to "/api/event/70/registration" with body:
        return $this->iSendARequestTo('POST', "/api/event/".$this->doctrine_context->getEventJobsWithoutAutoValidationAndWithoutJobRegistration()->getId()."/registration", $body, $files);
    }

    /**
     * @Then the response body should contains the event in fixtures
     */
    public function theResponseBodyShouldContainsTheEventInFixtures()
    {
        $content = (array)json_decode($this->request->getContent());
        $contains = false;

        foreach ($content as $node) {
            if ($node->id == 10000) {
                $contains = true;
                break;
            }
        }
        if (!$contains) {
            throw new \Exception('the event in fixtures (id=10000) not found in response');
        }
    }

    /**
     * @When I scan the candidate in fixtures and store candidate_comment in session
     */
    public function iScanTheCandidateInFixturesAndStoreCandidateCommentInSession()
    {
        if ($this->session->get('candidate_participation_id')) {
            $candidate_participation_id = $this->session->get('candidate_participation_id');
        } else {
            $candidate_participation_id = $this->doctrine_context->loadCandidateParticipationIdInSessionAndDeleteComment()->getId();
        }
        $date = new \Datetime();
        $date->setTimestamp(1756364913);
        $scan = $date->format('YmdHi');
        
        echo $this->base_url . "/api/exposant/scan/" . $candidate_participation_id."\n";
        $response = $this->request->send(
            "POST",
            $this->base_url . "/api/exposant/scan/" . $candidate_participation_id,
            ["scannedAt" => $scan],
            [],
            json_encode(["scannedAt" => $scan])
        );
        
        $this->session->set('candidate_comment', $response->getContent());
    }

    /**
     * @When I get the comment in session
     */
    public function iGetTheCommentInSession()
    {

        $candidate_comment = json_decode($this->session->get('candidate_comment'));

        $id = $candidate_comment->id;
        $response = $this->request->send(
            "GET",
            $this->base_url . "/api/exposant/note/" . $id
        );
        $expected = json_decode($response->getContent());
        
        $this->assertEquals($candidate_comment->id, $expected->id);
        $this->assertEquals($candidate_comment->candidate_participation->id, $expected->candidate_participation->id);
        $this->assertEquals($candidate_comment->candidate_participation->event->id, $expected->candidate_participation->event->id);

    }

    /**
     * @When I edit the comment in session
     */
    public function iEditTheCommentInSession()
    {
        $candidate_comment = json_decode($this->session->get('candidate_comment'));
        $id = $candidate_comment->id;
        $response = $this->request->send(
            "PATCH",
            $this->base_url . "/api/exposant/note/" . $id,
            [],
            [],
            json_encode([
                "like" => 1,
                "comment" => "comment"
            ])
        );
        
        $this->session->set('candidate_comment', $response->getContent());

    }

    /**
     * @When I send the comment in session by email
     */
    public function iSendTheCommentInSessionByEmail()
    {
        // $this->iAddHeaderEqualTo('Authorization', "Bearer ZmU1MTgxNzNkZjM5ZTU2NzdiODVkODgxMmE2ZmE2OWUyMjY1ZWIxYzgxMjdlOTA3NjBiODexposant");
        $candidate_comment = json_decode($this->session->get('candidate_comment'));
        
        $id = $candidate_comment->id;
        $response = $this->request->send(
            "GET",
            $this->base_url . "/api/exposant/send-profile/" . $id
        );
    }

    /**
     * @Then the JSON should be equal to the candidate_comment in session
     */
    public function theJsonShouldBeEqualToTheCandidateCommentInSession()
    {
        if ($this->session->get('candidate_participation_id')) {
            $candidate_participation_id = $this->session->get('candidate_participation_id');
        } else {
            $candidate_participation_id = $this->doctrine_context->getCandidateParticipationInFixtures()->getId();
        }
        $candidate_comment = json_decode($this->session->get('candidate_comment'));

        $this->assertEquals('10000', $candidate_comment->candidate_participation->event->id);
        $this->assertEquals($candidate_participation_id, $candidate_comment->candidate_participation->id);
    }

    /**
     * @Then the candidate comment was edited
     */
    public function theCandidateCommentWasEdited()
    {
        $candidate_comment = json_decode($this->session->get('candidate_comment'));
        $this->assertEquals('1', $candidate_comment->like);
        $this->assertEquals('comment', $candidate_comment->comment);
    }


    /**
     * @Then the ExposantScanUser can login
     */
    public function theExposantscanuserCanLogin()
    {
        $user = $this->session->get('exposant_scan_user');

        if (!$user) {
            $user = $this->doctrine_context->getEm()->getRepository(ExposantScanUser::class)->findOneByUsername('test_organization');
        }
        
        $response = $this->request->send(
            "POST",
            $this->base_url . "/oauth/v2/token",
            [
                "client_id" => "465fd597a66ca51b3cae150ed712db2b", //from fixtures
                "client_secret" => "561b5d487befb18261992b47887f1e1b122ceb50f0646ae3f7d2f8b5a5e75787d72e2ffd1a931bd428665c339828cae99249dfe7e1cc62108451246af87273d0", //from fixtures
                "username" => $user->getUsername(),
                "password" => $user->getSavedPlainPassword(),
                "grant_type" => "password"
            ]
        );
        
        $bearer = json_decode($response->getContent())->access_token;
        echo "BEARER : ".$bearer."\n";
        $this->iAddHeaderEqualTo('Authorization', "Bearer " . $bearer);
    }


    /**
     * @Then the response should contains the candidate in fixtures
     */
    public function theResponseShouldContainsTheCandidateInFixtures()
    {
        $em = $this->doctrine_context->getEm();
        $candidate_comment = json_decode($this->request->getContent());
        $this->assertTrue(count((array)$candidate_comment) > 0);
        $candidate = $em->getRepository(CandidateUser::class)->findOneByEmail('candidate@l4m.fr');
        $ids = array();
        foreach($candidate_comment as $cc) {
            $candidate_json = $cc->candidate_participation->candidate;
            $ids[$candidate_json->id] = $candidate_json->id;
        }
        $this->assertArrayHasKey($candidate->getId(), $ids);
    }

    /**
     * @When I log exposant
     */
    public function iLogExposant()
    {
        $this->loginExposant();
    }

    /**
     * @When I log ExposantScan
     */
    public function iLogExposantscan()
    {
        $this->loginExposantScan();
    }

    /**
     * @Then the response status code should be :arg1
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        if(
            $arg1 == '500' || 
            $this->getMink()->getSession()->getStatusCode() == '500' || 
            $arg1 != $this->getMink()->getSession()->getStatusCode()) {
            echo $this->request->getContent();
        }
        
        return $this->assertSession()->statusCodeEquals($arg1);
    }


    /**
     * Opens homepage
     * Example: Given I am on "/"
     * Example: When I go to "/"
     * Example: And I go to "/"
     *
     * @Given /^(?:|I )am on (?:|the )homepage$/
     * @When /^(?:|I )go to (?:|the )homepage$/
     */
    public function iAmOnHomepage()
    {
        //copy from mink context
        $this->visitPath('/');
    }

    /**
     * Checks, that current page PATH is equal to specified
     * Example: Then I should be on "/"
     * Example: And I should be on "/bats"
     * Example: And I should be on "http://google.com"
     *
     * @Then /^(?:|I )should be on "(?P<page>[^"]+)"$/
     */
    public function assertPageAddress($page)
    {
        //copy from mink context
        $this->assertSession()->addressEquals($this->locatePath($page));
    }
}
