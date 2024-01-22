<?php

\chdir( __DIR__ );

if ( ! \file_exists( 'config.php' ) ) {
	die( 'No config file' );
}

require_once( 'config.php' );

if ( \php_sapi_name() !== 'cli' ) {
	\http_response_code( 403 );
	die();
}

if ( \getcwd() !== __DIR__ ) {
	die( 'Could not change into tile cache root directory...' );
}

// this function is based on: https://github.com/cyclestreets/tilecache/blob/master/index.php.
function clean_tiles( int $expiryDays ) : void {
	$layers = \array_keys( TILECACHE_LAYERS ?? [] );
	$layers = \array_filter( $layers, fn( string $l ) => \strlen( $l ) <= 10 && \preg_match( '/^[a-z]+$/', $l ) );
	if ( $layers === [] ) {
		echo 'No layers defined!' . \PHP_EOL;
		return;
	}
	$layers_shellsafe = \implode( ',', $layers );

	$clean_maxz = 0;
	foreach ( TILECACHE_LAYERS as [ $_, $maxz ] ) {
		$clean_maxz = \max([$clean_maxz, $maxz]);
	}

	// Command to clear out the tiles from subfolders
	$command = 'find . -mindepth 2 -type f -name \'*.png\' -mtime +' . $expiryDays . ' -delete';
	echo 'Starting tile clearance: ' . $command . \PHP_EOL;
	$lastLine = \exec($command);
	echo 'Completed tile clearance: ' . $lastLine . \PHP_EOL;

	if (
		\defined( 'TILECACHE_CLEAN_DAYS_HIGH_RES' ) && is_int( TILECACHE_CLEAN_DAYS_HIGH_RES ) && TILECACHE_CLEAN_DAYS_HIGH_RES > 0
		&& \is_int( $clean_maxz ) && $clean_maxz > 15
	) {
		$command = 'find ./{'. $layers_shellsafe . '}/{15..' . $clean_maxz . '} -type f -name \'*.png\' -mtime +' . ( (int) TILECACHE_CLEAN_DAYS_HIGH_RES ) . ' -delete';
		echo 'Starting high-res tile clearance: ' . $command . \PHP_EOL;
		$lastLine = \exec($command);
		echo 'Completed high-res tile clearance: ' . $lastLine . \PHP_EOL;
	}

	// Remove all empty folders
	$command = 'find . -type d -empty -exec rmdir {} \; -prune';
	echo 'Starting empty folder clearance: ' . $command . \PHP_EOL;
	$lastLine = \exec($command);
	echo 'Completed empty folder clearance: ' . $lastLine . \PHP_EOL;

	$quota = \shell_exec('quota -gls');
	if ( $quota !== \PHP_EOL ) {
		echo \PHP_EOL . \PHP_EOL . 'Quota: ' . \PHP_EOL . $quota . \PHP_EOL;
	}
}

clean_tiles( TILECACHE_CLEAN_DAYS ?? 35 );
