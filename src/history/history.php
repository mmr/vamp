<?
// $Id: history.php,v 1.2 2004/04/12 00:34:26 mmr Exp $
if(is_array($history)){
  echo '<table><tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
  foreach($history as $h){
    echo '<tr>';
    echo '<td><nobr>'.b1n_formatDateHourShow($h['log_add_dt']).'</nobr></td>';

    $msg = $lang['log_'.$h['log_action']];

    $vars = '';
    if(!empty($h['log_vars'])){
      $vars = explode(';', $h['log_vars']);
      if(is_array($vars)){
        foreach($vars as $var){
          list($k, $v) = explode(':', $var);
          if(b1n_cmp($k, 'money')){
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
