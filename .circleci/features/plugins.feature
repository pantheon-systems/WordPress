Feature: Manage WordPress plugins

  Background:
    Given I log in as an admin

  @upstreamonly
  Scenario: Install, activate, deactivate, and delete a plugin
    When I go to "/wp-admin/plugin-install.php?tab=search&s=hello+dolly"
    And I follow "Hello Dolly"
    Then print current URL
    Then I should see "Hello Dolly" in the "#plugin-information-title" element

    When I follow "Install Now"
    Then print current URL
    And I should see "Successfully installed the plugin Hello Dolly"

    When I follow "Activate Plugin"
    Then print current URL
    And I should see "Plugin activated." in the "#message" element
    And I should see a "#dolly" element
    And I should see "1 item" in the ".displaying-num" element

    When I follow "Deactivate"
    Then print current URL
    And I should see "Plugin deactivated." in the "#message" element

    When I follow "Delete"
    Then I should see "You are about to remove the following plugin:"

    When I press "submit"
    Then print current URL
    And I should see "The selected plugin has been deleted." in the "#message" element
    And I should see "No plugins are currently available."
