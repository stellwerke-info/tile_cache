# Tile Cache

> This is a modernized and stripped-down version of https://github.com/cyclestreets/tilecache that also adds a seperate cron script. All files that are based on the original code are marked (tile.php, clean.inc.php).

This is a tile caching software written in PHP to quickly setup your own intermediary cache for OpenStreetMap tiles.
In our production use case, we cache the tiles for a long time. Caching for shorter times (e.g. a couple of days instead of >1 month)
my lead to worse performance.

Only the tile retrieval is handled by PHP, subsequent requests are served by the HTTP server.

## Requirements
1. PHP 7+ with cURL library
2. Apache HTTP Server, although using nginx should be working fine if you adapt the config file.
3. Enough storage for the tiles (multiple GB)

## Installation
1. Adjust config parameters in `config.php` (rename from `config.sample.php`). Add all layers you want to cache and specifiy all needed information about your site (e.g. User agent and Referrer that will be sent to the upstream tile provider. *Note: Those settings are mandatory as many upstream require contact information for mass downloading of tiles.*)
2. Adjust access policy. This can be created from a mixture of two components
    - Adjust allowed refererrers in `.htaccess` (rename from `.htaccess.sample`). Some browsers do not send the Referer header, and an embedding web page can specify not to send the referer, thus this check is not 100% reliable.
    - Fetch metadata checks [are supported by many moderen browsers](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-Fetch-Site#browser_compatibility) and cannot be spoofed by pages trying to embed tiles from the tile cache.
3. Put an image file into `img/bg.pg` that will be shown in the background on the public landing page and the admin page.
3. Upload all files
4. Add a cron job to `cron.php` (preferably run with PHP-CLI, alternatively you cann specify a (random) `TILECACHE_CRON_TOKEN` in `config.php` and setup a HTTP cron with you token as GET param `?token=...`).

## Administration
Cleanup happens automatically via cron.
