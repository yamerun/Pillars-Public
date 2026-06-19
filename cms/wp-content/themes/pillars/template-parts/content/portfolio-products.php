<?php

defined('ABSPATH') || exit;

if (isset($args['products']) && $args['products']) :
	$replace = array(
		'<div class="product '	=> '<div class="product-slider__slide swiper-slide '
	);
?>
	<section class="related-products p-untop p-unbottom">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h2>Изделия которые мы использовали в этом проекте</h2>
						<div class="product-slider">
							<div class="product-slider__container swiper-container">
								<div class="product-slider__wrapper swiper-wrapper">
									<?php foreach ($args['products'] as $product) {
										$post_object = get_post($product);
										setup_postdata($GLOBALS['post'] = &$post_object); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
										ob_start();
										wc_get_template_part('content', 'product');
										echo strtr(ob_get_clean(), $replace);
									} ?>
								</div>
								<div class="pillars-slider__navigations">
									<div class="pillars-slider__pagination"></div>
									<div class="pillars-slider__buttons">
										<div class="pillars-slider__button-prev"></div>
										<div class="pillars-slider__button-next"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
endif;

wp_reset_postdata();
