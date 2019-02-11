_Bbc(function($) {
	if(typeof $.fn.colorpicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/colorpicker.css') );
		$.ajax({
		  url: _path+'js/colorpicker.js',
		  dataType: "script",
		  success: function(){
		  	$('input[type="color"]').colorpicker();
		  }
		});
	}else{
		$('input[type="color"]').colorpicker();
	}
});