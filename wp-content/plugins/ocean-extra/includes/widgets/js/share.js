var $j = jQuery.noConflict();

/* ==============================================
ON CLICK
============================================== */
function owpShareOnClick( href ) {
	var windowWidth 	= '640',
		windowHeight 	= '480',
		windowTop 		= screen.height / 2 - windowHeight / 2,
		windowLeft 		= screen.width / 2 - windowWidth / 2,
		shareWindow 	= 'toolbar=0,status=0,width=' + windowWidth + ',height=' + windowHeight + ',top=' + windowTop + ',left=' + windowLeft;

	open( href, '', shareWindow );
}