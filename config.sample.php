<?php

// Password used to access the administration script.
const TILECACHE_ADMIN_PASSWORD = '';
// Access token for HTTP request to the cron script (cron.php?token=XXXXX...).
// Set to an empty string to disallow HTTP access (only allow CLI access).
const TILECACHE_CRON_TOKEN = '';

const TILECACHE_CLEAN_DAYS = 35;
const TILECACHE_BROWSER_CACHE_DAYS = 14; // This also needs to be changed in the .htaccess file.

// Branding that is shown to the user and User Agent/Referer sent to the upstresm tile servers.
const TILECACHE_BRANDING = 'Test TileCache';
const TILECACHE_USER_AGENT = 'Test tilecache / Contact: info@example.invalid';
const TILECACHE_REFERER = 'https://example.invalid/';

// All defined updatresm tilesets.
const TILECACHE_LAYERS = [
	'osm'   => 'https://{s}.tile.openstreetmap.org',
	'orm'   => 'https://{s}.tiles.openrailwaymap.org/standard',
];

// Some config options for the public index site.
const INDEX_DESCRIPTION = 'A short public description';

const INDEX_LINKS = [
	'https://example.invalid' => 'An example link',
];

const INDEX_META_LINKS = [
	'https://example.invalid' => 'An example meta link',
];