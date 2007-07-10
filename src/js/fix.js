//<![CDATA[
//<!--
function b1n_fix()
{
  var map_table;

  if(document.all){
    map_table = document.all.map_table;
  }
  else if(document.getElementById()){
    map_table = document.getElementById('map_table');
  }

  if(map_table){
    var width, height;
    var ratio = 0.625;

    width   = screen.width  * ratio;
    height  = screen.height * ratio; 

    map_table.style.width   = width;
    map_table.style.height  = height;
//    alert(screen.width + ' ' + screen.height + ' ' + width + ' ' + height);
  }
}

// TODO: Rewrite this stuff ;P
//b1n_fix();
//-->
//]]>
