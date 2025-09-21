<?php

/** Define ABSPATH as this file's directory */
if (!defined('ABSPATH')) {
	require(dirname(__FILE__) . '../../../../../wp-blog-header.php');
}

/** The config file resides in ABSPATH */
if (file_exists(ABSPATH . 'wp-config.php') && defined('THEPLUGIN_DIR')) :
	require_once THEPLUGIN_DIR . '/template-parts/header-simple.php';

	function theplugin_get_term_seo_yoast($term_id, $taxonomy)
	{

		global $wpdb;
		$table = $wpdb->prefix . 'yoast_indexable';

		$seo	= $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE object_id = %s AND object_type = %s AND object_sub_type = %s",
				$term_id,
				'term',
				$taxonomy
			)
		);

		if ($seo) {
			return [
				'title'			=> $seo->title,
				'description'	=> $seo->description,
			];
		}

		return [
			'title'			=> '',
			'description'	=> '',
		];
	}

	$data = [
		'city'		=> [],
		'nocity'	=> [],
		'default'	=> [],
	];
	$taxonomies = ['category', 'product_cat'];

	foreach ($taxonomies as $taxonomy) {
		$terms = get_terms($taxonomy, [
			'hide_empty' => false,
		]);

		if ($terms) {
			foreach ($terms as $term) {

				$seo = theplugin_get_term_seo_yoast($term->term_id, $taxonomy);
				$item = [
					'title'			=> $term->name,
					'seo'			=> $seo['title'],
					'description'	=> $seo['description'],
					'type'			=> $term->taxonomy,
					'count'			=> $term->count,
					'link'			=> edit_term_link($term->name, '', '', $term, 0)
				];

				if (mb_strpos($item['description'], 'Екатеринбург') !== false) {
					$data['city'][$term->term_id] = $item;
				} elseif (!$item['description']) {
					$data['default'][$term->term_id] = $item;
				} else {
					$data['nocity'][$term->term_id] = $item;
				}
			}
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
					<th>Категория</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($data[$key] as $term_id => $item) {

					echo sprintf(
						'	<tr><td>%d.</td><td><a href="%s" target=_blank>#%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>' . PHP_EOL,
						$i,
						get_term_link($term_id, $item['type']),
						$term_id,
						($item['link']) ? $item['link'] : $item['title'],
						($item['seo']) ? $item['seo'] : $item['title'],
						($item['description']) ? $item['description'] : '–',
						strtr($item['type'], [
							'category'		=> 'Постов',
							'product_cat'	=> 'Товаров'
						]),
					);
					$i++;
				}
				?>
			</tbody>
		</table>
	<?php } ?>
<?php
	// _yoast_wpseo_metadesc

	require_once THEPLUGIN_DIR . '/template-parts/footer-simple.php';
endif;
