<?
// $Id: player.php,v 1.6 2004/04/13 21:24:13 mmr Exp $
?>
        <tr>
          <td><?= $lang['player_experience'] . ':</td><td>' . $_SESSION['player']['pla_exp'] ?></td>
        </tr>
        <tr>
          <td><?= $lang['player_ranking'] . ':</td><td>' . $lang[$_SESSION['player']['ran_name']] ?></td>
        </tr>
        <tr>
          <td><?= $lang['player_money'] . ':</td><td>' . b1n_formatCurrency($_SESSION['player']['pla_money']) ?></td>
        </tr>
<?
// Lineage
if(sizeof($player['lineage'])){
?>
        <tr>
          <td>
<?
  $last = array_pop($player['lineage']);

  echo $lang['player_lineage_text'] . ':</td><td>';
  if(sizeof($player['lineage'])){
    echo implode(', ', $player['lineage']) . ' ' . $lang['and'] . ' ';
  }

  echo $lang['player_lineage_master'] . ' ' . $last;
?>
          </td>
        </tr>
<?
}

// Siblings
if(sizeof($player['siblings'])){
?>
        <tr>
          <td>
<?
  $last = array_pop($player['siblings']);

  echo $lang['player_siblings'] . ':</td><td>';
  if(sizeof($player['siblings'])){
    $aux = array();
    foreach($player['siblings'] as $p){
      $aux[] = $p['player'];
    }
    echo implode(', ', $aux) . ' ' . $lang['and'] . ' ' . $last;
  }
  else {
    echo $last;
  }
?>
          </td>
        </tr>
<?
}
?>
