<?php
class WootablesProWtbp extends ModuleWtbp {
	public $acf_prefix = 'acf';
	public $ctax_prefix = 'ctax';
	public $yith_prefix = 'yith';
	public $xoo_prefix  = 'xoo';
	public $defaultFont = 'Default';
	public $customTaxonomies = null;

	public function init() {
		parent::init();
		DispatcherWtbp::addAction('addScriptsContent', array($this, 'addScriptsContent'));
		DispatcherWtbp::addAction('addEditAdminSettings', array($this, 'addEditAdminSettings'), 10, 2);
		DispatcherWtbp::addFilter('getEnabledColumns', array($this, 'getEnabledColumns'));
		DispatcherWtbp::addFilter('addFullColumnList', array($this, 'addFullColumnList'));
		DispatcherWtbp::addFilter('getColumnContent', array($this, 'getColumnContent'), 10, 3);
		DispatcherWtbp::addFilter('getTableFilters', array($this, 'getTableFilters'), 10, 5);
		DispatcherWtbp::addFilter('addHiddenColumns', array($this, 'addHiddenColumns'), 10, 3);
		DispatcherWtbp::addFilter('getCustomStyles', array($this, 'getCustomStyles'), 10, 5);
		DispatcherWtbp::addFilter('getLoaderHtml', array($this, 'getLoaderHtml'), 10, 4);
		DispatcherWtbp::addFilter('filterProductIds', array($this, 'filterProductIds'), 10, 4);
		DispatcherWtbp::addFilter('setSSPQueryFilters', array($this, 'setSSPQueryFilters'), 10, 5);
		DispatcherWtbp::addFilter('setLazyLoadQueryFilters', array($this, 'setLazyLoadQueryFilters'), 10, 5);
		DispatcherWtbp::addAction('removeSSPQueryFilters', array($this, 'removeSSPQueryFilters'));
		DispatcherWtbp::addFilter('customizeCartButton', array($this, 'customizeCartButton'));
		DispatcherWtbp::addFilter('customizeCartButtonMPC', array($this, 'customizeCartButtonMPC'));
		DispatcherWtbp::addFilter('optionsDefine', array($this, 'optionsDefine'), 10, 1);
		DispatcherWtbp::addFilter('dynamicProductsFiltering', array($this, 'dynamicProductsFiltering'), 10, 2);

		FrameWtbp::_()->getTable('tables')->addField('auto_add', 'text', 'int');
		DispatcherWtbp::addFilter('addTableSettings', array($this, 'addTableSettings'), 10, 1);
		add_action('woocommerce_update_product', array($this, 'addProductToTables'), 10, 1);

		add_filter('woocommerce_get_item_data', array($this, 'renderMetaOnCart'), 10, 2);
	}

	public function renderMetaOnCart( $cartData, $cartItem ) {
		$custom_items = array();
		// Woo 2.4.2 updates
		if (!empty($cartData)) {
			$custom_items = $cartData;
		}
		foreach ($cartItem as $key => $value) {
			if (strpos($key, 'wtbp_') === 0) {
				$custom_items[] = array( 'name' => empty($cartItem['label_' . $key]) ? $key : $cartItem['label_' . $key], 'value' => $value );
			}
		}
		return $custom_items;
	}

	public function addScriptsContent( $adminArea ) {
		$modPath = $this->getModPath();
		FrameWtbp::_()->addScript('wtbp.core.tables.pro.js', $modPath . 'js/wootables.core.pro.js');
		if ($adminArea) {
			FrameWtbp::_()->addScript('wtbp.admin.tables.pro.js', $modPath . 'js/wootables.admin.pro.js');
			FrameWtbp::_()->addStyle('wtbp.admin.tables.pro.css', $modPath . 'css/wootables.admin.pro.css');
		} else {
			FrameWtbp::_()->addScript('wtbp.frontend.tables.pro.js', $modPath . 'js/wootables.frontend.pro.js');
			FrameWtbp::_()->getModule('templates')->loadFontAwesome();
		}
		FrameWtbp::_()->addScript('wtbp.multiple.select.js', WTBP_JS_PATH . 'multiple-select.js');
		FrameWtbp::_()->addStyle('wtbp.multiple.select.css', WTBP_CSS_PATH . 'multiple-select.css');
		FrameWtbp::_()->addStyle('wtbp.frontend.tables.pro.css', $modPath . 'css/wootables.frontend.pro.css');

		if ($this->isACFPluginActivated()) {
			FrameWtbp::_()
				->getModule('wootablespro')
				->getModel('acf')
				->enqueueAcfAdminScripts();
		}
	}

