Feature: get lists : areas , mobility, sector, degree, places 

@login
Scenario: get areas
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/areas"
  	Then the response status code should be 200
    And the response should be in JSON
    And the first response body node contains JSON:
    """
    {
        "name": "Alsace"
    }
    """

@login
Scenario: get countries
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/countries"
    Then the response status code should be 200
    And the response should be in JSON
    And the first response body node contains JSON:
    """
    {
        "name": "France"
    }
    """

@login
Scenario: get department
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/departments"
    Then the response status code should be 200
    And the response should be in JSON
    And the first response body node contains JSON:
    """
    {
        "name": "Bas-Rhin"
    }
    """

@login
Scenario: get cities 'li' as parameter
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/cities?filter=li"
    Then the response status code should be 200
    And the response should be in JSON
    And the first response body node contains JSON:
    """
    {
        "name": "Licy-Clignon"
    }
    """
@login
Scenario: get cities 'lille' as parameter
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/cities?filter=lille"
    Then the response status code should be 200
    And the response should be in JSON
    And the first response body node contains JSON:
    """
    {
        "name": "Lillemer"
    }
    """
    And the JSON response should have "5" elements

@login
Scenario: get mobilities
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/mobility"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should exist in first node
    And the JSON node "name" should exist in first node
    And the JSON node "slug" should exist in first node

@login
Scenario: get sectors
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/sector"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should exist in first node
    And the JSON node "name" should exist in first node

@login
Scenario: get degrees
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/degree"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should exist in first node
    And the JSON node "name" should exist in first node
    And the JSON node "slug" should exist in first node

@login
Scenario: get places
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/places"
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON node "id" should exist in first node
    And the JSON node "name" should exist in first node
    And the JSON node "slug" should exist in first node
    And the JSON node "address" should exist in first node
    And the JSON node "colors" should exist in first node

@login
Scenario: get kinepolis place '(id=1)'
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "GET" request to "/api/places/1"
    Then the response status code should be 200
    And the response should be in JSON
    And the response body contains JSON:
    """
    {
      "id": 1,
      "name": "Kinépolis",
      "address": "1 rue du Château d'Isenghien",
      "cp": "59160",
      "city": "Lomme",
      "slug": "lomme",
      "latitude": "50.6517681",
      "longitude": "2.9808181"
    }
    """
    And the JSON node "colors" should exist