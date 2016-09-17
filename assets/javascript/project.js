$(document).ready(function() {

	// polyfill to add support for postion:sticky
	$('.sticky').Stickyfill();

	// filter dropdowns
	$('.dropdown li a').on('click', function(e) {
		// get the webroot variable
		var webroot = $('body').data('webroot');

		// set the value in the hidden input
		var hashURL = $(this).prop('href');
		var hashValue = hashURL.substring(hashURL.indexOf("#")+1);
		var hidden = $(this).parent().parent().parent().find('input[type="hidden"]');
		hidden.val(hashValue);

		// visit the new URL
		var pathname;
		if( window.location.pathname == webroot ) {
			pathname = 'transactions';
		} else {
			var pathparts = window.location.pathname.split('/');
			pathname = pathparts[pathparts.length-2];
		}
		if( hashValue == 'transactions' ) window.location.href = window.location.protocol + '//' + window.location.host + webroot + pathname;
		else if( hashValue == 'charts' ) window.location.href = window.location.protocol + '//' + window.location.host + webroot + pathname + '/';
		else if( hashValue == 'trends' ) window.location.href = window.location.protocol + '//' + window.location.host + webroot + pathname + '/';
		else window.location.href = window.location.protocol + '//' + window.location.host + webroot + pathname + '/' + hashValue + '/';

		// prevent event bubbling
		e.preventDefault();
	});

});
