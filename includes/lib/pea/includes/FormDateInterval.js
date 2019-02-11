_Bbc(function($) {
	if(typeof $.fn.datepicker!='function') {
		var _path =  _URL+'templates/admin/bootstrap/';
		var _parm = {format: "yyyy-mm-dd", autoclose: true, todayHighlight: true, todayBtn: true, clearDate: false };
		$('head').append( $('<link rel="stylesheet" type="text/css" />').attr('href', _path+'css/datepicker.css') );
		$.ajax({
		  url: _path+'js/datepicker.js',
		  dataType: "script",
		  success: function(){
		  	$('.input-daterange').datepicker(_parm);
		  }
		});
	}else{
		$('.input-daterange').datepicker(_parm);
	}
});