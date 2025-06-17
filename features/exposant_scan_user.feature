Feature: ExposantScanUser created on Organization created
ExposantScanUser can login in api
ExposantScanUser can access list of events
ExposantScanUser can scan candidates
ExposantScanUser can list candidates for an event
ExposantScanUser can take candidate note

@login
Scenario: create new organization and login exposant scan user
	Given I create a new organization
	Then I should have a new ExposantScanUser and I give it a bearer
	And the ExposantScanUser can login

	
@loginExposantScanUser
Scenario: get exposant type	
	When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/user"
    Then the response status code should be 200
	And the response status code should be 200
	And the JSON node "role" should be equal to the string "exposant"
	
@loginExposantScanUser
Scenario: access list of events
	Given I add the organization to the event in fixtures
	When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event"
    Then the response status code should be 200
	And the response status code should be 200
	# And the first response body node contains JSON:
	# """
	# {
	#     "id": 10000,	    
	#     "place": 2
	#   }
# 	"""
 
@loadCandidateParticipationIdInSessionAndDeleteComment
@loginExposantScanUser
Scenario: exposant scan user can scan candidate and create comment, get comment, edit comment
    #scan candidate
    Given I describe "exposant scan user scan candidate"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I scan the candidate in fixtures and store candidate_comment in session
    #failure here problem multiple organisation with test_organization as name
    Then the response status code should be 201
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the JSON should be equal to the candidate_comment in session   

    #get comment
    Given I describe "exposant scan user get candidate comment"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I log ExposantScan
    # check if get comment = session comment
    And I get the comment in session
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"

    #edit comment
    Given I describe "exposant scan user edit candidate comment"
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I log ExposantScan
    And I edit the comment in session
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the candidate comment was edited


@loginExposantScanUser
Scenario: List seen candidates
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And I send a GET request to "/api/exposant/event/10000/candidates"
    Then the response status code should be 200
    And the response should be in JSON
    And the header "Content-Type" should contain "application/json"
    And the response should contains the candidate in fixtures