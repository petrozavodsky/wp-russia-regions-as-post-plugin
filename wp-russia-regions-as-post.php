<?php
/**
 * Plugin Name: wp russia regions as post
 * Description: Show a map of Russian regions
 * Version: 1.0.0
 * Author: petrozavodsky, anatolykulikov, soulseekah
 * Author: http
 * Text Domain: wp_russia_regions_as_post
 * License: GPL-3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

if ( ! defined( 'WP_RUSSIA_REGIONS_AS_POST_POST_TYPE' ) ) {
	define( 'WP_RUSSIA_REGIONS_AS_POST_POST_TYPE', 'region' );
}

if ( ! defined( 'WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_CODE' ) ) {
	define( 'WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_CODE', '_region_code' );
}

if ( ! defined( 'WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_REGION_NUMBER' ) ) {
	define( 'WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_REGION_NUMBER', '_region_number' );
}

if ( ! defined( 'WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN' ) ) {
	define( 'WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN', 'wp_russia_regions_as_post' );
}

add_action( 'plugins_loaded', 'wp_russia_regions_as_post_textdomain' );

function wp_russia_regions_as_post_textdomain() {
	load_plugin_textdomain(
		WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN,
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages/'
	);
}

// Использовать функцию только внутри хука init
add_action( 'init', 'wp_russia_regions_as_post_register' );

function wp_russia_regions_as_post_register() {
	$labels = array(
		'name'               => __( 'Regions', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'singular_name'      => __( 'Region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'add_new'            => __( 'Add region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'add_new_item'       => __( 'Add region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'edit_item'          => __( 'To edit the region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'new_item'           => __( 'New region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'all_items'          => __( 'All region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'view_item'          => __( 'View on site', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'search_items'       => __( 'Search region', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'not_found'          => __( 'Nothing', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'not_found_in_trash' => __( 'Nothing', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'menu_name'          => __( 'Regions', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN )
	);
	$args   = array(
		'labels'              => $labels,
		'description'         => __( 'Subjects of Russia', WP_RUSSIA_REGIONS_AS_POST_POST_TEXTDOMAIN ),
		'public'              => true,
		// показывать интерфейс в админке
		'show_ui'             => true,
		'show_in_rest'        => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'hierarchical'        => true,
		'capability_type'     => 'page',
		// иконка в меню
		'menu_icon'           => 'dashicons-location-alt',
		// порядок в меню
		'menu_position'       => 30,
		'supports'            => array( 'title', 'editor', 'page-attributes', 'custom-fields' ),
		'rewrite'             => true,
		'show_admin_column'   => true,
		'query_var'           => true,
		'can_export'          => true
	);
	register_post_type( WP_RUSSIA_REGIONS_AS_POST_POST_TYPE, $args );
}

add_shortcode( 'wp_russia_regions_map', 'wp_russia_regions_as_post_add_map' );

function wp_russia_regions_as_post_add_map( $attrs ) {

	$attrs = shortcode_atts(
		array(
			'region_numbers_highlight' => false,
		),
		$attrs
	);

	$args = array(
		'post_type'      => WP_RUSSIA_REGIONS_AS_POST_POST_TYPE,
		'posts_per_page' => - 1
	);

	$posts = get_posts( $args );
	ob_start();

	if ( ! empty( $posts ) ): ?>
		<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
        <svg version="1.1"
             class="wp_russia_regions_as_post-csv"
             xmlns="http://www.w3.org/2000/svg"
             xmlns:xlink="http://www.w3.org/1999/xlink"
             x="0px"
             y="0px"
             viewBox="0 0 1200 682.9"
             style="enable-background:new 0 0 1200 682.9;"
             xml:space="preserve">
            <g transform="scale(0.68) translate(0, 0)">
	            <?php
	            foreach ( $posts as $post ): ?>
		            <?php
		            $class = '';
		            if ( ! empty( $attrs['region_numbers_highlight'] ) ) {
			            $region_number = get_post_meta( $post->ID, '_region_number', false );
			            if ( in_array( $attrs['region_numbers_highlight'], $region_number ) ) {
				            $class = 'active';
			            }
		            }
		            ?>
                    <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" class="<?php echo $class; ?>">
			            <?php echo wp_russia_regions_as_post_svg_kses(
				            get_post_meta( $post->ID, WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_CODE, true )
			            ); ?>
                    </a>
	            <?php endforeach; ?>
	            <?php wp_reset_postdata(); ?>
            </g>
        </svg>
	<?php endif;

	return ob_get_clean();
}

add_action( 'wp_footer', 'wp_russia_regions_as_post_add_css' );

function wp_russia_regions_as_post_add_css() {

	if ( is_singular() ) {
		$post = get_post( get_queried_object_id() );
		if ( has_shortcode( $post->post_content, 'wp_russia_regions_map' ) ) {

			wp_enqueue_style(
				'wp_russia_regions_as_post_add_map_css',
				plugin_dir_url( __FILE__ ) . "public/css/shortcode-style.css",
				[],
				'1.0.0'
			);
		}
	}
}

function wp_russia_regions_as_post_svg_kses( $string ) {
	return wp_kses(
		$string,
		array(
			'path' => array(
				'id'    => true,
				'class' => true,
				'd'     => true,
			)
		)
	);
}

add_filter( 'the_content', 'wp_russia_regions_as_post_add_region_map' );

function wp_russia_regions_as_post_add_region_map( $content ) {

	if ( is_singular( WP_RUSSIA_REGIONS_AS_POST_POST_TYPE ) ) {
		add_action( 'wp_footer', 'wp_russia_regions_as_post_add_region_map_css' );
		$post          = get_post( get_queried_object_id() );
		$region_number = get_post_meta(
			get_queried_object_id(),
			WP_RUSSIA_REGIONS_AS_POST_POST_META_KEY_REGION_NUMBER,
			false
		);
		$first_number  = false;

		if ( is_array( $region_number ) && ! empty( $region_number ) ) {
			$first_number = array_shift( $region_number );
		}

		$map = wp_russia_regions_as_post_add_map( [
			'region_numbers_highlight' => $first_number
		] );

		return apply_filters( 'wp_russia_regions_as_post_add_region_map', $content . PHP_EOL . $map, $content, $map );
	}

	return $content;
}

function wp_russia_regions_as_post_add_region_map_css() {
	wp_enqueue_style(
		'wp_russia_regions_as_post_add_map_css',
		plugin_dir_url( __FILE__ ) . "public/css/shortcode-style.css",
		[],
		'1.0.0'
	);
}
