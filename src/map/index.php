<?
// $Id: index.php,v 1.29 2004/04/17 01:57:13 mmr Exp $
// This page will build the map based on player's position

require(b1n_PATH_LIB . '/map.lib.php');

// Checking actions 
switch($data['action']){
case 'move':
  b1n_getVar('x', $data['x']);
  b1n_getVar('y', $data['y']);
  b1n_mapMovePlayer($data['x'], $data['y']);
  break;
case 'drink':
  b1n_getVar('target', $data['target']);
  b1n_getVar('id', $data['id']);
  switch($data['target']){
  case 'player':
    b1n_mapDrinkFromPlayer($data['id']);
    break;
  case 'npc':
    if($ret = b1n_mapDrinkFromNPC($data['id'])){
      $msg = str_replace('{exp}', $ret[0], $lang['map_drink_from_npc_exp']);
      if($ret[1] > 0){
        $msg .= ' ' . $lang['and'] . ' ';
        $msg .= str_replace('{money}', $ret[1], $lang['map_drink_from_npc_money']);
      }
      b1n_retMsg($msg, array(), b1n_SUCCESS);
    }
    break;
  default:
    b1n_retMsg($lang['map_drink_invalid_target']);
    break;
  }
  break;
case 'asknpc':
  $msg = $lang['unexpected'];
  b1n_getVar('bty_id', $data['bty_id']);
  if($ret = b1n_mapNearest($data['bty_id'])){
    $msg = str_replace('{bty}', $lang[$ret['bty_code']], $lang['map_action_asknpc_answer']);
    //if(is_null(strpos($ret['bty_code'], 'bank'))){
      $msg = str_replace('{building}', $lang[$ret['bui_code']], $msg);
    //}
    //else {
    //  $msg = str_replace('{building}', '', $msg);
    //}
    $msg = str_replace('{str1}', $lang[$ret['str1_code']], $msg);
    $msg = str_replace('{str2}', $lang[$ret['str2_code']], $msg);
  }
  b1n_retMsg($msg, array(), b1n_SUCCESS);
  break;
case 'show_more_commands':
  $_SESSION['show_more_commands'] = 1;
  break;
case 'hide_more_commands':
  if(isset($_SESSION['show_more_commands'])){
    unset($_SESSION['show_more_commands']);
  }
  break;
case 'deposit_money':
  b1n_getVar('money', $data['money']);
  b1n_mapDeposit($data['money']);
  break;
case 'withdraw_money':
  b1n_getVar('money', $data['money']);
  b1n_mapWithdraw($data['money']);
  break;
case 'move_city':
  b1n_getVar('cit_id', $data['cit_id']);
  b1n_getVar('x', $data['x']);
  b1n_getVar('y', $data['y']);
  b1n_mapMoveCityPlayer($data['cit_id'], $data['x'], $data['y']);
}

$cur_x = $_SESSION['player']['pla_pos_x'];
$cur_y = $_SESSION['player']['pla_pos_y'];

/*
That is what the map looks like:
 
 ---------.---------.---------
|         |         |         |
| X-1,Y-1 |  X,Y-1  | X+1,Y-1 |
|         |         |         |
|---------|---------|---------|
|         |         |         |
|  X-1,Y  |   X,Y   |  X+1,Y  |
|         |         |         |
|---------|---------|---------|
|         |         |         |
| X-1,Y+1 |  X,Y+1  | X+1,Y+1 |
|         |         |         |
 ---------^---------^---------

We need everything (buildings, other players, etc) in each of these 9 coordinates.
*/
  // Buildings
$query = "
  SELECT *
  FROM
    func_mapGetBuildings(" . $_SESSION['player']['cit_id'] . ", " . $cur_x . ", " . $cur_y . ") AS (
      bty_name  text,
      bty_code  text,
      bui_id    integer,
      bui_code  text,
      bui_hold  integer,
      bui_pos_x integer,
      bui_pos_y integer
    )";

$aux = $sql->sqlQuery($query);

if(is_array($aux)){
  foreach($aux as $i){
    $map[$i['bui_pos_x'].':'.$i['bui_pos_y']]['building'] = $i;
  }
}

// Players
$query = "
  SELECT *
  FROM
    func_mapGetPlayers(
      " . $_SESSION['player']['cit_id'] . ", " . $cur_x . ", " . $cur_y . ", " . $_SESSION['player']['pla_id'] . ", '" . b1n_MAX_INACTIVE_TIME . "'
    )
    AS (
      pla_id    integer,
      pla_login text,
      pla_exp   integer,
      pla_pos_x integer,
      pla_pos_y integer
    )";

$aux = $sql->sqlQuery($query);

if(is_array($aux)){
  foreach($aux as $i){
    $map[$i['pla_pos_x'].':'.$i['pla_pos_y']]['players'][] = $i;
  }
}

// Getting NPCs
$query = "
  SELECT *
  FROM
    func_mapGetNPCs (
      " . $_SESSION['player']['cit_id'] . ", " . $cur_x . ", " . $cur_y . "
    )
    AS (
      nty_name  text,
      nty_code  text,
      nty_blood char(4),
      npc_id    int,
      npc_pos_x int,
      npc_pos_y int
    )";

$aux = $sql->sqlQuery($query);

if(is_array($aux)){
  foreach($aux as $i){
    $map[$i['npc_pos_x'].':'.$i['npc_pos_y']]['npcs'][] = $i;
  }
}

// Getting Street Junctions
$query = "
  SELECT *
  FROM
    func_mapGetStreetJunctions(" . $_SESSION['player']['cit_id'] . ", $cur_x, $cur_y) AS (
      str1_code text,
      str2_code text,
      str_pos_x integer,
      str_pos_y integer
    )";

$aux = $sql->sqlQuery($query);

if(is_array($aux)){
  foreach($aux as $i){
    $map[$i['str_pos_x'].':'.$i['str_pos_y']]['street'] = $i;
  }
}

// Seeing if there are messages to be written
if(sizeof($ret_msgs)){
  require(b1n_PATH_INC . '/ret.inc.php');
}
?>
<table class='game_box' id='map_table'>
  <tr>
    <td>
      <table class='map_top'>
        <tr>
          <td>
<? require($data['page'] . '/top.php'); ?>
          </td>
        </tr>
      </table> 

            <table class='map_map' id='map_box'>
<? require($data['page'] . '/map.php'); ?>
            </table>

      <table class='map_bottom'>
        <tr>
          <td>
<? require($data['page'] . '/bottom.php'); ?> 
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<div class='map_text'>
  <br />
<?
$aux = $lang['map_label_you'];
$aux = str_replace('{player}',  '<b>' . $_SESSION['player']['pla_login'] . '</b>', $aux);
$aux = str_replace('{exp}',   '<b>' . $_SESSION['player']['pla_exp']   . '</b>',   $aux);
$aux = str_replace('{money}', '<b>' . b1n_formatCurrency($_SESSION['player']['pla_money']) . '</b>', $aux);
$aux = str_replace('{rank}',  '<b>' . $lang[$_SESSION['player']['ran_name']]  . '</b>',  $aux);
echo $aux;
?>
</div>
<script src='<?= b1n_PATH_JS ?>/fix.js'></script>
