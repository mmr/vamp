<?
// $Id: historic.php,v 1.1 2004/01/02 08:05:45 mmr Exp $
if(is_array($historic))
{
  echo '<table><tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
  foreach($historic as $h)
  {
    echo '<tr>';
    echo '<td><nobr>'.b1n_formatDateHourShow($h['log_add_dt']).'</nobr></td>';

    $msg = $lang['log_'.$h['log_action']];

    $vars = '';
    if(!empty($h['log_vars']))
    {
      $vars = explode(';', $h['log_vars']);
      if(is_array($vars))
      {
        foreach($vars as $var)
        {
          list($k, $v) = explode(':', $var);
          if(b1n_cmp($k, 'money'))
          {
            $v = b1n_formatCurrency($v);
          }
          $msg = str_replace('{'.$k.'}', $v, $msg);
        }
      }
    }

    echo '<td>'.$msg.'</td>';
    echo '</tr>';
  }
  echo "</table>";
}
?>
