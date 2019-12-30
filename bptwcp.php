<?php
/**
 * Plugin Name: Blog Posts to WooCommerce Product Categories
 * Plugin URI:
 * Description: Add a list of relevant blog posts to product category archives in WooCommerce
 * Version: 1.0
 * Author: Ido Barnea
 * Author URI: https://www.barbareshet.co.il
 * Textdomain: bptwcp
 *
 * based on @link https://code.tutsplus.com/tutorials/create-a-blog-for-each-category-or-department-in-your-woocommerce-store--cms-26154
 **/

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'BPTWCP_VERSION', '1.0.0' );
define( 'BPTWCP_TEXTDOMAIN', 'bptwcp' );



function bptwcp_check_woocommerce_active() {

	// check if woocommerce is activated
	if ( class_exists( 'woocommerce' ) ) {

		add_action( 'init', 'bptwcp_register_productcats_posts' );
		add_filter( 'woocommerce_taxonomy_args_product_cat', 'bptwcp_change_productcat_label' );


	}

	else {
		return;
	}

}
add_action( 'plugins_loaded', 'bptwcp_check_woocommerce_active' );


function bptwcp_register_productcats_posts() {

	register_taxonomy_for_object_type( 'product_cat', 'post' );

}


function bptwcp_change_productcat_label( $args ) {

	$args['labels'] = array(
		'label'         => _x( 'Product Categories', 'bptwcp' ),
		'menu_name'     => _x( 'Product Categories', 'Admin menu name', 'bptwcp' ),
		'add_new_item'  => _x( 'Add new product category', 'bptwcp' ),
	);

	return $args;

}

function bptwcp_display_posts_in_category_archives() {
	if ( is_product_category() ) {

		$productcat = get_queried_object();
		$cat = $productcat->name;
		$args = array(
			'post_type' => 'post',
			'product_cat' => $productcat->slug,
			'posts_per_page' => 5
		);
		$query = new WP_query ( $args );
		if ( $query->have_posts() ) { ?>

			<section class="product-cat-posts">
				<h2><?php echo _x($cat . ' Related Blog Posts', BPTWCP_TEXTDOMAIN);?></h2>

				<?php while ( $query->have_posts() ) : $query->the_post(); ?>

					<?php //contents of loop ?>
					<article id="post-<?php the_ID(); ?>"<?php post_class(); ?>>
				    <?php if ( has_post_thumbnail() ){ ?>
                        <div class="post-thumbnail-wrap">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium');?>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="post-excerpt-wrap">
                        <h3>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
	                    <?php the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>"> Read More...</a>
                    </div>

					</article>

				<?php endwhile; ?>

				<?php rewind_posts(); ?>

			</section>

		<?php }

	}
}
add_action( 'woocommerce_after_shop_loop', 'bptwcp_display_posts_in_category_archives' );