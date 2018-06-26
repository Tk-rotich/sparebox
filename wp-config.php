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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sparebox');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6UrJL,Bv9H)$4s$PES`AYx]n=55Hfw-_{z0]7`*bC$RaE(G6)@H]h+VOo8a|HXkP');
define('SECURE_AUTH_KEY',  'sH3tfCfABjgb$:ZJlCPqezwZ<L;&H>!_FP)J@DNIogb*TUX,i1)xvfIOcAh` GV:');
define('LOGGED_IN_KEY',    '-O;k I =]Cbv;TV.V]nKA5q$_=eXs_Xgv`ZJ3_!<h7zdybWD@/.HR^mewBpg/HW!');
define('NONCE_KEY',        'IR8{L!3s5U,[UJnY2NZZI)2Hn<)n18#a!!_sj>T7bUorGW-NH:8O_,[kVCvW4d U');
define('AUTH_SALT',        '& %3N%=.d!>t#+re1?PKS|6J,Y_?,pd3iF+BGOs|dw,w^W5g+N3aO2b9].DFtW@x');
define('SECURE_AUTH_SALT', '/MXg9x!e>:0otWIBYe&Q28bXQf2r%/G0];^6~4wHP(1JKOk>Z.2D9u8!O;;t8X=&');
define('LOGGED_IN_SALT',   'C!{zjqk*A!S*2PI&c3YjWpU&$Wu2Mr[F$uJZ+UIXi$r:+2 y/m)d+YZU}mOH.CdC');
define('NONCE_SALT',       'Z?Lb=c3H7Tu[wo<=RZ{17<n>N&?h%J$8m,`oP.Zxi{?G0}UIF tgr^t;)+3c+iED');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'sparebox_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
