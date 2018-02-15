<?php

require_once('CdataStrategy.php');

class CdataStrategyIllegalCharacters implements CdataStrategy
{
    private $illegalCharacters = array('<','>','&', '\'', '"');

    public function should_cdata_be_applied($field)
    {
        foreach($this->illegalCharacters as $character) {
            if(strpos($field, $character) !== false) {
                return true;
            }
        }

        return false;
    }

}