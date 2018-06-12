<?php

require_once(__DIR__.'/CdataStrategyAlways.php');
require_once(__DIR__.'/CdataStrategyIllegalCharacters.php');
require_once(__DIR__.'/CdataStrategyNever.php');


class CdataStrategyFactory
{
    public function create_strategy($strategy) {

        if($strategy == 'all') {
            return new CdataStrategyAlways();
        } else if($strategy == 'never') {
            return new CdataStrategyNever();
        } else if($strategy == 'auto') {
            return new CdataStrategyIllegalCharacters();
        } else {
            return new CdataStrategyIllegalCharacters();
        }
    }
}