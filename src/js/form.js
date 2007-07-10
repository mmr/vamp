function b1n_checkAll(f)
{
  var i;

  if(document.getElementById){
    document.getElementById('delete').disabled = !f.elements['checkall'].checked;
  }

  for(i=0; i<f.elements['ids[]'].length; i++){
    f.elements['ids[]'][i].checked = f.elements['checkall'].checked;
  }
}

function b1n_checkAllLink(f)
{
  f.elements['checkall'].checked = !f.elements['checkall'].checked;
  b1n_checkAll(f);
}

function b1n_check(o)
{
  var f = o.form;

  if(!o.checked){
    f.elements['checkall'].checked = false;
  }

  var d = true;
  for(i=0; i<f.elements['ids[]'].length; i++){
    if(f.elements['ids[]'][i].checked){
      d = false;
      break;
    }
  }

  if(document.getElementById){
    document.getElementById('delete').disabled = d;
  }
}
