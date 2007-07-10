<?
// $Id: checkdata.lib.php,v 1.2 2004/04/12 00:34:26 mmr Exp $
function b1n_checkDate($month, $day, $year, $mandatory = false)
{
  if(!$mandatory && (empty($month) && empty($day) && empty($year))){
    return true;
  }

  return checkdate($month, $day, $year);
}

function b1n_checkHour($hour, $min, $mandatory = false)
{
  if(!$mandatory && (empty($hour) && empty($min))){
    return true;
  }

  $ret = b1n_checkNumeric($hour, $mandatory) && b1n_checkNumeric($min, $mandatory);
  $ret = $ret && ($hour >= 0 && $hour <= 23) && ($min >= 0 && $min <= 59);

  return $ret;
}

function b1n_checkDateHour($month, $day, $year, $hour, $min, $mandatory = false)
{
  return b1n_checkDate($month, $day, $year, $mandatory) && b1n_checkHour($hour, $min, $mandatory);
}

function b1n_checkNumeric($str, $mandatory = false)
{
  if(!$mandatory && empty($str)){
    return true;
  }

  return is_numeric($str);
}

function b1n_checkFilled($str)
{
  return is_numeric($str) || !empty($str);
}

function b1n_checkBoolean($str, $mandatory = false)
{
  if(!$mandatory && empty($str)){
    return true;
  }

  // PHP Boolean
  $ret = is_bool($str);

  // PostgreSQL Boolean
  if(!$ret){
   $ret = b1n_cmp($str, 't') || b1n_cmp($str, 'f');
  }

  // Numeric Boolean
  if(!$ret){
    $ret = is_numeric($str) && ($str === 0 || $str === 1);
  }

  return $ret;
}

function b1n_checkTrue($str)
{
  return b1n_cmp($str, 't');
}

function b1n_checkPhone($str, $mandatory = false)
{
  if(!$mandatory && empty($str)){
    return true;
  }

  return ereg("^([[:digit:]]|-)$", $str);
}

function b1n_checkEmail($str, $mandatory = false)
{
  if(!$mandatory && empty($str)){
    return true;
  }

  $er = "^\w+(\.[\w\-]+)*\@[\w\-]+(\.[\w\-]+)+$";
  return preg_match("#$er#", $str); 
}

function b1n_checkUrl(&$str, $mandatory = false)
{
  if(!$mandatory && empty($str)){
    return true;
  }

  $er  = "^((https?|ftp)://)?\w+(\.[\w\-]+)+(/[\w\-]+\.?[\w\-]*)*/?$";
  $ret = preg_match("#$er#", $str, $match); 

  if($ret && empty($match[2])){
    $str = 'http://' . $str;
  }
  return $ret;
}
?>
