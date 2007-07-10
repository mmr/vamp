#!/usr/local/bin/php
<?
// $Id: action_points.php,v 1.3 2004/04/16 22:25:38 mmr Exp $
// Update Active Players Action Points

// Library Path
define('b1n_PATH_LIB',  '/var/www/htdocs/vamp/src/lib');

// Turning Debug Off
define('b1n_DEBUG', false);

// Configuration
require(b1n_PATH_LIB . '/config.lib.php');

// Database Access
require(b1n_PATH_LIB . '/sqllink.lib.php');
$sql = new b1n_sqlLink('/var/www/htdocs/vamp/doc/cron/db_conf_vamp.lib.php');

$query = "
  UPDATE player
    SET pla_action_points = pla_action_points + 1
  WHERE
    pla_active = 't' AND
    pla_last_login >=
      (CURRENT_TIMESTAMP::timestamp - '".b1n_MAX_INACTIVE_TIME."'::interval) AND
    pla_action_points < 50";

$sql->sqlQuery($query);
?>
