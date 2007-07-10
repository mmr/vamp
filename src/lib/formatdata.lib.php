<?
// $Id: formatdata.lib.php,v 1.6 2004/01/02 08:05:33 mmr Exp $
function b1n_formatDateShow($var)
{
  global $lang;
  $var = strtok($var, '.');
  return date($lang['date_format'], strtotime($var));
}

function b1n_formatDateHourShow($var)
{
  global $lang;
  $var = strtok($var, '.');
  return date($lang['date_hour_format'], strtotime($var));
}

function b1n_formatHour($hour, $min)
{
  return sprintf('%02d:%02d', $hour, $min);
}

function b1n_formatCurrency($n,$sign=true)
{
  global $lang;
  return ($sign?'$':'').number_format($n,0,$lang['decimails_sep'],$lang['thousands_sep']);
}
?>
