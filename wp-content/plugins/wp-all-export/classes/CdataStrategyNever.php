<?php

require_once('CdataStrategy.php');

class CdataStrategyNever implements CdataStrategy
{
    public function should_cdata_be_applied($field, $hasSnippets = false)
    {
        return false;
    }

}