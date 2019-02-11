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
		  		var a = {format: "yyyy-mm-dd", autoclose: true, todayHighlight: true, todayBtn: true, startView: 0/*0=tanggal,1=bulan,2=tahun*/};
		  		if ($(this).data('date-format')) {
		  			a.format=$(this).data('date-format')
		  		};
		  		if ($(this).data('startview')) {
		  			a.startView=$(this).data('startview')
		  		};
		  		$(this).datepicker(a);
		  	});
		  }
		});
	}else{
		$('input[type="date"]').each(function(){
			var a = {format: "yyyy-mm-dd", autoclose: true, todayHighlight: true, todayBtn: true, startView: 0/*0=tanggal,1=bulan,2=tahun*/};
			if ($(this).data('date-format')) {
				a.format=$(this).data('date-format')
			};
  		if ($(this).data('startview')) {
  			a.startView=$(this).data('startview')
  		};
			$(this).datepicker(a);
		});
	}
});