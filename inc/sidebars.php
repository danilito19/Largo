<?php

/**
 * Register our sidebars and other widget areas
 *
 * @todo move the taxonomy landing page sidebar registration here
 *  (currently in inc/wp-taxonomy-landing/functions/cftl-admin.php)
 * @since 0.3
 */
function largo_register_sidebars() {
	$sidebars = array (
		// the default widget areas
		array (
			'name'	=> __( 'Main Sidebar', 'largo' ),
			'desc' 	=> __( 'The sidebar for the homepage. If you do not add widgets to any of the other sidebars, this will also be used on all of the other pages of your site.', 'largo' ),
			'id' 	=> 'sidebar-main'
		),
		array (
			'name' 	=> __( 'Single Sidebar', 'largo' ),
			'desc' 	=> __( 'The sidebar for posts and pages', 'largo' ),
			'id' 	=> 'sidebar-single'
		),
		array (
			'name' 	=> __( 'Footer 1', 'largo' ),
			'desc' 	=> __( 'The first footer widget area.', 'largo' ),
			'id' 	=> 'footer-1'
		),
		array (
			'name' 	=> __( 'Footer 2', 'largo' ),
			'desc' 	=> __( 'The second footer widget area.', 'largo' ),
			'id' 	=> 'footer-2'
		),
		array(
			'name' 	=> __( 'Footer 3', 'largo' ),
			'desc' 	=> __( 'The third footer widget area.', 'largo' ),
			'id' 	=> 'footer-3'
		),
		array(
			'name' 	=> __( 'Article Bottom', 'largo' ),
			'desc' 	=> __( 'Footer widget area for posts', 'largo' ),
			'id' 	=> 'article-bottom'
		),
		array(
			'name' 	=> __( 'Homepage Alert', 'largo' ),
			'desc' 	=> __( 'Region atop homepage reserved for breaking news and announcements', 'largo' ),
			'id' 	=> 'homepage-alert'
		),	);

	// optional widget areas
	if ( of_get_option( 'use_topic_sidebar' ) ) {
		$sidebars[] = array(
			'name' 	=> __( 'Archive/Topic Sidebar', 'largo' ),
			'desc' 	=> __( 'The sidebar for category, tag and other archive pages', 'largo' ),
			'id' 	=> 'topic-sidebar'
		);
	}
	if ( of_get_option( 'use_before_footer_sidebar' ) ) {
		$sidebars[] = array(
			'name' 	=> __( 'Before Footer', 'largo' ),
			'desc' 	=> __( 'Full-width area immediately above footer', 'largo' ),
			'id' 	=> 'before-footer'
		);
	}
	if ( of_get_option('footer_layout') == '4col' ) {
		$sidebars[] = array(
			'name' 	=> __( 'Footer 4', 'largo' ),
			'desc' 	=> __( 'The fourth footer widget area.', 'largo' ),
			'id' 	=> 'footer-4'
		);
	}
	if ( of_get_option('homepage_layout') == '3col' ) {
		$sidebars[] = array(
			'name' 	=> __( 'Homepage Left Rail', 'largo' ),
			'desc' 	=> __( 'An optional widget area that, when enabled, appears to the left of the main content area on the homepage.', 'largo' ),
			'id' 	=> 'homepage-left-rail'
		);
	}
	if ( of_get_option('homepage_bottom') == 'widgets' ) {
		$sidebars[] = array(
			'name' 	=> __( 'Homepage Bottom', 'largo' ),
			'desc' 	=> __( 'An optional widget area at the bottom of the homepage', 'largo' ),
			'id' 	=> 'homepage-bottom'
		);
	}

	// @todo - probably add an option to enable this because not everyone is going to have ads here
	// @todo - add additional widget area in the footer for a leaderboard ad unit there too
	$sidebars[] = array(
		'name' 	=> __( 'Header Ad Zone', 'largo'),
		'desc' 	=> __( 'An optional leaderboard ad zone above the main site header', 'largo' ),
		'id' 	=> 'header-ads'
	);

	// user-defined custom widget areas
	$custom_sidebars = preg_split( '/$\R?^/m', of_get_option( 'custom_sidebars' ) );
	if ( is_array( $custom_sidebars ) ) {
		foreach( $custom_sidebars as $sidebar ) {
			$sidebar_slug = largo_make_slug( $sidebar );
			if ( $sidebar_slug ) {
				$sidebars[] = array(
					'name' 	=> __( $sidebar, 'largo' ),
					'desc' 	=> '',
					'id' 	=> $sidebar_slug
				);
			}
		}
	}

	// register the active widget areas
	foreach ( $sidebars as $sidebar ) {
		register_sidebar( array(
			'name' 			=> $sidebar['name'],
			'description' 	=> $sidebar['desc'],
			'id' 			=> $sidebar['id'],
			'before_widget' => '<aside id="%1$s" class="%2$s clearfix">',
			'after_widget' 	=> "</aside>",
			'before_title' 	=> '<h3 class="widgettitle">',
			'after_title' 	=> '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'largo_register_sidebars' );

/**
 * Builds a dropdown menu of the custom sidebars
 * Used in the meta box on post/page edit screen and landing page edit screen
 * $skip_default was deprecated in Largo 0.4
 *
 * @since 0.3
 */
if( !function_exists( 'largo_custom_sidebars_dropdown' ) ) {
	function largo_custom_sidebars_dropdown( $selected='', $skip_default=false, $post_id=NULL ) {
		global $wp_registered_sidebars, $post;

		$the_id = ( $post_id ) ? $post_id:$post->ID;
		$custom = ( $selected ) ? $selected:get_post_meta( $the_id, 'custom_sidebar', true );

		// for the ultimate in backwards compatibility, if nothing's set or using deprecated 'default'
		$default = ( of_get_option( 'single_template' ) == 'classic' ) ? 'sidebar-single' : 'none';
		$val = $default;

		// for new posts
		if ($admin_page->action == 'add')
			$val = 'none';

		// for posts and taxonomies with values set
		if ( $custom && $custom !== 'default' )
			$val = $custom;

		$admin_page = get_current_screen();
		$output = '';
		if ( $admin_page->base == 'post' ) {

			// Add a default option for one column post/page layout (e.g. no sidebar)
			$default_template = of_get_option( 'single_template' );
			$custom_template = get_post_meta( $the_id, '_wp_post_template', true );

			$one_column_layout_test = (
				(
					in_array( $default_template, array( 'normal' ) ) &&
					in_array( $custom_template, array( '', 'single-one-column.php', 'full-page.php' ) )
				) ||
				$custom_template == 'single-one-column.php'
			);

			if ( $one_column_layout_test ) {
				$default_label = __( 'Default (no sidebar)', 'largo' );
			} else {
				// Posts with classic layout should default to single sidebar
				$default_label = sprintf( __('Default (%s)', 'largo'), $wp_registered_sidebars['sidebar-single']['name'] );
			}

			$output .= '<option value="none" ';
			$output .= selected('none', $val, false);
			$output .= '>' . $default_label . '</option>';
		}

		if ( $admin_page->base == 'edit-tags' ) {
			if ( of_get_option( 'use_topic_sidebar' ) && is_active_sidebar( 'topic-sidebar' ) ) {
				$default_label = sprintf( __( 'Default (%s)', 'largo' ), $wp_registered_sidebars['topic-sidebar']['name'] );
			} else {
				$default_label = sprintf( __( 'Default (%s)', 'largo' ), $wp_registered_sidebars['sidebar-main']['name'] );
			}
			$output .= '<option value="none" ';
			$output .= selected('none', $val, false);
			$output .= '>' . $default_label . '</option>';
		}

		// Filter list of sidebars to exclude those we don't want users to choose
		$excluded = largo_get_excluded_sidebars();

		// Fill the select element with all registered sidebars that are custom
		foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
			if ( in_array( $sidebar_id, $excluded ) || in_array( $sidebar['name'], $excluded ) )
				continue;

			$output .= '<option value="' . $sidebar_id . '" ' . selected( $sidebar_id, $val, false ) . '>' . $sidebar['name'] . '</option>';
		}

		echo $output;
	}
}

/**
 * Returns sidebars that users should not be able to select for post, page and taxonomy layouts
 */
function largo_get_excluded_sidebars() {
	$excluded = array(
		'Footer 1',
		'Footer 2',
		'Footer 3',
		'Article Bottom',
		'Header Ad Zone',
		'Homepage Alert'
	);
	// Let others change the list
	$excluded = apply_filters( 'largo_excluded_sidebars', $excluded );
	return $excluded;
}

/**
 * Returns slug of custom sidebar that should be used
 */
function largo_get_custom_sidebar() {
	$custom_sidebar = false;

	if ( is_singular() ) {
		$custom_sidebar = get_post_meta( get_the_ID(), 'custom_sidebar', true) ;
		if (in_array($custom_sidebar, array('', 'default')))
			$custom_sidebar = 'none';
	} else if ( is_archive() ) {
		$term = get_queried_object();
		$custom_sidebar = largo_get_term_meta(
			$term->taxonomy, $term->term_id, 'custom_sidebar', true);
	}

	return $custom_sidebar;
}

/**
 * Determines if is_single or is_singular context requires a sidebar
 */
function largo_is_sidebar_required() {
	global $post;

	$default_template = of_get_option(' single_template' );
	$custom_template = get_post_meta( $post->ID, '_wp_post_template', true );
	$custom_sidebar = largo_get_custom_sidebar();

	$two_column_layout_test_forced = ( $custom_template == 'single-two-column.php' );
	$two_column_layout_test = (
		$default_template == 'classic' &&
		in_array($custom_template, array('', 'single-two-column.php'))
	);
	$one_column_layout_test = (
		in_array( $default_template, array( 'normal', 'classic' ) ) &&
		in_array( $custom_template, array( '', 'single-one-column.php' ) ) &&
		$custom_sidebar !== 'none'
	);

	return ( $two_column_layout_test || $two_column_layout_test_forced || $one_column_layout_test );
}

/**
 * Render the widget setting fields on the widget page
 */
function largo_widget_settings() {
	?>
 		<div class="advance-widget-settings">
 			<div class="advance-widget-settings-title"><?php _e( 'Largo Sidebar Options', 'largo' ); ?></div>
			<div id="optionsframework-metabox" class="metabox-holder">
			    <div id="optionsframework" class="postbox">
					<form action="options.php" method="post">
						<div> <?php // Extra open <div> because optinosframework_fields() adds an extra closing </div> ?>
					<?php
					// Prints hidden tags for WP's options.php to save the value
					settings_fields('optionsframework');

					// Print all the currently saved values for those fields that aren't outputted
					$options_to_show = optionsframework_options();
					$config = get_option( 'optionsframework', array() );
					// Gets the unique option id
					if ( isset( $config['id'] ) ) {
						$option_name = $config['id'];
					}
					else {
						$option_name = 'optionsframework';
					};

					$current_values = get_option( $option_name, array() );

					foreach ( $options_to_show as $key => $field ) {
						if ( isset($field['id']) && isset($current_values[$field['id']]) ) {
							unset( $current_values[$field['id']] );
						}
					}

					foreach ( $current_values as $key => $val ) {
						echo '<input type="hidden" name="', esc_attr( $option_name . '[' . $key . ']'), '" value="', esc_attr( $val ) ,'" />';
					}

					// Prints the fields
					optionsframework_fields(); /* Settings */ ?>
					<div id="optionsframework-submit">
						<input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options', 'largo' ); ?>" />
						<input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults', 'largo' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!', 'largo' ) ); ?>' );" />
						<br class="clear" />
					</div>
					</form>
				</div>
			</div>
		</div>
	<?php
}
add_action( 'widgets_admin_page', 'largo_widget_settings' );

/**
 * Load up the scripts for options framework on the widgets
 */
function largo_load_of_script_for_widget( $hook ) {

	if ( $hook == 'widgets.php' ) {
		optionsframework_load_scripts( 'appearance_page_options-framework' );
		optionsframework_load_styles();
		wp_enqueue_style( 'largo-widgets-php', get_template_directory_uri() . '/css/widgets-php.css');
	}
}
add_action('admin_enqueue_scripts', 'largo_load_of_script_for_widget');
