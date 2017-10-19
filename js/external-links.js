$( document ).ready(function() {
	$('head').append('<style type="text/css">form[action="/cart/add"] input[type="submit"], form[action="/cart/add"] input[type="button"], form[action="/cart/add"] button {display:none;}select[name="id"] {visibility:hidden;}</style>');
	if($('.selector-wrapper select option').length == 1 && $('.selector-wrapper select').find('option:first').val() == 'Default Title'){
		$('head').append('<style type="text/css">.selector-wrapper{display:none !important;}</style>');
	}
});

