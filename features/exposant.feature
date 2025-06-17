Feature: Scan / l4m . Scan candidate at entrance. Search candidate in list

@loginExposant
Scenario: List exposant events
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response body should contains the event in fixtures

  @loginExposant
  @loadCandidateParticipationIdInSessionAndDeleteComment
  Scenario: exposant scan candidate and create comment, get comment, edit comment, send comment by email
    #scan candidate
    Given I describe "exposant scan candidate"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I scan the candidate in fixtures and store candidate_comment in session
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON should be equal to the candidate_comment in session   

    #get comment
    Given I describe "exposant get candidate comment"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I log exposant
    # check if get comment = session comment
    And I get the comment in session 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"

    #edit comment
    Given I describe "exposant edit candidate comment"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I log Exposant
    And I edit the comment in session
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the candidate comment was edited

     #send comment by email
    Given I describe "exposant send comment by email"
    When I add "Content-Type" header equal to "application/json"
    And I log exposant
    And I add "Accept" header equal to "application/json"
    And I send the comment in session by email
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And  email with subject "Profil du candidat Candidate CANDIDATE" should have been sent to "exposant@l4m.fr"

@loginExposant
Scenario: List seen candidates
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event/10000/candidates"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response should contains the candidate in fixtures
    ##this fail if exposant has two comments