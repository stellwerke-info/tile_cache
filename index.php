<?php

if ( ! file_exists( 'config.php' ) ) {
    die( 'No config file' );
}

require_once( 'config.php' );

if ( strpos( TILECACHE_USER_AGENT, 'example.invalid' ) !== false || strpos( TILECACHE_USER_AGENT, 'example.invalid' ) !== false ) {
	die( 'No user agent or referer specified! This is needed to successfully run your tile cache!' );
}

?>
<!doctype html>
<html>
    <head>
		<title><?php echo htmlspecialchars( TILECACHE_BRANDING, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex,nofollow">
		<link rel="stylesheet" href="styles.css">
	</head>
    <body>
        <div id="container">
			<div id="content">
            <h1><?php echo htmlspecialchars( TILECACHE_BRANDING, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ); ?></h1>
            <p><?php echo INDEX_DESCRIPTION; ?></p>
            <ul id="links">
                <?php
                $index_links = defined( 'INDEX_LINKS' ) && is_array( INDEX_LINKS ) ? INDEX_LINKS : [];
                foreach( $index_links as $link => $display ) {
                    echo '<a class="button" href="' . htmlspecialchars( $link, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ) . '">' . htmlspecialchars( $display, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ) . '</a>';
                } ?>
            </ul>
            <p>
                <?php $meta_links = defined( 'INDEX_META_LINKS' ) && is_array( INDEX_META_LINKS ) ? INDEX_META_LINKS : [];

                $meta_links = array_map( fn( $l ) => '<a class="meta" href="' . htmlspecialchars( $l, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ) . '">' . htmlspecialchars( $meta_links[ $l ], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ) . '</a>', array_keys( $meta_links ) );
                echo implode( ' | ', $meta_links );
                ?>
            </p>
        </div>
        </div>
    </body>
</html>