<?php

use Behat\MinkExtension\Context\RawMinkContext;

class ResponseHeader extends RawMinkContext {

    /**
     * Checks, that current page response header is equal to specified.
     *
     * @Then /^the response header "(?P<name>(?:[^"]|\\")*)" should be "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertResponseHeader($name, $value)
    {
        $this->assertSession()->responseHeaderEquals($name, $value);
    }

    /**
     * Checks, that current page response header is not equal to specified.
     *
     * @Then /^the response header "(?P<name>(?:[^"]|\\")*)" should not be "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertResponseHeaderIsNot($name, $value)
    {
        $this->assertSession()->responseHeaderNotEquals($name, $value);
    }

    /**
     * Checks, that current page response header contains specified value.
     *
     * @Then /^the response header "(?P<name>(?:[^"]|\\")*)" should contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertResponseHeaderContains($name, $value)
    {
        $this->assertSession()->responseHeaderContains($name, $value);
    }
    /**
     * Checks, that current page response header does not contain specified value.
     *
     * @Then /^the response header "(?P<name>(?:[^"]|\\")*)" should not contain "(?P<value>(?:[^"]|\\")*)"$/
     */
    public function assertResponseHeaderNotContains($name, $value)
    {
        $this->assertSession()->responseHeaderNotContains($name, $value);
    }

}
