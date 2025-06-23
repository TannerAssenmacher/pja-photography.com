<?php
/**
 * Class contain intagration with Advanced Custom Field plugin.
 *
 * @link https://wordpress.org/plugins/advanced-custom-fields/
 */

/**
 * Class contain intagration with Advanced Custom Field plugin.
 * You can use it in any part of your code with construction
 * FrameWtbp::_()->getModule('wootablespro')->getModel('acf');
 */
class AcfModelWtbp extends ModelWtbp {
	/**
	 * ACF plugin prefix
	 *
	 * @var array
	 */
	public $acfPrefix = 'acf';

	/**
	 * ACF field admin settings
	 *
	 * @var array
	 */
	public $fieldSettings;

	/**
	 * ACF field type
	 *
	 * @var array
	 */
	public $fieldType;

	/**
	 * ACF field data
	 *
	 * @var mix
	 */
	public $field;

	/**
	 * Single ACF data in multiple return
	 *
	 * @var mix
	 */
	public $subField;

	/**
	 * Column settings
	 *
	 * @var array
	 */
	public $column;

	/**
	 * Post type product id
	 *
	 * @var int
	 */
	public $productId;

	/**
	 * If ACF belong to List
	 * ACF field types that has some additional html to shot in table
	 *
	 * @var bool
	 */
	public $isTypeTableHtml;

	/**
	 * If ACF belong to List
	 * List ACF field that has return type optionlaity.
	 *
	 * @var bool
	 */
	public $isTypeOptionalReturn;

	/**
	 * If ACF belong to List
	 * List ACF field that has multiple value return optionality.
	 *
	 * @var bool
	 */
	public $isTypeMultipleReturn;

	/**
	 * If ACF belong to List
	 * List ACF field that has return type optionlaity.
	 *
	 * @var bool
	 */
	public $isTypeMultipleReturnWithoutOption;

	/**
	 * If ACF belong to List
	 * List ACF field that has admin view optionality.
	 *
	 * @var bool
	 */
	public $isTypeWithAdminViewOptionality;

	/**
	 * If ACF belong to List
	 * List types that can contain another fields
	 *
	 * @var bool
	 */
	public $isTypeGrouped;

	/**
	 * If ACF belong to List
	 * List ACF field that has multiple value return optionality.
	 * And multiple return option activated for field.
	 *
	 * @var bool
	 */
	public $isMultipleReturnActive;

	/**
	 * Static cache property for a get_field_object function
	 *
	 * @var array
	 */
	public static $fieldSettingsCache;

	/**
	 * List ACF fields types
	 * That plugin support.
	 *
	 * @var array
	 */
	public $enabledFieldTypes = array(
		'text',
		'number',
		'textarea',
		'wysiwyg',
		'range',
		'email',
		'url',
		'radio',
		'select',
		'button_group',
		'checkbox',
		'true_false',
		'oembed',
		'link',
		'file',
		'image',
		'post_object',
		'page_link',
		'taxonomy',
		'user',
		'google_map',
		'date_picker',
		'date_time_picker',
		'time_picker',
		'color_picker',
		'message',
		'relationship',
		'accordion',
		'tab',
		'group',
	);

	/**
	 * * List ACF fields types
	 * That we optionaly can enable display in table as admin view.
	 *
	 * @var array
	 */
	public $fieldTypesWithAdminViewOptionality = array(
		'text',
		'checkbox',
		'radio',
		'select',
		'button_group',
	);

	/**
	 * List ACF fields types.
	 * That has some additional html to shot in table.
	 *
	 * @var array
	 */
	public $fieldTypeTableHtmlList = array(
		'text',
		'link',
		'file',
		'image',
		'post_object',
		'color_picker',
		'page_link',
		'taxonomy',
		'google_map',
		'relationship',
		'oembed',
		'wysiwyg'
	);

	/**
	 * List ACF fields types
	 * That has multiple value return optionality.
	 *
	 * @var array
	 */
	public $fieldTypeMultipleReturnList = array(
		'select',
		'user',
		'checkbox',
		'post_object',
		'page_link',
		'relationship',
		'taxonomy',
	);

