<?
$img = '';

// 1
if($x==($cur_x-1) && $y==($cur_y-1)){
  switch($map_model){
  case 'A': $img = 1; break;
  case 'B': $img = 2; break;
  case 'D': $img = 11; break;
  case 'H': $img = 2; break;
  case 'K': $img = 1; break;
  }
}
// 2
elseif($x==($cur_x) && $y==($cur_y-1)){
  switch($map_model){
  case 'B': $img = 11; break;
  case 'C': $img = 1; break;
  case 'D': $img = 2; break;
  case 'E': $img = 1; break;
  case 'F': $img = 1; break;
  case 'G': $img = 10; break;
  case 'H': $img = 6; break;
  case 'I': $img = 1; break;
  case 'O': $img = 1; break;
  case 'P': $img = 1; break;
  }
}
// 3
elseif($x==($cur_x+1) && $y==($cur_y-1)){
  switch($map_model){
  case 'A': $img = 1; break;
  case 'B': $img = 2; break;
  case 'D': $img = 11; break;
  case 'G': $img = 2; break;
  case 'K': $img = 1; break;
  }
}
// 4
elseif($x==($cur_x-1) && $y==($cur_y)){
  switch($map_model){
  case 'A': $img = 11; break;
  case 'C': $img = 2; break;
  case 'D': $img = 1; break;
  case 'F': $img = 2; break;
  case 'I': $img = 2; break;
  case 'J': $img = 2; break;
  case 'K': $img = 8; break;
  case 'L': $img = 4; break;
  case 'N': $img = 2; break;
  case 'P': $img = 2; break;
  }
}
// 5
elseif($x==($cur_x) && $y==($cur_y)){
  switch($map_model){
  case 'A': $img = 2; break;
  case 'B': $img = 1; break;
  case 'C': $img = 11; break;
  case 'E': $img = 10; break;
  case 'F': $img = 6; break;
  case 'G': $img = 1; break;
  case 'H': $img = 1; break;
  case 'I': $img = 8; break;
  case 'J': $img = 4; break;
  case 'K': $img = 2; break;
  case 'L': $img = 2; break;
  case 'M': $img = 3; break;
  case 'N': $img = 5; break;
  case 'O': $img = 9; break;
  case 'P': $img = 7; break;
  }
}
// 6
elseif($x==($cur_x+1) && $y==($cur_y)){
  switch($map_model){
  case 'A': $img = 11; break;
  case 'C': $img = 2; break;
  case 'D': $img = 1; break;
  case 'E': $img = 2; break;
  case 'I': $img = 2; break;
  case 'J': $img = 2; break;
  case 'K': $img = 8; break;
  case 'L': $img = 4; break;
  case 'M': $img = 2; break;
  case 'O': $img = 2; break;
  }
}
// 7
elseif($x==($cur_x-1) && $y==($cur_y+1)){
  switch($map_model){
  case 'A': $img = 1; break;
  case 'B': $img = 2; break;
  case 'D': $img = 11; break;
  case 'H': $img = 2; break;
  case 'L': $img = 1; break;
  }
}
// 8
elseif($x==($cur_x) && $y==($cur_y+1)){
  switch($map_model){
  case 'B': $img = 11; break;
  case 'C': $img = 1; break;
  case 'D': $img = 2; break;
  case 'E': $img = 1; break;
  case 'F': $img = 1; break;
  case 'G': $img = 10; break;
  case 'H': $img = 6; break;
  case 'J': $img = 1; break;
  case 'M': $img = 1; break;
  case 'N': $img = 1; break;
  }
}
// 9
elseif($x==($cur_x+1) && $y==($cur_y+1)){
  switch($map_model){
  case 'A': $img = 1; break;
  case 'B': $img = 2; break;
  case 'D': $img = 11; break;
  case 'G': $img = 2; break;
  case 'L': $img = 1; break;
  }
}

echo "<td style='background-image: url(img/r".$img.".png)' class='map_street' >";
?>
