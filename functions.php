<?php
/**
 * functions.php
 *
 */
/**
 * Functions to show tema-coletivo sections on custom pages
 * @param string $name
 * @param bool|string $default
 * @return string
 */
if ( ! function_exists( 'coletivo_get_theme_mod' ) ) {
	function coletivo_get_theme_mod( $name, $default = false ) {
		// if it's not template-sections.php page template, ignore and run wp get_theme_mod function
		global $post;
		if ( ! $post || ! is_object( $post ) ) {
			return get_theme_mod( $name, $default );
		}
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( ! $page_template || $page_template != 'template-sections.php' ) {
			return get_theme_mod( $name, $default );
		}
		$general_settings_ignore = array( 'coletivo_header_settings', 'coletivo_hide_tagline', 'coletivo_primary_color', 'coletivo_animation_disable', 'coletivo_header_transparent', 'coletivo_header_bg_color', 'coletivo_logo_text_color', 'coletivo_menu_color', 'coletivo_menu_hover_color', 'coletivo_menu_hover_bg_color', 'coletivo_menu_toggle_button_color', 'coletivo_vertical_align_menu', 'coletivo_sticky_header_disable', 'coletivo_footer_text', 'coletivo_footer_text_link', 'coletivo_footer_info_bg', 'coletivo_btt_disable', 'coletivo_blog_page_style', 'coletivo_hide_sitetitle', 'coletivo_hide_tagline' );
		// if $name is general settings (like header, footer, etc), ignore and run wp get_theme_mod
		if ( in_array( $name, $general_settings_ignore ) ) {
			return get_theme_mod( $name, $default );
		}
		if ( $post ) {
			return get_theme_mod( $name . '_' . $post->ID, $default );
		}
		return get_theme_mod( $name, $default );
	}
}
/**
 * function to change customizer settings to use in more than one page
 * @param string $name
 * @return string
 */
if ( ! function_exists( 'coletivo_add_settings') ) {
	function coletivo_add_settings( $name ) {
		// if it's not template-sections.php page template, ignore and return default $name;
		if ( ! isset( $_GET[ 'url' ] ) ) {
			return $name;
		}
			$post_id = url_to_postid( $_GET[ 'url' ] );
			if ( ! $post_id && ! is_numeric( $post_id ) ) {
			return $name;
		}
		$post = get_post( $post_id );
		if ( ! $post || ! is_object( $post ) ) {
			return $name;
		}
		$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( ! $page_template || $page_template != 'template-sections.php' ) {
			return $name;
		}
		$general_settings_ignore = array( 'coletivo_header_settings', 'coletivo_hide_tagline', 'coletivo_primary_color', 'coletivo_animation_disable', 'coletivo_header_transparent', 'coletivo_header_bg_color', 'coletivo_logo_text_color', 'coletivo_menu_color', 'coletivo_menu_hover_color', 'coletivo_menu_hover_bg_color', 'coletivo_menu_toggle_button_color', 'coletivo_vertical_align_menu', 'coletivo_sticky_header_disable', 'coletivo_footer_text', 'coletivo_footer_text_link', 'coletivo_footer_info_bg', 'coletivo_btt_disable', 'coletivo_blog_page_style', 'coletivo_hide_sitetitle', 'coletivo_hide_tagline' );
		// if $name is general settings (like header, footer, etc), ignore and run wp get_theme_mod
		if ( in_array( $name, $general_settings_ignore ) ) {
			return $name;
		}
		if ( $post ) {
			return $name . '_' . $post->ID;
		}
		return $name;
	}
}
 /*
 * Adiciona arquivos JS
 *
 */
function csem_theme_scripts() {
	// Enfileira o script
	wp_enqueue_script( 'csem-theme-home', get_stylesheet_directory_uri() . '/assets/js/home.js', array( 'jquery'), '1.0', true );
	wp_localize_script( 'csem-theme-home', 'ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script( 'csem-theme-geral', get_stylesheet_directory_uri() . '/assets/js/geral.js', array( 'jquery'), '1.0', true );
	wp_enqueue_script( 'csem-theme-fullpage-js', get_stylesheet_directory_uri() . '/assets/js/fullpage.js', array( 'jquery'), '1.0', true );
	wp_enqueue_script( 'csem-theme-template-metodo', get_stylesheet_directory_uri() . '/assets/js/template-metodo.js', array( 'jquery'), '1.0', true );


}
add_action( 'wp_enqueue_scripts', 'csem_theme_scripts' );

/**
 *
 * Sobrepoe a função padrão do tema pai "coletivo_site_header"
 *
 */
function coletivo_site_header() {
	?>
	  	<header id="masthead" class="site-header" role="banner">
	  			<a href="#" class="csem-open-menu">
	  				<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-menu.png" class="open">
	  				<img src="<?php echo get_stylesheet_directory_uri();?>/assets/images/icon-close-menu.png" class="closed">
	  			</a>
        </header><!-- #masthead -->
        <nav class="menu-logo-toggle" >
          			<div class="site-branding">
                		<?php
                		coletivo_site_logo();
                		?>
               		</div>
                    <nav id="site-navigation" class="main-navigation" role="navigation">
                        <ul class="coletivo-menu">
                            <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => '', 'items_wrap' => '%3$s')); ?>
                        </ul>
                    </nav>
                    <!-- #site-navigation -->
        </nav>

    <?php
}

