<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Kk!&YFI/LunF::mcXqH_*}YTc|1^{gS6%s8lo X<,ptZNX~~|qc/Y WeWYF!V?OS' );
define( 'SECURE_AUTH_KEY',  'tvN!Mr0`7l)yo(SFVIRAlFSm+}p1A3dXbJ2P!2GSe!v-{h/}=A8AH|:,!+BZ,t)2' );
define( 'LOGGED_IN_KEY',    '`p*EkARW8wyVb= GNr@47mJFmQ71>rjAL&P JNnx=vUvG|b~>9a2a+HdP_>i<Y:{' );
define( 'NONCE_KEY',        'H[G4Y]0(O1(mIa#NEU6{;5ZDN<pp^}XMjBVtD`r%:<@OSo%(kiCJoGym6# :D/[2' );
define( 'AUTH_SALT',        't=;G&v!rUalX43nZ}%WL-dL9cR|+<Uw,_+C&%x 94=NEWUZ) S<,.DgO#jJP;WFN' );
define( 'SECURE_AUTH_SALT', 'OkjyFuE bQ?z0H,|n2o{AWSYZwC]2V<2onm,[j8VUPl[D9V]aWR4HePdgMu}fXhV' );
define( 'LOGGED_IN_SALT',   'km0tsnO8Dc2[coBU{s_|4{_Fl%4edjc0=`hxpTA,fl`0M`8$Mfb4ft_=RrfrLk=b' );
define( 'NONCE_SALT',       '&Mexg2r[,-q4`MC=V*jA2mfv`U)h>m3v8XPgIu(4<8;%|IG_f&h2i@ w[)6(siK;' );

/**#@-*/

define('JWT_AUTH_SECRET_KEY', 'km0tsnO8Dc2[coBU{s_|4{_Fl%4edjc0=`hxpTA,fl`0M`8$Mfb4ft_=RrfrLk=b');
define('JWT_AUTH_CORS_ENABLE', true);
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
