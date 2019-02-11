function init_multifile_load(a)
{
	var b = document.getElementsByTagName('textarea');
	var c = null;
	a = a.replace('[','\\[');
	a = a.replace(']','\\]');
	for(var i=0;i < b.length;i++)
	{
		var d = new RegExp(a+"\\[(.*?)\\]$", 'g');
		var c = d.exec(b[i].name);
		if(c != null)
		{
			var e = (b[i].title != '') ? b[i].title : c[1];
			var f = {'id':c[1],'text':b[i].value,'title':e, 'object':b[i]};
			editAreaLoader.openFile(a, f);
		}
	}
};
function init_multifile_submit(a)
{
	var b = editAreaLoader.getAllFiles(a);
	for(i in b)
	{
		b[i].object.value = b[i].text;
	}
};
