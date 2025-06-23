"use strict";
jQuery(document).ready(function(){
	jQuery('#wtbpLicenseForm').submit(function(){
		jQuery(this).sendFormWtbp({
			btn: jQuery(this).find('button.button-primary')
		,	onSuccess: function(res) {
				if(!res.error) {
					toeReload();
				}
			}
		});
		return false;
	});
});