<?php

/**
 * Chroma tab
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;
?>
<div class="col-12">
	<div class="block wp-block">
		<h2 class="pillars-wc-product-tab__title">Варианты расцветок</h2>
		<p>Изделия Pillars очень прочные и подходят для локаций с высокой проходимостью. Благодаря разным вариантам окраса они вписываются в любой ландшафт: натуральные каменные тона — для городских и общественных пространств, яркие оттенки — для спортивных и игровых зон.</p>
	</div>
</div>
<div class="col-12">
	<div class="block wp-block">
		<h3>Белый и цветной RAL</h3>
	</div>
</div>
<?php foreach (['white' => 'Белый', 'blue' => 'Голубой', 'green' => 'Зелёный', 'red' => 'Красный'] as $key => $title) { ?>
	<div class="col-sm-3 col-6">
		<div class="block">
			<div class="chroma-item">
				<div class="chroma-item__thumb">
					<div class="media-ratio">
						<img src="<?= get_template_directory_uri() ?>/assets/images/chroma/<?= $key ?>.png" loading="lazy" decoding="async">
					</div>
					<div class="chroma-item__title">
						<div class="meniscus --top-right"><?= $title ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<div class="col-12">
	<div class="block wp-block">
		<h3>Расцветки под натуральный камень </h3>
	</div>
</div>
<?php foreach (['stone-1' => 'Вариант 1', 'stone-2' => 'Вариант 2', 'stone-3' => 'Вариант 3', 'stone-4' => 'Вариант 4'] as $key => $title) { ?>
	<div class="col-sm-3 col-6">
		<div class="block">
			<div class="chroma-item">
				<div class="chroma-item__thumb">
					<div class="media-ratio">
						<img src="<?= get_template_directory_uri() ?>/assets/images/chroma/<?= $key ?>.png" loading="lazy" decoding="async">
					</div>
					<div class="chroma-item__title">
						<div class="meniscus --top-right"><?= $title ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>