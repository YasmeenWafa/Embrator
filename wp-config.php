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
define('DB_NAME', 'embrator_mdb2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'HJe2Dr7v1hL]%m8 EG{1+MWAfL[,iI.#?K/N(8SI5QL3~{-cGs@_Z2:+v:0?E1i1');
define('SECURE_AUTH_KEY',  'GeR@<Z)^/n(E~e6O,o>8N*pE:~#i;RMB%U~rU8TuhX{L!g`weO,F#2ueG87nXl@I');
define('LOGGED_IN_KEY',    'Z2zQ[c{F&GcM.U3@f=.&nF{*]6fdqTML#EUPJ^Ok3L:~&U<[Lsu)$-f--yLA.QcR');
define('NONCE_KEY',        'UAf;UE9ZaqO<?~R.6!+{zZ_:$jbw0#A.l[KVM>H }^W~wV5wHfP7jc*CXLC5<j0B');
define('AUTH_SALT',        '5ASi<.5HIvuQr@:{}XuY=~GpTGzYo5]t/c,Y?/K72d4,jMF[}B|DE6xY-@&|8t9m');
define('SECURE_AUTH_SALT', 'E2}oA6GNa[ku:nronaswv0XoF,/l8zzeD<bG=5+)QyeBHX^pY~Szei.KL3O*esro');
define('LOGGED_IN_SALT',   ';H8qV/GRsReu$jdUQBV%B9u))86}97mX~(h$SIJ*+qk@Vyr.9p=^LEw=IbTtK*L^');
define('NONCE_SALT',       'ouQ`zfiI]s~>OX9;Kkd[7mj*e32D[p`BM]!{OcR<|8:711{p.!)1i1Uz+hQqD9Yj');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
