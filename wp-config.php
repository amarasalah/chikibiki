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
define( 'DB_NAME', 'chikibiki' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



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
define( 'AUTH_KEY',         'tloQzdJFJ2Qf2WFuFfX28QRSV97fAUFX6V2KqMyCF10RjuMxEP1pQZ1GHAD6bv93' );
define( 'SECURE_AUTH_KEY',  'kpybw7rte7KnxFuoAYnXNbx4FncAs4ARYTvjeNkowDnvu8VZ5JoMksghBcZuHEPq' );
define( 'LOGGED_IN_KEY',    '91BHOdnSwxyJkaELMg92hK8bdnzLlxMqNMeOGGos8B5Kfz7PxeS4lKJvIRffCF3R' );
define( 'NONCE_KEY',        'uGrpSkERK83cenMKEsjnfbHSBGeMVsjjEUq6RyKAMECI71aHWanqScTKZS3rcYPZ' );
define( 'AUTH_SALT',        'KZ9pgUlFdKt28X0N1tei0VbNaku9bLyrGE3QaIR5zuXvm7FTyVrL2L5BegDPFnbr' );
define( 'SECURE_AUTH_SALT', '4jINaFMHFFZQAWAb1f9rhwle49n3OZ5pULu6iAPd0JjCsjPO1Pystfvaq1YXBPdP' );
define( 'LOGGED_IN_SALT',   'h9gYStH7XDkRKiiHDNnuYvJ7qxVd6CzbTqlMeDeZIV81ielnFcIIbETERstHu86k' );
define( 'NONCE_SALT',       '4rTFwEL3VpjGrzHpOGi1ZazM0Lvl2orewlohXEhNoXXXYnGVc2iJtbHRzwWWGxhf' );

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
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
