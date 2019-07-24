_Bbc(function($){
	$('[name="add_link"], [name="edit_link"]').change(function () {
		var a = $(this).val();
		if (/^(?:ht|f)tps?:\/\//.test(a)) {
			a = a.replace(/\/\/www\./, '//');
			var b = new RegExp('^' + _URL);
			$(this).attr("req", "url false")
			if (b.test(a)) {
				var c = $(this);
				$.ajax({
					type: "POST",
					url: _URL + 'admin/index.php?mod=_cpanel.menu&act=check_link',
					data: {
						"link": $(this).val()
					},
					global: false,
					success: function (a) {
						if (a.ok) {
							$(c).val(a.result.link)
						};
						if (a.result) {
							if (a.result.module_id) {
								$(c).attr("req", "any false")
							}
						}
					},
					dataType: 'json'
				})
			}
		}
	}).trigger("change");
});