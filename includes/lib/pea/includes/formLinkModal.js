_Bbc(function($) {
	$('a[rel="editlinksmodal"]').on("click", function(e){
		e.preventDefault();
		var a = $(this).attr("href");
		a += a.match(/\?/) ? '&' : '?';
		a += "is_ajax=1";
		var b = $("#editlinksmodal");
		if (!b.length) {
			$(document.body).append('<div class="modal" id="editlinksmodal" tabindex="-1" role="dialog" aria-labelledby="editlinksmodalLabel">\
  <div class="modal-dialog" role="document">\
    <div class="modal-content">\
      <div class="modal-header">\
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
        <h4 class="modal-title" id="editlinksmodalLabel"></h4>\
      </div>\
      <div class="modal-body"></div>\
    </div>\
  </div>\
</div>');
			b = $("#editlinksmodal");
		}else{
			$(".modal-wrapper", b).hide();
		}
		var c = a.replace(/[^a-z0-9]+/ig, '_');
		b.modal();
		$("#editlinksmodalLabel").html($(this).html());
		if ($("#"+c).length) {
			$("#"+c).show();
		}else{
			$(".modal-body", b).append('<div class="modal-wrapper" id="'+c+'"><center><i class="fa fa-spinner fa-spin fa-4x fa-fw"></i></center></div>');
			$.ajax({
				url: a,
				type: "GET"
			}).done(function (a) {
				$("#"+c).html(a);
			})
		}
	});
});