(function ($, app) {
	"use strict";
	function wtbpEventsTablesPro() {

		var WtbpAdminPage = app.WtbpAdminPage,
			wtbpForm = $('body').find('#wtbpTablePressEditForm');
		//Filter Settings
		wtbpForm.find('select[name="settings[filter_attribute_selected][]"]').chosen({ width:"100%" });

		//Custom Taxonomies Settings
		wtbpForm.find('select[name="settings[filter_ctax_selected][]"]').chosen({ width:"100%" });
		
		//Custom Sorting Settings
		function changeCustomSorting(sortableBox) {
			var items = '';
			sortableBox.find('.chosen-choices li.search-choice').each(function(){
				var item = parseInt(jQuery(this).find('a').attr('data-option-array-index'));
				var val = sortableBox.find('select option').eq(item).attr('value');
				if (typeof val !== 'undefined') {
					items += val+',';
				}
			});
			sortableBox.find('.woobewoo-selected-order').val(items.replace(/,$/,""));
		}
		wtbpForm.find('select[name="settings[filter_category_selected][]"]').chosen({ width:"100%" });
		wtbpForm.find('select[name="settings[filter_tag_selected][]"]').chosen({ width:"100%" });
		wtbpForm.find('select[name="settings[filter_category_selected][]"],select[name="settings[filter_tag_selected][]"],select[name="settings[filter_attribute_selected][]"]')
			.on('change', function(evt, params) {
				var parent = jQuery(this).closest('[data-use-sortable="true"]');
				changeCustomSorting(parent);
		});
		
		jQuery('document').ready(function(){
			jQuery('[data-use-sortable="true"]').each(function(){
				var sortableBox = jQuery(this);
				sortableBox.find('.chosen-choices').sortable({
					stop: function( event, ui ) {
						changeCustomSorting(sortableBox);
					}
				});
			});
		});

		//Loader Settings
		var iconPreview = wtbpForm.find('.wtbpIconPreview');
		setIconPreview();

		$('body').on('click', '.chooseLoaderIcon', function (e) {
			e.preventDefault();

			var color = wtbpForm.find('input[name="settings[table_loader_icon_color]"]').val();
			$('body').on('click', '#chooseIconPopup .item-inner', function (e) {
				e.preventDefault();
				var el = $(this),
					name = el.find('.preicon_img').attr('data-name'),
					dataItems = el.find('.preicon_img').attr('data-items');

				wtbpForm.find('input[name="settings[table_loader_icon_name]"]').val(name);
				wtbpForm.find('input[name="settings[table_loader_icon_number]"]').val(dataItems);
				setIconPreview();
				
				$container.empty();
				$container.dialog('close');
			});

			var $container = $('<div id="chooseIconPopup" style="display: none;" title="" /></div>').dialog({
				modal: true
				, autoOpen: false
				, width: 900
				, height: 750
				, buttons: {
					OK: function () {
						$container.empty();
						$container.dialog('close');
					}
					, Cancel: function () {
						$container.empty();
						$container.dialog('close');
					}
				},
				create:function () {
					$(this).closest('.ui-dialog').addClass('woobewoo-plugin');
				}
			});

			var contentHtml = $('.wtbpLoaderIconTemplate').clone().removeClass('wtbpHidden');
			contentHtml.find('div.preicon_img[data-name="spinner"] div').css({'backgroundColor': color});
			contentHtml.find('div.preicon_img').not('[data-name="spinner"]').css({'color':color});
			$container.append(contentHtml);

			var title = $('.chooseLoaderIcon').text();
			$container.dialog("option", "title", title);
			$container.dialog('open');
		});

		var loaderColors = wtbpForm.find('input[name="settings[table_loader_icon_color]"]').closest('.wtbpStyleColors');

		loaderColors.find('.wtbpColorResult').wpColorPicker({
			hide: true,
			defaultColor: false,
			width: 200,
			border: false,
			change: function(event, ui) {
				var color = ui.color.toString(),
					wrapper = $(event.target).closest('.wtbpStyleColors'),
					result = wrapper.find('.wtbpColorResultText'),
					loader = iconPreview.find('.woobewoo-table-loader');
				result.val(color);
				wrapper.find('.button').css('color', color);
				loader.css(loader.hasClass('spinner') ? 'backgroundColor' : 'color', color);
			}
		});
		loaderColors.find('.wtbpColorResultText').on('change', function() {
			var $this = $(this);
			$this.closest('.wtbpStyleColors').find('.wtbpColorResult').wpColorPicker('color', $this.val());		
		}).trigger('change');


		//Custom Styles Settings
		var styleColors = wtbpForm.find('.wtbpCustomStyles .wtbpStyleColors, .wtbpCartStyles .wtbpStyleColors'),
			colorTimeout = 0;

		styleColors.find('.wtbpColorResult').wpColorPicker({
			hide: true,
			defaultColor: false,
			width: 200,
			border: false,
			change: function(event, ui) {
				var color = ui.color.toString(),
					wrapper = $(event.target).closest('.wtbpStyleColors'),
					result = wrapper.find('.wtbpColorResultText'),
					now = Date.now();
				result.val(color);
				calcLightenDarkenColors(result);
				wrapper.find('.button').css('color', color);
				if(colorTimeout == 0 || now - colorTimeout > 1000) {
					colorTimeout = now;
					result.trigger('change');
				}
			}
		});
		styleColors.find('.wtbpColorResultText').on('change', function() {
			var $this = $(this),
				color = $this.val();
			$this.closest('.wtbpStyleColors').find('.wtbpColorResult').wpColorPicker('color', color);
			if(color.length == 0) $this.closest('.wtbpStyleColors').find('.button').css('backgroundColor', '#ffffff').css('color', '#ffffff');
			calcLightenDarkenColors($this);
			
		}).trigger('change');

		wtbpForm.find('.wtbpCartStyles .wtbpCopyStyles').on('click', function() {
			WtbpAdminPage.tablePreviewEnabled = false;
			var forCopy = $(this).attr('data-style'),
				mainEl = false;
			switch(forCopy){
				case 'font':
						mainEl = wtbpForm.find('input[name="settings[cart_styles][font_color_hover]"]');
						mainEl.val(wtbpForm.find('input[name="settings[cart_styles][font_color]"]').val());
						wtbpForm.find('select[name="settings[cart_styles][font_weight_hover]"]').val(wtbpForm.find('select[name="settings[cart_styles][font_weight]"]').val()).trigger('change');
					break;
				case 'borders':
						mainEl = wtbpForm.find('input[name="settings[cart_styles][button_border_color_hover]"]');
						mainEl.val(wtbpForm.find('input[name="settings[cart_styles][button_border_color]"]').val());
						wtbpForm.find('input[name="settings[cart_styles][button_border_top_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][button_border_top]"]').val());
						wtbpForm.find('input[name="settings[cart_styles][button_border_right_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][button_border_right]"]').val()).trigger('change');
						wtbpForm.find('input[name="settings[cart_styles][button_border_bottom_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][button_border_bottom]"]').val()).trigger('change');
						wtbpForm.find('input[name="settings[cart_styles][button_border_left_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][button_border_left]"]').val()).trigger('change');
					break;
				case 'background':
						mainEl = wtbpForm.find('select[name="settings[cart_styles][background_hover]"]');
						mainEl.val(wtbpForm.find('select[name="settings[cart_styles][background]"]').val());
						wtbpForm.find('input[name="settings[cart_styles][button_color_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][button_color]"]').val()).trigger('change');
						wtbpForm.find('input[name="settings[cart_styles][bg_color1_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][bg_color1]"]').val()).trigger('change');
						wtbpForm.find('input[name="settings[cart_styles][bg_color2_hover]"]').val(wtbpForm.find('input[name="settings[cart_styles][bg_color2]"]').val()).trigger('change');
					break;

			}
			WtbpAdminPage.tablePreviewEnabled = true;
			if(mainEl && mainEl.length) mainEl.trigger('change');
		});
		

		function setIconPreview() {
			var name = wtbpForm.find('input[name="settings[table_loader_icon_name]"]').val(),
				color = wtbpForm.find('input[name="settings[table_loader_icon_color]"]').val(),
				dataItems = wtbpForm.find('input[name="settings[table_loader_icon_number]"]').val();
			iconPreview.html('');

			if (name === 'default') {
				iconPreview.html('<div class="woobewoo-table-loader wtbpLogoLoader"></div>');
			} else if (name === 'spinner') {
				iconPreview.html('<div class="woobewoo-table-loader spinner" style="background-color:' + color + '"></div>');
			} else {
				var htmlIcon = ' <div class="woobewoo-table-loader la-' + name + ' la-2x" style="color: ' + color + ';">';
				for (var i = 0; i < dataItems; i++) {
					htmlIcon += '<div></div>';
				}
				htmlIcon += '</div>';
				iconPreview.html(htmlIcon);
			}
		}

		function calcLightenDarkenColors($el) {
			if($el.attr('name') == 'settings[styles][cell_bg_color]') {
				var wrapper = $el.closest('.wtbpStyleColors'),
					color = $el.val();
				wrapper.find('input[name="settings[styles][cell_color_even]"]').val(color.length ? getLightenDarkenColor(color, -20) : '');
				wrapper.find('input[name="settings[styles][cell_color_hover]"]').val(color.length ? getLightenDarkenColor(color, -40) : '');
				wrapper.find('input[name="settings[styles][cell_color_order]"]').val(color.length ? getLightenDarkenColor(color, -60) : '');
			}
		}

		function getLightenDarkenColor(col, amt) {
			var usePound = false;
			if(col[0] == "#") {
				col = col.slice(1);
				usePound = true;
			}
 			var num = parseInt(col, 16),
				r = (num >> 16) + amt,
				b = ((num >> 8) & 0x00FF) + amt,
				g = (num & 0x0000FF) + amt;
			if(r > 255) r = 255;
			else if(r < 0) r = 0;
			if(b > 255) b = 255;
			else if(b < 0) b = 0;
			if(g > 255) g = 255;
			else if(g < 0) g = 0;
			var res = (g | (b << 8) | (r << 16)).toString(16);
			return (usePound?"#":"") + '0'.repeat(6 - res.length) + res;
		}

		wtbpSetupPriceByHands();


		//Auto Add Products
		var autoSelect = $('#wtbpAutoCategoriesList'),
			autoInput = $('#wtbpTablePressEditForm input[name="settings[auto_categories_list]"]'),
			autoButton = $('#wtbpAutoAddProducts');

		autoSelect.multipleSelect({
			selectAll: true,
			onClick: function(element) {
				var selected = autoSelect.multipleSelect('getSelects');
				if(element.checked) {
					selected = $.merge(selected, checkSubCategories(element.value, []));
					autoSelect.multipleSelect('setSelects', selected);
				}
				autoInput.val(selected);
			},
			onCheckAll: function() {
				autoInput.val('all');
			},
			onUncheckAll: function() {
				autoInput.val('');
			}
		});
		function checkSubCategories(parent, list) {
			autoSelect.find('option[data-parent="'+parent+'"]').each(function() {
				var value = $(this).val();
				list.push(value);
				list = checkSubCategories(value, list);
			});
			return list;
		}
		var autoValue = autoInput.val();
		if(autoValue == 'all') {
			autoSelect.multipleSelect('checkAll');
		} else {
			autoSelect.multipleSelect('setSelects', autoValue.split(','));
		}

		wtbpForm.find('input[name="settings[auto_categories_enable]"]').on('change', function () {
			if($(this).is(':checked')) {
				autoSelect.multipleSelect('enable');
				autoButton.removeAttr('disabled');
			} else {
				autoSelect.multipleSelect('disable');
				autoButton.attr('disabled', 'disabled');
			}
		}).trigger('change');

		$('#wtbpAutoAddProducts').on('click', function (e) {
			e.preventDefault();
			WtbpAdminPage.loadProductsContentTbl(true, true);
		});
		
		// Auto add products with variations
		var autoVariableSelect = $('#wtbpAutoVariationsList'),
			autoVariableInput = $('#wtbpTablePressEditForm input[name="settings[auto_variations_list]"]'),
			autoVariableButton = $('#wtbpAutoAddVariations');
		
		autoVariableSelect.multipleSelect({
			selectAll: true,
			onClick: function(element) {
				var selected = autoVariableSelect.multipleSelect('getSelects');
				autoVariableInput.val(selected);
			},
			onCheckAll: function() {
				autoVariableInput.val('all');
			},
			onUncheckAll: function() {
				autoVariableInput.val('');
			}
		});
		var autoValue = autoVariableInput.val();
		if(autoValue == 'all') {
			autoVariableSelect.multipleSelect('checkAll');
		} else {
			autoVariableSelect.multipleSelect('setSelects', autoValue.split(','));
		}
		
		wtbpForm.find('input[name="settings[auto_variations_enable]"]').on('change', function () {
			if($(this).is(':checked')) {
				autoVariableSelect.multipleSelect('enable');
				autoVariableButton.removeAttr('disabled');
			} else {
				autoVariableSelect.multipleSelect('disable');
				autoVariableButton.attr('disabled', 'disabled');
			}
		}).trigger('change');
		
		$('#wtbpAutoAddVariations').on('click', function (e) {
			e.preventDefault();
			WtbpAdminPage.loadProductsContentTbl(false, true, true);
		});
		
		WtbpAdminPage.tablePreviewEnabled = true;
	}

	function wtbpSetupPriceByHands() {
		var priceRangeInput = $('#wtbpRangeList'),
			options = {
				modal: true,
				autoOpen: false,
				width: 530,
				height: 400,
				buttons: {
					OK: function () {
						var emptyInput = false,
							options = '',
							range = $('#wtbpRangeListPopup .wtbpRangeList');

						//check if input is empty
						range.find('input').each(function () {
							var $this = $(this);
							if(!$this.val()) {
								$this.addClass('wtbpWarning');
								emptyInput = true;
							}
						});

						if(!emptyInput) {
							var rangeCount = range.length,
								i = 1;
							range.each(function () {
								var el = $(this);
								options += el.find('.wtbpRangeListFrom input').val() + ',';
								if(i === rangeCount) {
									options += el.find('.wtbpRangeListTo input').val();
								} else {
									options += el.find('.wtbpRangeListTo input').val() + ',';
								}

								i++;
							});

							priceRangeInput.val(options).trigger('change');
							$container.empty();
							$container.dialog('close');
						}

					},
					Cancel: function () {
						$container.empty();
						$container.dialog('close');

					}
				},
				create:function () {
					$(this).closest('.ui-dialog').addClass('woobewoo-plugin');
				}
			};
		var $container = $('<div id="wtbpRangeListPopup"></div>').dialog(options);

		$('#wtbpRangeListSetup').on('click', function (e) {
			e.preventDefault();
			var appendTemplate = '',
				priceRange = priceRangeInput.val(),
				template = $('.wtbpRangeListTemplate').clone().html(),
				templAddButton = $('.wtbpRangeListButtonTemplate').clone().html();
			$container.empty();

			if(priceRange.length <= 0) {
				for(var i = 1; i < 2; i++ ){
					appendTemplate += template;
				}
				appendTemplate += templAddButton;
				$container.append(appendTemplate);
				$container.dialog('option', 'title', 'Price Range');
				$container.dialog('open');
			} else {
				var priceRangeArray = priceRange.split(',');
				for(var i = 0; i < priceRangeArray.length/2; i++ ){
					appendTemplate += template;
				}

				appendTemplate += templAddButton;
				$container.append(appendTemplate);
				$container.dialog('option', 'title', 'Price Range');
				$container.dialog('open');

				var k = 0;
				$('#wtbpRangeListPopup input').each(function(){
					var input = $(this);
					if (k < priceRangeArray.length) {
						input.val(priceRangeArray[k]);
						k++;
					} else {
						input.closest('.wtbpRangeList').remove();
					}
				});
			}
		});

		$('body').on('click', '.wtbpAddPriceRange', function (e) {
			e.preventDefault();
			var templates = $('.wtbpRangeListTemplate').clone().html();
			$(templates).insertBefore('.wtbpRangeListButton');
			sortablePrice();
		});

		$('body').on('click', '.wtbpRangeListRemove', function(e){
			e.preventDefault();
			var _this = $(this);
			_this.closest('.wtbpRangeList').remove();
		});

		//make properties sortable
		function sortablePrice(){
			$('#wtbpRangeListPopup').sortable({
				cursor: 'move',
				axis: 'y',
				handle: '.wtbpRangeListHandler'
			});
		}
		sortablePrice();

	}

	$(document).ready(function() {
		wtbpEventsTablesPro();
	});


}(window.jQuery, window.woobewoo));
