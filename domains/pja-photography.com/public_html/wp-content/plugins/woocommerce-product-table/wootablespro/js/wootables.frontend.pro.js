(function ($, app) {
	"use strict";
	$(document).ready(function() {
		var WtbpFrontendPage = app.wtbpFrontendPage;
		wtbpEventsFrontendPro();

		function wtbpEventsFrontendPro() {

			$('.wtbpTableWrapper').on('change', '.wtbpAddMulty', function(e){
				e.preventDefault();
				var checkbox = $(this);
				var row = checkbox.closest('tr');
				if (row.hasClass('child')) {
					row = row.prev();
				}
				if (checkbox.is(":checked")) {
					row.addClass('wtbpMultiAddCheck');
				} else {
					row.removeClass('wtbpMultiAddCheck');
				}
			});

			unDisabledCheckbox();

			// use action to add additional data to cart (right now only with acf field)
			$('.wtbpTableWrapper').on('click', '.add_to_cart_button.product_type_simple', function(e) {
				var button = jQuery(this),
					wrapper = button.closest('.wtbpAddToCartWrapper');
				if(wrapper.hasClass('wtbpDisabledLink')) return false;
				var productId = button.attr('data-product_id'),
					productIdMain = wrapper.attr('data-product_id'),
					addFieldList = button.closest('tr').find('.wtbpAddDataToCartMeta'),
					addData = app.getAddProductCartMetaPro(addFieldList);

				if (addFieldList.length) {

					var selectedProduct = [],
						pushObj = {};

					pushObj.id = productId;
					pushObj.varId = button.attr('data-variation_id');
					pushObj.quantity = button.attr('data-quantity');
					pushObj['addData'] = addData;
					selectedProduct.push(pushObj);
					var data = {
						mod: 'wootablepress',
						action: 'multyProductAddToCart',
						selectedProduct: selectedProduct,
						pl: 'wtbp',
						reqType: "ajax"
					};
					jQuery.ajax({
						url: url,
						data: data,
						type: 'POST',
						success: function (res) {
						}
					});
					return false;
				}
			});

			$('.wtbpTableWrapper').on('click', '.add_to_cart_button', function () {
				var settings    = app.getSetting(false, jQuery(this).closest('.wtbpContentTable'));
				if ('use_cart_styles' in settings && settings.use_cart_styles) {
					let textOnclick = settings.cart_styles.text_onclick;
					if ('undefined' !== typeof textOnclick && ''!==textOnclick) {
						$(this).text(textOnclick);
					}
				}
			});

			$('.wtbpTableWrapper').on('click', '.wtbp_favorites', function(e) {
				e.preventDefault();
				var _this = jQuery(this);

					var data = {
						mod: 'wootablepress',
						action: 'toggleFavorites',
						active : _this.hasClass('active'),
						productId: _this.data('product-id'),
						pl: 'wtbp',
						reqType: "ajax"
					};

					jQuery.ajax({
						url: url,
						data: data,
						type: 'POST',
						beforeSend: function () {
							_this.addClass('fa-spin');
						},
						complete: function () {
							_this.removeClass('fa-spin');
						},
						success: function (res) {
							var res = JSON.parse(res);
							_this.data('favorites', res.data.active);
							if ( true === res.data.active ) {
								_this.addClass('active');
							} else {
								_this.removeClass('active');
							}
						}
					});
			});
		}

		function unDisabledCheckbox(){
			let interval = setInterval(function(){
				let items = $('.wtbpTableWrapper').find('.wtbpAddMulty[disabled]:not([data-variation_id])');
				let selects = $('.wtbpTableWrapper').find('.select2-hidden-accessible');
				if (!items && !selects) {
					clearInterval(interval);
				} else {
					items.removeAttr('disabled');
					$('.wtbpTableWrapper').find('select').removeClass('select2-hidden-accessible');
					$('.wtbpTableWrapper').find('.select2').remove();
				}
			}, 500);
		}
	});

	app.getAddProductCartMetaPro = (function(addFieldList) {
		var addData = {};
		if (addFieldList.length) {
			addFieldList.each(function() {
				var wrapper = jQuery(this),
					acfType = wrapper.data('acf-type'),
					columnTitle = wrapper.data('column-title'),
					fieldKey = wrapper.data('field-key');

				switch (acfType) {
					case 'checkbox':
						var value = [];
						wrapper.find('.acf-field-checkbox input:checked').each(function() {
							var checkbox = jQuery(this);
							value.push(checkbox.closest('label').text());
						});
						if (value.length !== 0 && columnTitle && fieldKey) {
							addData['wtbp_' + fieldKey ] = value.join(', ');
							addData['label_' + 'wtbp_' + fieldKey] = columnTitle;
						}
						break;
					case 'select':
						var value = [];
						wrapper.find('.acf-field-select option:selected').each(function() {
							var option = jQuery(this);
							value.push(option.text());
						});
						if (value.length !== 0 && columnTitle && fieldKey) {
							addData['wtbp_' + fieldKey] = value.join(', ');
							addData['label_' + 'wtbp_' + fieldKey] = columnTitle;
						}
						break;
					case 'text':
						var input = wrapper.find('.acf-field-text input[type=text]'),
							value = input.val();
						if ( value && columnTitle && fieldKey) {
							addData['wtbp_' + fieldKey] = value;
							addData['label_' + 'wtbp_' + fieldKey] = columnTitle;
						}
						break;
					case 'radio':
						var input = wrapper.find('.acf-field-radio input:checked'),
							value = input.closest('label').text();
						if (value && columnTitle && fieldKey) {
							addData['wtbp_' + fieldKey] = value;
							addData['label_' + 'wtbp_' + fieldKey] = columnTitle;
						}
						break;
					case 'button_group':
						var button = wrapper.find('.acf-field-button-group label.selected input'),
							value = button.closest('label').text();
						if (value && columnTitle && fieldKey) {
							addData['wtbp_' + fieldKey] = value;
							addData['label_' + 'wtbp_' + fieldKey] = columnTitle;
						}
						break;
				}
			});
		}

		return addData;
	});

}(window.jQuery, window.woobewoo.WooTablepress));
