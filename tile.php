<?php

// this file is based on: https://github.com/cyclestreets/tilecache/blob/master/index.php.

if ( ! file_exists( 'config.php' ) ) {
    die( 'No config file' );
}

require_once( 'config.php' );

ini_set( 'display_errors', 'off' );

if ( strpos( TILECACHE_USER_AGENT, 'example.invalid' ) !== false || strpos( TILECACHE_USER_AGENT, 'example.invalid' ) !== false ) {
	die( 'No user agent or referer specified! This is needed to successfully run your tile cache!' );
}

// Ensure the layer is supported
if ( ! isset( $_GET['layer'] ) || ! is_string( $_GET['layer'] ) || ! isset( TILECACHE_LAYERS[ $_GET['layer'] ] ) ) {
	return false;
}
$layer = $_GET['layer'];

// Ensure the x/y/z parameters are present and numeric
$parameters = [ 'x', 'y', 'z' ];
foreach ( $parameters as $parameter ) {
	if ( ! isset( $_GET[$parameter] ) || ! ctype_digit( $_GET[ $parameter ] ) ) {
		return false;
	}
	${$parameter} = $_GET[ $parameter ];
}

// Define the location
$path = '/' . $z . '/' . $x . '/';
$location = $path . $y . '.png';

// Get the tileserver URL for a specified layer
function getTileserverUrl( array $layers, string $layer ) {
	$tileserver = $layers[ $layer ];
	$serverLetter = chr( 97 + rand( 0, 2 ) );	// i.e. a, b, or c
	$tileserver = str_replace( '(a|b|c)', $serverLetter, $tileserver );
	$tileserver = str_replace( '{s}', $serverLetter, $tileserver );
	return $tileserver;
}

// Retreive a tile from a remote server.
function getTile( array $layers, string $layer, string $location ) {
    $headers = [
		'User-Agent: ' . TILECACHE_USER_AGENT,
		'Referer: ' . TILECACHE_REFERER,
	];

	$tileserver = getTileserverUrl( $layers, $layer );

	// If the tileserver URL has explicit x,y,z parameter placeholders, use that instead of the standard /{z}/{x}/{y}.png layout
	if ( substr_count( $tileserver, '{x}' ) && ( substr_count( $tileserver, '{y}' ) || substr_count( $tileserver, '{-y}' ) ) && substr_count( $tileserver, '{z}' ) ) {
		preg_match( '|^/(.+)/(.+)/(.+)\.png$|', $location, $matches );
		[ $_, $z, $x, $y ] = $matches;
		$url = str_replace ( [ '{x}', '{y}', '{-y}', '{z}' ],  [ $x, $y, $y, $z ], $tileserver );
	} else {
		$url = $tileserver . $location;
	}

    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_FAILONERROR, true );
    $binary = curl_exec( $ch );

	// Get the tile
	if ( $binary === false ) {
		error_log( "Remote tile failed {$url}" );
		return false;
	}
	return $binary;
}

// Try to donload the tile two times.
function getTileWithRetries( array $layers, string $layer, string $location ) {
	for ( $i = 0; $i < 2; $i++ ) {
		if ( $binary = getTile( $layers, $layer, $location ) ) {
			return $binary;
		}
	}

	return false;
}

// Cahe a tile on disk.
function cacheTile( $binary, string $layer, string $path, string $location ) : bool {
	// Ensure the cache is writable
	$cache = __DIR__ . '/';
	if ( ! is_writable( $cache ) ) {
		error_log( "Cannot write to cache $cache" );
		return false;
	}

	// Ensure the directory for the file exists
	$directory = $cache . $layer . $path;
	if ( ! is_dir( $directory ) ) {
		mkdir( $directory, 0777, true );
	}

	// Ensure the directory is writable
	if ( ! is_writable( $directory ) ) {
		error_log( "Cannot write file to directory $directory" );
		return false;
	}

	// Save the file to disk
	$file = $cache . $layer . $location;
	file_put_contents( $file, $binary );

	return true;
}

// Get the tile
$binary = getTileWithRetries( TILECACHE_LAYERS, $layer, $location );

// If no tile was retrieved, serve the null tile and end at this point
if ( ! $binary ) {
	$binary = file_get_contents( './nulltile.png' );
} else {
	// Cache tile on disk.
	if ( ! cacheTile( $binary, $layer, $path, $location ) ) {
		return false;
	}

	// Send cache headers; see https://developers.google.com/speed/docs/best-practices/caching
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', strtotime( '+' . TILECACHE_BROWSER_CACHE_DAYS . ' days' ) ) . ' GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) );
}

// Serve the file
header ( 'Content-Type: image/png' );
die( $binary );
