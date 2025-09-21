<?php

/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');

?>
<section>
	<div class="container">
		<div data-header class="row">
			<div class="col-12">
				<div class="block">
					<?php
					/**
					 * Hook: woocommerce_archive_description.
					 *
					 * @hooked pillars_wc_archive_title_header - 5
					 * @hooked pillars_wc_archive_description_header - 10
					 * @hooked woocommerce_taxonomy_archive_description - 10 --> remove
					 * @hooked woocommerce_product_archive_description - 10 --> remove on
					 */
					do_action('woocommerce_archive_description');
					?>
				</div>
			</div>
		</div>

		<?php

		if (woocommerce_product_loop()) {

			$term = get_term_by('name', woocommerce_page_title(false), 'product_cat');

			if ($term) {
				$tabs	= pillars_wc_get_categories_tabs($term->term_id);
				$groups	= get_term_meta($term->term_id, '_pillars_group_products', true);
				$filter = true;

				if ($groups && count($tabs) < 2) {
					$tabs[$term->term_id] = array(
						'title'			=> get_term_meta($term->term_id, '_pillars_tab_title_long', true),
						'short'			=> get_term_meta($term->term_id, '_pillars_tab_title_short', true),
						'redirect'		=> get_term_meta($term->term_id, '_pillars_tab_title_redirect', true),
						'hide_title'	=> true
					);
					$filter = false;
				}

				if (count($tabs) > 1) {
					pillars_wc_get_categories_list_filter_by_grid($tabs, $groups, $filter);
					/*
					if ($term->term_id == 253) {
						pillars_wc_get_categories_list_filter_by_grid($tabs, $groups, $filter);
					} else {
						pillars_wc_get_categories_list_filter($tabs, $groups, $filter);
					}
					*/
				} else {
					wc_get_template('archive-product/no-filters.php');
				}
			} elseif (is_shop()) {
				wc_get_template('archive-product/catalog.php');
			} else {
				wc_get_template('archive-product/shop.php');
			}
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action('woocommerce_no_products_found');
		}
		?>
	</div>
</section>
<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
// do_action('woocommerce_sidebar');

get_footer('shop');