	/**
	 * List ACF fields types
	 * There are a cases when we do not have multiple option but have mutiple return
	 *
	 * @var array
	 */
	public $fieldTypeMultipleReturnWithoutOptionList = array(
		'checkbox',
		'relationship',
		'taxonomy',
	);

	/**
	 * * List ACF fields types 
	 * That has return type optionlaity.
	 *
	 * @var array
	 */
	public $fieldTypeOptionalReturnList = array(
		'button_group',
		'user',
		'checkbox',
		'link',
		'file',
		'image',
		'post_object',
		'taxonomy',
	);

	/**
	 * List ACF fields types
	 * That we can't recieve with standard get_filed function
	 * Cos they do not have post_meta in database
	 * Or returned value do not contain necessary frontend content for a final user.
	 *
	 * @var array
	 */
	public $fieldTypeWithoughtPostMeta = array(
		'message',
		'radio',
		'select',
		'button_group',
		'checkbox',
	);

	/**
	 * List ACF fields types
	 * Removed from filtering.
	 *
	 * @var array
	 */
	public $fieldTypeRemovedFromFiltersList = array(
		'google_map',
		'oembed',
		'message',
		'wysiwyg',
		'accordion',
		'tab',
		'group',
	);

	/**
	 * List ACF fields types
	 * That can contain another fields in the list below.
	 *
	 * @var array
	 */
	public $fieldTypeGroupedInRow = array(
		'accordion',
		'tab',
	);

	/**
	 * List ACF fields types
	 * That can contain another fields.
	 *
	 * @var array
	 */
	public $fieldTypeGrouped = array(
		'group',
	);

	/**
	 * List ACF fields types
	 * Grouped tapes that do not return any value.
	 *
	 * @var array
	 */
	public $fieldTypeGroupedWithoughtValue = array(
		'message',
	);

	/**
	 * Class constructor.
	 *
	 * @param array
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Fire up module hooks and actions
	 */
	public function init() {
		DispatcherWtbp::addFilter('getExceptionFilterTaxonomies', array($this, 'getExceptionFilterTaxonomies'), 10, 1);
		DispatcherWtbp::addFilter('getFilterCustomTaxonomies', array($this, 'getFilterCustomTaxonomies'), 10, 2);
		DispatcherWtbp::addFilter('getFilterCustomTaxonomiesSelected', array($this, 'getFilterCustomTaxonomiesSelected'), 10, 2);
		DispatcherWtbp::addFilter('addAdditionalDataColumnListAdminSelect', array($this, 'addAdditionalDataColumnListAdminSelect'), 10, 2);
		DispatcherWtbp::addFilter('addAdditionalDataAdminOrderCoulumnList', array($this, 'addAdditionalDataAdminOrderCoulumnList'), 10, 2);
		DispatcherWtbp::addFilter('addCheckColumnInTable', array($this, 'addCheckColumnInTable'), 10, 4);
	}

