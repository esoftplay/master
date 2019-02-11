function checkAll( a, b) {
	BS3('.'+BS3(a).prop('class'), document.forms[b]).prop('checked', BS3(a).is(':checked'));
}
