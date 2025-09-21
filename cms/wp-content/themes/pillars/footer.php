<?php
if (!isset($_COOKIE['pillars_cookie_agree'])) {
	get_template_part('template-parts/section/cookie');
}
// echo pillars_view_messanger_buttons();
?>

</main>
<footer class="wc-cart-hide">
	<div class="container">
		<div class="row">
			<div class="col-sm-3 col-6">
				<div class="block">
					<div class="footer-logo">
						<?= theplugin_get_custom_logo() ?>
						<?= do_shortcode('[tp-logo-footer-desc]') ?>
					</div>
				</div>
			</div>
			<div class="col-sm-3 col-6">
				<div class="block">
					<h5 class="footer-title">Меню</h5>
					<?php wp_nav_menu(array(
						'theme_location'	=> 'footer_menu',
						'items_wrap'		=> '%3$s',
						'container'			=> 'ul',
						'container_class'	=> '',
						'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'menu_class'		=> 'footer-list footer-menu',
						'link_before'		=> '',
						'link_after'		=> '',
						'depth'				=> 1
					)); ?>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block">
					<h5 class="footer-title">Контакты</h5>
					<ul class="contacts footer-list footer-menu">
						<li class="phone">
							<?= do_shortcode('[tp-get-contact]') ?>
						</li>
						<li class="email">
							<?= do_shortcode('[tp-get-contact type="email" key="contacts_email"]') ?>
						</li>
						<li class="address">
							<?= (get_current_blog_id() == 1) ? do_shortcode('[tp-get-contact type="raw" key="contacts_address_2"]') : do_shortcode('[tp-get-contact type="raw" key="contacts_address_1"]') ?>
						</li>
					</ul>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block">
					<h5 class="footer-title"><span class="hide-sm">Социальные </span><span class="show-sm">Соц</span>сети</h5>
					<?= do_shortcode('[tp-social-links title="true"]') ?>
					<?= do_shortcode('[tp-social-yandex-map]') ?>
				</div>
			</div>
		</div><!-- .row -->
		<div class="row">
			<div class="col-12">
				<div class="block">
					<div class="footer-spacer"></div>
				</div>
				<div class="block">
					<h5 class="footer-title"><?= get_the_title(wc_get_page_id('shop')) ?></h5>
					<?= pillars_theme_wc_get_product_cat_for_menu(array('before' => '<ul class="footer-menu-catalog footer-menu">', 'links' => ['https://molding.pillars.ru' => 'Производство форм'])) ?>
				</div>
			</div>
		</div><!-- .row -->
		<div class="row">
			<div class="col-12">
				<div class="block">
					<div class="spacer"></div>
					<?= do_shortcode('[tp-get-copyright]') ?>
				</div>
			</div>
		</div><!-- .row -->
	</div>
</footer>

<div class="btn-up btn-up__hide"></div>

<?php wp_footer(); ?>

</body>

</html>