	/**
	 * Populate properies before init any method for a obtainig field data.
	 *
	 * @param string $fieldName acf field name
	 * @param int $id product id
	 * @param array $column
	 */
	public function initGetAcf( $fieldName, $productId, $column ) {
		if (empty($column['plugin_data'][$this->acfPrefix])) {
			$column = $this->addPluginSettingsToColumn($column);
		}

		$this->column = $column;

		if (!empty($this->column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key'])) {
			$fieldName  = $this->column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key'];
		}

		if ( empty(self::$fieldSettingsCache[$productId][$fieldName]) ) {
			$this->fieldSettings = get_field_object($fieldName, $productId, false);
			self::$fieldSettingsCache[$productId][$fieldName] = $this->fieldSettings;
		} else {
			$this->fieldSettings = self::$fieldSettingsCache[$productId][$fieldName];
		}

		$this->fieldType = $this->fieldSettings['type'];
		$this->productId = $productId;
		$this->subField = '';

		if (in_array($this->fieldType, $this->fieldTypeWithoughtPostMeta)) {
			$this->field = $this->getFieldValueWithoghtMeta();
		} else {
			$this->field = $this->fieldSettings['value'];
		}

		if ($this->fieldType) {
			$this->setFieldOptionality();
		}

		$this->resetPropertyDueToExeptionalCases();

		return $this;
	}

	/**
	 * Find which group optionality by settings and display in table field belong.
	 */
	public function setFieldOptionality() {
		$this->isTypeTableHtml = in_array($this->fieldType, $this->fieldTypeTableHtmlList) ? true : false;
		$this->isTypeMultipleReturn = in_array($this->fieldType, $this->fieldTypeMultipleReturnList) ? true : false;
		$this->isTypeMultipleReturnWithoutOption = in_array($this->fieldType, $this->fieldTypeMultipleReturnWithoutOptionList) ? true : false;
		$this->isTypeWithAdminViewOptionality = in_array($this->fieldType, $this->fieldTypesWithAdminViewOptionality);
		$this->isTypeGrouped = in_array($this->fieldType, $this->fieldTypeGrouped) || in_array($this->fieldType, $this->fieldTypeGroupedInRow) ? true : false;

		if (!empty($this->fieldSettings['multiple']) && $this->fieldSettings['multiple']) {
			$isFieldMultySettingActive = true;
		} else {
			$isFieldMultySettingActive = false;
		}

		if ($this->isTypeMultipleReturn) {
			$this->isMultipleReturnActive = true;
			if (!$isFieldMultySettingActive) {
				$this->isMultipleReturnActive = false;
			}
		} else {
			$this->isMultipleReturnActive = false;
		}

		if ($this->isTypeMultipleReturnWithoutOption) {
			$this->isMultipleReturnActive = true;
		}
	}

	/**
	 * Add plugin settings to column
	 *
	 * @param array $column
	 *
	 * @return array
	 */
	public function addPluginSettingsToColumn( $column ) {
		$columnValue =
			FrameWtbp::_()
				->getModule('wootablepress')
				->getModel('columns')
				->searchColumnInFullColumnListBySlug($column['slug']);
		if (!empty($columnValue['plugin_data'][$this->acfPrefix])) {
			$column['plugin_data'][$this->acfPrefix] = $columnValue['plugin_data'][$this->acfPrefix];
		}

		return $column;
	}

	/**
	 * Get table format view for individual fields
	 *
	 * @return array
	 */
	public function getAcfTableView() {
		$fieldValue = $this->getAcfValue();
		$filterValue = $this->getAcfValue(true);

		return array(
			$this->formatFilterValue($fieldValue),
			$this->formatFilterValue($filterValue, true),
			$this->formatFilterValue($filterValue, true),
		);
	}

	/**
	 * Get filter format view for individual field
	 *
	 * @return array
	 */
	public function getAcfFilterView() {
		if (!empty($this->column['acf_text_input']) && $this->isTypeWithAdminViewOptionality) {
			$filterValue = array();
		} else {
			return $this->getAcfValue(true);
		}
	}

	/**
	 * Display filed as admin view with optionality add to cart input options
	 *
	 * @return string
	 */
	public function getAcfTableAddToCartView() {
		$html = '';

		$isBelongFieldGroupLogic = $this->checkFieldGroupLogicProductBelong();

		if ($isBelongFieldGroupLogic) {
			ob_start();
			acf_render_field_wrap($this->fieldSettings);
			$html = ob_get_clean();

			$html = preg_replace('#<div class=\"acf-label\">(.*?)</div>#is', '', $html);

			$html = preg_replace('#\s(id)="[^"]+"#', '', $html);

			$name = uniqid('acf');
			$html = preg_replace('#\s(name)="[^"]+"#', ' name="' . $name . '"', $html);

			$columnName = !empty($this->column['show_display_name']) ? $this->column['display_name'] : $this->column['original_name'];

			$html =
				'<div class="wtbpAddDataToCartMeta ' .
					'" data-acf-type="' . $this->fieldType .
					'" data-column-title="' . $columnName .
					'" data-field-key="' . $this->fieldSettings['key'] .
				'">' .
					$html .
				'</div>';
		}


		return $html;
	}

	/**
	 * Get value of field.
	 *
	 * @param bool $isFilter is filter value return
	 *
	 * @return array
	 */
	public function getAcfValue( $isFilter = false ) {
		$fieldValueList = array();

		if ($this->field) {

			if ($this->isMultipleReturnActive) {
				foreach ($this->field as $subField) {
					$this->subField = $subField;

					if ($isFilter) {
						$fieldValueList[] = $this->getFieldFilterValue();
					} else {
						$fieldValueList[] = $this->getFieldTableValue();
					}
				}
			} else {
				if ($isFilter) {
					$fieldValueList[] = $this->getFieldFilterValue();
				} else {
					$fieldValueList[] = $this->getFieldTableValue();
				}
			}
		}

		return $fieldValueList;
	}

	/**
	 * Format filter value list to appropriate format
	 *
	 * @param array $fieldValueList
	 * @param bool $isFilter
	 *
	 * @return string
	 */
	public function formatFilterValue( $fieldValueList, $isFilter = false ) {

		$fieldValueList = array_filter($fieldValueList);

		if ($isFilter || $this->isMultipleReturnActive && !$this->isTypeTableHtml ) {
			$html = implode(', ', $fieldValueList);
		} else {
			$html = implode($fieldValueList);
		}

		return $html;
	}

	/**
	 * Provide some ACF filed types in table with additional html
	 *
	 * @return string
	 */
	public function getFieldTableHtml() {
		$field  = $this->checkSubfield();

		$html = '';
		switch ($this->fieldType) {
			case 'text':
				$html =
					FrameWtbp::_()
						->getModule('wootablepress')
						->getView()
						->getTableSetting($this->column, 'acf_text_shortcode', false) ? do_shortcode($field) : $field;
				break;
			case 'color_picker':
				$html = '<div class="wtbp-color-picker"' .
							'data-color-picker="' . $this->field .
						'"></div>';
				break;
			case 'page_link':
				$html = '<a href="' . $field . '">' .
							$field .
						'</a><br>';
				break;
			case 'image':
				$fileName = basename(get_attached_file($field));
				$fileName = !empty($fileName) ? $fileName : $this->fieldSettings['label'];

				if ($field) {
					$imageSize = isset($this->fieldSettings['preview_size']) ? $this->fieldSettings['preview_size'] : 'full';
					$html = wp_get_attachment_image($field, $imageSize);
				}
				$html = '<div class="wtbpAcfImage">' . $html . '</div>';
				$html .= '<div class="wtbpHidden">' . $fileName . '</div>';
				break;
			case 'file':
				$fileName = basename(get_attached_file($field));
				$fileName = !empty($fileName) ? $fileName : $this->fieldSettings['label'];

				$showAs =
					FrameWtbp::_()
						->getModule('wootablepress')
						->getView()
						->getTableSetting($this->column, 'acf_link_show_as', 'link');
				$title = get_the_title( $field );
				$title = $title ? $title : $this->fieldSettings['label'];
				$link = wp_get_attachment_url($field);

				$html = '<a' . ( 'button' == $showAs ? ' class="button wtbpAcfLinkButton"' : '' ) .
					' href="' . esc_url($link) .
					'" target="_blank">';

				if ('icon' == $showAs) {
					$html .= '<i class="fa fa-fw fa-file" title="' . esc_attr($title) . '"></i>';
				} else if ('image' == $showAs) {
					$html .=
						'<img class="wtbpAcfLinkImage" src="' . 
							esc_url(
								FrameWtbp::_()
									->getModule('wootablepress')
									->getView()
									->getTableSetting($this->column, 'acf_image_path', WTBP_IMG_PATH . 'default.png')
							) .
							'" title="' . esc_attr($title) .
						'">';
				} else {
					$html .= esc_html($title);
				}
				$html .= '</a>';
				$html .= '<div class="wtbpHidden">' . $fileName . '</div>';
				break;
			case 'link':
				$showAs =
					FrameWtbp::_()
						->getModule('wootablepress')
						->getView()
						->getTableSetting($this->column, 'acf_link_show_as', 'link');
				$title = isset($field['title']) ? $field['title'] : $this->fieldSettings['label'];

				$html = '<a' . ( 'button' == $showAs ? ' class="button wtbpAcfLinkButton"' : '' ) .
					' href="' . esc_url( isset($field['url']) ? $field['url'] : $field ) .
					'" target="' . esc_attr(isset($field['target']) ? $field['target'] : '_blank') . '">';

				if ('icon' == $showAs) {
					$html .= '<i class="fa fa-fw fa-link" title="' . esc_attr($title) . '"></i>';
				} else if ('image' == $showAs) {
					$html .=
						'<img class="wtbpAcfLinkImage" src="' .
							esc_url(
								FrameWtbp::_()
								->getModule('wootablepress')
								->getView()->getTableSetting($this->column, 'acf_image_path', WTBP_IMG_PATH . 'default.png')
							) .
							'" title="' . esc_attr($title) .
						'">';
				} else {
					$html .= esc_html($title);
				}
				$html .= '</a>';
				break;
			case 'post_object':
				$html .= '<a href="' . get_post_permalink($field) . '">' .
							get_the_title($field) .
						'</a><br>';
				break;
			case 'taxonomy':
				$args = array(
					'taxonomy'            => $this->fieldSettings['taxonomy'],
					'include'             => $field,
					'echo'                => false,
					'title_li'            => '',
					'hide_title_if_empty' => true,
				);
				$html = wp_list_categories($args);
				break;
			case 'google_map':
				$zoom = $this->fieldSettings['zoom'] ? $this->fieldSettings['zoom'] : 14;
				$height = $this->fieldSettings['height'] ? $this->fieldSettings['height'] : 400;

				$id = md5(uniqid(rand(), true));

				$html = 
				'<div id="' . $id .
					'" class="wtbp-map' .
					'" data-google-map-lat="' . $field['lat'] .
					'" data-google-map-lng="' . $field['lng'] .
					'" data-google-map-lng="' . $field['lng'] .
					'" data-google-map-zoom="' . $zoom .
					'" data-google-map-height="' . $height .
				'"></div>';
				break;
			case 'relationship':
				$html .= '<a href="' . get_post_permalink($field) . '">' .
							get_the_title($field) .
						'</a><br>';
				break;
			case 'wysiwyg':
			case 'oembed':
				$html = apply_filters('the_content', $field);
				break;
		}

		return $html;
	}

	/**
	 * Return filter field value for a individual field or individual value in multiple field case
	 *
	 * @return string
	 */
	public function getFieldFilterValue() {
		$field  = $this->checkSubfield();

		$html = '';
		switch ($this->fieldType) {
			case 'user':
				$user = get_userdata($field);
				if (!empty($user->data->user_nicename) ) {
					$html = $user->data->user_nicename;
				}
				break;
			case 'true_false':
				$trueShotAs =
					FrameWtbp::_()
						->getModule('wootablepress')
						->getView()
						->getTableSetting($this->column, 'true_show_as', 'true');
				$html = ! empty($field) ? $trueShotAs : '';
				break;
			case 'link':
				$title = isset($field['title']) ? $field['title'] : $this->fieldSettings['label'];
				$html = esc_html($title);
				break;
			case 'file':
			case 'image':
				$fileName = basename(get_attached_file($field));
				$fileName = !empty($fileName) ? $fileName : $this->fieldSettings['label'];
				$html = esc_html($fileName);
				break;
			case 'post_object':
				$html = get_the_title($field);
				break;
			case 'taxonomy':
				$term  = get_term($field, $this->fieldSettings['taxonomy']);
				if (!empty($term->name)) {
					$html = $term->name;
				}
				break;
			case 'relationship':
				$html = get_the_title($field);
				break;
			default:
				if (is_scalar($field)) {
					$html = $field;
				}
				break;
		}

		return $html;
	}

 
	/**
	 * Get filter without any additional optionality.
	 *
	 * @return string
	 */
	public function getFieldTableSimpleValue() {
		$field  = $this->checkSubfield();

		$html = '';
		switch ($this->fieldType) {
			case 'user':
				$user = get_userdata($field);
				if (!empty($user->data->user_nicename) ) {
					$html = $user->data->user_nicename;
				}
				break;
			case 'true_false':
				$trueShotAs =
					FrameWtbp::_()
						->getModule('wootablepress')
						->getView()
						->getTableSetting($this->column, 'true_show_as', 'true');
				$html = ! empty($field) ? $trueShotAs : '';
				break;
			default:
				if (is_scalar($field)) {
					$html = $field;
				}
				break;
		}

		return $html;
	}

	/**
	 * There is fileds types that do not keep they value in wp_postmeta table
	 * For them we must receive value in special case
	 *
	 * @return mix
	 */
	public function getFieldValueWithoghtMeta() {
		$fieldValue = false;
		switch ($this->fieldType) {
			case 'message':
				$fieldValue = $this->fieldSettings['message'];
				break;
			case 'checkbox':
			case 'select':
				$chosen  = $this->fieldSettings['value'];
				$choices = $this->fieldSettings['choices'];

				if ( is_array( $choices ) ) {
					if ( is_array( $chosen ) ) {
						foreach ( $chosen as $chosenValue ) {
							if ( ! empty( $choices[ $chosenValue ] ) ) {
								$fieldValue[] = $choices[ $chosenValue ];
							}
						}
					} else {
						if ( ! empty( $choices[ $chosen ] ) ) {
							$fieldValue[] = $choices[ $chosen ];
						}
					}
				}
				break;
			case 'button_group':
			case 'radio':
				$chosen = $this->fieldSettings['value'];
				$choices = $this->fieldSettings['choices'];

				if (!empty($choices[$chosen])) {
					$fieldValue = $choices[$chosen];
				}
				break;
		}

		return $fieldValue;
	}

	/**
	 * Return table field value for a individual field or individual value in multiple field case
	 *
	 * @return string
	 */
	public function getFieldTableValue() {
		if ($this->isTypeTableHtml) {
			$fieldValue = $this->getFieldTableHtml();
		} else {
			$fieldValue = $this->getFieldTableSimpleValue();
		}

		return $fieldValue;
	}

	/**
	 * Reset class properties due to exceptional cases
	 */
	public function resetPropertyDueToExeptionalCases() {
		// Sometimes We can hasve exceptional cases when switch field setting in admin area
		// from multiple to single display. Sometimes we rescive unappropriate data types
		// In such cases we try to reset field data type manualy
		if ($this->isTypeMultipleReturn && !$this->isMultipleReturnActive) {
			if ( is_array($this->field) && !empty($this->field[0])) {
				$this->field = $this->field[0];
			}
		}

		if ('textarea' == $this->fieldType) {
			$this->field = str_replace(array("\r", "\n"), "\n", $this->field);
		}
	}

	/**
	 * Check if subfield is set
	 * We call subfield single value in multiple field case.
	 *
	 * @return string
	 */
	public function checkSubfield() {
		if ($this->subField) {
			$field = $this->subField;
		} else {
			$field = $this->field;
		}

		return $field;
	}

	/**
	 * Get all fields that belong to field with grouped optionality
	 *
	 * @return array
	 */
	public function getFieldGroupList() {
		$groupList = array();

		if (in_array($this->fieldType, $this->fieldTypeGrouped)) {
			$groupList = $this->getFieldGroup();
		} else {
			$groupList = $this->getFieldGroupInRowList();
		}

		return $groupList;
	}

	/**
	 * Get fields in group
	 *
	 * @return void
	 */
	public function getFieldGroup() {
		$groupList = array();

		if (is_array($this->fieldSettings['sub_fields'])) {
			foreach ($this->fieldSettings['sub_fields'] as $subField) {

				if (in_array($subField['type'], $this->fieldTypeGroupedWithoughtValue)) {
					switch ($subField['type']) {
						case 'message':
							$groupList[] = array(
								'settings' => $subField,
								'value'    => $subField['message'],
							);
							break;
					}
				}

				if (!empty($this->field[$subField['key']])) {
					$groupList[] = array(
						'settings' => $subField,
						'value'    => $this->field[$subField['key']],
					);
				}
			}
		}

		return $groupList;
	}

	/**
	 * Get Field type list grouped in Row
	 *
	 * @return array
	 */
	public function getFieldGroupInRowList() {
		$groupList = array();

		if ($this->fieldSettings['parent']) {
			$allFieldsGroupList = acf_get_fields($this->fieldSettings['parent']);

			if (is_array($allFieldsGroupList)) {
				$parentKey = array_search($this->fieldSettings['ID'], array_column($allFieldsGroupList, 'ID'));

				if ($parentKey || 0 === $parentKey) {
					$groupList = array_slice($allFieldsGroupList, $parentKey + 1);
				}
			}
		}

		return $groupList;
	}

	/**
	 * Add additional html to child field
	 *
	 * @param string $childFieldHtml
	 *
	 * @return string
	 */
	public function formatChildField( $childFieldHtml ) {
		return '<div acf-child-wrapper>' . $childFieldHtml . '</div><hr>';
	}

	/**
	 * Add additional comumns to table according to anabled ACF field
	 *
	 * @param array $columns
	 * @param bool $light return array verstion
	 *
	 * @return array
	 */
	public function getTableColumns( $columns, $light ) {
		$prefix = $this->acfPrefix . '-';

		$acfLocalFieldGroups = acf_get_local_field_groups();
		$acfFieldGroups = acf_get_field_groups();

		$acfLocalFieldGroups = $acfLocalFieldGroups ? $acfLocalFieldGroups : array();
		$acfFieldGroups = $acfFieldGroups ? $acfFieldGroups : array();

		$groups = array_merge($acfLocalFieldGroups, $acfFieldGroups);
		if (is_array($groups)) {
			foreach ($groups as $group) {
				$forProduct = false;
				if (!is_array($group['location'])) {
					continue;
				}
				foreach ($group['location'] as $location) {
					if (!is_array($location)) {
						continue;
					}
					foreach ($location as $obj) {
						if ('post_type' == $obj['param'] && '==' == $obj['operator'] && 'product' == $obj['value']) {
							$forProduct = true;
							break;
						}
					}
				}
				if (!$forProduct) {
					continue;
				}
				$fields = acf_get_fields($group['ID']);
				foreach ($fields as $field) {
					if (empty($field['label'])) {
						continue;
					}
					$type = $field['type'];
					if (!in_array($type, $this->enabledFieldTypes)) {
						continue;
					}

					$slug = empty($field['name']) ? $prefix . $field['key'] : $prefix . $field['name'];
					if ($light) {
						$columns[$slug] = $field['label'];
					} else {
						// if acf column list already has column with the same name,that we show only one of them
						$isInColumnList = array_search($slug, array_column($columns, 'slug'));
						if (!$isInColumnList) {
							$columns[] = array(
								'slug'        => $slug,
								'name'        => $field['label'],
								'is_enabled'  => true,
								'is_default'  => false,
								'is_custom'   => true,
								'sub'         => 0,
								'class'       => 'wtbpCustomField',
								'type'        => ( 'file' == $type ? 'link' : $type ),
								'plugin'      => $this->acfPrefix,
								'plugin_data' => array(
									$this->acfPrefix =>array(
										$this->acfPrefix . '_admin_view' => in_array($field['type'], $this->fieldTypesWithAdminViewOptionality),
										$this->acfPrefix . '_key'        => $field['key'],
									)
								),
							);
						}
					}
				}
			}
		}

		return $columns;
	}

	/**
	 * Enqueue Acf Admin area scripts
	 */
	public function enqueueAcfAdminScripts() {
		add_action( 'init', array( 'ACF_Assets', 'register_scripts' ) );
	}

	/**
	 * Add ACF some ACF filed types to exeptional filter taxonomies
	 *
	 * @param array $exceptionFilterTaxonomies
	 *
	 * @return void
	 */
	public function getExceptionFilterTaxonomies( $exceptionFilterTaxonomies ) {
		$exceptionFilterTaxonomies  = array_merge($this->fieldTypeRemovedFromFiltersList, $exceptionFilterTaxonomies);

		return $exceptionFilterTaxonomies;
	}

	/**
	 * Remove some ACF types from custom taxonomies table filter
	 *
	 * @param array $taxonomies
	 * @param array $settings
	 *
	 * @return array
	 */
	public function getFilterCustomTaxonomies( $taxonomies, $settings ) {
		$columnSettingList = json_decode($settings['order']);

		if (is_array($columnSettingList)) {
			foreach ($columnSettingList as $column) {
				if (!empty($taxonomies[$column->slug]) && !empty($column->acf_text_input) ) {
					unset($taxonomies[$column->slug]);
				}
			}
		}

		return $taxonomies;
	}

	/**
	 * Remove some ACF types from custom taxonomies selected table filter
	 *
	 * @param array $taxonomies
	 * @param array $column
	 *
	 * @return array
	 */
	public function getFilterCustomTaxonomiesSelected( $taxonomies, $settings ) {
		$columnSettingList = json_decode($settings['order']);

		if (is_array($columnSettingList)) {
			foreach ($columnSettingList as $column) {
				if (!empty($taxonomies[$column->slug]) && !empty($column->acf_text_input) ) {
					unset($taxonomies[$column->slug]);
				}
			}
		}

		return $taxonomies;
	}

	/**
	 * Check if current display field logic correspond displayed product.
	 *
	 * @return bool
	 */
	public function checkFieldGroupLogicProductBelong() {
		$isBelong = false;
		$groups = acf_get_field_groups(array('post_id' => $this->productId));
		$parent = $this->fieldSettings['parent'];
		foreach ($groups as $group) {
			if ($group['ID'] == $parent) {
				$isBelong = true;
				break;
			}
		}

		return $isBelong;
	}

	/**
	 * Add additional data options to admin add new column select option dependin on column plugins ettings
	 *
	 * @param string $dataPluginDisplay
	 * @param array $column
	 *
	 * @return string
	 */
	public function addAdditionalDataColumnListAdminSelect( $dataPluginDisplay, $column ) {
		if (!empty($column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_admin_view'])) {
			$dataPluginDisplay .= ' data-plugin-display="' . $column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_admin_view'] . '" ';
		}

		return $dataPluginDisplay;
	}

	/**
	 * Add addtitonal data to admin column list
	 *
	 * @param array $orderCols
	 * @param array $column
	 *
	 * @return array
	 */
	public function addAdditionalDataAdminOrderCoulumnList( $orderCols, $column ) {
		if (!empty($column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key']) && !empty($column['slug']['key']) && !empty( $orderCols[$column['slug']['key']])) {
			$orderCols[$column['slug']]['key'] = $column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key'];
		}

		return $orderCols;
	}

	/**
	 * Add addtitonal data to admin column list
	 *
	 * @param bool $isInTable
	 * @param array $column
	 * @param array $orderCols
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function addCheckColumnInTable( $isInTable, $column, $orderCols, $slug ) {
		if (!empty($column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key']) && !empty($orderCols[$slug]['key'])) {
			$columnKey = $column['plugin_data'][$this->acfPrefix][$this->acfPrefix . '_key'];
			$InTableColumnKey = $orderCols[$slug]['key'];
			$isInTable = isset($InTableColumnKey[$columnKey]) && $enabled ? true : false;
		}

		return $isInTable;
	}
}
