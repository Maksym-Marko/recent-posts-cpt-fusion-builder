<?php
/**
 * Plugin Name: Recent Posts CPT Fusion Builder Addon
 * Plugin URI: https://github.com/Maxim-us/recent-posts-cpt-fusion-builder
 * Description: Adds Recent Posts CPT for fusion builder.
 * Version: 1.0
 * Author: Marko Maksym
 * Author URI: http://markomaksym.com.ua/
 *
 * @package Recent Posts CPT Fusion Builder Addon
 */

// Plugin Folder Path.
if ( ! defined( 'RECENT_POSTS_CPT_ADDON_PLUGIN_DIR' ) ) {
	define( 'RECENT_POSTS_CPT_ADDON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! class_exists( 'Mx_Recent_Posts_CPT_Addon_FB' ) ) {

	// Include the main plugin class.
	include_once wp_normalize_path( RECENT_POSTS_CPT_ADDON_PLUGIN_DIR . '/inc/class-recent-posts-cpt-fb.php' );

	register_activation_hook( __FILE__, array( 'Mx_Recent_Posts_CPT_Addon_FB', 'activation' ) );

	/**
	 * Instantiate Mx_Recent_Posts_CPT_Addon_FB class.
	 */
	function sample_addon_activate() {
		Mx_Recent_Posts_CPT_Addon_FB::get_instance();
	}

	add_action( 'wp_loaded', 'sample_addon_activate', 10 );


	/**
	 * Map shortcode to Fusion Builder.
	 *
	 * @since 1.0
	 */
	function map_recent_posts_cpt_addon_with_fb() {

		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$post_types = get_post_types( NULL, 'objects' );

		$array_post_types = array();

		foreach( $post_types as $key => $value ) :

			$array_post_types[$value->name] = $value->label;

		endforeach;

		// Map settings for parent shortcode.
		fusion_builder_map(
			array(
					'name'       => esc_attr__( 'Recent Posts CPT', 'fusion-builder' ),
					'shortcode'  => 'mx_fusion_recent_posts_cpt',
					'icon'       => 'fusiona-feather',
					'preview'    => RECENT_POSTS_CPT_ADDON_PLUGIN_DIR . '/js/preview/fusion-recent-posts-preview.php',
					'preview_id' => 'fusion-builder-block-module-recent-posts-cpt-preview-template',
					'help_url'   => 'https://theme-fusion.com/documentation/fusion-builder/elements/recent-posts-element/',
					'params'     => [
						[
							'type'        => 'textfield',
							'heading'     => esc_attr__( 'Post Type Slug', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Post Type Slug', 'fusion-builder' ),
							'description' => esc_attr__( 'Add a post type slug.', 'fusion-builder' ),
							'param_name'  => 'post_type_cpt',
							'default'       => 'post',
							'value'       => 'post',
							'group'       => esc_attr__( 'General', 'fusion-builder' ),
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Layout', 'fusion-builder' ),
							'description' => esc_attr__( 'Select the layout for the element.', 'fusion-builder' ),
							'param_name'  => 'layout',
							'value'       => [
								'default'            => esc_attr__( 'Standard', 'fusion-builder' ),
								'thumbnails-on-side' => esc_attr__( 'Thumbnails on Side', 'fusion-builder' ),
								'date-on-side'       => esc_attr__( 'Date on Side', 'fusion-builder' ),
							],
							'default'     => 'default',
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Picture Size', 'fusion-builder' ),
							'description' => __( 'Fixed = width and height will be fixed.<br/>Auto = width and height will adjust to the image.<br/>', 'fusion-builder' ),
							'param_name'  => 'picture_size',
							'default'     => 'fixed',
							'value'       => [
								'fixed' => esc_attr__( 'Fixed', 'fusion-builder' ),
								'auto'  => esc_attr__( 'Auto', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'layout',
									'value'    => 'date-on-side',
									'operator' => '!=',
								],
								[
									'element'  => 'thumbnail',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Hover Type', 'fusion-builder' ),
							'description' => esc_attr__( 'Select the hover effect type.', 'fusion-builder' ),
							'param_name'  => 'hover_type',
							'value'       => [
								'none'    => esc_attr__( 'None', 'fusion-builder' ),
								'zoomin'  => esc_attr__( 'Zoom In', 'fusion-builder' ),
								'zoomout' => esc_attr__( 'Zoom Out', 'fusion-builder' ),
								'liftup'  => esc_attr__( 'Lift Up', 'fusion-builder' ),
							],
							'default'     => 'none',
							'preview'     => [
								'selector' => '.fusion-flexslider>.slides>li>a',
								'type'     => 'class',
								'toggle'   => 'hover',
							],
							'dependency'  => [
								[
									'element'  => 'layout',
									'value'    => 'date-on-side',
									'operator' => '!=',
								],
								[
									'element'  => 'thumbnail',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'range',
							'heading'     => esc_attr__( 'Number of Columns', 'fusion-builder' ),
							'description' => esc_attr__( 'Select the number of columns to display.', 'fusion-builder' ),
							'param_name'  => 'columns',
							'value'       => '3',
							'min'         => '1',
							'max'         => '6',
							'step'        => '1',
						],
						[
							'type'        => 'range',
							'heading'     => esc_attr__( 'Posts Per Page', 'fusion-builder' ),
							'description' => esc_attr__( 'Select number of posts per page.  Set to -1 to display all.', 'fusion-builder' ),
							'param_name'  => 'number_posts',
							'value'       => '6',
							'min'         => '-1',
							'max'         => '25',
							'step'        => '1',
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'multiple_select',
							'heading'     => esc_attr__( 'Post Status', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Post Status', 'fusion-builder' ),
							'description' => esc_attr__( 'Select the status(es) of the posts that should be included or leave blank for published only posts.', 'fusion-builder' ),
							'param_name'  => 'post_status',
							'value'       => [
								'publish' => esc_attr__( 'Published' ),
								'draft'   => esc_attr__( 'Drafted' ),
								'future'  => esc_attr__( 'Scheduled' ),
								'private' => esc_attr__( 'Private' ),
								'pending' => esc_attr__( 'Pending' ),
							],
							'default'     => '',
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'range',
							'heading'     => esc_attr__( 'Post Offset', 'fusion-builder' ),
							'description' => esc_attr__( 'The number of posts to skip. ex: 1.', 'fusion-builder' ),
							'param_name'  => 'offset',
							'value'       => '0',
							'min'         => '0',
							'max'         => '25',
							'step'        => '1',
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Pull Posts By', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show posts by category or tag.', 'fusion-builder' ),
							'param_name'  => 'pull_by',
							'default'     => 'category',
							'value'       => [
								'category' => esc_attr__( 'Category', 'fusion-builder' ),
								'tag'      => esc_attr__( 'Tag', 'fusion-builder' ),
							],
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'multiple_select',
							'heading'     => esc_attr__( 'Categories', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Categories', 'fusion-builder' ),
							'description' => esc_attr__( 'Select a category or leave blank for all.', 'fusion-builder' ),
							'param_name'  => 'cat_slug',
							'value'       => $builder_status ? fusion_builder_shortcodes_categories( 'category' ) : [],
							'default'     => '',
							'dependency'  => [
								[
									'element'  => 'pull_by',
									'value'    => 'tag',
									'operator' => '!=',
								],
							],
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'multiple_select',
							'heading'     => esc_attr__( 'Exclude Categories', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Categories', 'fusion-builder' ),
							'description' => esc_attr__( 'Select a category to exclude.', 'fusion-builder' ),
							'param_name'  => 'exclude_cats',
							'value'       => $builder_status ? fusion_builder_shortcodes_categories( 'category' ) : [],
							'default'     => '',
							'dependency'  => [
								[
									'element'  => 'pull_by',
									'value'    => 'tag',
									'operator' => '!=',
								],
							],
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'multiple_select',
							'heading'     => esc_attr__( 'Tags', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Tags', 'fusion-builder' ),
							'description' => esc_attr__( 'Select a tag or leave blank for all.', 'fusion-builder' ),
							'param_name'  => 'tag_slug',
							'value'       => $builder_status ? fusion_builder_shortcodes_tags( 'post_tag' ) : [],
							'default'     => '',
							'dependency'  => [
								[
									'element'  => 'pull_by',
									'value'    => 'category',
									'operator' => '!=',
								],
							],
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'multiple_select',
							'heading'     => esc_attr__( 'Exclude Tags', 'fusion-builder' ),
							'placeholder' => esc_attr__( 'Tags', 'fusion-builder' ),
							'description' => esc_attr__( 'Select a tag to exclude.', 'fusion-builder' ),
							'param_name'  => 'exclude_tags',
							'value'       => $builder_status ? fusion_builder_shortcodes_tags( 'post_tag' ) : [],
							'default'     => '',
							'dependency'  => [
								[
									'element'  => 'pull_by',
									'value'    => 'category',
									'operator' => '!=',
								],
							],
							'callback'    => [
								'function' => 'fusion_ajax',
								'action'   => 'get_fusion_recent_posts_cpt',
								'ajax'     => true,
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Thumbnail', 'fusion-builder' ),
							'description' => esc_attr__( 'Display the post featured image.', 'fusion-builder' ),
							'param_name'  => 'thumbnail',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'default'     => 'yes',
							'dependency'  => [
								[
									'element'  => 'layout',
									'value'    => 'date-on-side',
									'operator' => '!=',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Title', 'fusion-builder' ),
							'description' => esc_attr__( 'Display the post title below the featured image.', 'fusion-builder' ),
							'param_name'  => 'title',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'default'     => 'yes',
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Meta', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show all meta data.', 'fusion-builder' ),
							'param_name'  => 'meta',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'default'     => 'yes',
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Author Name', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show the author.', 'fusion-builder' ),
							'param_name'  => 'meta_author',
							'default'     => 'no',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'meta',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Categories', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show the categories.', 'fusion-builder' ),
							'param_name'  => 'meta_categories',
							'default'     => 'no',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'meta',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Date', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show the date.', 'fusion-builder' ),
							'param_name'  => 'meta_date',
							'default'     => 'yes',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'meta',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Comment Count', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show the comments.', 'fusion-builder' ),
							'param_name'  => 'meta_comments',
							'default'     => 'yes',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'meta',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Show Tags', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to show the tags.', 'fusion-builder' ),
							'param_name'  => 'meta_tags',
							'default'     => 'no',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'meta',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Content Alignment', 'fusion-builder' ),
							'description' => esc_attr__( 'Select the alignment of contents.', 'fusion-builder' ),
							'param_name'  => 'content_alignment',
							'default'     => '',
							'value'       => [
								''       => esc_attr__( 'Text Flow', 'fusion-builder' ),
								'left'   => esc_attr__( 'Left', 'fusion-builder' ),
								'center' => esc_attr__( 'Center', 'fusion-builder' ),
								'right'  => esc_attr__( 'Right', 'fusion-builder' ),
							],
							'dependency'  => [
								[
									'element'  => 'layout',
									'value'    => 'default',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Text display', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose to display the post excerpt.', 'fusion-builder' ),
							'param_name'  => 'excerpt',
							'value'       => [
								'yes'  => esc_attr__( 'Excerpt', 'fusion-builder' ),
								'full' => esc_attr__( 'Full Content', 'fusion-builder' ),
								'no'   => esc_attr__( 'None', 'fusion-builder' ),
							],
							'default'     => 'yes',
						],
						[
							'type'        => 'range',
							'heading'     => esc_attr__( 'Excerpt Length', 'fusion-builder' ),
							'description' => esc_attr__( 'Insert the number of words/characters you want to show in the excerpt.', 'fusion-builder' ),
							'param_name'  => 'excerpt_length',
							'value'       => '35',
							'min'         => '0',
							'max'         => '500',
							'step'        => '1',
							'dependency'  => [
								[
									'element'  => 'excerpt',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Strip HTML', 'fusion-builder' ),
							'description' => esc_attr__( 'Strip HTML from the post excerpt.', 'fusion-builder' ),
							'param_name'  => 'strip_html',
							'value'       => [
								'yes' => esc_attr__( 'Yes', 'fusion-builder' ),
								'no'  => esc_attr__( 'No', 'fusion-builder' ),
							],
							'default'     => 'yes',
							'dependency'  => [
								[
									'element'  => 'excerpt',
									'value'    => 'yes',
									'operator' => '==',
								],
							],
						],
						[
							'type'        => 'radio_button_set',
							'heading'     => esc_attr__( 'Pagination Type', 'fusion-builder' ),
							'description' => esc_attr__( 'Choose the type of pagination.', 'fusion-builder' ),
							'param_name'  => 'scrolling',
							'default'     => 'no',
							'value'       => [
								'no'               => esc_attr__( 'No Pagination', 'fusion-builder' ),
								'pagination'       => esc_attr__( 'Pagination', 'fusion-builder' ),
								'infinite'         => esc_attr__( 'Infinite Scrolling', 'fusion-builder' ),
								'load_more_button' => esc_attr__( 'Load More Button', 'fusion-builder' ),
							],
						],
						'fusion_animation_placeholder' => [
							'preview_selector' => '.fusion-column',
						],
						[
							'type'        => 'checkbox_button_set',
							'heading'     => esc_attr__( 'Element Visibility', 'fusion-builder' ),
							'param_name'  => 'hide_on_mobile',
							'value'       => fusion_builder_visibility_options( 'full' ),
							'default'     => fusion_builder_default_visibility( 'array' ),
							'description' => esc_attr__( 'Choose to show or hide the element on small, medium or large screens. You can choose more than one at a time.', 'fusion-builder' ),
						],
						[
							'type'        => 'textfield',
							'heading'     => esc_attr__( 'CSS Class', 'fusion-builder' ),
							'description' => esc_attr__( 'Add a class to the wrapping HTML element.', 'fusion-builder' ),
							'param_name'  => 'class',
							'value'       => '',
							'group'       => esc_attr__( 'General', 'fusion-builder' ),
						],
						[
							'type'        => 'textfield',
							'heading'     => esc_attr__( 'CSS ID', 'fusion-builder' ),
							'description' => esc_attr__( 'Add an ID to the wrapping HTML element.', 'fusion-builder' ),
							'param_name'  => 'id',
							'value'       => '',
							'group'       => esc_attr__( 'General', 'fusion-builder' ),
						],
					],
					'callback'   => [
						'function' => 'fusion_ajax',
						'action'   => 'get_fusion_recent_posts_cpt',
						'ajax'     => true,
					],

			)
		);

	}

	add_action( 'fusion_builder_before_init', 'map_recent_posts_cpt_addon_with_fb', 111 );


}