<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="yandex-verification" content="dbd739e20dcaab5a" />
	<meta name="google-site-verification" content="3CyCo_1pYmueceumIgsWiX7kmg67qV2uF5QDiQ6cv-4" />
	<?php if (get_current_blog_id() !== 1) { ?>
		<meta name="google-site-verification" content="KP7aJjW0UkQJ_PFACXWXrEKxtm4zWpCR8D4yqF7en4o" />
	<?php } ?>
	<meta name="facebook-domain-verification" content="9xldkld3xk8m8arvhl6lk0qskq6jur" />
	<meta name="p:domain_verify" content="6861d6535137a0fb0fb869af31415bb1" />

	<?= do_shortcode('[tp-get-theme-color]') ?>
	<?php wp_head(); ?>

</head>

<body <?php body_class() ?>>
	<?php wp_body_open(); ?>
	<header>
		<div class="body-header__top">
			<div class="container">
				<div class="row">
					<div class="col-tagline hide-sm">
						<?= do_shortcode('[pillars_header_tagline]') ?>
						<?= do_shortcode('[pillars_header_multisite]') ?>
					</div>
					<div class="col-logo">
						<?= theplugin_get_custom_logo() ?>
					</div>
					<div class="col-contact hide-sm">
						<?= do_shortcode('[pillars_header_contacts]') ?>
					</div>
					<div class="col-account">
						<?= do_shortcode('[pillars_header_account]') ?>
					</div>
				</div>
			</div>
		</div><!-- .body-header__top -->
		<div class="body-header__bottom">
			<div class="container">
				<div class="row">
					<div class="col-menu">
						<nav class="navbar-default">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-target="#main_menu">
									<span></span>
									<span></span>
									<span></span>
								</button>
							</div>
							<div id="main_menu" class="navbar-collapse">
								<div class="show-sm">
									<?= do_shortcode('[pillars_header_contacts type="raw-link"]') ?>
								</div>
								<ul id="menu-header-catalog" class="nav navbar-nav catalog-menu">
									<li id="menu-item-catalog" class="menu-item menu-item-has-children">
										<a href="<?= get_permalink(wc_get_page_id('shop')) ?>">
											<span><?= get_the_title(wc_get_page_id('shop')) ?></span>
										</a>
										<?= pillars_theme_wc_get_product_cat_for_menu(array('before' => '<div class="sub-menu">', 'after' => '</div>', 'groups' => true)) ?>
									</li>
								</ul>
								<?php wp_nav_menu(array(
									'theme_location'	=> 'primary',
									'items_wrap'		=> '%3$s',
									'container'			=> 'ul',
									'container_class'	=> '',
									'items_wrap'		=> '<ul id="%1$s" class="%2$s">%3$s</ul>',
									'menu_class'		=> 'nav navbar-nav main-menu',
									'menu_id'			=> 'main-menu',
									'link_before'		=> '<span>',
									'link_after'		=> '</span>',
									'depth'				=> 2
								)); ?>
								<div class="show-sm">
									<?= do_shortcode('[tp-social-links]') ?>
								</div>
							</div>
						</nav>
					</div>
					<div class="col-search">
						<?= do_shortcode('[pillars_header_search]') ?>
					</div>
					<div class="col-action">
						<a class="btn-category pillars-popup__btn" data-form="form-catalog" href="#catalog" data-form_args="<?= theplugin_array_to_args(['page_id' => get_the_ID()]) ?>"><span>Запросить каталог</span></a>
					</div>
				</div>
			</div>
		</div><!-- .body-header__bottom -->
	</header>
	<main>
		<div class="container --breadcrumbs">
			<?php get_template_part('template-parts/breadcrumbs'); ?>
		</div>