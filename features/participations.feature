Feature: Participations
  @login
  Scenario: Get list of participation for event id=78 (rennes-2018)
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/participations/event/78"
    # Then I dump the response
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,company_name,logo" should exist in first node

  @login
  Scenario: Get a participation id = 3366 (martin bower france)
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "GET" request to "/api/participation/3366"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON nodes "id,presentation,company_name,slug,info,address_l1,address_number,address_l2,address_l3,cp,city,contact_title,contact_name,contact_first_name,contact_email,contact_phone,facebook,instagram,twitter,viadeo,linkedin,th,div,senior,jd,youtube,stand_number,premium,pub,start_pub,end_pub,pub_count,street,organization,sites,logo" should exist
