<?php

/* Zon Theme */

function zon_import_files() {
		return array(
    array(
      'import_file_name'             => 'Default Zon',
      'categories'                   => array( 'Default'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/default/zon.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/default/zon-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/default/zon-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2021/09/screenshot.png',
      'import_notice'                => __( 'Recommended : 
       <strong>  After the demo is installed, your site look a bit messy. So you need to setup slider and Frontpage (breaking news, feature news)from customizer and also setup category from widgets. </strong>
        
        <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/zon/reviews/" target="_blank" rel="nofollow">rating</a> our theme. <br>
        <a href="https://themefreesia.com/theme-instruction/zon/" target="_blank" rel="nofollow">Documentation</a>
        <br>', 'zon' ),

       'preview_url'                  => 'https://demo.themefreesia.com/zon/',
    ),

    array(
      'import_file_name'             => 'Zon Daily News',
      'categories'                   => array( 'Daily News'),
      'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/daily-news/daily-news.wordpress.xml',
      'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/daily-news/daily-news-widgets.wie',
      'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/daily-news/daily-news-export.dat',
      'import_preview_image_url'   	 => 'https://themefreesia.com/wp-content/uploads/2021/10/The-Daily-News.jpg',
      'import_notice'                => __( 'Recommended : 
       <strong>  After the demo is installed, your site look a bit messy. So you need to setup slider and Frontpage (breaking news, feature news)from customizer and also setup category from widgets. </strong>
        
        <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/zon/reviews/" target="_blank" rel="nofollow">rating</a> our theme. <br>
        <a href="https://themefreesia.com/theme-instruction/zon/" target="_blank" rel="nofollow">Documentation</a>
        <br>', 'zon' ),

       'preview_url'                  => 'https://demo.themefreesia.com/daily-news/',
    ),

    array(
		'import_file_name'             => 'Zon Plus',
		'categories'                   => array( 'Zon Plus'),
		'local_import_file'            => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/zon.wordpress.xml',
		'local_import_widget_file'     => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/zon-widgets.wie',
		'local_import_customizer_file' => trailingslashit( plugin_dir_path( __FILE__ ) ).'dummy/zon/zon-export.dat',
		'import_preview_image_url'     => 'https://themefreesia.com/wp-content/uploads/2021/10/zon-plus.jpg',
		'import_notice'                => __( 'Recommended : 
       <strong>  After the demo is installed, your site look a bit messy. So you need to setup slider and Frontpage (breaking news, feature news)from customizer and also setup category from widgets. </strong>
        
        <br> <br> Thanks for being part of us. If you really like our theme, Please help us <a href="https://wordpress.org/support/theme/zon/reviews/" target="_blank" rel="nofollow">rating</a> our theme. <br>
        <a href="https://themefreesia.com/theme-instruction/zon/" target="_blank" rel="nofollow">Documentation</a>
        <br>', 'zon' ),
		'preview_url'                  => 'https://demo.themefreesia.com/zon-plus/',
	  )
  ); 
	  
}
add_filter( 'ocdi/import_files', 'zon_import_files' );

function zon_after_import_setup($selected_import) {

  if ( 'Default Zon' === $selected_import['import_file_name'] ) {
    // Assign menus to their locations.
    $top_menu = get_term_by( 'name','Top Menu', 'nav_menu' );
    $main_menu = get_term_by( 'name','Main menu', 'nav_menu' );
    $side_nav_menu = get_term_by( 'name','Side Menu', 'nav_menu' );
    $footer_menu = get_term_by( 'name','Footer menu', 'nav_menu' );
    $social_icon = get_term_by( 'name','Social link', 'nav_menu' );

    set_theme_mod( 'nav_menu_locations', array(
        'top-menu' => $top_menu->term_id,
        'primary' => $main_menu->term_id,
        'side-nav-menu' => $side_nav_menu->term_id,
        'footer-menu' => $footer_menu->term_id,
        'social-link' => $social_icon->term_id,
      )
    );

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_title( 'Home' );
    $blog_page_id  = get_page_by_title( 'Blog' );

    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $front_page_id->ID );
    update_option( 'page_for_posts', $blog_page_id->ID );

	}elseif ( 'Zon Daily News' === $selected_import['import_file_name'] ) {

	// Assign menus to their locations.
	$top_menu = get_term_by( 'name','Top Menu', 'nav_menu' );
	$main_menu = get_term_by( 'name','Main menu', 'nav_menu' );
	$side_nav_menu = get_term_by( 'name','Side Menu', 'nav_menu' );
	$footer_menu = get_term_by( 'name','Footer menu', 'nav_menu' );
	$social_icon = get_term_by( 'name','Social link', 'nav_menu' );

	set_theme_mod( 'nav_menu_locations', array(
		'top-menu' => $top_menu->term_id,
		'primary' => $main_menu->term_id,
		'side-nav-menu' => $side_nav_menu->term_id,
		'footer-menu' => $footer_menu->term_id,
		'social-link' => $social_icon->term_id,
		)
	);

	// Assign front page and posts page (blog page).
	$front_page_id = get_page_by_title( 'Home' );
	$blog_page_id  = get_page_by_title( 'Blog' );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_page_id->ID );
	update_option( 'page_for_posts', $blog_page_id->ID );

	} else{
		// Assign menus to their locations.
		$top_menu = get_term_by( 'name','Top Menu', 'nav_menu' );
		$main_menu = get_term_by( 'name','Main menu', 'nav_menu' );
		$side_nav_menu = get_term_by( 'name','Side Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name','Footer menu', 'nav_menu' );
		$social_icon = get_term_by( 'name','Social link', 'nav_menu' );
	
		set_theme_mod( 'nav_menu_locations', array(
			'top-menu' => $top_menu->term_id,
			'primary' => $main_menu->term_id,
			'side-nav-menu' => $side_nav_menu->term_id,
			'footer-menu' => $footer_menu->term_id,
			'social-link' => $social_icon->term_id,
		)
		);
	
		// Assign front page and posts page (blog page).
		$front_page_id = get_page_by_title( 'Home' );
		$blog_page_id  = get_page_by_title( 'Blog' );
	
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );
		update_option( 'page_for_posts', $blog_page_id->ID );
	}
}
add_action( 'ocdi/after_import', 'zon_after_import_setup' );