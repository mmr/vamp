<table class='game_box'>
  <tr>
    <td>
<?
// $Id: ret.inc.php,v 1.9 2004/04/12 00:34:26 mmr Exp $
foreach($ret_msgs as $msg){
echo '<div class="' . (($msg['status'] === b1n_SUCCESS)?'retsuccess':'retfizzles') . '">' . $msg['msg'] . '</div>';
}
?>
    </td>
  </tr>
</table>
<div>
  <br />
</div>
