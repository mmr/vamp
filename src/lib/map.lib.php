<?
// $Id: map.lib.php,v 1.29 2004/12/05 15:28:02 mmr Exp $
function b1n_mapCheckDrinkFromNPC($id)
{
  global $sql, $lang;
  if($_SESSION['player']['pla_action_points'] >= 1){
    $query = "
      SELECT nty_blood
      FROM npc n JOIN npc_type nt ON (n.nty_id = nt.nty_id)
      WHERE
        npc_id = '" . $id . "' AND
        npc_pos_x = '" . $_SESSION['player']['pla_pos_x'] . "' AND
        npc_pos_y = '" . $_SESSION['player']['pla_pos_y'] . "'";

    $ret = $sql->sqlSingleQuery($query);
    if(!$ret){
      b1n_retMsg($lang['map_drink_invalid_target']);
    }
  }
  else {
    b1n_retMsg($lang['map_action_not_enough']);
    $ret = false;
  }
  return $ret;
}

function b1n_mapCheckDrinkFromPlayer($id)
{
  global $sql, $lang;

  // Checking whether the target is at the same position as the biter
  if($_SESSION['player']['pla_action_points'] >= 1){
    $query = "
      SELECT 
        pla_login, pla_exp
      FROM
        player
      WHERE
        pla_id = '" . $id . "' AND
        pla_pos_x = '" . $_SESSION['player']['pla_pos_x'] . "' AND
        pla_pos_y = '" . $_SESSION['player']['pla_pos_y'] . "'";

    if($ret = $sql->sqlSingleQuery($query))
    {
      // Checking if the biter is trying to bite a player that it did already
      // and that the target didnt move since then
      $query = "
        SELECT
          log_add_dt > (
            SELECT CASE WHEN ((
              SELECT COUNT(log_id) FROM log WHERE
                pla_id = $id AND log_action = 'move') > 0)
            THEN (
              SELECT log_add_dt FROM log WHERE 
                pla_id = $id AND log_action = 'move'
              ORDER BY log_add_dt DESC LIMIT 1
            )
            ELSE 
              CURRENT_TIMESTAMP
            END AS log_add_dt
          ) AS did_bite
        FROM
          log
        WHERE
          pla_id = ".$_SESSION['player']['pla_id']." AND
          log_action = 'drink_from_player' AND
          log_vars LIKE 'pla_id:$id;%'
        ORDER BY
          log_add_dt DESC";

      $aux = $sql->sqlSingleQuery($query);
      if(b1n_checkTrue($aux['did_bite'])){
        b1n_retMsg($lang['map_drink_not_again']);
        $ret = false;
      }
    }
    else {
      b1n_retMsg($lang['map_drink_invalid_target']);
    }
  }
  else {
    b1n_retMsg($lang['map_action_not_enough']);
    $ret = false;
  }
  return $ret;
}

function b1n_mapCheckMoveCityPlayer($cit_id, &$x, &$y)
{
  global $sql, $lang;
  if($_SESSION['player']['pla_action_points'] >= 1){
    $query = "
      SELECT
        cit_code,
        cit_pos_x0, cit_pos_x1,
        cit_pos_y0, cit_pos_y1
      FROM
        city
      WHERE
        cit_id = ".b1n_inBd($cit_id)." AND (
        (cit_pos_x1 = ".($x-1)." AND cit_pos_y0 <= $y AND cit_pos_y1 >= $y) OR
        (cit_pos_x0 = ".($x+1)." AND cit_pos_y0 <= $y AND cit_pos_y1 >= $y) OR
        (cit_pos_y1 = ".($y-1)." AND cit_pos_x0 <= $x AND cit_pos_x1 >= $x) OR
        (cit_pos_y0 = ".($y+1)." AND cit_pos_x0 <= $x AND cit_pos_x1 >= $x))";
      
    if($ret = $sql->sqlSingleQuery($query)){
      if($x < $_SESSION['player']['cit_pos_x0']){
        $x--;
      }
      elseif($y < $_SESSION['player']['cit_pos_y0']){
        $y--;
      }
      elseif($x > $_SESSION['player']['cit_pos_x1']){
        $x++;
      }
      elseif($y > $_SESSION['player']['cit_pos_y1']){
        $y++;
      }
      else {
        b1n_retMsg($lang['map_move_illegal']);
        $ret = false;
      }
    }
    else {
      b1n_retMsg($lang['map_move_illegal']);
    }
  }
  else {
    b1n_retMsg($lang['map_action_not_enough']);
    $ret = false;
  }
  return $ret;
}

