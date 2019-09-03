_Bbc(function($) {
	if(typeof $.fn.datetimepicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datetimepicker.css') );
		$.ajax({
		  url: _path+'js/datetimepicker.js',
		  dataType: "script",
		  success: function(){
		  	$('input[type="datetime"]').each(function(){
		  		$(this).attr("type", "text");
		  		var a = $(this).data();
		  		$(this).datetimepicker(a);
		  	});
		  }
		});
	}else{
		$('input[type="date"]').each(function(){
			$(this).attr("type", "text");
			var a = $(this).data();
			$(this).datetimepicker(a);
		});
	}
});