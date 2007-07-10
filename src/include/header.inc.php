<?
// $Id: header.inc.php,v 1.11 2004/08/06 00:33:59 mmr Exp $
// We like to follow standards
echo "<?xml version='1.0' encoding='ISO-8859-1'?>\n";
?>
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.1//EN' '/comum/dtd/xhtml11.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' >
<head>
  <title><?= b1n_PROGNAME ?></title>
  <link rel='stylesheet' href='<?= b1n_PATH_CSS ?>/vamp.css' />
</head>
<body>
<div class='main'>
<a href='<?= b1n_URL ?>' class='progname'>Vamp</a>
<a href='<?= b1n_URL ?>?language=pt_br&page=<?= $data['page'] ?>'><img
  src='<?= b1n_PATH_IMG ?>/pt_br.gif' alt='Portuguese (Brazil)' width='23' height='15' /></a>
<a href='<?= b1n_URL ?>?language=en_us&page=<?= $data['page'] ?>'><img
  src='<?= b1n_PATH_IMG ?>/en_us.gif' alt='English (USA)' width='23' height='15' /></a>
<hr />