function b1n_mapCheckMovePlayer($x, $y)
{
  global $lang;
  if($_SESSION['player']['pla_action_points'] >= 1){
    $cur_x = $_SESSION['player']['pla_pos_x'];
    $cur_y = $_SESSION['player']['pla_pos_y'];

    $valid_moves = array(
      ($cur_x-1).':'.($cur_y-1),  ($cur_x).':'.($cur_y-1),  ($cur_x+1).':'.($cur_y-1),
      ($cur_x-1).':'.($cur_y),    ($cur_x).':'.($cur_y),    ($cur_x+1).':'.($cur_y),
      ($cur_x-1).':'.($cur_y+1),  ($cur_x).':'.($cur_y+1),  ($cur_x+1).':'.($cur_y+1));

    foreach($valid_moves as $aux){
      $i = explode(':', $aux);
      if($i[0] < $_SESSION['player']['cit_pos_x0'] ||
         $i[0] > $_SESSION['player']['cit_pos_x1'] ||
         $i[1] < $_SESSION['player']['cit_pos_y0'] ||
         $i[1] > $_SESSION['player']['cit_pos_y1'])
      {
        unset($valid_moves[array_search($aux, $valid_moves)]);
      }
    }
    
    $ret = in_array($x.':'.$y, $valid_moves);
    if(!$ret){
      b1n_retMsg($lang['map_move_illegal']);
    }
  }
  else {
    b1n_retMsg($lang['map_action_not_enough']);
    $ret = false;
  }
  return $ret;
}

function b1n_mapDrinkFromNPC($id)
{
  global $sql, $lang;
  if($ret = b1n_mapCheckDrinkFromNPC($id)){
    srand(microtime()*1000);
    $exp = explode(':', $ret['nty_blood']);
    $exp = rand((int)$exp[0], (int)$exp[1]);

    // 50% chances of getting any money
    if(rand(1,2)==1){
      // 1 ~ 500 
      $money = rand(1,500);
    }
    else {
      $money = 0;
    }
    // Starting Trans
    $sql->sqlQuery('BEGIN TRANSACTION');

    // Updating NPC Stats
    $query = "
      UPDATE npc SET
        npc_pos_x = random()*".$_SESSION['player']['cit_cols'].",
        npc_pos_y = random()*".$_SESSION['player']['cit_rows']."
      WHERE
        npc_id = '" . $id . "'";

    if($sql->sqlQuery($query)){
      // Updating Player Stats
      $query = "
        UPDATE player SET
          pla_exp = pla_exp + '" . $exp . "',
          pla_money = pla_money + '" . $money . "'
        WHERE
          pla_id = '" . $_SESSION['player']['pla_id'] . "'";

      if($sql->sqlQuery($query)){
        if($sql->sqlQuery('COMMIT TRANSACTION')){
          $aux = array(
            'npc_id'  => $id,
            'exp'     => $exp,
            'money'   => $money);
          b1n_logAction('drink_from_npc', $aux);

          $_SESSION['player']['pla_exp'] += $exp;
          $_SESSION['player']['pla_money'] += $money;
          return array($exp,$money);
        }
      }
    }
    b1n_retMsg($lang['unexpected']);
    $sql->sqlQuery('ROLLBACK TRANSACTION');
  }
  return false;
}

