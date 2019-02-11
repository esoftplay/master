_Bbc(function($){
	$('#email_format').change(function() {
		var a = $(this).parent().next();
		if($(this).val() == '1')
		{
			$('textarea', a).each(function(a){
				realtext[a] = $(this).val();
				$(this).val(realtext[a].replace(/\n/g, '<br />'));
				CKEDITOR.replace($(this).get(0), {toolbar: 'Basic'});
			});
		}else{
			for(name in CKEDITOR.instances)
			{
				CKEDITOR.instances[name].destroy();
			}
			$('textarea', a).each(function(a){
				var b = '';
				if (typeof realtext[a] == 'string') {
					b = realtext[a];
				}else{
					b = $(this).val();
				}
				$(this).val(b.replace(/(<br.*?>)/g, ""))
				.css('visibility', 'visible').show();
			});
		}
	}).trigger('change');
});
var realtext = [];