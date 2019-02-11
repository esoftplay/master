_Bbc(function($){
	$('.checked_others').change(function(){
		var a = $(this).prop('id');
		$('#'+a+'_checked').val($(this).val());
		$('#'+a+'_checked').attr('checked', true);
	});
	$('.radio_others').change(function(){
		var a = $(this).prop('id');
		$('#'+a+'_checked').val($(this).val());
		$('#'+a+'_radio').attr('checked', true);
	});
});