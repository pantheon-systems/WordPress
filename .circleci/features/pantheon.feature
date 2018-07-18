Feature: Perform Pantheon-specific actions

  Background:
    Given I log in as an admin

  Scenario: Change the cache TTL
    When I go to "/wp-admin/options-general.php?page=pantheon-cache"
    Then I should see "Pantheon Page Cache"
    And the "pantheon-cache[default_ttl]" field should contain "600"

    When I fill in "pantheon-cache[default_ttl]" with "300"
    And I press "Save Changes"
    Then I should see "Settings saved."
    And the "pantheon-cache[default_ttl]" field should contain "300"

    When I fill in "pantheon-cache[default_ttl]" with "600"
    And I press "Save Changes"
    Then I should see "Settings saved."
    And the "pantheon-cache[default_ttl]" field should contain "600"

  Scenario: Clear the site cache
    When I go to "/wp-admin/options-general.php?page=pantheon-cache"
    Then I should see "Clear Site Cache"
    And I should not see "Site cache flushed."

    When I press "Clear Cache"
    Then print current URL
    And I should be on "/wp-admin/options-general.php?page=pantheon-cache&cache-cleared=true"
    And I should see "Site cache flushed." in the ".updated" element

  Scenario: Verify the Pantheon MU plugin is present
    When I go to "/wp-admin/plugins.php?plugin_status=mustuse"
    Then I should see "Files in the /wp-content/mu-plugins directory are executed automatically." in the ".tablenav" element
    And I should see "Pantheon" in the "#the-list" element
    And I should see "Building on Pantheon's and WordPress's strengths, together." in the "#the-list" element
