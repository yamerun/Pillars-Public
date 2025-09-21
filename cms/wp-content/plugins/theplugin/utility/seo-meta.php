<?php

/** Define ABSPATH as this file's directory */
if (!defined('ABSPATH')) {
	require(dirname(__FILE__) . '../../../../../wp-blog-header.php');
}

/** The config file resides in ABSPATH */
if (file_exists(ABSPATH . 'wp-config.php') && defined('THEPLUGIN_DIR')) :
	require_once THEPLUGIN_DIR . '/template-parts/header-simple.php';

	$data = array();
	$post_type = ['page', 'informer', 'portfolio', 'product'];
	if (isset($_GET['post'])) {
		$post_type[] = 'post';
	}

	global $wpdb;

	if (isset($_GET['blog_id'])) {
		$prefix = theplugin_multisite_get_blog_prefix(absint($_GET['blog_id']));
	} else {
		$prefix	= $wpdb->prefix;
	}
	$table		= $prefix . 'posts';

	foreach ($post_type as $type) {
		$results	= $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE post_type = %s", $type));
		foreach ($results as $item) {
			$ancestors = get_ancestors($item->ID, $type);
			if (!in_array(3167, $ancestors)) {
				$link = get_edit_post_link($item->ID);
				$data[$item->ID] = [
					'title'			=> $item->post_title,
					'seo'			=> (isset($_GET['blog_id'])) ? theplugin_multisite_post_get_meta(absint($_GET['blog_id']), $item->ID, '_yoast_wpseo_title') : get_post_meta($item->ID, '_yoast_wpseo_title', true),
					'description'	=> (isset($_GET['blog_id'])) ? theplugin_multisite_post_get_meta(absint($_GET['blog_id']), $item->ID, '_yoast_wpseo_metadesc') : get_post_meta($item->ID, '_yoast_wpseo_metadesc', true),
					'type'			=> $item->post_type,
					'status'		=> $item->post_status,
					'link'			=> ($link) ? '<a href="' . $link . '">' . $item->post_title . '</a>' : ''
				];
			}
		}
	}

	$result = [
		'city'		=> [],
		'nocity'	=> [],
		'default'	=> [],
	];

	foreach ($data as $post_id => $item) {
		foreach ($item as $key => $value) {
			if (is_wp_error($value)) {
				$item[$key] = '';
			}
		}

		if (mb_strpos($item['description'], 'Екатеринбург') !== false) {
			$result['city'][$post_id] = $item;
		} elseif (!$item['description']) {
			$result['default'][$post_id] = $item;
		} else {
			$result['nocity'][$post_id] = $item;
		}
	}
?>
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
		}

		table td,
		table th {
			border: 1px solid;
			padding: .5em;
		}

		table td {
			padding: 10px 20px;
			line-height: 20px;
		}

		table thead {
			background-color: #ccc;
		}

		table tbody tr:nth-child(2n) {
			background-color: #eee;
		}
	</style>
	<?php foreach (['city' => 'Екатеринбург', 'nocity'	=> 'Без города', 'default' => 'По умолчанию'] as $key => $label) {
		$i = 1; ?>
		<h3><?= $label ?></h3>
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>ID</th>
					<th>Заголовок</th>
					<th>SEO</th>
					<th>Описание</th>
					<th>Тип</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($result[$key] as $post_id => $item) {
					if ($item['status'] == 'publish') {
						echo sprintf(
							'	<tr><td>%d.</td><td><a href="%s" target=_blank>#%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>' . PHP_EOL,
							$i,
							get_permalink($post_id),
							$post_id,
							($item['link']) ? $item['link'] : $item['title'],
							($item['seo']) ? $item['seo'] : $item['title'],
							($item['description']) ? $item['description'] : '–',
							strtr($item['type'], [
								'post'	=> 'Новости',
								'page'	=> 'Страницы',
								'informer'	=> 'Информер',
								'portfolio'	=> 'Портфолио',
								'product'	=> 'Продукты'
							]),
						);
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	<?php } ?>
<?php
	// _yoast_wpseo_metadesc

	require_once THEPLUGIN_DIR . '/template-parts/footer-simple.php';
endif;
