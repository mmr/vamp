<?
// $Id: bottom.php,v 1.7 2004/04/12 00:34:26 mmr Exp $
if(!empty($form)){
  echo $form;
}

if(isset($_SESSION['show_more_commands'])){
  if(isset($show_more_commands) && !empty($show_more_commands)){
?>
<div class='map_more_commands'>
<table>
  <tr>
    <td><?= $show_more_commands ?></td>
  </tr>
</table>
<?
    echo "<a href='".b1n_URL."?page=".$data['page']."&amp;action=hide_more_commands'>".$lang['map_more_commands_hide']."</a></div>";
  }
?>
</div>
<?
}
else {
  if(isset($show_more_commands) && !empty($show_more_commands)){
    echo "<div class='map_more_commands'><a href='".b1n_URL."?page=".$data['page']."&amp;action=show_more_commands'>".$lang['map_more_commands_show']."</a></div>";
  }
}
?>
