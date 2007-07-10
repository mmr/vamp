function b1n_setTargets()
{
  if(!document.getElementsByTagName){
    return; 
  }

  var anchors = document.getElementsByTagName('a'); 

  for(var i=0; i<anchors.length; i++){ 
    var anchor = anchors[i]; 
    if(anchor.getAttribute('href') && anchor.getAttribute('rel')){
      switch(anchor.getAttribute('rel')){
        // TODO: listar todos rel's
        case 'stylesheet':
        case 'next':
        case 'previous':
          break;
        default:
          anchor.target = anchor.getAttribute('rel');
      }
    }
  }
}
window.onLoad = b1n_setTargets();
