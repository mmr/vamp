<?
// $Id: sqllink.lib.php,v 1.7 2004/12/05 15:28:02 mmr Exp $

class b1n_sqlLink
{
  var $sqllink = NULL;
  var $db      = NULL;

  function b1n_sqlLink($file)
  {
    $i = 0;
    while((!$this->sqlConnect($file)) && ($i < 3)){
      $i++;
      sleep(1);
    }
  }

  function sqlConnect($file)
  {
    if($this->sqlIsConnected()){ 
      user_error('Already connected.');
      return false; 
    }

    require_once($file);

    $db_host = (empty($db_host)?'':'host = ' . $db_host);
    $connstr = $db_host . ' dbname = ' . $db_name . ' user = ' . $db_user . ' password = ' . $db_pass;

    return $this->sqlSetLink(pg_connect($connstr));
  }
  
  function sqlIsConnected()
  {
    return $this->sqlGetLink();
  }

  function sqlSingleQuery($query)
  {
    if(!$query){
      return false;
    } 

    if(b1n_DEBUG){
      echo '<pre style="text-align: left">QUERY: ' . $query . ' LIMIT 1</pre>';
    }

    if(!$this->sqlIsConnected()){
      user_error('DB NOT CONNECTED');
      return false;
    }

    $result = pg_query($this->sqlGetLink(), $query . ' LIMIT 1');
    if((pg_num_rows($result)> 0) && ($aux = pg_fetch_array($result, 0, PGSQL_ASSOC))){
      return $aux;
    }
    return false;
  }

  function sqlQuery($query)
  {
    if(!$query){
      return false;
    }

    if(b1n_DEBUG){
      echo '<pre style="text-align: left">QUERY: ' . $query . '</pre>';
    }

    if(!$this->sqlIsConnected()){
      user_error('DB NOT CONNECTED');
      return false;
    }

    $result = pg_query($this->sqlGetLink(), $query);

    if(is_bool($result)){
      return pg_affected_rows($result);
    }

    $num = pg_num_rows($result);

    if($num > 0){
      for($i=0; $i<$num; $i++){
        $row[$i] = pg_fetch_array($result, $i, PGSQL_ASSOC);
      }

      return $row;
    }
    return true;
  }

  function sqlSetLink($link)
  {
    return $this->sqllink = $link;
  }

  function sqlGetLink()
  {
    return $this->sqllink;
  }

  function sqlClose()
  {
    return pg_close($this->sqlGetLink());
  }
}
?>
