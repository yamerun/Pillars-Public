<h2 class="visuallyhidden">Производство и продажа световой мебели и аксессуаров</h2>

<section class="p-untop m-untop p-unbottom">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block m-unbottom">
					<?= do_shortcode('[pl-informer-main size="1536x1536"]') ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?= do_shortcode('[tp-get-part part="category-frontpage"]') ?>
<?= do_shortcode('[tp-get-part part="portfolio"]') ?>
<?= do_shortcode('[tp-get-part part="advantage" args="section:true,"]') ?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h2>Хиты продаж</h2>
					<?= do_shortcode('[tp-get-part part="featured"]') ?>
				</div>
			</div>
		</div><!-- .row -->
	</div>
</section>

<?= do_shortcode('[tp-get-part part="news-slider"]') ?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-md-7">
				<div class="block">
					<?= do_shortcode('[pl-form-components part="company"]') ?>
				</div>
			</div>
			<div class="col-md-5">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(7604, 'medium') ?>
						</div>
					</div>
				</div>
			</div>
		</div><!-- .row -->
	</div>
</section>