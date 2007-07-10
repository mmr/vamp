<?
// $Id: login.inc.php,v 1.19 2004/04/13 21:24:13 mmr Exp $
?>
<table>
  <tr>
    <td>
      <?= $lang['login_label_about'] ?>
    </td>
  </tr>
</table>
<table>
  <tr>
    <td colspan='2' class='login_box'>
      <table style='width: 100%'>
        <tr>
          <td class='title_box'><?= $lang['login_form_seccode'] ?></td>
        </tr>
        <tr>
          <td class='c'><img src='createimg.php' alt='' style='border: 1pt solid #fff' /></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td class='login_box'>
      <form method='post' action='<?= b1n_URL ?>'>
      <input type='hidden' name='page'    value='login' />
      <input type='hidden' name='action'  value='login' />
      <table>
        <tr>
          <td class='title_box' colspan='2'><?= $lang['login_title'] ?></td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_login'] ?></td>
          <td>
            <input type='text' name='login' maxlength='255' class='i_text' />
          </td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_password'] ?></td>
          <td>
            <input type='password' name='passwd' maxlength='64' class='i_text' />
          </td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_seccode'] ?></td>
          <td>
            <input type='text' name='seccode' maxlength='5' class='i_text' />
          </td>
        </tr>
        <tr>
          <td colspan='2' class='c'>
            <input type='submit' value=' <?= $lang['ok'] ?> ' class='i_button' />
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class='login_box'>
      <form method='post' action='<?= b1n_URL ?>'>
      <input type='hidden' name='page'    value='login' />
      <input type='hidden' name='action'  value='newplayer' />
      <input type='hidden' name='master'  value='<?= $data['master'] ?>' />
      <table>
        <tr>
          <td class='title_box' colspan='2'><?= $lang['login_label_newplayer'] ?></td>
        </tr>
<?
if(!empty($data['master'])){
  echo "<tr><td colspan='2'>";
  echo str_replace('{master}', $data['master'], $lang['login_label_master']);
  echo "</td></tr>";
}
?>
        <tr>
          <td class='formitem'><?= $lang['login_form_login'] ?></td>
          <td>
            <input type='text' name='login' maxlength='255' class='i_text' />
          </td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_password'] ?></td>
          <td>
            <input type='password' name='passwd' maxlength='64' class='i_text' />
          </td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_email'] ?></td>
          <td>
            <input type='text' name='email' maxlength='64' class='i_text' />
          </td>
        </tr>
        <tr>
          <td class='formitem'><?= $lang['login_form_seccode'] ?></td>
          <td>
            <input type='text' name='seccode' maxlength='5' class='i_text' />
          </td>
        </tr>
        <tr>
          <td colspan='2' class='c'>
            <input type='submit' value=' <?= $lang['ok'] ?> ' class='i_button' />
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</form>
