Feature: user request for password

@login
@resetPasswordRequest
Scenario: user enter his email and ask for new password
	Given user with email "webmaster@l4m.fr" has no active password request
    When I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"   
    And I send a "POST" request to "/api/request/password" with body:
    """
    {
        "email": "webmaster@l4m.fr"
    }
    """
  	Then the response status code should be 200
    And the response should be in JSON
    And email with subject "Réinitialisation de votre mot de passe pour les salons emploi L4M" should have been sent to "webmaster@l4m.fr"