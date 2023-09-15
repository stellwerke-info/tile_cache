<?php

const TILECACHE_CLEAN_DAYS = 35;
const TILECACHE_BROWSER_CACHE_DAYS = 14; // This also needs to be changed in the .htaccess file.

// Branding that is shown to the user and User Agent/Referer sent to the upstream tile servers.
const TILECACHE_BRANDING = 'Test TileCache';
const TILECACHE_USER_AGENT = 'Test tilecache / Contact: info@example.invalid';
const TILECACHE_REFERER = 'https://example.invalid/';

// All defined upstream tilesets.
// - Only keys consisting of letters can be used!
// - The value is an array consisting of a template string, and the max zoom level.
// - The template string supports the parameters {s} and {x},{y},{z}. If the URL does not contain
//   the three coordinate placeholders, a default of `/{z}/{x}/{y}.png` will be appended.
const TILECACHE_LAYERS = [
	'osm'   => [ 'https://{s}.tile.openstreetmap.org', 17 ],
	'orm'   => [ 'https://{s}.tiles.openrailwaymap.org/standard', 17 ],
];

// Some config options for the public index site.
const INDEX_DESCRIPTION = 'A short public description';

const INDEX_LINKS = [
	'https://example.invalid' => 'An example link',
];

const INDEX_META_LINKS = [
	'https://example.invalid' => 'An example meta link',
];
