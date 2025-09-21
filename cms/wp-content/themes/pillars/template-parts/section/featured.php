<?php
$columns	= esc_attr(wc_get_loop_prop('columns'));
$html		= do_shortcode('[featured_products]');
$html		= strtr($html, array(
	'<div class="woocommerce columns-' . $columns . ' ">'	=> '',
	'products-columns-' . $columns			=> 'product-slider__wrapper swiper-wrapper',
	'<div class="product '					=> '<div class="product-slider__slide swiper-slide '
));
$html = mb_strrchr($html, '</div>', true);
?>

<div class="product-slider">
	<div class="product-slider__container swiper-container">
		<?= $html ?>
		<div class="pillars-slider__navigations">
			<div class="pillars-slider__pagination"></div>
			<div class="pillars-slider__buttons">
				<div class="pillars-slider__button-prev"></div>
				<div class="pillars-slider__button-next"></div>
			</div>
		</div>
	</div>
</div>