function b1n_mapDrinkFromPlayer($id)
{
  global $sql, $lang;
  if($ret = b1n_mapCheckDrinkFromPlayer($id)){
    $sql->sqlQuery('BEGIN TRANSACTION');
      
    if($ret['pla_exp'] > b1n_MIN_EXP_POINTS_DRINK){
      // Updating Target Stats
      $query = "
        UPDATE player SET
          pla_exp = pla_exp - 1
        WHERE
          pla_id = '" . $id . "'";

      if($sql->sqlQuery($query)){
        // Updating Player Stats
        $query = "
          UPDATE player SET
            pla_exp = pla_exp + 1
          WHERE
            pla_id = '" . $_SESSION['player']['pla_id'] . "'";

        if($sql->sqlQuery($query)){
          if($sql->sqlQuery('COMMIT TRANSACTION')){
            $aux = array(
              'pla_id' => $id,
              'player' => $ret['pla_login']);
            b1n_logAction('drink_from_player', $aux);

            $_SESSION['player']['pla_exp']++;
            b1n_retMsg($lang['map_drink_from_player'], array('{player}'=>$ret['pla_login']), b1n_SUCCESS);
            return true;
          }
        }
      }
    }
    b1n_retMsg($lang['unexpected']);
    $sql->sqlQuery('ROLLBACK TRANSACTION');
  }
  return false;
}

function b1n_mapMovePlayer($x, $y)
{
  global $sql, $lang;
  if(b1n_mapCheckMovePlayer($x, $y)){
    $query = "
      UPDATE player
      SET
        pla_pos_x = " . b1n_inBd($x) . ",
        pla_pos_y = " . b1n_inBd($y) . "
      WHERE
        pla_id = '" . $_SESSION['player']['pla_id'] . "'";

    if($sql->sqlQuery($query)){
      $aux = array(
        'cit_id'  => $_SESSION['player']['cit_id'],
        'from_x'  => $_SESSION['player']['pla_pos_x'],
        'from_y'  => $_SESSION['player']['pla_pos_y'],
        'to_x'    => $x,
        'to_y'    => $y);
      b1n_logAction('move', $aux);
      unset($aux);

      $_SESSION['player']['pla_pos_x'] = $x;
      $_SESSION['player']['pla_pos_y'] = $y;
      return true;
    }
    else {
      b1n_retMsg($lang['unexpected']);
    }
  }
  return false;
}

function b1n_mapBuildShop($id)
{
  global $sql, $lang;

  $ret = '';
  $query = "
    SELECT
      ite_id, ite_code,
      ite_price, ite_quantity
    FROM
      view_item
    WHERE
      bui_id = '" . $id . "'";

  $rs = $sql->sqlQuery($query);

  if(is_array($rs)){
    $ret  = "<form action='".b1n_URL."' method='post'>";
    $ret .= "<input type='hidden' name='page'   value='map' />";
    $ret .= "<input type='hidden' name='action' value='buy' />";
    $ret .= "<input type='hidden' name='type'   value='item' />";
    $ret .= "<select class='i_select' name='ite_id'>";
    foreach($rs as $i){
      $ret .= "<option value='".$i['ite_id']."'>".$lang[$i['ite_code']]." (".$i['ite_quantity'].") \$".$i['ite_price']."</option>";
    }
    $ret .= "</select>";
    $ret .= "&nbsp;&nbsp;<input type='submit' value='".$lang['ok']."' class='i_button' />";
    $ret .= "</form>";
  }

  return $ret;
}

