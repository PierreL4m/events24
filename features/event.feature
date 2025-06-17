Feature: Events

  @login
  Scenario: Get list of events
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/events"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    Then the JSON nodes "id,date,online,offline,has_slots,logo,place,type should exist in first node

  @login
  Scenario: Get an event
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/event/110"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,slug,date,closing_at,online,offline,l4m_registration,has_slots,banner,logo,pub,place,type should exist
