<?php
$section = get_post_meta(get_the_ID(), '_product_category_section', true);
if ($section) { ?>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h2>Мы производим</h2>
					</div>
				</div>
				<?= do_shortcode('[pl-product-category-group category="' . $section . '"]') ?>
			</div>
		</div>
	</section>
<?php } ?>