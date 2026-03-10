Feature: Manage WordPress users

  Background:
   Given I log in as an admin

  Scenario: User create, update and delete
    When I go to "/wp-admin/user-new.php"
    And I fill in "user_login" with "pantheontestuser"
    And I fill in "email" with "test@example.com"
    And I fill in "pass1" with "password"
    And I fill in "pass2" with "password"
    And I press "createuser"
    Then print current URL
    And I should be on "/wp-admin/users.php?id=2"
    And I should see "New user created." in the "#message" element
    And I should see "2 items" in the ".displaying-num" element

    When I go to "/wp-admin/users.php"
    And I follow "pantheontestuser"
    Then print current URL
    And I should be on "/wp-admin/user-edit.php?user_id=2&wp_http_referer=%2Fwp-admin%2Fusers.php"
    And the "first_name" field should not contain "Pantheon Test"

    When I fill in "first_name" with "Pantheon Test"
    And I press "submit"
    Then print current URL
    And I should be on "/wp-admin/user-edit.php?user_id=2&wp_http_referer=%2Fwp-admin%2Fusers.php"
    And I should see "User updated." in the "#message" element
    And the "first_name" field should contain "Pantheon Test"

    When I go to "/wp-admin/users.php"
    And I follow "Delete"
    Then print current URL
    And I should see "You have specified this user for deletion:"

    When I press "submit"
    Then print current URL
    And I should be on "/wp-admin/users.php?delete_count=1"
    And I should see "User deleted." in the "#message" element
    And I should see "1 item" in the ".displaying-num" element
