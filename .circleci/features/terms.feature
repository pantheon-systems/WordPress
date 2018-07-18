Feature: Manage WordPress terms

  Background:
    Given I log in as an admin

  Scenario: Create a new tag
    When I go to "/wp-admin/edit-tags.php?taxonomy=post_tag"
    And I fill in "tag-name" with "Pantheon Testing Tag"
    And I press "submit"
    Then print current URL
    And I should see "Tag added."
    And I should see "Pantheon Testing Tag"
