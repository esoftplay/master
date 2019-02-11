_Bbc(function($) {
	$('#to-top').hide();
	var offset = 150;
	var duration = 500;
	$(window).scroll(function() {
		if ($(this).scrollTop() > offset) {
			$('#to-top').fadeIn(duration);
		} else {
			$('#to-top').fadeOut(duration);
		}
	});
	$('#to-top').click(function(event) {
		event.preventDefault();
		$('html, body').animate({scrollTop: 0}, duration);
		return false;
	});
});