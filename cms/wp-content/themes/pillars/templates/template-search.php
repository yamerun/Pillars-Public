<?php

/**
 * Template Name: Поиск
 */

?>

<?php get_header(); ?>

<?php
$products	= [];
$term		= '';

if ($_POST) {

	/* Check wp nonce */
	$security = false;
	if (isset($_POST['search_verify_key']) && sanitize_key($_POST['search_verify_key']) != '') {
		if (wp_verify_nonce(sanitize_key($_POST['search_verify_key']), 'search_verify_action') == 1) {
			$security = true;
		}
		unset($_POST['search_verify_key']);
	}

	/**
	 * Add security for spam begin
	 */
	if ($security) {
		/* Validation of data */
		$term = sanitize_text_field($_POST['search']);
		$errors = ($term) ? false : true;
		if ($term) {
			$products = theplugin_wc_product_search_ids_by_indexing($term);
			if ($products['maybe']['base']) {
				if (!$products['maybe']['variation']) {
					$result = $products['variation'];
				} elseif ($products['base']) {
					$result = $products['base'];
				} else {
					$result = $products['variation'];
				}
			} else {
				$result = $products['base'];
			}

			$products = $result;
		}

		/* Send data --> end */
	} /* Add security for spam end */
}
?>

<section class="page p-unbottom" id="post-<?= get_the_ID() ?>">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h1><?php the_title(); ?></h1>
					<?php if ($term) { ?><h5>Результаты поиска по: <b><?= $term ?></b></h5><?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="p-untop">
	<div class="container">
		<?php if ($products) { ?>
			<div class="row">
				<div class="col-12">
					<div class="block">
						<div class="woocommerce-message">Найдено: <?= count($products) ?></div>
					</div>
				</div>
			</div>
			<div class="products-columns-4">
				<?php foreach ($products as $id) {
					$post		= get_post($id);
					$product	= wc_get_product($id);
					wc_get_template('content-product.php');
				} ?>
			</div>
		<?php } else { ?>
			<div class="row">
				<div class="col-12">
					<div class="block">
						<div class="woocommerce-info">Поиск не дал результатов</div>
					</div>
				</div>
			</div>
		<?php } ?>

	</div>
</section>

<?php get_footer(); ?>