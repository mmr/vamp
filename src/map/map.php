<?
// $Id: map.php,v 1.20 2004/08/06 00:33:03 mmr Exp $
$show_more_commands = '';


/*
For us to show the correct image for streets we have
to first figure out what map model are we talking about.

There are 16 different models, they vary according buildings
position or city boundaries.
 
- Ordinary Models:
A _ _ _  B _ _ _  C _ _ _  D _ _ _
 |_|b|_|  |_|_|_|  |b|_|b|  |_|_|_|
 |_|_|_|  |b|_|b|  |_|_|_|  |_|b|_|
 |_|b|_|  |_|_|_|  |b|_|b|  |_|_|_|
       
- Special Models:
E _ _ _  F _ _ _  G _ _ _  H _ _ _
 |x|_|b|  |b|_|x|  |x|_|_|  |_|_|x|
 |x|_|_|  |_|_|x|  |x|_|b|  |b|_|x|
 |x|_|b|  |b|_|x|  |x|_|_|  |_|_|x|

I _ _ _  J _ _ _  K _ _ _  L _ _ _
 |b|_|b|  |x|x|x|  |_|b|_|  |x|x|x|
 |_|_|_|  |_|_|_|  |_|_|_|  |_|_|_|
 |x|x|x|  |b|_|b|  |x|x|x|  |_|b|_|

M _ _ _  N _ _ _  O _ _ _  P _ _ _
 |x|x|x|  |x|x|x|  |x|_|b|  |b|_|x|
 |x|_|_|  |_|_|x|  |x|_|_|  |_|_|x|
 |x|_|b|  |b|_|x|  |x|x|x|  |x|x|x|

Where: b = building, x = city boundaries
*/

$map_model = '';

// Auxiliar Vars
$aux1=false;
$aux2=false;
$aux3=false;
$aux4=false;

// Checking for models A, K or L
if(isset($map[($cur_x).':'.($cur_y-1)]['building']) ||
   isset($map[($cur_x).':'.($cur_y+1)]['building']))
{
  $aux1=isset($map[($cur_x).':'.($cur_y-1)]['building']);
  $aux2=isset($map[($cur_x).':'.($cur_y+1)]['building']);
  if($aux1 && $aux2)
    $map_model = 'A';
  elseif($aux1)
    $map_model = 'K';
  else
    $map_model = 'L';
}
// Checking for models B, G or H
elseif(isset($map[($cur_x-1).':'.($cur_y)]['building']) ||
       isset($map[($cur_x+1).':'.($cur_y)]['building']))
{
  $aux1=isset($map[($cur_x-1).':'.($cur_y)]['building']);
  $aux2=isset($map[($cur_x+1).':'.($cur_y)]['building']);
  if($aux1 && $aux2)
    $map_model = 'B';
  elseif($aux1)
    $map_model = 'H';
  else
    $map_model = 'G';
}
// Checking for models C, E, F, I, J, M, N, O or P
elseif(isset($map[($cur_x-1).':'.($cur_y-1)]['building']) ||
       isset($map[($cur_x+1).':'.($cur_y-1)]['building']) ||
       isset($map[($cur_x+1).':'.($cur_y+1)]['building']) ||
       isset($map[($cur_x-1).':'.($cur_y+1)]['building']))
{
  $aux1=isset($map[($cur_x-1).':'.($cur_y-1)]['building']);
  $aux2=isset($map[($cur_x+1).':'.($cur_y-1)]['building']);
  $aux3=isset($map[($cur_x+1).':'.($cur_y+1)]['building']);
  $aux4=isset($map[($cur_x-1).':'.($cur_y+1)]['building']);

  if($aux1 && $aux2 && $aux3 && $aux4)
    $map_model = 'C';
  elseif($aux1 && $aux2)
    $map_model = 'I';
  elseif($aux3 && $aux4)
    $map_model = 'J';
  elseif($aux2 && $aux3)
    $map_model = 'E';
  elseif($aux1 && $aux4)
    $map_model = 'F';
  elseif($aux1)
    $map_model = 'P';
  elseif($aux2)
    $map_model = 'O';
  elseif($aux3)
    $map_model = 'M';
  else
    $map_model = 'N';
}
// Checkinf for model D
elseif(isset($map[$cur_x.':'.$cur_y]['building'])){
  $map_model = 'D';
}
unset($aux1, $aux2, $aux3, $aux4);

