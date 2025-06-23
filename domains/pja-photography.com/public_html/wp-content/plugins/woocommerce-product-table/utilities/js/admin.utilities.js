"use strict";
jQuery(document).ready(function(){
	jQuery('#wtbpConverterForm').submit(function(e){
		e.preventDefault();
		var $form = jQuery(this),
			error = false,
			$name = $form.find('input[name="attribute_name"]'),
			$slug = $form.find('input[name="attribute_slug"]');

		if($name.val() == '') {
			$name.addClass('woobewoo-input-error');
			error = true;
		} else {
			$name.removeClass('woobewoo-input-error');
		}

		if($slug.val() == '') {
			$slug.addClass('woobewoo-input-error');
			error = true;
		} else {
			$slug.removeClass('woobewoo-input-error');
		}
		if(error) return false;

		jQuery(this).sendFormWtbp({
			btn: jQuery(this).find('button.button-primary'),
			onSuccess: function(res) {
				if(!res.error && res.messages) {
					var $dialog = jQuery('<div title="'+ toeLangWtbp("Done")+ '" />').html(res.messages.join('<br />')).appendTo('body').dialog({
						modal: true,
						width: '500px',
						buttons: {
							OK: function () {
								$dialog.dialog('close');
								toeReload();
							},
						},
						create: function () {
							jQuery(this).closest(".ui-dialog").addClass('woobewoo-plugin');
						}
					});
				}
			}
		});
		return false;
	});
});