function b1n_mapBuildGuild($id)
{
  global $sql, $lang;

  $ret = '';
  $query = "
    SELECT
      pow_id, pow_code,
      pow_price, pow_quantity
    FROM
      view_power
    WHERE
      bui_id = '" . $id . "'"; 

  $rs = $sql->sqlQuery($query);

  if(is_array($rs)){
    $ret  = "<form action='".b1n_URL."' method='post'>";
    $ret .= "<input type='hidden' name='page'   value='map' />";
    $ret .= "<input type='hidden' name='action' value='buy' />";
    $ret .= "<input type='hidden' name='type'   value='power' />";
    $ret .= "<select class='i_select' name='pow_id'>";
    foreach($rs as $i){
      $ret .= "<option value='".$i['pow_id']."'>".$lang[$i['pow_code']]." (".$i['pow_quantity'].") \$".$i['pow_price']."</option>";
    }
    $ret .= "</select>";
    $ret .= "&nbsp;&nbsp;<input type='submit' value='".$lang['ok']."' class='i_button' />";
    $ret .= "</form>";
  }

  return $ret;
}

function b1n_mapBuildPub($id)
{
  global $sql, $lang;

  $ret = '';
  $query = "
    SELECT
      dri_id, dri_code,
      dri_price, dri_quantity
    FROM
      view_drink
    WHERE
      bui_id = '" . $id . "'";

  $rs = $sql->sqlQuery($query);

  if(is_array($rs)){
    $ret  = "<form action='".b1n_URL."' method='post'>";
    $ret .= "<input type='hidden' name='page'   value='map' />";
    $ret .= "<input type='hidden' name='action' value='buy' />";
    $ret .= "<input type='hidden' name='type'   value='drink' />";
    $ret .= "<select class='i_select' name='dri_id'>";
    foreach($rs as $i){
      $ret .= "<option value='".$i['dri_id']."'>".$lang[$i['dri_code']]." (".$i['dri_quantity'].") \$".$i['dri_price']."</option>";
    }
    $ret .= "</select>";
    $ret .= "&nbsp;&nbsp;<input type='submit' value='".$lang['ok']."' class='i_button' />";
    $ret .= "</form>";
  }

  return $ret;
}

function b1n_mapBuildBank($action)
{
  global $sql, $lang;

  $ret  = "<form name='f_bank' action='".b1n_URL."' method='post'>";
  $ret .= "<input type='hidden' name='page'   value='map' />";

  $ret .= "<select class='i_select' name='action'> ";
  $ret .= "<option value=''>---</option>";
  $ret .= "<option value='deposit_money'".(b1n_cmp($action, 'deposit_money')?' selected="selected"':'').">".$lang['bank_deposit']."</option>";
  $ret .= "<option value='withdraw_money'".(b1n_cmp($action, 'withdraw_money')?' selected="selected"':'').">".$lang['bank_withdraw']."</option>";
  $ret .= "</select> ";
  $ret .= "<input type='text' name='money' class='i_text' size='5' maxlength='5' /> ";
  $ret .= "<input type='submit' value='".$lang['ok']."' class='i_button' /><br />";
  $ret .= "</form>";
  $ret .= "<script type='text/javascript'>document.f_bank.money.focus();</script>";

  if(!empty($_SESSION['player']['pla_bank_money'])){
    $ret .= '('.str_replace('{money}', b1n_formatCurrency($_SESSION['player']['pla_bank_money']), $lang['bank_money']) . ')<br />';
  }

  $ret .= '('.str_replace('{money}', b1n_formatCurrency($_SESSION['player']['pla_money']), $lang['map_label_money']).')';
  
  return $ret;
}

function b1n_mapBuildGiveMoney($players)
{
  global $lang;
  $ret  = "<form action='".b1n_URL."' method='post'>";
  $ret .= "<input type='hidden' name='action' value='givemoney' />";
  $ret .= $lang['map_label_give_money'];
  $ret .= " <input type='text' name='money' size='5' maxlength='5' class='i_text' />";
  #$ret .= " (" . str_replace('{money}', b1n_formatCurrency($_SESSION['player']['pla_money']), $lang['map_label_money']) . ") ";
  $ret .= ' ' . $lang['to'];

  $ret .= " <select class='i_select' name='towho'>";
  foreach($players as $p){
    $ret .= "<option value='".$p['pla_id']."'>".$p['pla_login']."</option>";
  }
  $ret .= "</select>";

  $ret .= " <input type='submit' value='".$lang['ok']."' class='i_button' />";
  $ret .= "</form>";

  return $ret;
}

