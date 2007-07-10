<?
// $Id: debug.lib.php,v 1.1 2004/04/12 07:14:06 mmr Exp $
// Debug
if(isset($_GET['debug'])){
  if($_GET['debug'] === 'on'){
    if(!isset($_COOKIE['debug'])){
      setcookie('debug', 'on', time()+b1n_COOKIE_LIFE_TIME);
      $_COOKIE['debug'] = 'on';
    }
  }
  else {
    setcookie('debug', '', time()-3600);
    unset($_COOKIE['debug']);
  }
}
define('b1n_DEBUG', isset($_COOKIE['debug']));
?>
