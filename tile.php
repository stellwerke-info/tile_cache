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
	http_response_code( 400 );
	die();
}
$layer = $_GET['layer'];

// Ensure the x/y/z parameters are present and numeric
$parameters = [ 'x', 'y', 'z' ];
$l = [];
foreach ( $parameters as $parameter ) {
	if ( ! isset( $_GET[ $parameter ] ) || ! is_string( $_GET[ $parameter ] ) || ! ctype_digit( $_GET[ $parameter ] ) ) {
		http_response_code( 400 );
		die();
	}
	$l[ $parameter ] = (int) $_GET[ $parameter ];
}

final readonly class Loc {
	public function __construct( public int $x, public int $y, public int $z ) {}
	public function path() : string { return '/' . $this->z . '/' . $this->x . '/'; }
	public function location() : string { return $this->path() . $this->y . '.png'; }
}
// Define the location
$loc = new Loc( $l['x'], $l['y'], $l['z'] );

// Get the tileserver URL for a specified layer
/** @param array<array{ 0: string, 1: int }> $layers */
function getTileserverUrl( array $layers, string $layer ) : array {
	[ $tileserver, $maxz ] = $layers[ $layer ];
	$serverLetter = chr( 97 + rand( 0, 2 ) );	// i.e. a, b, or c
	$tileserver = str_replace( '(a|b|c)', $serverLetter, $tileserver );
	$tileserver = str_replace( '{s}', $serverLetter, $tileserver );
	return [ $tileserver, $maxz ];
}

// Retrieve a tile from a remote server.
function getTile( array $layers, string $layer, Loc $loc ) : string|false {
	$headers = [
		'User-Agent: ' . TILECACHE_USER_AGENT,
		'Referer: ' . TILECACHE_REFERER,
	];

	[ $tileserver, $maxz ] = getTileserverUrl( $layers, $layer );

	// Cut off at max allowed z.
	if ( $loc->z > $maxz ) {
		return false;
	}

	// If the tileserver URL has explicit x,y,z parameter placeholders, use that instead of the standard /{z}/{x}/{y}.png layout
	if ( substr_count( $tileserver, '{x}' ) && ( substr_count( $tileserver, '{y}' ) || substr_count( $tileserver, '{-y}' ) ) && substr_count( $tileserver, '{z}' ) ) {
		$url = str_replace( [ '{x}', '{y}', '{-y}', '{z}' ],  [ $loc->x, $loc->y, $loc->y, $loc->z ], $tileserver );
	} else {
		$url = $tileserver . $loc->location();
	}

	$ch = \curl_init( $url );
	\curl_setopt( $ch, \CURLOPT_HEADER, false );
	\curl_setopt( $ch, \CURLOPT_HTTPHEADER, $headers );
	\curl_setopt( $ch, \CURLOPT_RETURNTRANSFER, true );
	\curl_setopt( $ch, \CURLOPT_FAILONERROR, true );
	\curl_setopt( $ch, \CURLOPT_PROTOCOLS, \CURLPROTO_HTTPS );
	\curl_setopt( $ch, \CURLOPT_FORBID_REUSE, true );
	\curl_setopt( $ch, \CURLOPT_FRESH_CONNECT, true );
	\curl_setopt( $ch, \CURLOPT_SSL_VERIFYPEER, true );
	$binary = \curl_exec( $ch );

	// Get the tile
	if ( $binary === false ) {
		error_log( "Remote tile failed {$url}" );
		return false;
	}
	return $binary;
}

// Try to download the tile two times.
function getTileWithRetries( array $layers, string $layer, Loc $loc ) : string|false {
	for ( $i = 0; $i < 2; $i++ ) {
		if ( $binary = getTile( $layers, $layer, $loc ) ) {
			return $binary;
		}
	}

	return false;
}

// Cache a tile on disk.
function cacheTile( string $binary, string $layer, Loc $loc ) : bool {
	// Ensure the cache is writable
	$cache = __DIR__ . '/';
	if ( ! is_writable( $cache ) ) {
		error_log( "Cannot write to cache $cache" );
		return false;
	}

	// Ensure the directory for the file exists
	$directory = $cache . $layer . $loc->path();
	if ( ! is_dir( $directory ) ) {
		mkdir( $directory, 0777, true );
	}

	// Ensure the directory is writable
	if ( ! is_writable( $directory ) ) {
		error_log( "Cannot write file to directory $directory" );
		return false;
	}

	// Save the file to disk
	$file = $cache . $layer . $loc->location();
	file_put_contents( $file, $binary );

	return true;
}

// Get the tile
$binary = getTileWithRetries( TILECACHE_LAYERS, $layer, $loc );

// If no tile was retrieved, serve the null tile and end at this point
if ( ! $binary ) {
	http_response_code( 404 );
	die();
}

// Cache tile on disk.
if ( ! cacheTile( $binary, $layer, $loc ) ) {
	http_response_code( 500 );
	die();
}

// Send cache headers; see https://developers.google.com/speed/docs/best-practices/caching
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', strtotime( '+' . TILECACHE_BROWSER_CACHE_DAYS . ' days' ) ) . ' GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) );

// Serve the file
header ( 'Content-Type: image/png' );
die( $binary );
