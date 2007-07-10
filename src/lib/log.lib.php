<?
// $Id: log.lib.php,v 1.6 2004/04/12 07:13:55 mmr Exp $
function b1n_logAction($action, $vars=array(), $pla_id='', $dec_action_points=1, $arbitrary_date='CURRENT_TIMESTAMP')
{
  global $sql;

  if(empty($pla_id)){
    if(isset($_SESSION['player'])){
      $pla_id = $_SESSION['player']['pla_id'];
    }
    else {
      exit('Couldnt get pla_id, aborting');
    }
  }

  $aux = array();
  foreach($vars as $key => $value){
    $aux[] = $key . ':' . $value;
  }
  $aux = implode(';', $aux);

  $query = "
    INSERT INTO log (
      pla_id,
      log_action,
      log_add_dt,
      log_vars
    )
    VALUES (
      " . b1n_inBd($pla_id) . ",
      " . b1n_inBd($action) . ",
      " . $arbitrary_date . ",
      " . b1n_inBd($aux)  . "
    )";
  $sql->sqlQuery($query);

  if($dec_action_points > 0 &&
     $_SESSION['player']['pla_action_points'] >= $dec_action_points)
  {
    $query = "
      UPDATE player
      SET
        pla_action_points = pla_action_points - $dec_action_points
      WHERE
        pla_id = ".b1n_inBd($pla_id);

    $_SESSION['player']['pla_action_points'] -= $dec_action_points;
  }
}
?>