function b1n_mapBuildTalk()
{
  global $lang;
  $ret  = "<form action='".b1n_URL."' method='post'>";
  $ret .= "<input type='hidden' name='action' value='talk' />";
  $ret .= $lang['map_label_talk'];
  $ret .= " <input type='text' name='text' class='i_text' maxlength='255' /> ";
  $ret .= " <input type='submit' value='".$lang['ok']."' class='i_button' />";
  $ret .= "</form>";

  return $ret;
}

function b1n_mapBuildAskNpc()
{
  global $sql, $lang;
  $query = "
    SELECT
      bty_id, bty_code
    FROM
      building_type
    WHERE
      bty_canask = true";

  $rs = $sql->sqlQuery($query);
  $ret = '';

  if(is_array($rs)){
    $ret .= "<form action='".b1n_URL."' method='post'>";
    $ret .= "<input type='hidden' name='action' value='asknpc' />";
    $ret .= $lang['map_label_asknpc'];
    $ret .= " <select class='i_select' name='bty_id'>";
    foreach($rs as $b){
      $ret .= "<option value='".$b['bty_id']."'>".$lang[$b['bty_code']]."</option>";
    }
    $ret .= "</select>";
    $ret .= " <input type='submit' value='".$lang['ok']."' class='i_button' />";
  }

  return $ret;
}

function b1n_mapNearest($bty_id)
{
  global $sql;
  $cur_x = $_SESSION['player']['pla_pos_x'];
  $cur_y = $_SESSION['player']['pla_pos_y'];

  $query = "
    SELECT
      bt.bty_code,
      b.bui_code,
      b.bui_pos_x,
      b.bui_pos_y,
      s1.str_code AS str1_code,
      s2.str_code AS str2_code,
      ABS(
        (b.bui_pos_x - " . $_SESSION['player']['pla_pos_x'] . ") +
        (b.bui_pos_y - " . $_SESSION['player']['pla_pos_y'] . ")) AS ret
    FROM
      building b JOIN
      building_type bt ON (b.bty_id = bt.bty_id),
      street s1,
      street s2
    WHERE
      b.cit_id  = '" . $_SESSION['player']['cit_id'] . "' AND
      bt.bty_id = '" . $bty_id . "' AND

      NOT 
        (b.bui_pos_x = " . b1n_inBd($cur_x) . " AND b.bui_pos_y = " . b1n_inBd($cur_y) . ") AND

      ((s1.str_pos_x1 = b.bui_pos_x     AND s2.str_pos_y0 = b.bui_pos_y)  OR

      ((s1.str_pos_x1 = b.bui_pos_x - 1 AND s2.str_pos_y0 = b.bui_pos_y)  OR 
       (s1.str_pos_x1 = b.bui_pos_x + 1 AND s2.str_pos_y0 = b.bui_pos_y)) OR

      ((s1.str_pos_x1 = b.bui_pos_x     AND s2.str_pos_y0 = b.bui_pos_y - 1)  OR 
       (s1.str_pos_x1 = b.bui_pos_x     AND s2.str_pos_y0 = b.bui_pos_y + 1)) OR

      ((s1.str_pos_x1 = b.bui_pos_x - 1 AND s2.str_pos_y1 = b.bui_pos_y - 1) OR
       (s1.str_pos_x1 = b.bui_pos_x + 1 AND s2.str_pos_y1 = b.bui_pos_y - 1) OR  
       (s1.str_pos_x1 = b.bui_pos_x - 1 AND s2.str_pos_y0 = b.bui_pos_y + 1) OR  
       (s1.str_pos_x1 = b.bui_pos_x + 1 AND s2.str_pos_y0 = b.bui_pos_y + 1)))
    ORDER BY
        ABS(b.bui_pos_x - " . $_SESSION['player']['pla_pos_x'] . "),
        ABS(b.bui_pos_y - " . $_SESSION['player']['pla_pos_y'] . ")";

  b1n_logAction('asknpc');

  return $sql->sqlSingleQuery($query);
}

