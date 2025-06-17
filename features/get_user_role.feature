Feature: Get user role

  @login
  Scenario: Get user role for anonymous token
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON node "role" should be null
# OLD / FIXME ?     And the JSON node "role" should be equal to the string "anonymous"

  @loginScan
  Scenario: Get user role for scan token
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON node "role" should be equal to the string "scan"

  @loginL4M
  Scenario: Get user role for l4m token
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON node "role" should be equal to the string "l4m"

  @loginExposant
  Scenario: Get user role for exposant token
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/user"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON node "role" should be equal to the string "exposant"