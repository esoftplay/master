function multicheckboxIsChecked(cboxes, option) {
	var unchecked = 0;
	$(cboxes).each(function(a){
		if (!$(this).is(":checked")) {
			unchecked++;
		}
	});
	if (unchecked > 0) {
		$(option).text("checkAll");
	}else{
		$(option).text("uncheckAll");
	}
};
_Bbc(function($){
	$(".multicheckboxAll").each(function(a){
		var option = $(this);
		var parent = $(this).parent(".checkbox");
		var cboxes = $(parent).find('input:checkbox');
		multicheckboxIsChecked(cboxes, option)
		$(cboxes).change(function(){
			multicheckboxIsChecked(cboxes, option);
		});
		$(this).on("click", function(a){
			a.preventDefault();
			if ($(this).text()=="checkAll") {
				$(cboxes).prop('checked', true);
				$(this).text("uncheckAll");
			}else{
				$(cboxes).prop('checked', false);
				$(this).text("checkAll");
			}
		});
	})
});