<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

/**
 * Define application features from the specific context.
 */
class AdminLogIn implements Context, SnippetAcceptingContext {

    /** @var \Behat\MinkExtension\Context\MinkContext */
    private $minkContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @Given I log in as an admin
     */
    public function ILogInAsAnAdmin()
    {
        $this->minkContext->visit('wp-login.php');
        $this->minkContext->fillField('log', getenv('WORDPRESS_ADMIN_USERNAME'));
        $this->minkContext->fillField('pwd', getenv('WORDPRESS_ADMIN_PASSWORD'));
        $this->minkContext->pressButton('wp-submit');
        $this->minkContext->assertPageAddress("wp-admin/");
    }

    /**
     * Fills in form field with specified id|name|label|value
     * Example: When I fill in "admin_password2" with the command line global variable: "WORDPRESS_ADMIN_PASSWORD"
     *
     * @When I fill in :arg1 with the command line global variable: :arg2
     */
    public function fillFieldWithGlobal($field, $value)
    {
        $this->minkContext->fillField($field, getenv($value));
    }
}
