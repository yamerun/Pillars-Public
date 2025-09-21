<?php

/**
 * Chroma tab
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

$gallery = [10941, 10947, 10945, 10946];
$index = 0;
$image = $gallery[$index];
unset($gallery[$index]);
?>
<div class="col-12">
	<div class="block">
		<h2 class="h1">O коллекции SNAKE</h2>
		<p>Представьте плавную, извивающуюся линию отдыха, очерчивающую аллею, обрамляющую площадку или создающую уютный уголок... Это Snake – не просто скамейка, а модульная скульптура для города. Преобразите любое пространство с помощью модульных скамеек Snake! Изготовленные из сверхпрочного и долговечного пластика низкого давления, они не боятся влаги, морозов, УФ-излучения и вандализма. Скамейки разных радиусов позволяют создавать бесконечные плавные линии или компактные островки отдыха. Соберите идеальную конфигурацию под вашу территорию! Ваше воображение – единственный предел!</p>
	</div>
</div>

<div class="col-12">
	<div class="block wp-block">
		<h3>Варианты компоновки</h3>
	</div>
</div>
<div class="col-md-4">
	<div class="block wp-block">
		<a href="<?= wp_get_attachment_image_url(10940, 'full') ?>" data-fancybox="collection" class="image-radius">
			<?= wp_get_attachment_image(10940, 'large') ?>
		</a>
	</div>
</div>
<div class="col-md-8">
	<div class="block wp-block">
		<a href="<?= wp_get_attachment_image_url($image, 'full') ?>" data-fancybox="collection" class="image-radius">
			<div class="media-ratio">
				<?= wp_get_attachment_image($image, 'large') ?>
			</div>
		</a>
	</div>
</div>

<?php foreach ($gallery as $id) {
	echo sprintf('<a href="%s" data-fancybox="collection" class="visuallyhidden"></a>', wp_get_attachment_image_url($id, 'full'));
} ?>

<div class="col-12">
	<div class="block"></div>
	<div class="block wp-block">
		<h3>Варианты модулей</h3>
	</div>
</div>
<?php foreach (['Snake 1' => [7245, 10944], 'Snake 2' => [7260, 10942], 'Snake 3' => [7273, 10943]] as $title => $images) { ?>
	<div class="col-md-4 collection-item">
		<div class="block">
			<a href="<?= wp_get_attachment_image_url($images[0], 'full') ?>" data-fancybox="collection" class="image-radius">
				<?= wp_get_attachment_image($images[0], 'large') ?>
			</a>
		</div>
		<div class="block">
			<a href="<?= wp_get_attachment_image_url($images[1], 'full') ?>" data-fancybox="collection" class="image-radius collection-item__image">
				<?= wp_get_attachment_image($images[1], 'large') ?>
				<div class="collection-item__title"><?= $title ?></div>
			</a>
		</div>
	</div>
<?php } ?>