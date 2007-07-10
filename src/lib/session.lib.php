<?
// $Header: /cvs/vamp/src/lib/session.lib.php,v 1.12 2004/04/16 22:25:38 mmr Exp $
$ses_sql  = new b1n_sqlLink(b1n_PATH_LIB . '/db_conf_session.lib.php');

function b1n_sessionOpen($save_path, $ses_name)
{
  b1n_sessionGC(b1n_SESSION_LIFE_TIME);
  return true;
}
 
function b1n_sessionClose()
{
  return true;
}
 
function b1n_sessionRead($ses_id)
{
  global $ses_sql;

  $query = '
    SELECT
      ses_data
    FROM
      session
    WHERE
      ses_id = ' . b1n_inBd($ses_id) . ' AND
      ses_ip = ' . b1n_inBd($_SERVER['REMOTE_ADDR']);
  $rs = $ses_sql->sqlSingleQuery($query);

  if(is_array($rs)){
    return $rs['ses_data'];
  }
  else {
    $query = '
      INSERT INTO session (
        ses_id, ses_ip
      )
      VALUES (
        ' . b1n_inBd($ses_id) . ',
        ' . b1n_inBd($_SERVER['REMOTE_ADDR']) . '
      )';
    $ses_sql->sqlQuery($query);
    return '';
  }
}

function b1n_sessionWrite($ses_id, $ses_data)
{
  global $ses_sql;

  $query = '
    UPDATE session SET
      ses_data = ' . b1n_inBd($ses_data) . ',
      ses_last_updated = CURRENT_TIMESTAMP
    WHERE
      ses_id = ' . b1n_inBd($ses_id) . ' AND
      ses_ip = ' . b1n_inBd($_SERVER['REMOTE_ADDR']);

  $rs = (bool)$ses_sql->sqlQuery($query);
  if(!$rs){
    b1n_retMsg($lang['log_cheater']);
  }
  return $rs;
}
 
function b1n_sessionDestroy($ses_id)
{
  global $ses_sql;
  $query = 'DELETE FROM session WHERE ses_id = ' . b1n_inBd($ses_id);
  $ses_sql->sqlQuery($query);
  return true;
}

function b1n_sessionGC($life_time)
{
  global $ses_sql;
  if(isset($_REQUEST['PHPSESSID'])){
    $ses_id = $_REQUEST['PHPSESSID'];
  }
  else {
    $ses_id = '';
  }

  $query = "
    SELECT
      ses_id, ses_ip,
      ses_data, ses_last_updated
    FROM
      session
    WHERE
      (CURRENT_TIMESTAMP - ses_last_updated)::interval >
        '" . $life_time . "'::interval";

  $rs = $ses_sql->sqlQuery($query);

  if(is_array($rs)){
    // Logging off
    foreach($rs as $p){
      if(strstr($p['ses_data'], 'pla_id')){
        $pla_id = ereg_replace('.*:"pla_id";s:[0-9]+:"([0-9]+)";.*', '\1', $p['ses_data']);
        if(!empty($pla_id)){
          $aux  = "'" . $p['ses_last_updated'] . "'::timestamp + ";
          $aux .= "'" . ($life_time/2) . " seconds'::interval";
          b1n_logAction('logoff', array(), $pla_id, 0, $aux);
          if(b1n_cmp($ses_id, $p['ses_id'])){
            global $lang;
            b1n_retMsg($lang['session_has_expired']);
            if(!b1n_cmp($_SERVER['REMOTE_ADDR'], $p['ses_ip'])){
              b1n_retMsg($lang['log_cheater']);
            }
          }
        }
      }
    }
  }

  $query = "
    DELETE FROM session WHERE
      (CURRENT_TIMESTAMP - ses_last_updated)::interval >
        '" . $life_time . "'::interval";

  $ses_sql->sqlQuery($query);
  return true;
}

// Save Handlers
session_set_save_handler(
  'b1n_sessionOpen',
  'b1n_sessionClose',
  'b1n_sessionRead',
  'b1n_sessionWrite',
  'b1n_sessionDestroy',
  'b1n_sessionGC');

// Start Session
session_start();
?>
