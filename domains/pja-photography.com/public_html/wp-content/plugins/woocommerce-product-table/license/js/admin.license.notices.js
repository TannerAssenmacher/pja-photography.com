"use strict";
jQuery(document).ready(function(){
	jQuery(document).on('click', '.woobewoo-pro-notice.wtbp-notification .notice-dismiss', function(){
		jQuery.sendFormWtbp({
			msgElID: 'noMessages'
		,	data: {mod: 'license', action: 'dismissNotice'}
		});
	});
});