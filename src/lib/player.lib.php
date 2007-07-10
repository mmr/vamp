<?
// $Id: player.lib.php,v 1.9 2004/04/13 21:24:13 mmr Exp $

function b1n_playerGetPosition()
{
#  global $sql;
  return false;
}

function b1n_playerChangePasswd($data)
{
  global $sql, $lang;
  if(b1n_cmp($data['newpasswd'], $data['newpasswd2'])){
    b1n_retMsg($lang['player_password_mismatch']);
    return false;
  }

  $query = '
    UPDATE player
    SET
      pla_passwd  = ' . b1n_inBd(b1n_crypt($data['newpasswd'])) . '
    WHERE
      pla_login   = ' . b1n_inBd($_SESSION['pla_login']) . ' AND
      pla_passwd  = ' . b1n_inBd(b1n_crypt($data['curpasswd']));

  if($sql->sqlQuery($query)){
    b1n_retMsg($lang['player_password_success'], array(), b1n_SUCCESS);
  }
  else {
    b1n_retMsg($lang['player_password_wrong']);
  }
}

function b1n_playerGetLineage()
{
  global $sql;
  $lineage = array();
  if(!empty($_SESSION['player']['pla_parent_id'])){
    b1n_playerLineageAdd($lineage, $_SESSION['player']['pla_parent_id']);
  }
  return $lineage;
}

function b1n_playerLineageAdd(&$lineage, $pla_id)
{
  global $sql;
  $query = "
    SELECT
      pla_parent_id,
      pla_login || ' (' || pla_exp || ') ' AS player
    FROM
      player
    WHERE
      pla_id = " . b1n_inBd($pla_id) . " AND
      pla_active = true AND
      pla_last_login >
          (CURRENT_TIMESTAMP::timestamp - '" . b1n_MAX_INACTIVE_TIME . "'::interval)";

  $rs = $sql->sqlSingleQuery($query);
  if(is_array($rs)){
    $lineage[] = $rs['player'];
    if(!empty($rs['pla_parent_id'])){
      b1n_playerLineageAdd($lineage, $rs['pla_parent_id']);
    }
  }
}

function b1n_playerGetSiblings()
{
  global $sql;
  $siblings = array();

  if(!empty($_SESSION['player']['pla_parent_id'])){
    $query = "
      SELECT
        pla_login || ' (' || pla_exp || ') ' AS player
      FROM
        player
      WHERE
        pla_id != ".$_SESSION['player']['pla_id']." AND
        pla_parent_id = " . b1n_inBd($_SESSION['player']['pla_parent_id']) . " AND
        pla_active = true AND
        pla_last_login >
          (CURRENT_TIMESTAMP::timestamp - '" . b1n_MAX_INACTIVE_TIME . "'::interval)";

    $aux = $sql->sqlQuery($query);
    foreach($aux as $s){
      $siblings[] = $s['player'];
    }
  }
  return $siblings;
}
?>
