<?php

chdir( __DIR__ );

if ( ! file_exists( 'config.php' ) ) {
    die( 'No config file' );
}

require_once( 'config.php' );
require_once( 'clean.inc.php' );

if ( php_sapi_name() !== 'cli' ) {
    if ( ! defined( 'TILECACHE_CRON_TOKEN' ) || empty( TILECACHE_CRON_TOKEN ) || ! is_string( TILECACHE_CRON_TOKEN ) ) {
        die( 'Kein Zugangstoken definiert!' );
    }

    if ( ! isset( $_GET['token'] ) || ! is_string( $_GET['token'] ) || $_GET['token'] !== TILECACHE_CRON_TOKEN ) {
        die( 'Kein Token angegeben oder Token falsch!' );
    }
}

if ( getcwd() !== __DIR__ ) {
    die( 'Could not change into tile cache root directory...' );
}

clean_tiles( TILECACHE_CLEAN_DAYS ?? 35 );
