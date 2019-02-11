function urlencode( str )
{
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
};
function submit_search(a)
{
	var b = new RegExp('/', "g");
	document.location.href=document.getElementById('search_action_url').value+urlencode(a.id.value.replace(b, ''));
	return false;
};