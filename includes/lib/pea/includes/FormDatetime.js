_Bbc(function($) {
	var _parm = {format: "yyyy-mm-dd hh:ii:ss", autoclose: true, todayHighlight: true, orientation: "auto" };
	if(typeof $.fn.datetimepicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datetimepicker.css') );
		$.ajax({
		  url: _path+'js/datetimepicker.js',
		  dataType: "script",
		  success: function(){
		  	$('input[type="datetime"]').each(function(){
		  		var a = _parm;
		  		if ($(this).attr('data-date-format')!='') {
		  			a.format=$(this).attr('data-date-format')
		  		};
		  		$(this).datetimepicker(a);
		  	});
		  }
		});
	}else{
		$('input[type="date"]').each(function(){
			var a = _parm;
			if ($(this).attr('data-date-format')!='') {
				a.format=$(this).attr('data-date-format')
			};
			$(this).datetimepicker(a);
		});
	}
});