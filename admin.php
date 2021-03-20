<?php

session_start();

require_once( 'config.php' );

if ( ! defined( 'TILECACHE_ADMIN_PASSWORD' ) || empty( TILECACHE_ADMIN_PASSWORD ) ) {
    die( 'Kein Passwort gesetzt. Zugriff verweigert.' );
}

if ( isset( $_POST['pw'] ) && $_POST['pw'] == TILECACHE_ADMIN_PASSWORD ) {
    session_regenerate_id();
    $_SESSION['is_logged_in'] = true;
    header( 'Location: admin.php');
    die();
}

$logged_in = isset( $_SESSION['is_logged_in'] ) && $_SESSION['is_logged_in'] === true;

function stats_line( string $dir ) {
    $dir = escapeshellarg( $dir );
    echo shell_exec( 'echo -e "$(du -sh ' . $dir . ')\t$(find ' . $dir . ' -type f | wc -l)"' );
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
            <?php

            if ( ! $logged_in ) {
                echo '<form method="post">';
                echo '<input type="password" name="pw">';
                echo '<input type="submit" value="Login">';
                echo '</form>';
                die();
            }

            if ( $logged_in && isset( $_POST['clean'] ) ) {
                require_once( 'clean.inc.php' );
                echo '<pre>';
                clean_tiles( TILECACHE_CLEAN_DAYS ?? 35 );
                echo '</pre>';
            }

            echo '<h2>Speichernutzung</h2>';
            echo '<pre style="text-align: left; margin: 10px auto; display: inline-block;">';
            echo "Größe\tLayer\tAnzahl Kacheln\n";
            foreach ( TILECACHE_LAYERS as $layer => $_ ) {
                stats_line( $layer );
            }
            echo '</pre>';
            echo '<form method="post">';
            echo '<input type="submit" name="clean" value="Bereinigung jetzt ausführen">';
            echo '</form>';
            ?>
            </div>
        </div>
    </body>
</html>
