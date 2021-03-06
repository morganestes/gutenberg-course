<?php
/**
 * Plugin Name: Gutenberg - Block Examples
 * Plugin URI: https://gutenberg.courses
 * Description: An plugin containing example blocks for developers.  From <a href="https://gutenberg.courses">Zac
 * Gordon's Gutenberg Course</a>. Author: Zac Gordon Author URI: https://zacgordon.com Version: 1.0.0 License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package JSFORWPBLOCKS
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Filter args for post types.
 *
 * @param array  $args      Post type args.
 * @param string $post_type The post type.
 *
 * @return array The (maybe filtered) post type args.
 */
function jsforwpblocks_templates( $args, $post_type ) {

	if ( 'post' === $post_type ) {
		$args['template_lock'] = true;
		$args['template']      = [
			[
				'core/image',
				[
					'align' => 'left',
				],
			],
			[
				'core/paragraph',
				[
					'placeholder' => 'The only thing you can add',
				],
			],
		];
	}

	return $args;
}

add_filter( 'register_post_type_args', 'jsforwpblocks_templates', 20, 2 );

/**
 * Enqueue block editor only JavaScript and CSS.
 */
function jsforwpblocks_editor_scripts() {
	$block_path        = '/assets/js/editor.blocks.js';
	$editor_style_path = '/assets/css/blocks.editor.css';

	// Enqueue the bundled block JS file.
	wp_enqueue_script(
		'jsforwp-blocks-js',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path )
	);

	// Pass in REST URL.
	wp_localize_script(
		'jsforwp-blocks-js',
		'jsforwp_globals',
		[
			'rest_url' => esc_url( rest_url() ),
		]
	);

	// Enqueue optional editor-only styles.
	wp_enqueue_style(
		'jsforwp-blocks-editor-css',
		plugins_url( $editor_style_path, __FILE__ ),
		[ 'wp-blocks' ],
		filemtime( plugin_dir_path( __FILE__ ) . $editor_style_path )
	);

}

// Hook scripts function into block editor hook.
add_action( 'enqueue_block_editor_assets', 'jsforwpblocks_editor_scripts' );


/**
 * Enqueue front end and editor JavaScript and CSS.
 */
function jsforwpblocks_scripts() {
	$block_path = '/assets/js/frontend.blocks.js';
	$style_path = '/assets/css/blocks.style.css';

	// Enqueue the bundled block JS file.
	wp_enqueue_script(
		'jsforwp-blocks-frontend-js',
		plugins_url( $block_path, __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $block_path )
	);

	// Enqueue frontend and editor block styles.
	wp_enqueue_style(
		'jsforwp-blocks-css',
		plugins_url( $style_path, __FILE__ ),
		[ 'wp-blocks' ],
		filemtime( plugin_dir_path( __FILE__ ) . $style_path )
	);

}

// Hook scripts function into block editor hook.
add_action( 'enqueue_block_assets', 'jsforwpblocks_scripts' );

/**
 * Server rendering for /blocks/examples/12-dynamic.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The block markup.
 */
function jsforwp_dynamic_block_render( $attributes ) {

	$recent_posts = wp_get_recent_posts(
		[
			'numberposts' => 1,
			'post_status' => 'publish',
		]
	);
	if ( count( $recent_posts ) === 0 ) {
		return 'No posts';
	}
	$post    = $recent_posts[0];
	$post_id = $post['ID'];

	return sprintf(
		'<p><a class="wp-block-my-plugin-latest-post" href="%1$s">%2$s</a></p>',
		esc_url( get_permalink( $post_id ) ),
		esc_html( get_the_title( $post_id ) )
	);

}

// Hook server side rendering into render callback.
register_block_type(
	'jsforwp/dynamic', [
		'render_callback' => 'jsforwp_dynamic_block_render',
	]
);


/**
 * Server rendering for /blocks/examples/13-dynamic-lat.
 *
 * @param array $attributes The block attributes.
 *
 * @return string The block markup.
 */
function jsforwp_dynamic_alt_block_render( $attributes ) {

	$posts = wp_get_recent_posts(
		[
			'numberposts' => 5,
			'post_status' => 'publish',
		]
	);

	if ( count( $posts ) === 0 ) {
		return 'No posts';
	}

	$markup = '<ul>';
	foreach ( $posts as $post ) {

		$markup .= sprintf(
			'<li><a class="wp-block-my-plugin-latest-post" href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $post['ID'] ) ),
			esc_html( get_the_title( $post['ID'] ) )
		);

	}

	return $markup;
}

// Hook server side rendering into render callback.
register_block_type(
	'jsforwp/dynamic-alt', [
		'render_callback' => 'jsforwp_dynamic_alt_block_render',
	]
);
