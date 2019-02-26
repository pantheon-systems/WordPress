<?php

$mapping = array(
	'Delicious_Brains_API'          => __DIR__ . '/api.php',
	'Delicious_Brains_API_Plugin'   => __DIR__ . '/plugin.php',
	'Delicious_Brains_API_Base'     => __DIR__ . '/base.php',
	'Delicious_Brains_API_Licences' => __DIR__ . '/licences.php',
	'Delicious_Brains_API_Updates'  => __DIR__ . '/updates.php',
);

spl_autoload_register( function ( $class ) use ( $mapping ) {
	if ( isset( $mapping[ $class ] ) ) {
		require $mapping[ $class ];
	}
}, true );