	public function customizeCartButton( $settings ) {
		$this->getView()->customizeCartButton($settings);
	}

	public function customizeCartButtonMPC( $html ) {
		return $this->getView()->replaceAddToCartTextMPC($html);
	}

	public function optionsDefine( $options ) {
		$options['general']['opts']['google_api_map_key'] = array(
			'label' => __('Set google API key', 'woo-product-tables'),
			'desc' => __('Set google API key. We use it to access data to some columns in table', 'woo-product-tables'),
			'def' => '',
			'html' => 'text'
		);
		$options['general']['opts']['global_search_filtration'] = array(
			'label' => __('Filter by global search url parameters ', 'woo-product-tables'),
			'desc' => __('The tables content will be filtered by `s` parameter from the url.', 'woo-product-tables'),
			'def' => '0',
			'html' => 'checkboxHiddenVal'
		);

		return $options;
	}

	public function addProductToTables( $productId ) {
		$this->getModel()->addAutoProducts($productId);
	}

	public function addEditAdminSettings( $part, $settings ) {
		$this->getView()->addEditAdminSettings($part, $settings);
	}

	public function getEnabledColumns( $columns ) {
		$addEnabled = array('attribute', 'add_to_cart', 'ctax', 'sales', 'tags', 'weight', 'dimensions');
		if ($this->isACFPluginActivated()) {
			$addEnabled[] = 'acf';
		}
		if ($this->isYITHQuickViewPluginActivated()) {
			$addEnabled[] = 'yith';
		}
		if ($this->isXooQuickViewPluginActivated()) {
			$addEnabled[] = 'xoo';
		}
		if ($this->isWcVendorsPluginActivated()) {
			$addEnabled[] = 'vendor';
		}
		
		return array_merge($columns, $addEnabled);
	}

	public function addFullColumnList( $columns, $light = false ) {

		$taxonomies = $this->getCustomTaxonomies();
		$exclude = array('pwb-brand');
		if (count($taxonomies) > 0) {
			$prefix = $this->ctax_prefix . '-';
			foreach ($taxonomies as $slug => $label) {
				if (in_array($slug, $exclude)) {
					continue;
				}
				if ($light) {
					$columns[$prefix . $slug] = $label;
				} else {
					$columns[] = array(
						'slug' => $prefix . $slug,
						'name' => $label, 
						'is_enabled' => true, 
						'is_default' => false,
						'is_custom' => true, 
						'sub' => 0, 
						'class' => 'wtbpCustomTax', 
						'type' => 'custom_tax');
				}
			}
		}
		if ($this->isYITHQuickViewPluginActivated()) {
			$columns[] = array(
				'slug' => $this->yith_prefix . '-quick_view',
				'name' => 'YITH Quick view', 
				'is_enabled' => true, 
				'is_default' => false,
				'is_custom' => true, 
				'sub' => 0, 
				'class' => 'wtbpCustomColumn', 
				'type' => 'quick_view');
		}
		if ($this->isXooQuickViewPluginActivated()) {
			$columns[] = array(
				'slug' => $this->xoo_prefix . '-quick_view',
				'name' => 'Xoo Quick view',
				'is_enabled' => true,
				'is_default' => false,
				'is_custom' => true,
				'sub' => 0,
				'class' => 'wtbpCustomColumn',
				'type' => 'xoo_quick_view');
		}
		if ($this->isWcVendorsPluginActivated()) {
			$columns[] = array(
				'slug' => 'vendor',
				'name' => __('WC Vendor', 'woo-product-tables'),
				'is_enabled' => true,
				'is_default' => false,
				'is_custom' => true,
				'sub' => 0,
				'class' => 'wtbpCustomColumn',
				'type' => 'vendor'
			);
		}
		if ($this->isACFPluginActivated()) {
			$columns =
				FrameWtbp::_()
					->getModule('wootablespro')
					->getModel('acf')
					->getTableColumns($columns, $light);
		}
		$slugs = array_column($columns, 'slug');
		if (!in_array('weight', $slugs)) {
			$columns[] = array(
				'slug' => 'weight',
				'name' => __('Weight', 'woo-product-tables'),
				'is_enabled' => true,
				'is_default' => true,
				'is_custom' => false,
				'sub' => 0,
				'class' => ''
			);
		}
		if (!in_array('dimensions', $slugs)) {
			$columns[] = array(
				'slug' => 'dimensions',
				'name' => __('Dimensions', 'woo-product-tables'),
				'is_enabled' => true,
				'is_default' => true,
				'is_custom' => false,
				'sub' => 0,
				'class' => ''
			);
		}
		return $columns;
	}

