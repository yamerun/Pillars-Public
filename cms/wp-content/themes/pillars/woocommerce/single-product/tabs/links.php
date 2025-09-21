<?php

/**
 * Useful Links block
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

global $product;

$blog_id	= get_current_blog_id();
$links		= array(
	'gallery' => [
		'link'	=> get_post_type_archive_link('portfolio'),
		'title'	=> __pl('Портфолио продукции')
	],
	'faq' => [
		'link'	=> get_permalink(theplugin_multisite_post_get_sibling_id(24, $blog_id, 1)),
		'title'	=> __pl('Вопрос / ответ')
	],
	'delivery' => [
		'link'	=> get_permalink(theplugin_multisite_post_get_sibling_id(20, $blog_id, 1)),
		'title'	=> __pl('Доставка')
	],
	'payment' => [
		'link'	=> get_permalink(theplugin_multisite_post_get_sibling_id(3274, $blog_id, 1)),
		'title'	=> __pl('Оплата')
	],
);

?>
<div class="pillars-wc-product-tab__links">
	<h3 class="pillars-wc-product-tab__title">Полезные ссылки</h3>
	<ul class="pillars-wc-product-tab__links-list">
		<?php foreach ($links as $key => $item) {
			echo sprintf(
				'<li><a href="%s" class="pillars-wc-product-tab__links-list-item"><img src="%s"><span>%s</span></a></li>',
				$item['link'],
				get_template_directory_uri() . '/assets/images/icon-product-' . $key . '.svg',
				$item['title']
			);
		} ?>
		<li>
			<a class="pillars-wc-product-tab__links-list-item btn-category pillars-popup__btn" data-form="form-catalog" data-form_args="<?= theplugin_array_to_args(['page_id' => get_the_ID()]) ?>" href="#catalog">
				<img src="<?= get_template_directory_uri() ?>/assets/images/icon-product-download.svg">
				<span><?= __pl('Скачать каталог') ?></span>
			</a>
		</li>
	</ul>
</div>