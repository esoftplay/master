_Bbc(function($){
	$("[rel=btn_ColView]").on("click", function(e){
		e.preventDefault();
		var a = $(this);
		var b = {"ColView":[]};
		b[a.data("name")] = a.val();
		$("input:checked", a.closest(".dropdown-menu")).each(function(){
			b["ColView"].push($(this).val());
		});
		var x = document.location.href;
		x += x.indexOf("?") >= 0 ? "&" : "?";
		x += "is_ajax=1";
		a.html("loading...");
		$.ajax({
		    url: x,
		    method:"POST",
		    data:b,
		    global:false,
		    success: function(a){
		    	document.location.reload();
		    }
		});
	})
	$(".show_hide_column .dropdown-menu").on("click", function(e){
		e.stopPropagation();
	});
});
