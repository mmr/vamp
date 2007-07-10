<?
// $Id: lang.lib.php,v 1.12 2004/08/06 00:32:22 mmr Exp $
// Configuring Internationalization support


// Valid Languages
$valid_languages = array('pt_br', 'en_us');

if(isset($_GET['language'])){
  $data['language'] = $_GET['language'];
}
else {
  $data['language'] = '';
}

$use_default_lang = true;
$lan = $data['language'];

if(empty($data['language'])){
  if(isset($_COOKIE['language']) && !empty($_COOKIE['language'])){
    $use_default_lang = false;
  }
}
else {
  if(is_readable(b1n_PATH_LANG . '/' . $lan . '.lib.php')){
    setcookie('language', $lan, time()+b1n_COOKIE_LIFE_TIME);
    $_COOKIE['language'] = $lan;
    $use_default_lang = false;
  }
}

// Verifying if selected language is valid
if(!in_array($lan, $valid_languages)){
  $lan = b1n_LANG_DEFAULT;
  $use_default_lang = true;
}

// Use default language?
if($use_default_lang){
  setcookie('language', $lan, time()+b1n_COOKIE_LIFE_TIME);
  $_COOKIE['language'] = b1n_LANG_DEFAULT;
}
unset($use_default_lang);
require_once(b1n_PATH_LANG . '/' . $_COOKIE['language'] . '.lib.php');
?>
