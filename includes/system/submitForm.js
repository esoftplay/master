function submitForm(obj, seo){
	var doc = document.forms[obj.name];
	if(seo && doc.method=='get'){
		var fields = doc.elements;
		var act = _URL;
		for(var i=0; i < fields.length; i++){
			if(fields[i].name!='') {
				if(fields[i].name=='mod') {
					act += fields[i].value;
				}else{
					act += '/'+fields[i].name+','+urlencode(fields[i].value);
				}
			}
		}
		document.location.href= act;
	}else{
		doc.submit();
	} return false;
}
function urlencode( str ) {
  var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
  var ret = str.toString();
  var replacer = function(search, replace, str) {
	  var tmp_arr = [];
	  tmp_arr = str.split(search);
	  return tmp_arr.join(replace);
  };
  histogram['!']   = '%21';
  histogram['%20'] = '+';
  ret = encodeURIComponent(ret);
  for (search in histogram) {
		replace = histogram[search];
		ret = replacer(search, replace, ret) // Custom replace. No regexing
  }
  return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
		return "%"+m2.toUpperCase();
  });
  return ret;
}