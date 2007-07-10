<hr />
<?
if(isset($_COOKIE['debug'])){
?>
  <a href='<?= b1n_URL . '?debug=off' ?>'><?= $lang['debug_mode_off'] ?></a>
<?
}
else {
?>
  <a href='<?= b1n_URL . '?debug=on' ?>'><?= $lang['debug_mode_on'] ?></a>
<?
}
?>
<div>&copy; 2001-2004 <a href='http://b1n.org/' rel='_blank'>b1n.org</a></div>
<script type='text/javascript' src='<?= b1n_PATH_JS ?>/targets.js'></script>
<?
if(b1n_DEBUG && isset($_SESSION)){
  echo '<div class="debug"><pre>SESSION:<br />';
  print_r($_SESSION);
  echo '<br />';
  print_r($_COOKIE);
  echo '</pre></div>';
}
?>
</div>
</td></tr></table>
</body>
</html>
