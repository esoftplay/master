function selectFile(url)
{
  var selectedFileRowNum = $('#selectedFileRowNum').val();
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {
    var txt = url.replace(window.opener._URL, "");
    window.opener.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), txt);
    window.close();
  }else{
    alert(noFileSelected);
  }
};
function getUrlParam(paramName)
{
  var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
  var match = window.location.search.match(reParam) ;
  return (match && match.length > 1) ? match[1] : '' ;
};


function cancelSelectFile()
{
  // close popup window
  window.close() ;
  return false;
};