	public function getCustomTaxonomies() {
		if (is_null($this->customTaxonomies)) {
			$exclude = array('product_type', 'product_visibility', 'product_cat', 'product_tag', 'product_shipping_class');
			foreach (wc_get_attribute_taxonomies() as $attr) {
				$exclude[] = 'pa_' . $attr->attribute_name;
			}
			$taxonomies = array();
			foreach (get_object_taxonomies('product', 'objects') as $slug => $tax) {
				if (!in_array($slug, $exclude)) {
					$taxonomies[$slug] = $tax->label;
				}
			}
			$this->customTaxonomies = $taxonomies;
		}

		return $this->customTaxonomies;
	}

	public function getColumnContent( $data, $params ) {
		return $this->getView()->getColumnContent($data, $params);
	}

	public function getTableFilters( $str, $id, $settings ) {
		return $this->getView()->getFilterHtml($id, $settings);
	}

	public function getCustomStyles( $css, $tableId, $settings ) {
		return $this->getView()->getCustomStyles($css, $tableId, $settings);
	}

	public function getLoaderHtml( $html, $settings ) {
		return $this->getView()->getLoaderHtml($html, $settings);
	}

	public function filterProductIds( $products, $params ) {
		return $this->getView()->filterProductIds($products, $params);
	}

	public function setSSPQueryFilters( $settings, $args, $page ) {
		return $this->getView()->setSSPQueryFilters($settings, $args, $page);
	}
	
	public function setLazyLoadQueryFilters( $args, $settings, $page ) {
		if (FrameWtbp::_()->getModule('options')->getModel()->get('global_search_filtration') == '1' && ReqWtbp::getVar('s') ) {
			$args['s'] = ReqWtbp::getVar('s');
		}
		return $this->getView()->setLazyLoadQueryFilters($args, $settings, $page);
	}

	public function removeSSPQueryFilters() {
		$this->getView()->removeSSPQueryFilters();
	}

	public function addHiddenColumns( $order, $settings ) {
		return $this->getView()->addHiddenColumns($order, $settings);
	}
	
	public function activate() {
		$this->install();
	}
	public function install() {
		if (!dbWtbp::exist('@__tables', 'auto_add')) {
			dbWtbp::query("ALTER TABLE `@__tables` ADD COLUMN `auto_add` TINYINT NOT NULL DEFAULT '0';");
		}
	}
	public function addTableSettings( $data ) {
		$settings = $data['settings'];
		$data['auto_add'] = ( isset($settings['auto_categories_enable'])
			&& 1 == $settings['auto_categories_enable']
			&& isset($settings['auto_categories_list'])
			&& !empty($settings['auto_categories_list'])
			&& 'all' != $settings['auto_categories_list'] )
			|| ( isset($settings['auto_variations_enable'])
			&& 1 == $settings['auto_variations_enable']
			&& isset($settings['auto_variations_list'])
			&& !empty($settings['auto_variations_list'])
			&& 'all' != $settings['auto_variations_list'] ) ? 1 : 0;
		return $data;
	}
	public function isACFPluginActivated() {
		return class_exists('acf');
	}
	public function isYITHQuickViewPluginActivated() {
		return class_exists('YITH_WCQV_Frontend');
	}
	public function isXooQuickViewPluginActivated() {
		$pluginActive = false;
		foreach ( array( 'quick-view-woocommerce/xoo-quickview-main.php', 'quick-view-woocommerce-premium/xoo-quickview-main.php' ) as $plugin ) {
			if ( in_array( $plugin, (array) get_option( 'active_plugins', array()), true ) ) {
				$pluginActive = true;
			}
		}

		return $pluginActive;
	}
	public function isWcVendorsPluginActivated() {
		return class_exists('WC_Vendors');
	}

