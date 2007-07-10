<?
// $Id: history.lib.php,v 1.2 2004/04/12 00:34:26 mmr Exp $
function b1n_historyGet($data)
{
  global $sql;
  $query = "
    SELECT
      log_action,
      log_vars,
      log_add_dt
    FROM
      log
    WHERE
      pla_id = '" . $_SESSION['player']['pla_id'] . "'";

  $aux = array();
  if(!$data['show_login']){
    $aux[] = "log_action != 'login' AND log_action != 'logoff'";
  }

  if(!$data['show_move']){
    $aux[] = "log_action != 'move'";
  }

  if(!$data['show_drink']){
    $aux[] = "log_action != 'drink_from_npc' AND log_action != 'drink_from_player'";
  }

  if(!$data['show_asknpc']){
    $aux[] = "log_action != 'asknpc'";
  }

  if(!$data['show_bank']){
    $aux[] = "log_action != 'withdraw' AND log_action != 'deposit'";
  }

  if(sizeof($aux)){
    $query .= ' AND ';
  }

  $query .= implode(' AND ', $aux) . "
    ORDER BY
      log_add_dt DESC
    LIMIT " . b1n_HISTORY_SHOW;

  return $sql->sqlQuery($query);
}
?>
