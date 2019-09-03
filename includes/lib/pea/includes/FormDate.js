_Bbc(function($) {
	if(typeof $.fn.datepicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datepicker.css') );
		$.ajax({
			url: _path+'js/datepicker.js',
			dataType: "script",
			success: function(){
				$('input[type="date"]').each(function(){
					$(this).attr("type", "text");
					var a = $(this).data();
					$(this).datepicker(a);
				});
			}
		});
	}else{
		$('input[type="date"]').each(function(){
			$(this).attr("type", "text");
			var a = $(this).data();
			$(this).datepicker(a);
		});
	}
});