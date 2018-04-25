<?php

require_once('CdataStrategy.php');

class CdataStrategyIllegalCharactersHtmlEntities implements CdataStrategy
{
    public function should_cdata_be_applied($field, $hasSnippets = false)
    {
        return strlen($field) != strlen(htmlentities($field));
    }

}