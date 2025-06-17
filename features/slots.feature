Feature: Slots

  @login
  Scenario: Get slots entity
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/slots/28"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,max_candidats,begin_slot,ending_slot,name,is_full" should exist

    @login
    Scenario: Get slots entity for an event
      When I add "Content-Type" header equal to "application/json"
      And I add "Accept" header equal to "application/json"
      And I send a "GET" request to "/api/slots/event/110"
      Then the response status code should be 200
      And the response should be in JSON
      And the header "Content-Type" should contain "application/json"
      And the JSON nodes "id,max_candidats,begin_slot,ending_slot,name,is_full" should exist in first node
