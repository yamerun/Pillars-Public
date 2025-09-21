<?php

defined('ABSPATH') || exit;

// TODO в будущем файл будет не нужен

add_shortcode('get-informer-main', function ($params) {
	if (!is_admin()) {
		$atts = shortcode_atts(array(
			'size' 	=> 'catalog-thumb'
		), $params);

		$atts['size'] = (theplugin_is_mobile()) ? 'medium_large' : $atts['size'];
		$informers = theplugin_get_informer_data($atts['size']);

		ob_start(); ?>

		<div class="informer-slider">
			<?php
			foreach ($informers as $id => $informer) : ?>
				<div class="category-item">
					<div class="media-ratio"><?= $informer['image'] ?></div>
					<div class="title"><?= $informer['title'] ?></div>
					<?php if (!empty($informer['content'])) : ?><div class="content <?= $informer['align'] ?>">
							<div><?= apply_filters('the_content', $informer['content']) ?></div>
						</div><?php endif; ?>
					<?= $informer['link'] ?>
				</div>
			<?php endforeach; ?>
		</div>

<?php return ob_get_clean();
	}
});

/**
 * Получение массива данных об доступных информерах
 *
 * @param string $size
 * @return array
 */
function theplugin_get_informer_data($size = 'catalog-thumb')
{

	$query = new WP_Query(array(
		'post_type'      => 'informer',
		'post_parent'    => 0,
		'posts_per_page' => 10,
		'orderby'        => array('menu_order' => 'asc')
	));

	$data = [];
	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();
			$id         = get_the_ID();
			$data[$id]  = array(
				'title'     => get_the_title(),
				'link'		=> (get_post_meta($id, '_informer_link', true)) ? '<a class="link" href="' . get_post_meta($id, '_informer_link', true) . '">' . get_post_meta($id, '_informer_link_text', true) . '</a>' : '',
				'image'     => (get_post_thumbnail_id($id)) ? wp_get_attachment_image(get_post_thumbnail_id($id), $size) : '',
				'mobile'	=> '',
				'small'		=> '',
				'content'	=> (get_post_meta($id, '_informer_content_view', true) == 'yes') ? get_the_content() : '',
				'align'		=> str_replace('_', ' ', get_post_meta($id, '_informer_content_align', true))
			);
		endwhile;
	endif;

	wp_reset_query();

	return $data;
}
