Feature: Verify various Pantheon features as a logged-out user

  Scenario: Cache-Control should default to TTL=604800
    When I go to "/"
    And the response header "Cache-Control" should be "public, max-age=604800"
    And the response header "Pragma" should not contain "no-cache"

