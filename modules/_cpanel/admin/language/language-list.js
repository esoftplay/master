_Bbc(function($) {
	var r = $('.table textarea');
	$('#is_htmleditor').change(function() {
		if (!$('#is_htmleditor').is(':checked')) {
			$(r).show();
			$(r).css('visibility', 'visible');
			for(name in CKEDITOR.instances)
			{
				if (!/^add_/.test(name)) {
					CKEDITOR.instances[name].destroy();
				};
			}
		}
	});
	$('#email_format').trigger('change');
	$(r).focus(function(){
		if ($('#is_htmleditor').is(':checked')) {
			var e = CKEDITOR.inline($(this).get(0), {toolbar: 'Basic'});
			e.on('instanceReady', function(){this.focus()});
		};
	});
});
