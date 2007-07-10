<?
// $Id: index.php,v 1.26 2004/04/13 21:24:13 mmr Exp $
// Library Path
define('b1n_PATH_LIB',  'lib');

// Configuration
require(b1n_PATH_LIB . '/config.lib.php');

// Debug Mode
require(b1n_PATH_LIB  . '/debug.lib.php');

// Database Access
require(b1n_PATH_LIB . '/sqllink.lib.php');
$sql = new b1n_sqlLink(b1n_PATH_LIB . '/db_conf_vamp.lib.php');

// Return messages Array (success/fail)
$ret_msgs = array();

// Libs
require(b1n_PATH_LIB  . '/log.lib.php');        // Log Lirary
require(b1n_PATH_LIB  . '/data.lib.php');       // Data Get/Set
require(b1n_PATH_LIB  . '/formatdata.lib.php'); // Data Formatation
require(b1n_PATH_LIB  . '/checkdata.lib.php');  // Data Validation
require(b1n_PATH_LIB  . '/crypt.lib.php');      // Crypto Library
require(b1n_PATH_LIB  . '/permission.lib.php'); // Login Library
require(b1n_PATH_LANG . '/lang.lib.php');       // Language Configuration
require(b1n_PATH_LIB  . '/session.lib.php');    // Session Management

// Includes
$inc = array();

//------------------------------------------
// Getting vars from $_REQUEST
b1n_getVar('page',    $data['page']);
b1n_getVar('action',  $data['action']);

// Checking if the player is already logged in
if(b1n_isLogged()){
  // Yes, the player is logged in, show the MENU along with the asked page
  // If none was asked, show map page
  $inc[] = b1n_PATH_INC . '/menu.inc.php';
  switch($data['page']){
  case 'player':
  case 'map':
  case 'history':
  case 'help':
    $inc[] = $data['page'] . '/index.php';
    break;
  case 'logout':
    b1n_logOut();
    $inc = array(b1n_PATH_INC . '/login.inc.php');
    break;
  default:
    // None was asked, show map
    $data['page'] = 'map';
    $inc[] = $data['page'] . '/index.php';
  }
}
else {
  b1n_getVar('login',   $data['login']);
  b1n_getVar('passwd',  $data['passwd']);
  b1n_getVar('seccode', $data['seccode']);
  b1n_getVar('master',  $data['master']);
  b1n_getVar('email',   $data['email']);

  // The player is not logged
  if(b1n_cmp($data['page'], 'login')){
    if(b1n_cmp($data['action'], 'login')){
      if(b1n_doLogin($data['login'], $data['passwd'], $data['seccode'])){
        header('Location: ' . b1n_URL);
        exit();
      }
    }
    elseif(b1n_cmp($data['action'], 'newplayer')){
      if(b1n_permNewPlayer($data['login'], $data['passwd'], $data['email'], $data['seccode'], $data['master'])){
        if(b1n_doLogin($data['login'], $data['passwd'], $data['seccode'])){
          header('Location: ' . b1n_URL);
          exit();
        }
      }
    }
  }
  $inc[] = b1n_PATH_INC . '/login.inc.php';
}

// Header
require(b1n_PATH_INC . '/header.inc.php');

// Returned Messages
if(sizeof($ret_msgs)){
  require(b1n_PATH_INC . '/ret.inc.php');
}

// Asked Page(s)
foreach($inc as $i){
  require_once($i);
}

// Footer
require(b1n_PATH_INC . '/footer.inc.php');
?>
