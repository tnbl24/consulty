<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'consulty' );

/** Database username */
define( 'DB_USER', 'consulty' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'JF;{C5%BgVy_~1GBXRJk8OP-O=~t~Us41k40_$0~Hek4*coQrx^{}7A)>Ptkxz|2' );
define( 'SECURE_AUTH_KEY',  'K**cz~Z4V{BYC7cB.#gL5^6/>^C {m)6&*k%~BzCjY<SiKsM/Rf5?`*Y*chvc}GJ' );
define( 'LOGGED_IN_KEY',    'q c`9C0Tl<g8lU>;=GD?lt:vz0AV01hzxr!t2d{.qVFs;MS)kUa(G V2f(.U3WA=' );
define( 'NONCE_KEY',        'EToYj|92QRz>iePsSJ,>6aHqNN#K32lWSvzs.zEGQn95dN@&:3a|R=<ODo=?HjIa' );
define( 'AUTH_SALT',        'vR&KP4:3tYjJOj^p L[o>!{2>Tf3_LE$f.EW0l4k|{l5rYQEd}@,+?~8A7.9e=:3' );
define( 'SECURE_AUTH_SALT', '88$UDR<5As*Xk0eFHY0|v*% .x9)+&`=.t{>b?079kfk|!;~/it I:zufyh23>>z' );
define( 'LOGGED_IN_SALT',   '_ij`J}!QmlCl> G@k]hjDxiX:zbEw<9f9.p`W9G#m?*Q*r(n}OTw,<:SR:vEk4^$' );
define( 'NONCE_SALT',       '!9j25I^<tp;>(a;Gf7_>*3I$JYlVG=^87NBF&X5{`*2u iF@bg~h1I,fI0e)m&@<' );

/**#@-*/

/**
 * WordPress database table prefix.
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
