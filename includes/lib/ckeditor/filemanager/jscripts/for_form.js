function selectFile(url)
{
	var obj = window.opener.document.getElementById('txtUrl');
	obj.value = url.replace(window.opener._URL, "");
	window.top.close();
	window.top.opener.focus();
	obj.focus();
}
function cancelSelectFile()
{
  // close popup window
  window.close() ;
  return false;
}

