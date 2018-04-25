<?php

require_once('CdataStrategy.php');

class CdataStrategyIllegalCharacters implements CdataStrategy
{
    private $illegalCharacters = array('<','>','&', '\'', '"','**LT**', '**GT**');

    public function should_cdata_be_applied($field, $hasSnippets = false)
    {
        if($hasSnippets) {
            $this->illegalCharacters = array('<','>','&', '**LT**', '**GT**');
        }
        
        foreach($this->illegalCharacters as $character) {
            if(strpos($field, $character) !== false) {
                return true;
            }
        }

        return false;
    }

}