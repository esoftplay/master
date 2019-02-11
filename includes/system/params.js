function addFiles(rel, i, prefix) {
	var max = eval(prefix+'_max');
	if( i < max ) {
		i++;
		var div	= document.getElementById(prefix+i+'ID');
		if(div != null) {
			div.style.display = 'block';
		}
	}
};