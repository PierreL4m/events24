Feature: Candidate register, login, get profile page, edit profile, register to new event with no form datas, regiter to an event with form datas, cancel a participation, remove account

  @login
  @deleteCandidate
  Scenario: register to a simple event 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I try to register to event simple with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtest@l4m.fr",
        "phone" : "0698929728",
        "plainPassword" : "Test1234",
        "mailingEvents" : false,
        "mailingRecall" : false,
        "phoneRecall" : true,
        "cv_file" : "data:image/png;base64,AAAFBfj42Pj4"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Test",
        "lastname" : "TEST",
        "email" : "testtest@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : false,
        "mailing_recall" : false,
        "phone_recall" : true
      }
    """
    Then email with subject "Votre invitation" should have been sent to "testtest@l4m.fr"
    
  @login
  @deleteCandidate
  Scenario: register to a eventjobs without auto validation and without job registration 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I try to register to event jobs without auto validation and without job registration with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtest@l4m.fr",
        "phone" : "0698929728",
        "plainPassword" : "Test1234",
        "mailingEvents" : false,
        "mailingRecall" : false,
        "phoneRecall" : true,
        "cv_file" : "data:image/png;base64,AAAFBfj42Pj4"
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Test",
        "lastname" : "TEST",
        "email" : "testtest@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : false,
        "mailing_recall" : false,
        "phone_recall" : true
      }
    """
    Then email with subject "Votre inscription" should have been sent to "testtest@l4m.fr"
    
 @login
 @deleteCandidate
 Scenario: register to a eventjobs without auto validation and with job
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I try to register to event jobs with registration type job
    Then the response status code should be 201
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Test",
        "lastname" : "TEST",
        "email" : "testtest@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : false,
        "mailing_recall" : false,
        "phone_recall" : true
      }
    """
    Then email with subject "Votre inscription" should have been sent to "testtest@l4m.fr"

@login
@deleteCandidateTest
  Scenario: get token for candidate / show profile / edit profile /register to an other event with same datas /register to an other event and change form datas / cancel participation / remove cv
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/oauth/v2/token" with parameters:
    	| key | value |
    	| username | testtest@l4m.fr |
    	| password | Test1234 |
    	| client_id | 465fd597a66ca51b3cae150ed712db2b |
        | client_secret | 561b5d487befb18261992b47887f1e1b122ceb50f0646ae3f7d2f8b5a5e75787d72e2ffd1a931bd428665c339828cae99249dfe7e1cc62108451246af87273d0 |
        | grant_type | password |
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "access_token" should exist
    And I set bearer from response    
    And the JSON node "expires_in" should exist
    And the JSON node "token_type" should exist
    And the JSON node "refresh_token" should exist

    # get profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "GET" request to "/api/candidate/profile"
    Then the response status code should be 200
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Test",
        "lastname" : "TEST",
        "email" : "testtest@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : false,
        "mailing_recall" : false,
        "phone_recall" : true 
      }
    """
    
 
    #edit profile
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "PATCH" request to "/api/candidate/profile/edit" with body:
     """
    {
        "firstname" : "Testedit",
        "lastname" : "TESTedit",
        "email" : "testtestedit@l4m.fr",
        "degree" : "1",
        "sectors" : ["5", "6", "7"],
        "mailingEvents" : true,
        "cv_file" : "data:image/png;base64,AAAFBfj42Pj4"
      }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Testedit",
        "lastname" : "TESTEDIT",
        "email" : "testtestedit@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : true,
        "mailing_recall" : false,
        "phone_recall" : true,        
        "sectors" : ["5", "6", "7"]
      }
    """
    #register to new event
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "POST" request to "/api/event/71/registration" with body:
    """
    {
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Testedit",
        "lastname" : "TESTEDIT",
        "email" : "testtestedit@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : true,
        "mailing_recall" : false,
        "phone_recall" : true

      }
    """
    And I store participation_id

    #register to new event and change values
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "POST" request to "/api/event/74/registration" with body:
    """
    {

        "cv_file" : "data:image/png;base64,AAAFBfj42Pj4"  
    }
    """
    Then the response status code should be 201
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
        "firstname" : "Testedit",
        "lastname" : "TESTEDIT",
        "email" : "testtestedit@l4m.fr",
        "phone" : "06.98.92.97.28",        
        "mailing_events" : true,
        "mailing_recall" : false,
        "phone_recall" : true
      }
    """

    #cancel participation
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send the request to cancel participation_id
    Then the response status code should be 204

    #check if participations has been cancelled
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "GET" request to "/api/candidate/profile"
    Then the response status code should be 200
    And the response should be in JSON
    And the child node "candidate_participations" should not contains "participation_id"
    
    #remove account
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I authorize with my bearer
    And I send a "DELETE" request to "/api/candidate/remove-account"
    Then the response status code should be 204