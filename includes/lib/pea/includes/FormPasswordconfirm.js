_Bbc(function($) {
	var a = $('input[rel="password_real"]');
	if (a.length) {
		var b = false;
		for (var i = a.length - 1; i >= 0; i--) {
			if (!$(a[i]).parents('form').hasClass('formIsRequire')) {
				(a[i]).parents('form').addClass('formIsRequire');
				b = true;
			};
		};
		if (!b) {
			$.getScript(_URL+"includes/lib/pea/includes/formIsRequire.js");
		};
	};
});