Feature: Scan / l4m . Scan candidate at entrance. Search candidate in list

  @loginL4M
  Scenario: Get list of events
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,date,online,offline,logo,place" should exist in first node

  @loginScan
  Scenario: Get list of events
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,date,online,offline,logo,place" should exist in first node

  @loginScan
  @loadEventIdInSession
  Scenario: List candidates (only one in fixutres) 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to show candidates with event in fixtures 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response body should contains the candidate in fixtures

  @loginScan
  @loadEventIdInSession
  Scenario: Search for non existing candidate 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to search candidate with event in fixtures with search "fnxozlmf" 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON should be equal to:
    """
    []
    """
  @loginScan
  @loadEventIdInSession
  Scenario: Search and find candidate by email 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to search candidate with event in fixtures with search "candidate@l4m.fr" 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response body should contains the candidate in fixtures

  @loginScan
  @loadEventIdInSession
  Scenario: Search case insensitive and find candidate by lastname or firstname
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to search candidate with event in fixtures with search "candidate" 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response body should contains the candidate in fixtures

  @loginScan
  @loadEventIdInSession
  Scenario: Search case insensitive and find candidate by start of 
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to search candidate with event in fixtures with search "can" 
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response body should contains the candidate in fixtures