	public function getFontsList() {
		return array('ABeeZee','Abel','Abril Fatface','Aclonica','Acme','Actor','Adamina','Advent Pro','Aguafina Script','Akronim','Aladin','Aldrich','Alef','Alegreya','Alegreya SC','Alegreya Sans','Alegreya Sans SC','Alex Brush','Alfa Slab One','Alice','Alike','Alike Angular','Allan','Allerta','Allerta Stencil','Allura','Almendra','Almendra Display','Almendra SC','Amarante','Amaranth','Amatic SC','Amethysta','Amiri','Anaheim','Andada','Andika','Angkor','Annie Use Your Telescope','Anonymous Pro','Antic','Antic Didone','Antic Slab','Anton','Arapey','Arbutus','Arbutus Slab','Architects Daughter','Archivo Black','Archivo Narrow','Arimo','Arizonia','Armata','Artifika','Arvo','Asap','Asset','Astloch','Asul','Atomic Age','Aubrey','Audiowide','Autour One','Average','Average Sans','Averia Gruesa Libre','Averia Libre','Averia Sans Libre','Averia Serif Libre','Bad Script','Balthazar','Bangers','Basic','Battambang','Baumans','Bayon','Belgrano','Belleza','BenchNine','Bentham','Berkshire Swash','Bevan','Bigelow Rules','Bigshot One','Bilbo','Bilbo Swash Caps','Biryani','Bitter','Black Ops One','Bokor','Bonbon','Boogaloo','Bowlby One','Bowlby One SC','Brawler','Bree Serif','Bubblegum Sans','Bubbler One','Buenard','Butcherman','Butterfly Kids','Cabin','Cabin Condensed','Cabin Sketch','Caesar Dressing','Cagliostro','Calligraffitti','Cambay','Cambo','Candal','Cantarell','Cantata One','Cantora One','Capriola','Cardo','Carme','Carrois Gothic','Carrois Gothic SC','Carter One','Caudex','Cedarville Cursive','Ceviche One','Changa One','Chango','Chau Philomene One','Chela One','Chelsea Market','Chenla','Cherry Cream Soda','Cherry Swash','Chewy','Chicle','Chivo','Cinzel','Cinzel Decorative','Clicker Script','Coda','Codystar','Combo','Comfortaa','Coming Soon','Concert One','Condiment','Content','Contrail One','Convergence','Cookie','Copse','Corben','Courgette','Cousine','Coustard','Covered By Your Grace','Crafty Girls','Creepster','Crete Round','Crimson Text','Croissant One','Crushed','Cuprum','Cutive','Cutive Mono','Damion','Dancing Script','Dangrek','Dawning of a New Day','Days One','Dekko','Delius','Delius Swash Caps','Delius Unicase','Della Respira','Denk One','Devonshire','Dhurjati','Didact Gothic','Diplomata','Diplomata SC','Domine','Donegal One','Doppio One','Dorsa','Dosis','Dr Sugiyama','Droid Sans','Droid Sans Mono','Droid Serif','Duru Sans','Dynalight','EB Garamond','Eagle Lake','Eater','Economica','Ek Mukta','Electrolize','Elsie','Elsie Swash Caps','Emblema One','Emilys Candy','Engagement','Englebert','Enriqueta','Erica One','Esteban','Euphoria Script','Ewert','Exo','Exo 2','Expletus Sans','Fanwood Text','Fascinate','Fascinate Inline','Faster One','Fasthand','Fauna One','Federant','Federo','Felipa','Fenix','Finger Paint','Fira Mono','Fira Sans','Fjalla One','Fjord One','Flamenco','Flavors','Fondamento','Fontdiner Swanky','Forum','Francois One','Freckle Face','Fredericka the Great','Fredoka One','Freehand','Fresca','Frijole','Fruktur','Fugaz One','GFS Didot','GFS Neohellenic','Gabriela','Gafata','Galdeano','Galindo','Gentium Basic','Gentium Book Basic','Geo','Geostar','Geostar Fill','Germania One','Gidugu','Gilda Display','Give You Glory','Glass Antiqua','Glegoo','Gloria Hallelujah','Goblin One','Gochi Hand','Gorditas','Goudy Bookletter 1911','Graduate','Grand Hotel','Gravitas One','Great Vibes','Griffy','Gruppo','Gudea','Gurajada','Habibi','Halant','Hammersmith One','Hanalei','Hanalei Fill','Handlee','Hanuman','Happy Monkey','Headland One','Henny Penny','Herr Von Muellerhoff','Hind','Holtwood One SC','Homemade Apple','Homenaje','IM Fell DW Pica','IM Fell DW Pica SC','IM Fell Double Pica','IM Fell Double Pica SC','IM Fell English','IM Fell English SC','IM Fell French Canon','IM Fell French Canon SC','IM Fell Great Primer','IM Fell Great Primer SC','Iceberg','Iceland','Imprima','Inconsolata','Inder','Indie Flower','Inika','Irish Grover','Istok Web','Italiana','Italianno','Jacques Francois','Jacques Francois Shadow','Jaldi','Jim Nightshade','Jockey One','Jolly Lodger','Josefin Sans','Josefin Slab','Joti One','Judson','Julee','Julius Sans One','Junge','Jura','Just Another Hand','Just Me Again Down Here','Kalam','Kameron','Kantumruy','Karla','Karma','Kaushan Script','Kavoon','Kdam Thmor','Keania One','Kelly Slab','Kenia','Khand','Khmer','Khula','Kite One','Knewave','Kotta One','Koulen','Kranky','Kreon','Kristi','Krona One','Kurale','La Belle Aurore','Laila','Lakki Reddy','Lancelot','Lateef','Lato','League Script','Leckerli One','Ledger','Lekton','Lemon','Libre Baskerville','Life Savers','Lilita One','Lily Script One','Limelight','Linden Hill','Lobster','Lobster Two','Londrina Outline','Londrina Shadow','Londrina Sketch','Londrina Solid','Lora','Love Ya Like A Sister','Loved by the King','Lovers Quarrel','Luckiest Guy','Lusitana','Lustria','Macondo','Macondo Swash Caps','Magra','Maiden Orange','Mako','Mallanna','Mandali','Marcellus','Marcellus SC','Marck Script','Margarine','Marko One','Marmelad','Martel','Martel Sans','Marvel','Mate','Mate SC','Maven Pro','McLaren','Meddon','MedievalSharp','Medula One','Megrim','Meie Script','Merienda','Merienda One','Merriweather','Merriweather Sans','Metal','Metal Mania','Metamorphous','Metrophobic','Michroma','Milonga','Miltonian','Miltonian Tattoo','Miniver','Miss Fajardose','Modak','Modern Antiqua','Molengo','Monda','Monofett','Monoton','Monsieur La Doulaise','Montaga','Montez','Montserrat','Montserrat Alternates','Montserrat Subrayada','Moul','Moulpali','Mountains of Christmas','Mouse Memoirs','Mr Bedfort','Mr Dafoe','Mr De Haviland','Mrs Saint Delafield','Mrs Sheppards','Muli','Mystery Quest','NTR','Neucha','Neuton','New Rocker','News Cycle','Niconne','Nixie One','Nobile','Nokora','Norican','Nosifer','Nothing You Could Do','Noticia Text','Noto Sans','Noto Serif','Nova Cut','Nova Flat','Nova Mono','Nova Oval','Nova Round','Nova Script','Nova Slim','Nova Square','Numans','Nunito','Odor Mean Chey','Offside','Old Standard TT','Oldenburg','Oleo Script','Oleo Script Swash Caps','Open Sans','Oranienbaum','Orbitron','Oregano','Orienta','Original Surfer','Oswald','Over the Rainbow','Overlock','Overlock SC','Ovo','Oxygen','Oxygen Mono','PT Mono','PT Sans','PT Sans Caption','PT Sans Narrow','PT Serif','PT Serif Caption','Pacifico','Palanquin','Palanquin Dark','Paprika','Parisienne','Passero One','Passion One','Pathway Gothic One','Patrick Hand','Patrick Hand SC','Patua One','Paytone One','Peddana','Peralta','Permanent Marker','Petit Formal Script','Petrona','Philosopher','Piedra','Pinyon Script','Pirata One','Plaster','Play','Playball','Playfair Display','Playfair Display SC','Podkova','Poiret One','Poller One','Poly','Pompiere','Pontano Sans','Port Lligat Sans','Port Lligat Slab','Pragati Narrow','Prata','Preahvihear','Press Start 2P','Princess Sofia','Prociono','Prosto One','Puritan','Purple Purse','Quando','Quantico','Quattrocento','Quattrocento Sans','Questrial','Quicksand','Quintessential','Qwigley','Racing Sans One','Radley','Rajdhani','Raleway','Raleway Dots','Ramabhadra','Ramaraja','Rambla','Rammetto One','Ranchers','Rancho','Ranga','Rationale','Ravi Prakash','Redressed','Reenie Beanie','Revalia','Ribeye','Ribeye Marrow','Righteous','Risque','Roboto','Roboto Condensed','Roboto Slab','Rochester','Rock Salt','Rokkitt','Romanesco','Ropa Sans','Rosario','Rosarivo','Rouge Script','Rozha One','Rubik Mono One','Rubik One','Ruda','Rufina','Ruge Boogie','Ruluko','Rum Raisin','Ruslan Display','Russo One','Ruthie','Rye','Sacramento','Sail','Salsa','Sanchez','Sancreek','Sansita One','Sarina','Sarpanch','Satisfy','Scada','Scheherazade','Schoolbell','Seaweed Script','Sevillana','Seymour One','Shadows Into Light','Shadows Into Light Two','Shanti','Share','Share Tech','Share Tech Mono','Shojumaru','Short Stack','Siemreap','Sigmar One','Signika','Signika Negative','Simonetta','Sintony','Sirin Stencil','Six Caps','Skranji','Slabo 13px','Slabo 27px','Slackey','Smokum','Smythe','Sniglet','Snippet','Snowburst One','Sofadi One','Sofia','Sonsie One','Sorts Mill Goudy','Source Code Pro','Source Sans Pro','Source Serif Pro','Special Elite','Spicy Rice','Spinnaker','Spirax','Squada One','Sree Krushnadevaraya','Stalemate','Stalinist One','Stardos Stencil','Stint Ultra Condensed','Stint Ultra Expanded','Stoke','Strait','Sue Ellen Francisco','Sumana','Sunshiney','Supermercado One','Suranna','Suravaram','Suwannaphum','Swanky and Moo Moo','Syncopate','Tangerine','Taprom','Tauri','Teko','Telex','Tenali Ramakrishna','Tenor Sans','Text Me One','The Girl Next Door','Tienne','Timmana','Tinos','Titan One','Titillium Web','Trade Winds','Trocchi','Trochut','Trykker','Tulpen One','Ubuntu','Ubuntu Condensed','Ubuntu Mono','Ultra','Uncial Antiqua','Underdog','Unica One','UnifrakturMaguntia','Unkempt','Unlock','Unna','VT323','Vampiro One','Varela','Varela Round','Vast Shadow','Vesper Libre','Vibur','Vidaloka','Viga','Voces','Volkhov','Vollkorn','Voltaire','Waiting for the Sunrise','Wallpoet','Walter Turncoat','Warnes','Wellfleet','Wendy One','Wire One','Yanone Kaffeesatz','Yellowtail','Yeseva One','Yesteryear','Zeyada');
	}
	public function getStandardFontsList() {
		return array('Georgia','Palatino Linotype','Times New Roman','Arial','Helvetica','Arial Black','Gadget','Comic Sans MS','Impact','Charcoal','Lucida Sans Unicode','Lucida Grande','Tahoma','Geneva','Trebuchet MS','Verdana','Geneva','Courier New','Courier','Lucida Console','Monaco');
	}
	public function getExceptionFilterTaxonomies() {
		$exceptionFilterTaxonomies = array(
			'quick_view', 'vendor'
		);
		return DispatcherWtbp::applyFilters(
			'getExceptionFilterTaxonomies',
			$exceptionFilterTaxonomies
		);
	}
	public function getFilterMetaQueryTypes() {
		return array(
				'acf',
			);
	}
	public function dynamicProductsFiltering( $productIds, $settings ) {
		return ( is_admin() && 'ajax' !== ReqWtbp::getVar( 'reqType' ) ) ? $productIds : $this->getView()->dynamicProductsFiltering( $productIds, $settings );
	}
}
