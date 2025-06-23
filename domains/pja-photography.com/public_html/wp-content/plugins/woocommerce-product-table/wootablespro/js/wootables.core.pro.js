(function ($, app) {
	"use strict";

	var _alphabetSearch = '';

	app.setSettingsPro = (function(settings, objAttr, tableWrapper) {
		objAttr = this.enableMultyAddButton(settings, objAttr);
		objAttr = this.enableAllAddButton(settings, objAttr);
		objAttr = this.enableSSP(settings, objAttr, tableWrapper);
		objAttr = this.enableLazyLoad(settings, objAttr, tableWrapper);
		return objAttr;
	});

	app.setMethodsPro = (function(tableWrapper, tableObj, settings) {
		this.setTableFilters(tableWrapper, tableObj, settings);
		this.setTableEvents(tableWrapper, tableObj);
		this.initVariationsPopup(tableWrapper, settings);
		if (settings.search_by_letter) {
			this.initAlphabet(tableObj);
		}
	});

	app.enableMultyAddButton = (function(settings, objAttr) {
		this.multyAddButtonEnable = this.checkSettings(settings, 'multiple_add_cart', false) == '1';
		if(this.multyAddButtonEnable){
			objAttr['buttons'].push({
				text: settings.selected_to_cart,
				className: 'button wtbpAddSelToCart',
				action: function (e, dt, node, config) {
					if(node.closest('.wtbpTableWrapper').attr('data-table-id') == 'wtbpPreviewTable') return;

					var productMpc = [],
						interval = 0,
						key = 0;

					var selectedProduct = [],
						inputs = dt.rows('.wtbpMultiAddCheck').nodes().to$().find('td.thumbnail .add_to_cart_button'),
						checks = dt.rows('.wtbpMultiAddCheck').nodes().to$().find('input.wtbpAddMulty'),
						// for responsive and autohide
						inputsChild = node.closest('.wtbpTableWrapper').find('.child .wtbpAddMulty'),
						inputHeader = $(dt.table().header()).find('input.wtbpAddMultyAll'),
						inputFooter = $(dt.table().footer()).find('input.wtbpAddMultyAll');

					if(typeof inputs == 'undefined' || inputs.length == 0) {
						inputs = dt.rows('.wtbpMultiAddCheck').nodes().to$().find('.wtbpAddToCartWrapper a');
					}

					interval = 0;
					key = 0;

					// Fix for MPC plugin
					function clickByTimer(key) {
						if (productMpc[key]['cell'].length > 0) {
							productMpc[key]['cell'].trigger('click');
						} else {
							productMpc[key]['input'].trigger('click');
						}
					}

					inputs.each(function () {
						var input = $(this),
							pushObj = {},
							id = input.attr('data-product_id'),
							quantity = input.attr('data-quantity'),
							wrapper = input.closest('.wtbpAddToCartWrapper'),
							addFieldList = input.closest('tr').find('.wtbpAddDataToCartMeta');

						pushObj['addData'] = app.getAddProductCartMetaPro(addFieldList);
						pushObj.id = id;
						pushObj.varId = input.attr('data-variation_id');
						pushObj.quantity = quantity;
						if(typeof pushObj.varId != 'undefined' && pushObj.varId != '0') {
							var variation = {};
							$.each(this.attributes, function() {
								if(this.name.indexOf('data-attribute_') === 0) {
									variation[this.name.replace('data-', '')] = this.value;
								}
							});
							pushObj.variation = variation;
						}

						// Fix for MPC plugin
						if ( input.hasClass('product_mpc') ) {
							interval = interval + 900;
							productMpc[key] = [];
							productMpc[key]['index'] = $(this).closest('tr.parent').index();
							if (key > 0) {
								productMpc[key]['index'] = productMpc[key]['index'] - key;
							}
							productMpc[key]['cell'] = (dt.row('tr:eq('+productMpc[key]['index']+')').child()) ? dt.row('tr:eq('+productMpc[key]['index']+')').child().find('.product_mpc') : 0;
							productMpc[key]['input'] = input;
							setTimeout( clickByTimer.bind(null, key), interval);
							key = key + 1;
						} else {
							selectedProduct.push(pushObj);
						}
					});

					// if buy column is hidden
					const products_checked = $('input.wtbpAddMulty:checked');
					if (!inputs.length && products_checked.length) {
						products_checked.each(function() {
							selectedProduct.push({
								addData: {},
								id: $(this).attr('value'),
								quantity: 1,
								varId: undefined
							});
						});
						$(this).prop('checked', false);
					}

					var addToCartMessagePosition = app.checkSettings(settings, 'add_to_cart_message_position'),
						bunchAddCartResponse = app.getBunchAddCart(selectedProduct, settings);
					// falback for rpevious always true functionality
					if ( typeof addToCartMessagePosition == 'undefined') {
						isAddToCartMessage = true;
					} else {
						var isAddToCartMessage = app.checkSettings(settings, 'show_add_to_cart_message');
					}

					if (isAddToCartMessage && bunchAddCartResponse !== true) {
						$.sNotify({
							'icon': 'fa fa-exclamation',
							'content': bunchAddCartResponse,
							'delay' : 4000,
							'position': addToCartMessagePosition
						})
						return;
					}

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
							var added = 1;
							try{
								var result = JSON.parse(res);
								var message = result.messages;
							}catch(e){
								var message = 'Error!';
							}
							if(typeof result != 'undefined' && 'data' in result && 'added' in result.data){
								added = result.data.added;
							}
							if(added) {
								$(document.body).trigger( 'wc_fragment_refresh' );
							}

							inputs.each(function () {
								var buttonAddToCart = $(this),
								inputQty = buttonAddToCart.closest('td.add_to_cart .wtbpAddToCartWrapper').find('.qty'),
								minQty = inputQty.attr('min');

								if ( minQty === '0' ) {
									minQty = '1';
								}

								if ( minQty !== "undefined" ) {
									inputQty.val(minQty).trigger('change');
									buttonAddToCart.prop('checked', false).attr('data-quantity', minQty);
								}
							});

							inputsChild.each(function () {
								var buttonAddToCart = $(this),
								inputQty = buttonAddToCart.closest('.wtbpAddToCartWrapper').find('.qty'),
								minQty = inputQty.attr('min');

								if ( minQty === '0' ) {
									minQty = '1';
								}

								if ( minQty !== "undefined" ) {
									inputQty.val(minQty).trigger('change');
									buttonAddToCart.prop('checked', false).attr('data-quantity', minQty);
								}
							});
							if(inputHeader.length) {
								inputHeader.prop('checked', false);
							}
							if(inputFooter.length) {
								inputFooter.prop('checked', false);
							}
							if(checks.length) {
								checks.prop('checked', false).trigger('change');
							}

							if (message.length != 0 ) {
								if (added) {
									if ( isAddToCartMessage ) {
										$.sNotify({
											'icon': 'fa fa-check',
											'content': '<span>'+message+'</span>',
											'delay' : 2500,
											'position': addToCartMessagePosition
										});
									}
								} else {
									$.sNotify({
										'icon': 'fa fa-exclamation',
										'content': '<span>'+message+'</span>',
										'delay' : 2500,
									});
								}
							}
						}
					});
				}
			});
		}
		return objAttr;
	});

	app.enableAllAddButton = (function(settings, objAttr) {
		this.allAddButtonEnable = this.checkSettings(settings, 'all_add_cart', false) == '1';
		if(this.allAddButtonEnable){
			objAttr['buttons'].push({
				text: this.checkSettings(settings, 'all_to_cart', 'Add all to card'),
				className: 'button wtbpAddAllToCart',
				action: function (e, dt, node, config) {
					if(node.closest('.wtbpTableWrapper').attr('data-table-id') == 'wtbpPreviewTable') return;

					var selectedProduct = [],
						buttons = dt.rows({search:'applied'}).nodes().to$().find('td.thumbnail .add_to_cart_button'),
						checks = dt.rows('.wtbpMultiAddCheck').nodes().to$().find('input.wtbpAddMulty'),
						// for responcive and automode
						inputsChild = node.closest('.wtbpTableWrapper').find('.child .wtbpAddMulty'),
						inputHeader = $(dt.table().header()).find('input.wtbpAddMultyAll'),
						inputFooter = $(dt.table().footer()).find('input.wtbpAddMultyAll');

					if(typeof buttons == 'undefined' || buttons.length == 0) {
						buttons = dt.rows({search:'applied'}).nodes().to$().find('.wtbpAddToCartWrapper a');
					}

					buttons.each(function () {
						var button = $(this),
							pushObj = {},
							is_variable = button.closest('tr').find('.add_to_cart .add_to_cart_button').hasClass('product_type_variable'),
							is_variable_thumbnail = button.closest('tr').find('.thumbnail .add_to_cart_button').hasClass('product_type_variable');

						if(button.closest('.wtbpDisabledLink').length == 0 && ! is_variable && ! is_variable_thumbnail) {
							var wrapper = button.closest('.wtbpAddToCartWrapper'),
								productId = button.attr('data-product_id'),
								productIdMain = wrapper.attr('data-product_id'),
								addFieldList = button.closest('tr').find('.wtbpAddDataToCartMeta');

							pushObj['addData'] = app.getAddProductCartMetaPro(addFieldList);
							pushObj.id = productId;
							pushObj.varId = button.attr('data-variation_id');
							pushObj.quantity = button.closest('.wtbpAddToCartWrapper').find('input[name="quantity"]').val();

							if(typeof pushObj.varId != 'undefined' && pushObj.varId != '0') {
								var variation = {};
								$.each(this.attributes, function() {
									if(this.name.indexOf('data-attribute_') === 0) {
										variation[this.name.replace('data-', '')] = this.value;
									}
								});
								pushObj.variation = variation;
							}

							selectedProduct.push(pushObj);
						}
					});

					var bunchAddCartResponse = app.getBunchAddCart(selectedProduct, settings);
					if (bunchAddCartResponse !== true) {
						$.sNotify({
							'icon': 'fa fa-exclamation',
							'content': bunchAddCartResponse,
							'delay' : 4000,
						})
						return;
					}

					var data = {
						mod: 'wootablepress',
						action: 'multyProductAddToCart',
						selectedProduct: selectedProduct,
						pl: 'wtbp',
						reqType: "ajax"
					};
					if(selectedProduct.length > 0) {
						jQuery.ajax({
							url: url,
							data: data,
							type: 'POST',
							success: function (res) {
								var added = 1;
								try{
									var data = JSON.parse(res);
									var message = data.messages;
								}catch(e){
									var message = 'Error!';
								}
								if(typeof result != 'undefined' && 'data' in result && 'added' in result.data){
									added = result.data.added;
								}
								if(added) {
									$(document.body).trigger( 'wc_fragment_refresh' );
								}

								buttons.each(function () {
									var buttonAddToCart = $(this),
									inputQty = buttonAddToCart.closest('td.add_to_cart .wtbpAddToCartWrapper').find('.qty'),
									minQty = inputQty.attr('min');

									if ( minQty === '0' ) {
										minQty = '1';
									}

									if ( minQty !== "undefined" ) {
										inputQty.val(minQty).trigger('change');
										buttonAddToCart.prop('checked', false).attr('data-quantity', minQty);
									}
								});

								inputsChild.each(function () {
									var buttonAddToCart = $(this),
									inputQty = buttonAddToCart.closest('td.add_to_cart .wtbpAddToCartWrapper').find('.qty'),
									minQty = inputQty.attr('min');

									if ( minQty === '0' ) {
										minQty = '1';
									}

									if ( minQty !== "undefined" ) {
										inputQty.val(minQty).trigger('change');
										buttonAddToCart.prop('checked', false).attr('data-quantity', minQty);
									}
								});
								if(inputHeader.length) {
									inputHeader.prop('checked', false);
								}
								if(inputFooter.length) {
									inputFooter.prop('checked', false);
								}
								if(checks.length) {
									checks.prop('checked', false).trigger('change');
								}

								if (message.length != 0 ) {
									if (added) {
										var addToCartMessagePosition = window.woobewoo.WooTablepress.checkSettings(settings, 'add_to_cart_message_position')
										// falback for rpevious always true functionality
										if ( typeof addToCartMessagePosition == 'undefind') {
											isAddToCartMessage = true;
										} else {
											var isAddToCartMessage = window.woobewoo.WooTablepress.checkSettings(settings, 'show_add_to_cart_message');
										}
										if (isAddToCartMessage) {
											$.sNotify({
												'icon': 'fa fa-check',
												'content': '<span>'+message+'</span>',
												'delay' : 2500,
												'position': addToCartMessagePosition
											});
										}
									} else {
										$.sNotify({
											'icon': 'fa fa-exclamation',
											'content': '<span>'+message+'</span>',
											'delay' : 2500
										});
									}
								}
							}
						});
					}
				}
			});
		}
		return objAttr;
	});

	app.checkMultyAll = (function(check, wrapper) {
		var checked = check.is(':checked'),
			checks = wrapper.find('input.wtbpAddMulty'+(checked ? ':not(:disabled)' : '')).prop('checked', checked);
		if(checked) {
			checks.closest('tr').addClass('wtbpMultiAddCheck');
		} else {
			checks.closest('tr').removeClass('wtbpMultiAddCheck');
		}
		wrapper.find('input.wtbpAddMultyAll').prop('checked', checked);
		$('table.fixedHeader-floating[aria-describedby="'+wrapper.attr('data-table-id')+'_info"]').find('input.wtbpAddMultyAll').prop('checked', checked);
	});

	app.enableSSP = (function(settings, objAttr, tableWrapper) {
		var isSSP = this.checkSettings(settings, 'pagination', false) && this.checkSettings(settings, 'pagination_ssp', false);

		if (!isSSP) {
			return objAttr;
		}

		try {
			var columns = JSON.parse(settings.order);
		} catch(e)  {
			var columns = [];
		}

		var __this = this,
			table = tableWrapper.find('.wtbpContentTable'),
			tableId = table.data('table-id'),
			adminPage = table.attr('id') == 'wtbpPreviewTable' && window.woobewoo && window.woobewoo.WtbpAdminPage ? window.woobewoo.WtbpAdminPage : false,
			filters = tableWrapper.find('.wtbpFiltersWrapper select'),
			isMulty = this.checkSettings(settings, 'multiple_add_cart', false) == '1',
			productsVendor = this.checkSettings(settings, 'products_vendor', 0),
			loadedRows = [],
			loadedCells = [],
			urlParams = new URLSearchParams(window.location.search),
			s = urlParams.get('s'),
			s = (s) ? '&s=' + s : '',
			ajaxSource = {
				processing: this.checkSettings(settings, 'lazy_load', false) ? false : true,
				serverSide: true,
				ajax: {
					url: (window.ajaxurl && typeof(window.ajaxurl) == 'string' ? window.ajaxurl : WTBP_DATA.ajaxurl)+'?mod=wootablespro&action=getProductPage&pl=wtbp&reqType=ajax'+s,
					type: 'POST',
					data: function (d) {
						if(adminPage) {
							adminPage.tableSSPReloading = true;
							d.settings = adminPage.getSettingsFormData();
							var form = $('#wtbpTablePressEditForm');
							d.productids = form.find('input[name="settings[productids]"]').val();
							d.orders = form.find('input[name="settings[order]"]').val();
						}
						d.id = tableId;
						if(d['order'].length) {
							var sortColumn = d['order'][0]['column'] - (isMulty ? 1 : 0);
							d.sortCol = columns.length > sortColumn ? columns[sortColumn] : '';
						}
						d.products_vendor = productsVendor;
						var filterList = {},
							logic = {},
							children = {};
						filters.each(function(){
							var $this = $(this),
								filter = {
									filterParam: []
								};
							if($this.val() != '') {
								var key = $this.attr('data-tax-key'),
									queryType = $this.attr('data-query-type'),
									dataType = $this.attr('data-type'),
									isIds = $this.attr('data-ids'),
									dataMetaKey = $this.attr('data-meta-key');
								if ( ! queryType ) {
									queryType = 'taxonomy';
								}

								$($this).find('option:selected').each(function() {
									var $this = $(this);
									var optioinParam = {
										'filterValue': $this.val(),
										'productIdList': $this.data('product-id-list')
									};
									filter['filterParam'].push(optioinParam);
								});
								filter['queryType'] = queryType;
								filter['dataType'] = dataType;
								filter['dataMetaKey'] = dataMetaKey;
								filter['isIds'] = typeof(isIds) != 'undefined' ? isIds : 0;
								filterList[key] = filter; 
								logic[key] = $this.attr('data-logic');
								children[key] = $this.attr('data-children');
							}
						});
						d.filters = filterList;
						d.logic = logic;
						d.children = children;
						if ($('input[name="product_id"]').length === 1) {
							d.product_id = $('input[name="product_id"]').val();
						}
					},
					dataFilter: function(data){
						tableWrapper.find('input.wtbpAddMultyAll').prop('checked', false);
						var json = jQuery.parseJSON(data),
							rows = $(json.html).find('tr'),
							aData = [];

						loadedRows = [];
						loadedCells = [];
						for(var i = 0; i < rows.length; i++) {
							var row = rows[i];
							loadedRows.push(row.attributes);
							var cells = $(row).find('td'),
								attrs = [],
								vals = [];
							for(var j = 0; j < cells.length; j++) {
								var cell = cells[j];
								attrs.push(cell.attributes);
								vals.push(cell.innerHTML);
							}
							loadedCells.push(attrs);
							aData.push(vals);
						}
						json.html = '';
						json.data = aData;
						return JSON.stringify(json);
					}
				},
				createdRow: function (row, data, dataIndex) {
					if(typeof(loadedRows[dataIndex]) != 'undefined') {
						$(loadedRows[dataIndex]).each(function () {
							$(row).attr(this.name, this.value);
						});
					}
				}
			};
		objAttr = $.extend({}, objAttr, ajaxSource);
		//if ( !this.checkSettings(settings, 'lazy_load', false) ) {
			objAttr['columnDefs'].push({
				targets: '_all',
				cellType: 'td',
				createdCell: function (td, cellData, rowData, row, col) {
					if (typeof(loadedCells[row][col]) != 'undefined') {
						$(loadedCells[row][col]).each(function () {
							if (this.name != 'data-formula') {
								$(td).attr(this.name, this.value);
							}
						});
					}
				}
			});
		//}

		return objAttr;
	});
	
	app.enableLazyLoad = (function(settings, objAttr, tableWrapper) {
		if(this.checkSettings(settings, 'lazy_load', false) != '1') return objAttr;
		if(this.checkSettings(settings, 'pagination', false) == '1') return objAttr;
		
		var ajaxSource = {
				lazyLoad: {
					url: (window.ajaxurl && typeof(window.ajaxurl) == 'string' ? window.ajaxurl : WTBP_DATA.ajaxurl)+'?mod=wootablespro&action=getProductPage&pl=wtbp&reqType=ajax',
				}
			};
		objAttr = $.extend({}, objAttr, ajaxSource);
		
		return objAttr;
	});

	app.convertStringToNum = (function(str) {
		var n = Number(str);
		if(Number.isNaN(n)) return 0;
		return n;
	});
	app.convertArrayToNum = (function(arr) {
		if(arr.length < 2) return [0, 0];
		else {
			for(var i = 0; i < arr.length; i++) {
				arr[i] = this.convertStringToNum(arr[i]);
			}
		}
		return arr;
	});

	app.setTableFilters = (function(tableWrapper, table, settings) {
		var	filters = tableWrapper.find('.wtbpFiltersWrapper');
		if(filters.length > 0 && filters.closest('.wtbpFilters').length == 0){
			tableWrapper.find('.wtbpFilters').empty().append(filters);
		}
		var filtersInput = tableWrapper.find('.wtbpFiltersWrapper select'),
			__this = this;
		
		filtersInput.each(function() {
			var oControl = $(this),
				tableId = tableWrapper.attr('data-table-id'),
				columns = table.settings()[0].aoColumns,
				keys = oControl.attr('data-column-keys'),
				filterType = oControl.attr('data-type'),
				columnKeys = (typeof keys !== 'undefined' ? keys.split(',') : [filterType]),
				columnIds = [];
			for(var i = 0; i < columns.length; i++)	{
				if(columnKeys.indexOf(columns[i]['key']) != -1) {
					columnIds.push(columns[i]['idx']);
				}
			}
			if(oControl.attr('multiple') == 'multiple') {
				oControl.find('option').removeAttr('selected');
				oControl.multipleSelect({
					filter: true,
					selectAll: false,
					onUncheckAll: function() {
						oControl.val('');
					}
				});
			}

			if(columnIds.length > 0) {
				$.fn.dataTableExt.afnFiltering.push(function( oSettings, aData, iDataIndex ) {
					if(oSettings.sTableId != tableId) return true;
					var value = oControl.val();
					if(!value || value.length == 0) return true;
					var isMulti = oControl.attr('multiple') == 'multiple',
						cntValues = isMulti ? value.length : 1,
						cntNeed = isMulti && oControl.attr('data-logic') == 'and' ? cntValues : 1,
						isSpecific = oControl.attr('data-specific') == '1',
						isPrice = oControl.attr('data-type') == 'price';

					if(isPrice) {
						var minMax = [];
						if (isMulti) {
							for(var v = 0; v < cntValues; v++) {
								minMax.push(__this.convertArrayToNum(value[v].split(',', 2)));
							}
						} else {
							minMax.push(__this.convertArrayToNum(value.split(',', 2)));
						}
					}

					for(var i = 0; i < columnIds.length; i++) {
						if(isSpecific) {
							if(aData[columnIds[i]] === value) {
								return true;
							}
						} else {
							var dataFilter = $(oSettings.aoData[iDataIndex].anCells[columnIds[i]]).attr('data-custom-filter');
							if(isPrice) {
								var price = dataFilter;
								if(typeof price != 'undefined') {
									var prices = price.split(',');
									for(var p = 0; p < prices.length; p++) {
										price = __this.convertStringToNum(prices[p]);
										for(var v = 0; v < cntValues; v++) {
											if(price >= minMax[v][0] && price <= minMax[v][1]) {
												return true;
											}
										}
									}
								}
							} else {
								if(typeof dataFilter == 'undefined') dataFilter = aData[columnIds[i]];
								dataFilter = dataFilter.indexOf(',') > -1 ? dataFilter.split(',') : [dataFilter];
								if(isMulti) {
									var found = 0;
									for(var v = 0; v < cntValues; v++) {
										for(var f = 0; f < dataFilter.length; f++) {
											if (dataFilter[f].trim().indexOf(value[v]) != -1 && value[v].length == dataFilter[f].trim().length) {
												found++;
												if (found >= cntNeed) return true;
											}
										}
									}
								} else {
									for(var f = 0; f < dataFilter.length; f++) {
										if (dataFilter[f].trim().indexOf(value) != -1 && value.length == dataFilter[f].trim().length) {
											return true;
										}
									}
								}
							}
						}
					}
					return false;
				});
			}
		});
		tableWrapper.off('change', '.wtbpFiltersWrapper select').on('change', '.wtbpFiltersWrapper select', function(){
			var $this = $(this);
			if ($this.attr('data-type') == 'categories') {
				var wrapper = $this.closest('.wtbpFiltersWrapper'),
					relation = wrapper.find('input[name="wtbpFilterRelations"]');
				if (relation.length) {
					try {
						var relations = JSON.parse(relation.val());
					} catch(e)  {
						var relations = [];
					}
					var catIds = [];
					$this.find('option:selected').each(function() {
						var catId = $(this).attr('data-id');
						if (typeof catId != 'undefined') catIds.push(catId);
					});
					wrapper.find('select:not([data-type="categories"])').each(function(){
						var filter = $(this),
							key = filter.attr('data-tax-key'),
							options = filter.find('option').removeClass('wtbpHidden'),
							rels = [], 
							found = false;
						$.each(catIds, function(i, catId) {
							if (catId in relations && key in relations[catId]) {
								Array.prototype.push.apply(rels, relations[catId][key]);
								found = true;
							}
						});
						if(found) {
							options.each(function() {
								var option = $(this);
								if (option.val() != '' && rels.indexOf(parseInt(option.attr('data-id'))) == -1) {
									option.addClass('wtbpHidden');
								}
							});
						}
						if (filter.find('option.wtbpHidden:selected').length) {
							filter.val('');
						}
						if(filter.attr('multiple') == 'multiple') filter.multipleSelect('refresh');
					});
				}
			}

			if (settings.search_by_letter) {
				app.updateAlphabet(table);
			}

			if(table.isSSP) table.ajax.reload();
			else table.draw();
		});

		tableWrapper.off('click', '.wtbpResetFilter').on('click', '.wtbpResetFilter', function(e){
			e.preventDefault();
			table.search('').draw();
			filtersInput.each(function () {
				var select = $(this);
				if(select.attr('multiple') == 'multiple') select.multipleSelect('uncheckAll');
				else select.val(select.find('option:first').val());
			});
			if(table.isSSP) table.ajax.reload();
			else table.draw();
		});

		if(this.checkSettings(settings, 'hide_before_filtering', false) == '1') {
			var body = jQuery(table.table().body()).addClass('wtbpHidden');
			table.on('draw.dt', function(e) {
				var isFiltered = false;
				if(table.search() != '') isFiltered = true;
				else {
					var columnFilter = table.columns().search();
					if(columnFilter && columnFilter.length) {
						for(var i = 0; i < columnFilter.length; i++) {
  							if(columnFilter[i] != '') {
  								isFiltered = true;
  								break;
  							}
						}
					}
				}
				if(!isFiltered) {
					filtersInput.each(function () {
						if($(this).val() && $(this).val() != '') isFiltered = true;
					});
				}
				if(isFiltered) body.removeClass('wtbpHidden');
				else body.addClass('wtbpHidden');
			});
		}

		// inner table category filter
		tableWrapper.off('click', 'a[data-filter-in-table=1]').on('click', 'a[data-filter-in-table=1]', function(e) {
			e.preventDefault();
			var categoryId = jQuery(this).data('cat-id'),
				categoryName = jQuery(this).text(),
				categoryFilter = jQuery(this).closest('.wtbpTableWrapper').find('select[data-tax-key=product_cat]'),
				input = categoryFilter.closest('.wtbpFiltersWrapper').find('.ms-drop input[value='+categoryId+']');
			if ( input.length > 0 ) {
				input.click();
			} else {
				if (settings.filter_category_type === 'dropdown') {
					var option = categoryFilter.closest('.wtbpFiltersWrapper').find('option.wtbpInnerFilter'),
						select = categoryFilter.closest('.wtbpFiltersWrapper').find('select');

					option.attr('value', categoryId);
					option.removeClass('wtbpVisibilityHidden');
					option.attr('selected', 'selected')
					option.text(categoryName);
					option.attr('data-id', categoryId);

					select.val(categoryId);
					select.change();
				} else {
					var li = categoryFilter.closest('.wtbpFiltersWrapper').find('.ms-drop ul li.wtbpInnerFilter');
					categoryFilter.find('.ms-search input').val(categoryId);
					categoryFilter.closest('.wtbpFiltersWrapper').find('select option.wtbpInnerFilter').attr('value', categoryId).text(categoryName).attr('data-id', categoryId);
					li.toggleClass('wtbpVisibilityHidden');
					li.find('input').attr('value', categoryId);
					li.find('span').text(categoryName);
					input = categoryFilter.closest('.wtbpFiltersWrapper').find('.ms-drop input[value='+categoryId+']');
					input.click();
				}
			}
		});
	});

	app.setTableEvents = (function(tableWrapper, table) {

		var __this = this;
		var priceHide = false;

		if ( table.hide_check_multy ) {
			table.column(table.hide_table_column).visible(true);
		}

		table.columns(':contains(colAttrHide)').visible(false);

		jQuery('body').find('.product_mpc').each(function(){
			var title = jQuery('.product_mpc').closest('form').find('.single_add_to_cart_button').html();
			jQuery(this).html(title);
		});

		tableWrapper.find('td.thumbnail').each(function() {
			var verticalAlign = jQuery(this).css('vertical-align');
			var secondThumbWrapper = jQuery(this).find('.wtbpTableThumbnailWrapper');

			if ( verticalAlign && secondThumbWrapper ) {
				secondThumbWrapper.find('a').each(function() {
					jQuery(this).css('vertical-align', verticalAlign);
				});
			}
		});

		// manually hide columns of table of them empty visually but has some plugin inner data
		// that do not allow hide them automatically with data table optimality
		function hideAddToCartInner() {
			if(table.isSSP) return;
			var addToCartButton = jQuery('body').find("#"+tableWrapper[0].id).find('.add_to_cart_button, .single_add_to_cart_button, .product_type_external');
			var priceSpan = jQuery('body').find("#"+tableWrapper[0].id).find('.woocommerce-Price-amount');
			var addToCartButtonGrouped = tableWrapper.find('.product_type_grouped');

			if (addToCartButtonGrouped.length > 0) {
				addToCartButtonGrouped.closest('.wtbpAddToCartWrapper').find('.quantity').hide();
			}
			if (priceSpan.length < 1) {
				var indexColPrice = tableWrapper.find('[data-key=price]').index();
				if (indexColPrice != -1 && table.column(indexColPrice).visible()) {
						var dataKey = tableWrapper.find('th').eq(indexColPrice).attr('data-key');
						if (dataKey === 'price') {
							priceHide = true;
							table.column(indexColPrice).visible(false);
						}
				}
			}
			if (addToCartButton.length < 1) {
				var indexColAddToCart = tableWrapper.find('[data-key=add_to_cart]').index();
				if (indexColAddToCart != -1 && table.column(indexColAddToCart).visible()) {
						var dataKey = tableWrapper.find('th').eq(indexColAddToCart).attr('data-key');
						if (dataKey === 'add_to_cart') {
							if (priceHide) {
								indexColAddToCart = indexColAddToCart + 1;
							}
							table.column(indexColAddToCart).visible(false);
						}
				}

			}
		}

		tableWrapper.off('change', '.wtbpAddMultyAll').on('change', '.wtbpAddMultyAll', function(e){
			e.preventDefault();
			__this.checkMultyAll($(this), tableWrapper);
		});

		tableWrapper.off('click', '.check_multy').on('click', '.check_multy', function(e){
			var tr = jQuery(this).closest('tr').index();
			var row = table.row('tr:eq('+tr+')');
			if (row.child()) {
				var addToCartText = row.child().find('.single_add_to_cart_button').html();
				row.child().find('.product_mpc').html(addToCartText);
			}
		});

		tableWrapper.off('page.dt').on( 'page.dt', function() {
			hideAddToCartInner();
		});
		tableWrapper.off('responsive-resize.dt').on('responsive-resize.dt', function() {
			hideAddToCartInner();
		});
		hideAddToCartInner();

		tableWrapper.on('keyup click', '.wtbpOpenModal', function() {
			var modal = document.getElementById("wtbpModal");
			var close = document.getElementsByClassName("wtbpCloseModal")[0];

			close.onclick = function() {
				modal.style.display = "none";
			}
			window.onclick = function(event) {
				if (event.target == modal) {
					modal.style.display = "none";
				}
			}

			var popupContent = $(this).children('.wtbpModalContentFull').html();
			$('#wtbpModal .wtbpModalContentPlaceholder').html(popupContent);

			modal.style.display = "block";
		});
	});

	app.setLazyLoadDrawCallback = (function(tableWrapper, table, settings, objAttr) {
		if(this.checkSettings(settings, 'pagination', false)) return;
		if(!this.checkSettings(settings, 'lazy_load', false)) return;

		var adminPage = tableWrapper.attr('id') == 'wtbp-table-preview' && window.woobewoo && window.woobewoo.WtbpAdminPage ? window.woobewoo.WtbpAdminPage : false,
			form = $('#wtbpTablePressEditForm');

		if (objAttr.lazyLoad) {
			var length = parseInt(this.checkSettings(settings, 'lazy_load_length', 50)),
				page = table.attr('data-lazy-page') ? parseInt(table.attr('data-lazy-page')) : 2,
				ajaxData = {
					id: objAttr.tableId,
					page: page,
					length: length,
					start: (page - 1) * length,
					isLazyLoad: true,
				};
			if (adminPage) {
				ajaxData['settings'] = adminPage.getSettingsFormData();
				ajaxData['productids'] = form.find('input[name="settings[productids]"]').val();
				ajaxData['orders'] = form.find('input[name="settings[order]"]').val();
			}
			$.post(objAttr.lazyLoad.url, ajaxData, function(response) {
				response = JSON.parse(response);
				if (response.html.length) {
					if ($(response.html).find('tr').length) {
						var tableInstance = table.DataTable();
						table.attr('data-lazy-page', page + 1);
						tableInstance.rows.add($(response.html).find('tr')).draw();
						table.trigger('lazy-load.dt');
					}
				}
			});
		}
	});

	app.getBunchAddCart = (function(selectedProduct, settings) {
		var isBunchAddCart = app.checkSettings(settings, 'bunch_add_cart', false),
			minMultipleAddCart = app.checkSettings(settings, 'bunch_add_cart_min', false),
			maxMultipleAddCart = app.checkSettings(settings, 'bunch_add_cart_max', false),
			bunchAddCartResponse = '';

		if (isBunchAddCart) {
			if (minMultipleAddCart === false && maxMultipleAddCart === false) {
				bunchAddCartResponse = true;
			} else {
				var minMultipleAddCartMessage = '';
				if (minMultipleAddCart !== false && parseInt(minMultipleAddCart) > selectedProduct.length) {
					minMultipleAddCartMessage = '<span>Min quntity products in the bunch - ' + parseInt(minMultipleAddCart) + '</span>'
				}

				var maxMultipleAddCartMessage = '';
				if (maxMultipleAddCart !== false && parseInt(maxMultipleAddCart) < selectedProduct.length) {
					maxMultipleAddCartMessage = '<span>Max quntity products in the bunch - ' + parseInt(maxMultipleAddCart) + '</span>'
				}

				bunchAddCartResponse = '<span>Please note. </span>' + minMultipleAddCartMessage + maxMultipleAddCartMessage;

				if (minMultipleAddCartMessage === '' && maxMultipleAddCartMessage === '') {
					bunchAddCartResponse = true;
				}
			}
		} else {
			bunchAddCartResponse = true;
		}

		return bunchAddCartResponse;
	});
	
	app.initVariationsPopup = (function(tableWrapper, settings) {
		var variationsPopupBtnBlock = tableWrapper.find('.wtbpHasPopupVariations'),
			self = this;
		
		tableWrapper.off('click', '.wtbpHasPopupVariations .button.product_type_variable').on('click', '.wtbpHasPopupVariations .button.product_type_variable', function(e){
			e.preventDefault();
			
			if ($(this).closest('.wtbpModalContentForVariations').length) {
				return false;
			}
			
			var btn = $(this),
				td = btn.closest('td'),
				wrapper = td.find('.wtbpVarAttributes'),
				variations = [],
				addToCartBtn = td.find('.wtbpAddToCartWrapper'),
				attrVariations = JSON.parse(wrapper.attr('data-variations')),
				wtbpVariablesModal = tableWrapper.find('#wtbpVariablesModal'),
				wtbpVariablesModalCloseBtn = wtbpVariablesModal.find(".wtbpCloseModal");

			for (var key in attrVariations) {
				var	image = td.find('.wtbpVarImageForPopup[data-variation_id="' + key + '"]'),
					defaultVarId = wrapper.attr('data-default-id') || 0;
				if (image.length === 0) {
					image = td.find('.wtbpVarImageForPopup[data-variation_id="' + addToCartBtn.data('product_id') + '"]');
				}
				variations.push({
					id: key,
					price: td.find('.wtbpVarPrice[data-variation_id="' + key + '"]').html(),
					description: td.find('.wtbpVarDescription[data-variation_id="' + key + '"]').html(),
					image: image,
					attributes: attrVariations[key],
					def: defaultVarId == key
				});
			}
			
			for (var key in attrVariations) {
				variations.push({
					id: key,
					price: td.find('.wtbpVarPrice[data-variation_id="' + key + '"]').html(),
					description: td.find('.wtbpVarDescription[data-variation_id="' + key + '"]').html(),
					image: image,
					attributes: attrVariations[key]
				});
			}
			
			wtbpVariablesModalCloseBtn.on('click', function(){
				wtbpVariablesModal.hide();
			});
			
			$(document.body).on('click', function(event) {
				if (event.target == wtbpVariablesModal.get(0)) {
					wtbpVariablesModal.hide();
				}
			});
			$(document.body).on('wtbpCloseModal', function() {
				wtbpVariablesModal.hide();
			});
			
			wtbpVariablesModal.off( 'change', '.wtbpVarAttribute').on( 'change', '.wtbpVarAttribute', function() {
				var selects = wtbpVariablesModal.find('select.wtbpVarAttribute'),
					variation = null,
					tmpVariations = [];
				
				
				selects.each(function(){
					var currAttr = jQuery(this).attr('data-attribute'),
						currVal = jQuery(this).val();
					
					for (var key in variations) {
						var currVarAttributes = variations[key].attributes;
						
						if (currVarAttributes[currAttr] == currVal) {
							if (!(variations[key].id in tmpVariations)) {
								tmpVariations.push(variations[key].id);
							}
						}
					}
				});
				
				if (tmpVariations.length) {
					var counts = {};
					for (var i = 0; i < tmpVariations.length; i++) {
						counts[tmpVariations[i]] = 1 + (counts[tmpVariations[i]] || 0);
					}
					variation = Object.keys(counts).sort(function(a,b){return counts[b]-counts[a]})[0];
					
					if (variation) {
						self.setPopupVariation(wtbpVariablesModal, variations, variation);
					}
				}

				if (!variation) {
					wtbpVariablesModal.find('.wtbpModalVariationDescription').html(td.find('.wtbpProductDescription').html());
				}
			});
			
			var nameHtml = td.find('.wtbpProductName').html();
			if (td.find('.wtbpProductRating').length) {
				nameHtml += td.find('.wtbpProductRating').html();
			}
			wtbpVariablesModal.find('.wtbpModalVariationImages').html('');
			wtbpVariablesModal.find('.wtbpModalVariationName').html(nameHtml);
			wtbpVariablesModal.find('.wtbpModalVariationDescription').html(td.find('.wtbpProductDescription').html());
			wtbpVariablesModal.find('.wtbpModalVariationAttributes').html(wrapper.get(0).outerHTML);
			addToCartBtn.find('.add_to_cart_button').attr('data-product_id', addToCartBtn.data('product_id'));
			wtbpVariablesModal.find('.wtbpModalVariationBtns').html(addToCartBtn.get(0).outerHTML);
			
			if (variations.length) {
				self.setPopupVariation(wtbpVariablesModal, variations);
			}
			
			wtbpVariablesModal.show();
			
			return false;
		});
	});
	
	app.setPopupVariation = (function(wtbpVariablesModal, variations, variation_id) {
		var variation = variations[0],
			addToCartText = wtbpVariablesModal.find('.wtbpModalVariationBtns').data('add-to-cart-text');
		if (typeof variation_id !== 'undefined') {
			for (var key in variations) {
				if (variations[key].id == variation_id) {
					variation = variations[key];
				}
			}
		} else {
			for (var key in variations) {
				if (variations[key].def) {
					variation = variations[key];
					break;
				}
			}
		}
		
		if (variation.image.length) {
			wtbpVariablesModal.find('.wtbpModalVariationImages').html(variation.image.get(0).outerHTML);
		}

		if (jQuery('.wtbpVarDescriptions').data('show-var-description') == 1 && variation.description.trim().length) {
			wtbpVariablesModal.find('.wtbpModalVariationDescription').html(variation.description);
		} else {
			wtbpVariablesModal.find('.wtbpModalVariationDescription').html(jQuery('.wtbpProductDescription').html());
		}

		var btn = wtbpVariablesModal.find('.add_to_cart_button').eq(0);
		btn.attr('data-variation_id', variation.id).html(addToCartText + variation.price);
		wtbpVariablesModal.find('.wtbpVarAttributes,.wtbpVarImageForPopup').removeClass('wtbpHidden');
		wtbpVariablesModal.find('.wtbpAddToCartWrapper').removeClass('wtbpDisabledLink');
		for (var attr in variation.attributes) {
			btn.attr('data-attribute_' + attr, variation.attributes[attr]);
		}
		
		if (typeof variation_id === 'undefined') {
			for (var attr in variation.attributes) {
				wtbpVariablesModal.find('.wtbpVarAttribute[data-attribute="' + attr + '"]').val(variation.attributes[attr]);
			}
		}
	});

	app.getTitleIndex = (function () {
		var i = 0,
			index = false;
		jQuery('.dataTables_scrollBody tr:first th').each(function () {
			if (jQuery(this).data('key') === 'product_title') {
				index = i;
			}
			i++;
		});
		return index;
	});

	app.updateAlphabet = (function (table) {

		var index = this.getTitleIndex(),
			columnData = [];

		if (index) {
			_alphabetSearch = '';
			jQuery.fn.dataTable.ext.search.push(function (settings, searchData) {
				columnData.push(searchData[index]);
				return true;
			});
			table.draw();
			this.drawAlphabet(table, columnData);
		}
	});

	app.drawAlphabet = (function (table, columnData) {
		var letter, letters = [];

		jQuery.each(columnData, function () {
			letter = this.match(/\S/);
			letter = letter[0].toUpperCase();
			if (jQuery.inArray(letter, letters) === -1) {
				letters.push(letter);
			}
		});

		letters.sort();

		jQuery('div.alphabet').remove();
		var alphabet = jQuery('<div class="alphabet"/>').append('Search: ');
		jQuery('<span class="clear active"/>')
			.data('letter', '')
			.html('None')
			.appendTo(alphabet);
		jQuery.each(letters, function () {
			jQuery('<span/>')
				.data('letter', this)
				.html(this)
				.appendTo(alphabet);
		});
		alphabet.insertBefore(table.table().container());

		alphabet.on('click', 'span', function () {
			alphabet.find('.active').removeClass('active');
			$(this).addClass('active');
			_alphabetSearch = jQuery(this).data('letter');
			table.draw();
		});
	});

	app.initAlphabet = (function (table) {

		var index = this.getTitleIndex();

		if (index) {
			var columnData = table.column(index).data().map(function (data) {
				return jQuery(data).html();
			});
			this.drawAlphabet(table, columnData);

			jQuery.fn.dataTable.ext.search.push(function (settings, searchData) {
				if (!_alphabetSearch || searchData[index].charAt(0).toUpperCase() === _alphabetSearch) {
					return true;
				}

				return false;
			});
		}
	});

}(window.jQuery, window.woobewoo.WooTablepress));
