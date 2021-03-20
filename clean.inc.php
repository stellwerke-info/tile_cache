<?php

// this file is based on: https://github.com/cyclestreets/tilecache/blob/master/index.php.

function clean_tiles( int $expiryDays ) {
	// Command to clear out the tiles from subfolders
	$command = 'find . -mindepth 2 -type f -name \'*.png\' -mtime +' . $expiryDays . ' -exec rm -f {} \;';

	echo 'Starting tile clearance: ' . $command . PHP_EOL;
	$lastLine = exec ($command);
	echo 'Completed tile clearance: ' . $lastLine . PHP_EOL;

	// Remove all empty folders
	$command = 'find . -type d -empty -exec rmdir {} \; -prune';
	echo 'Starting empty folder clearance: ' . $command . PHP_EOL;

	// Run
	$lastLine = exec ($command);
	echo 'Completed empty folder clearance: ' . $lastLine . PHP_EOL;
}
