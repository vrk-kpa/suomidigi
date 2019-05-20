Feature: Ping
  Scenario: Check that the application's root URL is available
    Given I am on the homepage
    Then I should get a 200 HTTP response
