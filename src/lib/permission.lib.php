<?
// $Id: permission.lib.php,v 1.30 2004/08/06 01:02:18 mmr Exp $

function b1n_doLogin($login, $passwd, $seccode)
{
  global $sql, $lang;

  $seccode = b1n_crypt(strtolower($seccode));

  if(!isset($_SESSION['seccode'])){
    return false;
  }

  if(!b1n_cmp($seccode, $_SESSION['seccode'])){
    $msg  = b1n_decrypt($seccode);
    $msg .= ' != ';
    $msg .= b1n_decrypt($_SESSION['seccode']);
    b1n_retMsg($lang['login_err_wrong_code'], array('{code}'=>$msg));
    return false;
  }

  $sql->sqlQuery('BEGIN TRANSACTION');
  $query = "
    SELECT
    -- City
      p.cit_id, c.cit_code,
      c.cit_pos_x0, c.cit_pos_x1,
      c.cit_pos_y0, c.cit_pos_y1,

    -- Clan
      p.cla_id, cl.cla_name,

    -- Ranking
      ra.ran_id, ra.ran_code,

    -- Player Data
      p.pla_parent_id,        -- Parent
      p.pla_action_points,    -- Action Points
      p.pla_money,            -- Carry Money
      p.pla_bank_money,       -- Bank Money 
      p.pla_exp,              -- Experience
      p.pla_pos_x, p.pla_pos_y, -- Position
      p.pla_id,
      p.pla_active,
      p.pla_last_login <=
        (CURRENT_TIMESTAMP::timestamp -
          '" . b1n_MAX_INACTIVE_TIME . "'::interval) AS pla_expired -- Expiration
    FROM
      player p JOIN
      city c  ON (p.cit_id = c.cit_id) LEFT JOIN 
      clan cl ON (p.cla_id = cl.cla_id), ranking ra
    WHERE
      p.pla_login  = " . b1n_inBd($login) . " AND
      p.pla_passwd = " . b1n_inBd(b1n_crypt($passwd)) . "
    ORDER BY
      ra.ran_exp ASC";

  $ret = $sql->sqlSingleQuery($query);

  // Login is Correct
  if(is_array($ret)){
    $err = array('{login}' => $login);
    // Player is Active
    if(b1n_checkTrue($ret['pla_active'])){
      // Player has not Expired
      if(!b1n_checkTrue($ret['pla_expired'])){
        // Updating Last Login
        $query = "UPDATE player SET pla_last_login = CURRENT_TIMESTAMP WHERE pla_id = '" . $ret['pla_id'] . "'";
        if($sql->sqlQuery($query)){
          // Logging Login
          b1n_logAction('login', array(), $ret['pla_id'], 0);

          // Everything is fine
          $player = array(
            'cit_id'    => $ret['cit_id'],
            'cit_code'  => $ret['cit_code'],
            'cit_pos_x0'  => $ret['cit_pos_x0'],
            'cit_pos_x1'  => $ret['cit_pos_x1'],
            'cit_pos_y0'  => $ret['cit_pos_y0'],
            'cit_pos_y1'  => $ret['cit_pos_y1'],
            'cit_cols'    => (($ret['cit_pos_x1'] - $ret['cit_pos_x0'])-2)/2,
            'cit_rows'    => (($ret['cit_pos_y1'] - $ret['cit_pos_y0'])-2)/2,

            'cla_id'    => $ret['cla_id'],
            'cla_name'  => $ret['cla_name'],

            'ran_id'    => $ret['ran_id'],
            'ran_name'  => $ret['ran_code'],

            'pla_parent_id' => $ret['pla_parent_id'],

            'pla_id'    => $ret['pla_id'],
            'pla_login' => $login,
#            'pla_passwd'  => $passwd,
            'pla_action_points' => $ret['pla_action_points'],
            'pla_money' => $ret['pla_money'],
            'pla_bank_money' => $ret['pla_bank_money'],
            'pla_exp'   => $ret['pla_exp'],
            'pla_pos_x' => $ret['pla_pos_x'],
            'pla_pos_y' => $ret['pla_pos_y']);

          // Saving to Session
          $_SESSION['player'] = $player;

          if($sql->sqlQuery('COMMIT TRANSACTION')){
            unset($_SESSION['seccode']); 
            return true;
          }
          else {
            $sql->sqlQuery('ROLLBACK TRANSACTION');
            b1n_retMsg($lang['unexpected']);
          }
        } else b1n_retMsg($lang['unexpected']);
      } else b1n_retMsg($lang['login_err_player_expired'], $err);
    } else b1n_retMsg($lang['login_err_player_not_active'], $err);
  } else b1n_retMsg($lang['login_err_incorrect']);

  return false;
}

