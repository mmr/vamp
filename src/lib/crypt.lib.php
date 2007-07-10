<?
// $Id: crypt.lib.php,v 1.1 2004/01/04 09:37:42 mmr Exp $
function b1n_crypt($str)
{
  require(b1n_SECRETKEY_FILE);
  $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
  $str = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv));
  return $str;
}

function b1n_decrypt($str)
{
  require(b1n_SECRETKEY_FILE);
  $str = base64_decode($str);
  $iv  = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
  $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $str, MCRYPT_MODE_ECB, $iv);
  return $str;
}
?>
