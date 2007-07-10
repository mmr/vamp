<?
echo $lang['map_action_points'] . ":&nbsp;";
echo "<span id='map_action_points'>".$_SESSION['player']['pla_action_points'].'</span>/50 &nbsp;-&nbsp;';
echo $lang['map_action_time'] . ":&nbsp;<span id='map_action_time'></span>";

list($min, $seg) = explode(':', date('i:s'));
#$min=59;
#$seg=55;
$min-=29;
if($min<0){
  $min*=-1;
}
elseif($min>0){
  $min-=30;
  if($min<0){
    $min*=-1;
  }
}

$seg-=60;
$seg*=-1;
?>
<script type='text/javascript'>
//<![CDATA[
//<!--
  var p = <?= $_SESSION['player']['pla_action_points'] ?>;
  var min = <?= $min ?>;
  var seg = <?= $seg ?>;

  var obj_t = document.getElementById('map_action_time');
  var obj_p = document.getElementById('map_action_points');

  obj_t.innerHTML = ((min<=9)?'0':'')+min +':'+ ((seg<=9)?'0':'')+seg; 

  function b1n_mapUpdateTime()
  {
    var aux = obj_t.innerHTML.split(':');

    if(aux[0]<=9){
      aux[0] = aux[0].substr(1);
    }
    if(aux[1]<=9){
      aux[1] = aux[1].substr(1);
    }
    var min = parseInt(aux[0]);
    var seg = parseInt(aux[1]);

    seg--;
    if(seg<0){
      min--;
      if(min<0){
        b1n_mapUpdateAction();
        min=29;
        seg=59;
      }
      else{
        seg=59;
      }
    }

    obj_t.innerHTML = ((min<=9)?'0':'')+min +':'+ ((seg<=9)?'0':'')+seg; 
  }

  function b1n_mapUpdateAction()
  {
    var aux = parseInt(obj_p.innerHTML);
    obj_p.innerHTML = parseInt(obj_p.innerHTML)+1;
  }
  setInterval('b1n_mapUpdateTime()', 1000);
//-->
//]]>
</script>
