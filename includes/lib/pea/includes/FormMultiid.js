_Bbc(function($) {
	$('.input-group.FormMultiid').each(function(){
		var a = $(this);
		var b = $('.input-group-addon', a);
		var c = a.next('.list-group');
		c.css("margin", '0');
		b.css("cursor", "pointer").click(function(){
			var d = $('.form-control', a);
			if (d.val()=="") {
				alert("Please insert IDs for "+d.attr("title"));
			}else{
				$.ajax({
				  type: "POST",
				  url: _URL+'user/multiid',
				  data: {"id":d.val(),"token":d.data("token")},
				  dataType: "json",
				  success: function(e) {
				  	if (e.success) {
				  		d.val(e.value);
				  		c.html(e.html);
				  		$('a', c).click(function(e){
				  			e.preventDefault();
				  			var f = $(this).attr("href");
			  				f += /\?/.test(f) ? '&' : '?';
			  				f += 'return='+encodeURIComponent(document.location.href);
			  				document.location.href = f;
				  		});
				  	}else{
				  		d.val('');
				  		c.html('');
				  		alert("Sorry, you have inserted invalid IDs for "+d.attr("title")+"!");
				  		d.focus();
				  	}
				  }
				});
			}
		});
		$('.form-control', a).on('keydown', function(e){
			var f = e.charCode || e.which;
			if (f == 13) {
				b.trigger("click");
				e.preventDefault();
			};
		});
	});
});