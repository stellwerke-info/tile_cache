# Tile Cache

This is a tile caching software written in PHP to quickly setup your own indermediary cache for OpenStreetMap tiles.
In our production use case, we cache the tiles for a long time. Caching for shorter times (e.g. a couple of days instead of >1 month)
my lead to worse performance.

Only the tile retrieval is handled by PHP, subsequent requests are served by the HTTP server.

## Requirements
1. PHP 7+
2. Apache HTTP Server, although using nginx should be working fine if you adapt the config file.
3. Enough storage for the tiles (multiple GB)

## Installation
1. Adjust config parameters in `config.php` (rename from `config.sample.php`). Add all layers you want to cache and specifiy all needed information about your site (e.g. User agent and Referrer)
2. Adjust allowed refererrers in `.htaccess` (rename from `.htaccess.sample`)
3. Put an image file into `img/bg.pg` that will be shown in the background on the public landing page and the admin page.
3. Upload all files
4. Add a cron job to `cron.php` (preferably run with PHP-CLI, alternatively you cann specify a (random) `TILECACHE_CRON_TOKEN` in `config.php` and setup a HTTP cron with you token as GET param `?token=...`).

## Administration
Cleanup happens automatically via cron. Additionally you can view disk usage and manually trigger a cleanup on the admin page that is linked on the public landig page or by visiting `admin.php` directly.
