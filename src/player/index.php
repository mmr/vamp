<?
// $Id: index.php,v 1.6 2004/04/12 00:34:26 mmr Exp $
// This page will build the player page

require(b1n_PATH_LIB . '/player.lib.php');

// Checking actions 
switch($data['action']){
case 'chagepasswd':
  b1n_getVar('curpasswd', $data['curpasswd']);
  b1n_getVar('newpasswd', $data['newpasswd']);
  b1n_getVar('newpasswd2', $data['newpasswd2']);
  b1n_playerChangePasswd($data);
  break;
}

// Current Position
$player['position'] = b1n_playerGetPosition();

// Lineage
$player['lineage']  = b1n_playerGetLineage();

// Siblings
$player['siblings'] = b1n_playerGetSiblings();

// Seeing if there are messages to be written
if(sizeof($ret_msgs)){
  require(b1n_PATH_INC . '/ret.inc.php');
}
?>
<center>
<table class='extbox' cellpadding='0' cellspacing='0'>
  <tr>
    <td>
      <table class='intbox' cellspacing='5' cellpadding='2'>
<? require($data['page'] . '/' . $data['page'] . '.php'); ?>      
      </table>
    </td>
  </tr>
</table>
</center>