/**
 * Adiciona seções ao tema coletivo
 */
function csem_coletivo_customizer_sections( $sections ) {
	return $sections .= ',fazer-parte,ultimos_sociais';
}
add_filter( 'coletivo_sections_order_default_value', 'csem_coletivo_customizer_sections' );
function csem_coletivo_customize_after_register( $wp_customize ) {
	$pages  =  get_pages();
	$option_pages = array();
	$option_pages[0] = __( 'Select page', 'coletivo' );
	foreach( $pages as $p ){
		$option_pages[ $p->ID ] = $p->post_title;
	}

	/*------------------------------------------------------------------------*/
    /*  Section: Fazer Parte
    /*------------------------------------------------------------------------*/

    $wp_customize->add_panel( 'coletivo_fazer-parte',
		array(
			'priority'    => coletivo_get_customizer_priority( 'coletivo_fazer-parte' ),
			'title'           => esc_html__( 'Section: Fazer Parte', 'coletivo' ),
			'description'     => '',
			'active_callback' => 'coletivo_showon_frontpage'
		)
	);
	$wp_customize->add_section( 'coletivo_fazer-parte_settings',
		array(
			'priority'    => 3,
			'title'       => esc_html__( 'Section Settings', 'coletivo' ),
			'description' => '',
			'panel'       => 'coletivo_fazer-parte',
		)
	);
	// Show Content
	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_disable'),
		array(
			'sanitize_callback' => 'coletivo_sanitize_checkbox',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_disable'),
		array(
			'type'        => 'checkbox',
			'label'       => esc_html__('Hide this section?', 'coletivo'),
			'section'     => 'coletivo_fazer-parte_settings',
			'description' => esc_html__('Check this box to hide this section.', 'coletivo'),
		)
	);
	// Title
    $wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_title'),
        array(
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    $wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_title'),
        array(
            'label' 		=> esc_html__('Title section in customizer', 'coletivo'),
            'section' 		=> 'coletivo_fazer-parte_settings',
            'description'   => esc_html__( 'This title is only showed in customizer', 'coletivo'),
        )
    );

	// Section ID
	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_id'),
		array(
			'sanitize_callback' => 'coletivo_sanitize_text',
			'default'           => esc_html__('fazer-parte', 'coletivo'),
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_id'),
		array(
			'label' 		=> esc_html__('Section ID:', 'coletivo'),
			'section' 		=> 'coletivo_fazer-parte_settings',
			'description'   => esc_html__( 'The section id, we will use this for link anchor.', 'coletivo' )
		)
	);
	$wp_customize->add_section( 'coletivo_fazer-parte_content' ,
		array(
			'priority'    => 6,
			'title'       => esc_html__( 'Section Content', 'coletivo' ),
			'panel'       => 'coletivo_fazer-parte',
		)
	);
	// Select Page
	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_content_1'),
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_content_1'),
			array(
				'label' 		=> esc_html__('Featured Page', 'coletivo'),
				'section'       => 'coletivo_fazer-parte_content',
				'description' => esc_html__( 'You need to select a Featured Image for a background in full size.', 'coletivo' ),
				'type'     => 'select',
				'choices' => $option_pages,
				'fields'    => array(
					'options' => $option_pages
					)
		) );
    // More Button
	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_more_text_1'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__('Discover', 'coletivo'),
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_more_text_1'),
		array(
			'label'     	=> esc_html__('Featured Page Button Text', 'coletivo'),
			'section'       => 'coletivo_fazer-parte_content',
			'description'   => '',
		)
	);

	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_content_2'),
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_content_2'),
			array(
				'label' 		=> esc_html__('Featured Page', 'coletivo'),
				'section'       => 'coletivo_fazer-parte_content',
				'description' => esc_html__( 'You need to select a Featured Image for a background in full size.', 'coletivo' ),
				'type'     => 'select',
				'choices' => $option_pages,
				'fields'    => array(
					'options' => $option_pages
					)
		) );
    // More Button
	$wp_customize->add_setting( coletivo_add_settings('coletivo_fazer-parte_more_text_2'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => esc_html__('Discover', 'coletivo'),
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_fazer-parte_more_text_2'),
		array(
			'label'     	=> esc_html__('Featured Page Button Text', 'coletivo'),
			'section'       => 'coletivo_fazer-parte_content',
			'description'   => '',
		)
	);

    /*------------------------------------------------------------------------*/
    /*  End of Section Featured Page
    /*------------------------------------------------------------------------*/

	/*------------------------------------------------------------------------*/
    /*  Section: Ultimos redes sociais
    /*------------------------------------------------------------------------*/

    $wp_customize->add_panel( 'coletivo_ultimos_sociais',
		array(
			'priority'    => coletivo_get_customizer_priority( 'coletivo_ultimos_sociais' ),
			'title'           => esc_html__( 'Section: Ultimos posts nas redes', 'coletivo' ),
			'description'     => '',
			'active_callback' => 'coletivo_showon_frontpage'
		)
	);
	$wp_customize->add_section( 'coletivo_ultimos_sociais_settings',
		array(
			'priority'    => 3,
			'title'       => esc_html__( 'Section Settings', 'coletivo' ),
			'description' => '',
			'panel'       => 'coletivo_ultimos_sociais',
		)
	);
	// Show Content
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_disable'),
		array(
			'sanitize_callback' => 'coletivo_sanitize_checkbox',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_disable'),
		array(
			'type'        => 'checkbox',
			'label'       => esc_html__('Hide this section?', 'coletivo'),
			'section'     => 'coletivo_ultimos_sociais_settings',
			'description' => esc_html__('Check this box to hide this section.', 'coletivo'),
		)
	);
	// Title
    $wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_title'),
        array(
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        )
    );
    $wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_title'),
        array(
            'label' 		=> esc_html__('Title section in customizer', 'coletivo'),
            'section' 		=> 'coletivo_ultimos_sociais_settings',
            'description'   => esc_html__( 'This title is only showed in customizer', 'coletivo'),
        )
    );

	// Section ID
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_id'),
		array(
			'sanitize_callback' => 'coletivo_sanitize_text',
			'default'           => esc_html__('#ultimos-sociais', 'coletivo'),
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_id'),
		array(
			'label' 		=> esc_html__('Section ID:', 'coletivo'),
			'section' 		=> 'coletivo_ultimos_sociais_settings',
			'description'   => esc_html__( 'The section id, we will use this for link anchor.', 'coletivo' )
		)
	);

	$wp_customize->add_section( 'coletivo_ultimos_sociais_content' ,
		array(
			'priority'    => 6,
			'title'       => esc_html__( 'Section Content', 'coletivo' ),
			'panel'       => 'coletivo_ultimos_sociais',
		)
	);
    // Textarea redes
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_yt_url'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_yt_url'),
		array(
			'label'     	=> esc_html__('URL do YouTube', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
			'description'   => '',
		)
	);

	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_yt_id'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_yt_id'),
		array(
			'label'     	=> esc_html__('ID do Canal no Youtube', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
			'description'   => 'Verique o ID pelo site: https://commentpicker.com/youtube-channel-id.php',
		)
	);


	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_instagram_url'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_instagram_url'),
		array(
			'label'     	=> esc_html__('URL do Instagram', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
			'description'   => '',
		)
	);
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_instagram_token'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_instagram_token'),
		array(
			'label'     	=> esc_html__('Token de acesso Instagram', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
			'description'   => 'Gere o token pelo site: https://instagram.pixelunion.net/',
		)
	);

	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_fb'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_fb'),
		array(
			'label'     	=> esc_html__('ID da Página no Facebook', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
			'description'   => 'Verifique o ID da página em: https://findmyfbid.com/',
		)
	);

	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_fb_url'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_fb_url'),
		array(
			'label'     	=> esc_html__('Link da página no Facebook', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
		)
	);
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_fb_appid'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_fb_appid'),
		array(
			'label'     	=> esc_html__('AppID do Aplicativo no Facebook', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
		)
	);
	$wp_customize->add_setting( coletivo_add_settings('coletivo_ultimos_sociais_fb_secret'),
		array(
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
	$wp_customize->add_control( coletivo_add_settings('coletivo_ultimos_sociais_fb_secret'),
		array(
			'label'     	=> esc_html__('Secret do Aplicativo no Facebook', 'coletivo'),
			'section'       => 'coletivo_ultimos_sociais_content',
		)
	);


    /*------------------------------------------------------------------------*/
    /*  End of Section Featured Page
    /*------------------------------------------------------------------------*/

}
add_action( 'coletivo_customize_after_register', 'csem_coletivo_customize_after_register', 10, 1 );

/**
 * Adiciona shortcode para AGENDA
 * @return type
 */
function csem_agenda() {
	ob_start();
	get_template_part( 'section-parts/shortcode-agenda' );
	return ob_get_clean();
}
add_shortcode( 'csem_agenda', 'csem_agenda' );

// Classe para seção ultimos posts das redes
require_once get_stylesheet_directory() . '/inc/class-load-last-post-social-networks.php';

/**
 * Função para exibir todos posts no archive da agenda
 */
function hwl_home_pagesize( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! is_post_type_archive( 'el_events' ) ) {
        return;
    }
    $query->set( 'posts_per_page', 9999999 );
    if ( is_home() ) {
        // Display only 1 post for the original blog archive
        $query->set( 'posts_per_page', 1 );
        return;
    }

    if ( is_post_type_archive( 'movie' ) ) {
        // Display 50 posts for a custom post type called 'movie'
        $query->set( 'posts_per_page', 50 );
        return;
    }
}
add_action( 'pre_get_posts', 'hwl_home_pagesize', 1 );
