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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u154958445_rsmqy' );

/** Database username */
define( 'DB_USER', 'u154958445_mtRaH' );

/** Database password */
define( 'DB_PASSWORD', '5Q1lBwB869' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'c8`=-C^v-@U?U0P0Gl1XsYGWWg}r~J@@735IQ$xQzHzhQE.?O!lFmooO9sh/G&v ' );
define( 'SECURE_AUTH_KEY',   '/@cG6BUOz^U]I;V)43AId4gyuaCaB{xkG7bBfk2qc|7y_Y#3gyp~l*RykbKg~*0M' );
define( 'LOGGED_IN_KEY',     'vb?1=[9o75sY$|5W:v$ffGl_7pxP^tZ^EpJ]d2mtD7Qy4d .H(?WMv^uY=&iY<,b' );
define( 'NONCE_KEY',         '`&c[xEi ; g^V$[SB_r[tAS:9!`@Z}F_@%p+ 4^3+xO8Q#5^oS4@M?v_n}9?,nQW' );
define( 'AUTH_SALT',         'tDwOhkVKBP YIs?0bc72lUe>u?bqw%>l2$LX-Vu_5{WO_;%dC4~sS|.s(2}HI74p' );
define( 'SECURE_AUTH_SALT',  'G7~n?m. &5zk0l{oWxHV&Q(fw;VyJJ^1g3TB/1F`LLX|1sP006!P7*vM;-JQ6wb!' );
define( 'LOGGED_IN_SALT',    'wsC.U<rblFwK%? 22sKm99ux-G1.=7`H5(?s0}AfTD3}a~.tY*pBd`]i=9O9> em' );
define( 'NONCE_SALT',        '2l_&pSn;A:E/K9V4)Xvm>58_nK[j^q[n=-( X=r8N;h =4s20(KF|:=6GA]sZa1-' );
define( 'WP_CACHE_KEY_SALT', '+mE-7k&W6wt(}PmI*rM nd@dW|A2nSc=f[cU@}:fW#pjNF/>d_V_5RGp,]4L{$uR' );


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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );


/* Add any custom values between this line and the "stop editing" line. */



define( 'WP_AUTO_UPDATE_CORE', 'minor' );
define( 'FS_METHOD', 'direct' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
