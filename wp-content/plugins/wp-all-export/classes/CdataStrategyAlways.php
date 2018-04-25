<?php

require_once('CdataStrategy.php');

class CdataStrategyAlways implements CdataStrategy
{
    public function should_cdata_be_applied($field)
    {
        return true;
    }

}