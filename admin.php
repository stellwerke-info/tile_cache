<?php

ini_set( 'session.cookie_samesite', 'Strict' );
ini_set( 'session.cookie_secure', '1' );
ini_set( 'session.use_strict_mode', 'on' );

session_start();

if ( ! file_exists( 'config.php' ) ) {
    die( 'No config file' );
}

require_once( 'config.php' );

if ( ! defined( 'TILECACHE_ADMIN_PASSWORD' ) || empty( TILECACHE_ADMIN_PASSWORD ) ) {
    die( 'Kein Passwort gesetzt. Zugriff verweigert.' );
}

if ( isset( $_POST['pw'] ) && is_string( $_POST['pw'] ) && $_POST['pw'] == TILECACHE_ADMIN_PASSWORD ) {
    session_regenerate_id();
    $_SESSION['is_logged_in'] = true;
    $_SESSION['timestamp'] = time();
    header( 'Location: admin.php' );
    die();
}

$logged_in = isset( $_SESSION['is_logged_in'] ) && isset( $_SESSION['timestamp'] )
    && is_int( $_SESSION['timestamp'] )
    && $_SESSION['is_logged_in'] === true && $_SESSION['timestamp'] < time() + 10 * 60;

function stats_line( string $dir ) {
    if ( ! preg_match( '/^[a-z]$/', $dir ) ) {
        die();
    }
    $dir = escapeshellarg( $dir );
    $line = shell_exec( 'echo -e "$(du -sh ' . $dir . ')\t$(find ' . $dir . ' -type f | wc -l)"' );
    echo htmlspecialchars( $line, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 );
}
?>

<!doctype html>
<html>
    <head>
		<title><?php echo htmlspecialchars( TILECACHE_BRANDING, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="styles.css">
	</head>
    <body>
        <div id="container">
			<div id="content">
            <h1><?php echo htmlspecialchars( TILECACHE_BRANDING, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 ); ?></h1>
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
                //TODO: htmlspecialchars
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
