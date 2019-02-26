<?php
function pmxi_wpmu_drop_tables($tables){	
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'templates';
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'imports';
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'posts';
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'files';
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'history';
    $tables[] = PMXI_Plugin::getInstance()->getTablePrefix() . 'images';
    return $tables;
}