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
		<title><?php echo htmlspecialchars( TILECACHE_BRANDING ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="styles.css">
	</head>
    <body>
        <div id="container">
			<div id="content">
            <h1><?php echo htmlspecialchars( TILECACHE_BRANDING ); ?></h1>
            <p><?php echo INDEX_DESCRIPTION; ?></p>
            <ul id="links">
                <?php foreach( INDEX_LINKS as $link => $display ) {
                    echo '<a class="button" href="' . htmlspecialchars( $link ) . '">' . htmlspecialchars( $display ) . '</a>';
                } ?>
            </ul>
            <p>
                <?php $meta_links = array_merge( INDEX_META_LINKS, [ 'admin.php' => 'Admin' ] );

                $meta_links = array_map( fn( $l ) => '<a class="meta" href="' . htmlspecialchars( $l ) . '">' . htmlspecialchars( $meta_links[ $l] ) . '</a>', array_keys( $meta_links ) );
                echo implode( ' | ', $meta_links );
                ?>
            </p>
        </div>
        </div>
    </body>
</html>