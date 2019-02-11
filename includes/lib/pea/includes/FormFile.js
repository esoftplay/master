_Bbc(function($) {
	var a = $('.formFile-clickable');
	if (a.length) {
		$('.formFile-clickable').css({"height":"30px","cursor":"pointer"});
		$('.formFile-clickable').click(function(){
			var a = $(this).attr("src");
			var b = $(this).attr("title");
			var c = document.createElement('div');
			$(c).addClass("modal fade");
			$(c).attr("tabindex", "-1");
			if (a) {
				var d = '<img src="'+a+'" style="max-width: 100%;" />';
			}else{
				var d = $(this).attr("data-modal");
			}
			$(c).html('\
  <div class="modal-dialog">\
    <div class="modal-content">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>\
        <h4 class="modal-title">'+b+'</h4>\
      </div>\
      <div class="modal-body"><center>'+d+'</center></div>\
    </div>\
  </div>\
');
			$(c).modal("show");
			$(c).on("hidden.bs.modal", function(){
				$(this).remove();
			});
			return false;
		});
	};
});