function b1n_mapDeposit($money)
{
  global $sql;
  if(!empty($money) && b1n_checkNumeric($money) && $money > 0){
    $money = ((int)$money);
    if($money > $_SESSION['player']['pla_money']){
      $money = $_SESSION['player']['pla_money'];
    }

    if($money > 0){
      $query = "
        UPDATE player
        SET
          pla_money = pla_money - " . b1n_inBd($money) . ",
          pla_bank_money = pla_bank_money + " . b1n_inBd($money) . "
        WHERE
          pla_id = '" . $_SESSION['player']['pla_id'] . "'";

      $rs = $sql->sqlQuery($query);

      if($rs){
        $aux = array('money' => $money);
        b1n_logAction('deposit', $aux);

        $_SESSION['player']['pla_money'] -= $money;
        $_SESSION['player']['pla_bank_money'] += $money;
      }
    }
  }
}

function b1n_mapWithdraw($money)
{
  global $sql;
  if(!empty($money) && b1n_checkNumeric($money) && $money > 0){
    $money = ((int)$money);
    if($money > $_SESSION['player']['pla_bank_money']){
      $money = $_SESSION['player']['pla_bank_money'];
    }

    if($money > 0){
      $query = "
        UPDATE player
        SET
          pla_money = pla_money + " . b1n_inBd($money) . ",
          pla_bank_money = pla_bank_money - " . b1n_inBd($money) . "
        WHERE
          pla_id = '" . $_SESSION['player']['pla_id'] . "'";

      $rs = $sql->sqlQuery($query);

      if($rs){
        $aux = array('money' => $money);
        b1n_logAction('withdraw', $aux);

        $_SESSION['player']['pla_money'] += $money;
        $_SESSION['player']['pla_bank_money'] -= $money;
      }
    }
  }
}

function b1n_mapMoveCityPlayer($cit_id, $x, $y)
{
  global $sql, $lang;

  if($ret = b1n_mapCheckMoveCityPlayer($cit_id, $x, $y)){
    // Updating User data
    $query = "
      UPDATE player SET
        cit_id = ".b1n_inBd($cit_id)."
      WHERE
        pla_id = ".b1n_inBd($_SESSION['player']['pla_id']);

    if($sql->sqlQuery($query)){
      // Updating session data
      $_SESSION['player']['cit_id']     = $cit_id;
      $_SESSION['player']['cit_code']   = $ret['cit_code'];
      $_SESSION['player']['cit_pos_x0'] = $ret['cit_pos_x0'];
      $_SESSION['player']['cit_pos_x1'] = $ret['cit_pos_x1'];
      $_SESSION['player']['cit_pos_y0'] = $ret['cit_pos_y0'];
      $_SESSION['player']['cit_pos_y1'] = $ret['cit_pos_y1'];
      $_SESSION['player']['cit_cols']   = (($ret['cit_pos_x1'] - $ret['cit_pos_x0'])-2)/2;
      $_SESSION['player']['cit_rows']   = (($ret['cit_pos_y1'] - $ret['cit_pos_y0'])-2)/2;
      $_SESSION['player']['pla_pos_x']  = $x;
      $_SESSION['player']['pla_pos_y']  = $y;
    }
    else {
      $ret = false;
      b1n_retMsg($lang['unexpected']);
    }
  }
  else {
    b1n_retMsg($lang['map_move_illegal']);
  }
  return $ret;
}
?>