function b1n_isLogged()
{
  return isset($_SESSION['player']);
}

function b1n_logOut()
{
  b1n_logAction('logoff', array(), '', 0);
  $_SESSION['player'] = '';
  unset($_SESSION['player']);
  return session_destroy();
}

function b1n_permCheckNewPlayer($login, $passwd, $email, &$master)
{
  global $sql, $lang;

  // Password checking
  if(strlen($passwd) <= b1n_MIN_PASSWD){
    b1n_retMsg($lang['login_passwd_too_small']);
    return false;
  }

  // E-mail checking
  if(!b1n_checkEmail($email, true)){
    b1n_retMsg($lang['login_invalid_email'], array('{email}'=>$email));
    return false;
  }

  if(!empty($master)){
    $err = array('{master}' => $master);

    // Checking if the master exists
    $query = "
      SELECT
        pla_id,
        pla_active,
        pla_last_login <=
          (CURRENT_TIMESTAMP::timestamp -
            '" . b1n_MAX_INACTIVE_TIME . "'::interval) AS pla_expired -- Expiration
      FROM
        player
      WHERE
        pla_login = " . b1n_inBd($master);

    $rs = $sql->sqlSingleQuery($query);
    if(is_array($rs)){
      if(b1n_checkTrue($rs['pla_active'])){
        if(!b1n_checkTrue($rs['pla_expired'])){
          $master = $rs['pla_id'];
        }
        else {
          b1n_retMsg($lang['login_invalid_master'], $err);
          return false;
        }
      }
      else {
        b1n_retMsg($lang['login_err_master_not_active'], $err);
        return false;
      }
    }
    else {
      b1n_retMsg($lang['login_invalid_master'], $err);
      return false;
    }
  }

  // Checking if there is a player with this login already
  $query = "
    SELECT
      COUNT(pla_id) AS count
    FROM
      player
    WHERE
      pla_login = " . b1n_inBd($login);

  $rs = $sql->sqlSingleQuery($query);

  if($rs['count'] > 0){
    b1n_retMsg($lang['login_player_exists'], array('{login}'=>$login));
    return false;
  }

  return true;
}

function b1n_permNewPlayer($login, $passwd, $email, $seccode, $master)
{
  global $sql, $lang;

  $seccode = b1n_crypt($seccode);

  if(!b1n_cmp($seccode, $_SESSION['seccode'])){
    $msg  = b1n_decrypt($seccode);
    $msg .= ' != ';
    $msg .= b1n_decrypt($_SESSION['seccode']);
    b1n_retMsg($lang['login_err_wrong_code'], array('{code}'=>$msg));
    return false;
  }

  if(b1n_permCheckNewPlayer($login, $passwd, $email, $master)){
    $query = '
      SELECT
        func_newPlayer(
          ' . b1n_inBd($login)  . ',
          ' . b1n_inBd(b1n_crypt($passwd)) . ',
          ' . b1n_inBd($email)  . ',
          ' . b1n_inBd($master)  . ') AS ret';

    $rs = $sql->sqlSingleQuery($query);

    $rs['ret'] = b1n_checkTrue($rs['ret']);
    if($rs['ret']){
      b1n_retMsg($lang['unexpected']);
    }
    return $rs['ret'];
  }
  return false;
}
?>
