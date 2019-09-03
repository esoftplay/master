_Bbc(function($) {
	if(typeof $.fn.datepicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datepicker.css') );
		$.ajax({
		  url: _path+'js/datepicker.js',
		  dataType: "script",
		  success: function(){
		  	$('.input-daterange').each(function(){
					var a = $(this).data();
					$(this).datepicker(a);
				});
		  // 	$(this).attr("type", "text");
				// var a = $(this).data();
				// $('.input-daterange').datepicker(a);
		  }
		});
	}else{
		$('.input-daterange').each(function(){
			var a = $(this).data();
			$(this).datepicker(a);
		});
	}
});