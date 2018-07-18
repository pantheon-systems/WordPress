Feature: Install WordPress through the web UI

  @upstreamonly
  Scenario: Install WordPress with the en_US locale
    When I go to "/"
    Then print current URL
    And I should be on "/wp-admin/install.php"

    When I press "language-continue"
    Then print current URL
    And I should be on "/wp-admin/install.php?step=1"
    And I should see "Welcome to the famous five-minute WordPress installation process!"

    When I fill in "weblog_title" with "Pantheon WordPress Upstream"
    And I fill in "user_name" with the command line global variable: "WORDPRESS_ADMIN_USERNAME"
    And I fill in "admin_password" with the command line global variable: "WORDPRESS_ADMIN_PASSWORD"
    And I fill in "admin_password2" with the command line global variable: "WORDPRESS_ADMIN_PASSWORD"
    And I check "pw_weak"
    And I fill in "admin_email" with "wordpress-upstream@getpantheon.com"
    And I press "submit"
    Then print current URL
    And I should be on "/wp-admin/install.php?step=2"
    And I should see "WordPress has been installed."
    And I follow "Log In"
    And I fill in "Username or Email Address" with the command line global variable: "WORDPRESS_ADMIN_USERNAME"
    And I fill in "Password" with the command line global variable: "WORDPRESS_ADMIN_PASSWORD"
    And I press "Log In"
    And I should see "Welcome to WordPress!"

  Scenario: Attempting to install WordPress a second time should error
    When I go to "/wp-admin/install.php"
    Then I should see "You appear to have already installed WordPress."

  Scenario: Verify the active theme is Twenty-Something (no point breaking every year for no good reason)
    When I go to "/"
    Then the response should contain "<link rel='stylesheet' id='twenty"

  @upstreamonly
  Scenario: Delete Akismet and Hello Dolly
    Given I log in as an admin

    When I go to "/wp-admin/plugins.php"
    Then I should see "2 items" in the ".displaying-num" element

    When I follow "Delete"
    Then I should see "You are about to remove the following plugin:"

    When I press "submit"
    Then print current URL
    And I should see "The selected plugin has been deleted." in the "#message" element
    And I should see "1 item" in the ".displaying-num" element

    When I follow "Delete"
    Then I should see "You are about to remove the following plugin:"

    When I press "submit"
    Then print current URL
    And I should see "The selected plugin has been deleted." in the "#message" element
    And I should see "No plugins are currently available."
