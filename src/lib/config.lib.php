<?
// $Id: config.lib.php,v 1.18 2004/04/17 01:57:13 mmr Exp $
if(get_magic_quotes_gpc() || get_magic_quotes_runtime()){
  die('Turn magic_quote_gpc and magic_quote_runtime off.');
}

// Headers
header('Expires: Wed, 06 Aug 2003 15:50:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  // HTTP/1.1
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Cache-Control: private');
  // HTTP/1.0
header('Pragma: no-cache');

// Prog & Author
define('b1n_VERSION',     '0.1.1');
define('b1n_PROGNAME',    'Vamp ' . b1n_VERSION);
define('b1n_AUTHOR_MAIL', 'mmr@b1n.org');
define('b1n_AUTHOR_NAME', 'Marcio Ribeiro');

// PATHs
define('b1n_PATH_INC',  'include');
define('b1n_PATH_CSS',  'css');
define('b1n_PATH_JS',   'js');
define('b1n_PATH_IMG',  'img');
define('b1n_PATH_LANG', b1n_PATH_LIB . '/lang');
define('b1n_SECRETKEY_FILE',  '../doc/secured/secretkey.php');

// Default Language
  // Available
  // pt_br = Portuguese (Brazil)
  // en_us = English (USA)
define('b1n_LANG_DEFAULT', 'pt_br');

// Misc
define('b1n_URL',     $_SERVER['SCRIPT_NAME']);
define('b1n_FIZZLES', 666);
define('b1n_SUCCESS', 69);
define('b1n_MIN_PASSWD', 3);
define('b1n_MAX_INACTIVE_TIME', '5 days');
define('b1n_MIN_EXP_POINTS_DRINK',  3);
define('b1n_COOKIE_LIFE_TIME',  3600);
define('b1n_SESSION_LIFE_TIME', 3600);
define('b1n_HISTORY_SHOW', 30);
?>