if(empty($map_model)){
  die('Could not get map model. Aborting.');
}
#echo $map_model;

// ------------------------
// Printing the Map
// TODO: use map_model to optimize this
// Lines
for($y=$cur_y-1; $y<=$cur_y+1; $y++){
  echo '<tr>';

  // Columns
  for($x=$cur_x-1; $x<=$cur_x+1; $x++){
    $pos = $x.':'.$y;

    // Checking if this position has something (building, npcs, players, ...)
    if(isset($map[$pos])){
      // Current Position
      if($x == $cur_x && $y == $cur_y){
        // Building
        if(isset($map[$pos]['building'])){
          switch($map[$pos]['building']['bty_code']){
          case 'bty_shop':
            echo "<td class='map_building'><span class='map_building_shop'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            $form = b1n_mapBuildShop($map[$pos]['building']['bui_id']);
            break;
          case 'bty_pub':
            echo "<td class='map_building'>";
            echo "<span class='map_building_pub'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            $form = b1n_mapBuildPub($map[$pos]['building']['bui_id']);
            break;
          case 'bty_bank':
            echo "<td class='map_building'>";
            echo "<span class='map_building_bank'>$ ".$lang['bty_bank']." $</span>";
            $form = b1n_mapBuildBank($data['action']);
            break;
          case 'bty_guild':
            echo "<td class='map_building'>";
            echo "<span class='map_building_guild'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            $form = b1n_mapBuildGuild($map[$pos]['building']['bui_id']);
            break;
          default:
            echo "<td class='map_building'>";
          }
          echo "<br />";
        }
        else {
          // It must be a street, show the correct image
          require($data['page'] . '/img.php');

          // Checking if a junction between two streets
          if(isset($map[$pos]['street'])){
            echo "<span class='map_street_junction'>&nbsp; " . $lang[$map[$pos]['street']['str1_code']] . ' ' . $lang['and'] . ' ' . $lang[$map[$pos]['street']['str2_code']] . " &nbsp;</span><br /><br />";
          }
        }

        // My Player
        echo "<span class='map_my_player'>".$_SESSION['player']['pla_login'] . "</span><br />";

        // Players
        if(isset($map[$pos]['players'])){
          echo "<span class='map_player'>";
          foreach($map[$pos]['players'] as $n){
            echo $n['pla_login']."(".$n['pla_exp'].")";
            if($n['pla_exp'] > b1n_MIN_EXP_POINTS_DRINK){
              echo " <a class='map_action' href='".b1n_URL."?page=map&amp;action=drink&amp;target=player&amp;id=".$n['pla_id']."'>[".$lang['map_action_drink']."]</a>";
            }
            echo "<br />";
          }
          echo "</span>";

          // Adding Players interaction commands
          // Talk
          $show_more_commands .= b1n_mapBuildTalk();

          // Give Money
          $show_more_commands .= b1n_mapBuildGiveMoney($map[$pos]['players']);
        }

        // NPCs
        if(isset($map[$pos]['npcs'])){
          // Adding Ask part (interaction with NPCs)
          $show_more_commands .= b1n_mapBuildAskNpc();

          echo "<span class='map_npc'>";
          foreach($map[$pos]['npcs'] as $n){
            echo $lang[$n['nty_code']]." <a class='map_action' href='".b1n_URL."?page=map&amp;action=drink&amp;target=npc&amp;id=".$n['npc_id']."'>[".$lang['map_action_drink']."]</a><br />";
          }
          echo "</span>";
        }
      }
      else {
        // Building
        if(isset($map[$pos]['building'])){
          echo "<td class='map_building'>";

          // TODO: Check if the Building can hold more people
          // Move Here Link
          echo "<a class='map_action' href='".b1n_URL."?page=map&amp;action=move&amp;x=".$x."&amp;y=".$y."'>[".$lang['map_action_move']."]</a><br />";
          switch($map[$pos]['building']['bty_code']){
          case 'bty_shop':
            echo "<span class='map_building_shop'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            break;
          case 'bty_pub':
            echo "<span class='map_building_pub'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            break;
          case 'bty_bank':
            echo "<span class='map_building_bank'>$ ".$lang['bty_bank']." $</span>";
            break;
          case 'bty_guild':
            // Secret place
            //echo "<span class='map_building_guild'>".$lang[$map[$pos]['building']['bui_code']]."</span>";
            break;
          }
          echo "<br />";
        }
        else {
          // It must be a street, show the correct image
          require($data['page'] . '/img.php');

          // Move Here Link
          echo "<a class='map_action' href='".b1n_URL."?page=map&amp;action=move&amp;x=".$x."&amp;y=".$y."'>[".$lang['map_action_move']."]</a><br />";
          // Checking if is a junction between two streets
          if(isset($map[$pos]['street'])){
            echo "<span class='map_street_junction'>&nbsp; " . $lang[$map[$pos]['street']['str1_code']] . ' ' . $lang['and'] . ' ' . $lang[$map[$pos]['street']['str2_code']] . " &nbsp;</span><br /><br />";
          }
        }

        // Players
        if(isset($map[$pos]['players'])){
          echo "<span class='map_player'>";
          foreach($map[$pos]['players'] as $n){
            echo $n['pla_login'].'('.$n['pla_exp'].') <br />';
          }
          echo "</span>";
        }

        // NPCs
        if(isset($map[$pos]['npcs'])){
          echo "<span class='map_npc'>";
          foreach($map[$pos]['npcs'] as $n){
            echo $lang[$n['nty_code']].'<br />';
          }
          echo "</span>";
        }
      }
    } // $map[$pos]
    else {
      // Checking if its is beyound the city boundaries
      if($x < $_SESSION['player']['cit_pos_x0'] ||
         $y < $_SESSION['player']['cit_pos_y0'] ||
         $x > $_SESSION['player']['cit_pos_x1'] ||
         $y > $_SESSION['player']['cit_pos_y1'])
      {
        // Yes, it is
        // Printing boundaries info
        echo "<td class='map_out'>&nbsp;";
        echo str_replace('{city}', $lang[$_SESSION['player']['cit_code']], $lang['map_city_limits']);

        // Checking if theres another city there
        $query = "
          SELECT
            cit_id, cit_code
          FROM
            city
          WHERE
            cit_id != ".$_SESSION['player']['cit_id']." AND (
            (cit_pos_x1 = ".($x-1)." AND cit_pos_y0 <= $y AND cit_pos_y1 >= $y) OR
            (cit_pos_x0 = ".($x+1)." AND cit_pos_y0 <= $y AND cit_pos_y1 >= $y) OR
            (cit_pos_y1 = ".($y-1)." AND cit_pos_x0 <= $x AND cit_pos_x1 >= $x) OR
            (cit_pos_y0 = ".($y+1)." AND cit_pos_x0 <= $x AND cit_pos_x1 >= $x))";

        //print $query;

        $ret = $sql->sqlSingleQuery($query);

        if(is_array($ret)){
          // Yes, theres another city, print the Go.
          echo '<br /><br />';
          echo str_replace('{city}', $lang[$ret['cit_code']], $lang['map_city_goto']);
          echo '&nbsp;';
          // Printing the arrow
          echo '<a href="'.b1n_URL.'?page=map&amp;action=move_city&amp;cit_id='.$ret['cit_id'].'&amp;x='.$x.'&amp;y='.$y.'">';
          if($x < $_SESSION['player']['cit_pos_x0']){
            echo '&lt;&lt;';
          }
          elseif($y < $_SESSION['player']['cit_pos_y0']){
            echo '^^';
          }
          elseif($x > $_SESSION['player']['cit_pos_x1']){
            echo '&gt;&gt;';
          }
          elseif($y > $_SESSION['player']['cit_pos_y1']){
            echo 'VV';
          }
          echo '</a>';
        }
      }
      else {
        // No, it is not
        // Well... it may be a street
        require($data['page'] . '/img.php');

        // Checking if that is the current position
        if($x == $cur_x && $y == $cur_y){
          // Yes, it is
          echo "<span class='map_my_player'>".$_SESSION['player']['pla_login'] . "</span><br />";
        }
        else {
          // No, its not, print the Move Here link
          echo "<a class='map_action' href='".b1n_URL."?page=map&amp;action=move&amp;x=".$x."&amp;y=".$y."'>[".$lang['map_action_move']."]</a>";
        }
      }
    } // $map[$pos]
    echo '</td>';
  } // For(...x...)
  echo '</tr>';
} // For(...y...)
?>
