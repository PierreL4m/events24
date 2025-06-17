Feature: Candidate registration fail
A candidate cannot register to an event until he fills all required form field.
A candidate cannot register if he already has an account

  @login
  Scenario: register to an event with only firstname
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "message" should contain "Validation Failed"    
    And the JSON node "errors.children.firstname" should have 0 elements
    And the JSON node "errors.children.lastname.errors[0]" should contain "Merci de saisir votre nom"
    And the JSON node "errors.children.email.errors[0]" should contain "Merci de saisir votre email"
    And the JSON node "errors.children.phone.errors[0]" should contain "Merci de saisir votre numéro de téléphone"
#    And the JSON node "errors.children.mobility.errors[0]" should contain "Merci de choisir votre mobilité"
#    And the JSON node "errors.children.degree.errors[0]" should contain "Merci de choisir votre diplôme"
    And the JSON node "errors.children.cv_file.errors" should have 1 elements
    And the JSON node "errors.children.plainPassword.errors" should have 1 elements

 @login
 Scenario: register to an event with only firstname lastname
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "message" should contain "Validation Failed"    
    And the JSON node "errors.children.firstname" should have 0 elements
    And the JSON node "errors.children.lastname" should have 0 element
    And the JSON node "errors.children.email.errors[0]" should contain "Merci de saisir votre email"
    And the JSON node "errors.children.phone.errors[0]" should contain "Merci de saisir votre numéro de téléphone"
#    And the JSON node "errors.children.mobility.errors[0]" should contain "Merci de choisir votre mobilité"
#    And the JSON node "errors.children.degree.errors[0]" should contain "Merci de choisir votre diplôme"
    And the JSON node "errors.children.cv_file.errors" should have 1 elements
    And the JSON node "errors.children.plainPassword.errors" should have 1 elements    

 @login
 Scenario: register to an event with only firstname lastname email
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtesttest@l4m.fr"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "message" should contain "Validation Failed"    
    And the JSON node "errors.children.firstname.errors" should not exist
    And the JSON node "errors.children.lastname.errors" should not exist
    And the JSON node "errors.children.email.errors" should not exist
    And the JSON node "errors.children.phone.errors[0]" should contain "Merci de saisir votre numéro de téléphone"
#    And the JSON node "errors.children.mobility.errors[0]" should contain "Merci de choisir votre mobilité"
#    And the JSON node "errors.children.degree.errors[0]" should contain "Merci de choisir votre diplôme"
    And the JSON node "errors.children.cv_file.errors" should have 1 elements
    And the JSON node "errors.children.plainPassword.errors" should have 1 elements
 
  @login
  Scenario: register to an event missing cv_file city sectors
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtesttest@l4m.fr",
        "phone" : "0698929728",
        "mobility" : "1",
        "degree" : "2",
        "plainPassword" : "Test1234",
        "mailingEvents" : true,
        "mailingRecall" : true,
        "phoneRecall" : true
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "message" should contain "Validation Failed"    
    And the JSON node "errors.children.firstname.errors" should not exist
    And the JSON node "errors.children.lastname.errors" should not exist
    And the JSON node "errors.children.email.errors" should not exist
    And the JSON node "errors.children.phone.errors" should not exist
#    And the JSON node "errors.children.mobility.errors" should not exist
#    And the JSON node "errors.children.degree.errors" should not exist
    And the JSON node "errors.children.cv_file.errors" should have 1 elements
    And the JSON node "errors.children.plainPassword.errors" should not exist
    And the JSON node "errors.children.mailingEvents.errors" should not exist
    And the JSON node "errors.children.mailingRecall.errors" should not exist
    And the JSON node "errors.children.phoneRecall.errors" should not exist

  
  @login
  Scenario: register to an event missing sectors city
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtesttest@l4m.fr",
        "phone" : "0698929728",
        "mobility" : "1",
        "degree" : "2",
        "plainPassword" : "Test1234",
        "mailingEvents" : true,
        "mailingRecall" : true,
        "phoneRecall" : true,
        "cv_file" : "test"
    }
    """
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "errors.children.firstname.errors" should not exist
    And the JSON node "errors.children.lastname.errors" should not exist
    And the JSON node "errors.children.email.errors" should not exist
    And the JSON node "errors.children.phone.errors" should not exist
#    And the JSON node "errors.children.mobility.errors" should not exist
#    And the JSON node "errors.children.degree.errors" should not exist    
    And the JSON node "errors.children.plainPassword.errors" should not exist
    And the JSON node "errors.children.mailingEvents.errors" should not exist
    And the JSON node "errors.children.mailingRecall.errors" should not exist
    And the JSON node "errors.children.phoneRecall.errors" should not exist
    # And the JSON node "errors.children.sectors.errors[0]" should contain "Merci de choisir au moins un secteur"
    
  
 @login
  Scenario: register to an event no city
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtesttest@l4m.fr",
        "phone" : "0698929728",
        "mobility" : "1",
        "degree" : "2",
        "plainPassword" : "Test1234",
        "mailingEvents" : true,
        "mailingRecall" : true,
        "phoneRecall" : true,
        "cv_file" : "test",
        "sectors" : ["1", "2"]
    }
    """
    Then the response status code should be 400
    And the response should be in JSON   
    And the JSON node "errors.children.firstname.errors" should not exist
    And the JSON node "errors.children.lastname.errors" should not exist
    And the JSON node "errors.children.email.errors" should not exist
    And the JSON node "errors.children.phone.errors" should not exist
#    And the JSON node "errors.children.mobility.errors" should not exist
#    And the JSON node "errors.children.degree.errors" should not exist    
    And the JSON node "errors.children.plainPassword.errors" should not exist
    And the JSON node "errors.children.mailingEvents.errors" should not exist
    And the JSON node "errors.children.mailingRecall.errors" should not exist
    And the JSON node "errors.children.phoneRecall.errors" should not exist
    And the JSON node "errors.children.sectors.errors" should not exist
    # And the JSON node "errors.children.city.errors[0]" should contain "Merci de choisir une ville dans la liste"


  @login
  Scenario: register to an event wrong cv format
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a "POST" request to "/api/event/70/registration" with body:
    """
    {
        "firstname" : "test",
        "lastname" : "test",
        "email" : "testtesttest@l4m.fr",
        "phone" : "0698929728",
        "plainPassword" : "Test1234",
        "mailingEvents" : true,
        "mailingRecall" : true,
        "phoneRecall" : true,
        "cv_file" : "test"
    }
    """
    # OLD "city" : "2"
    # "sectors" : ["1", "2"],
    #    "mobility" : "1",
    #    "degree" : "2",
        
    Then the response status code should be 400
    And the response should be in JSON
    And the JSON node "message" should contain "CV file is not valid"
    And the JSON node "errors.children.firstname.errors" should not exist
    And the JSON node "errors.children.lastname.errors" should not exist
    And the JSON node "errors.children.email.errors" should not exist
    And the JSON node "errors.children.phone.errors" should not exist
#    And the JSON node "errors.children.mobility.errors" should not exist
#    And the JSON node "errors.children.degree.errors" should not exist    
    And the JSON node "errors.children.plainPassword.errors" should not exist
    And the JSON node "errors.children.mailingEvents.errors" should not exist
    And the JSON node "errors.children.mailingRecall.errors" should not exist
    And the JSON node "errors.children.phoneRecall.errors" should not exist
    And the JSON node "errors.children.sectors.errors" should not exist
    And the JSON node "errors.children.city.errors" should not exist