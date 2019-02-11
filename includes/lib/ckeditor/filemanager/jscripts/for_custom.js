function selectFile(url)
{
	window.top.close();
	if (window.opener) {
		window.top.opener.focus();
		if (window.opener.selectFile) {
			window.opener.selectFile(url);
		}
	}
}
function cancelSelectFile()
{
  window.top.close();
	if (window.opener) {
		if (window.opener.cancelSelectFile) {
			window.opener.cancelSelectFile();
		}
	}
  return false;
}

