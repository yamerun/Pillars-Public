<?php
$defaults = array(
	'section' => false
);
$args = wp_parse_args($args, $defaults);

$data = [
	'advantage-inrussia' => [
		'title'			=> 'Российское производство',
		'description'	=> 'Собственное производство полного цикла в городе Екатеринбург'
	],
	'advantage-quality' => [
		'title'			=> 'Качество и материалы',
		'description'	=> 'Все изделия изготовлены из высококачественных и надёжных материалов'
	],
	'advantage-project-budget' => [
		'title'			=> 'Подстроимся под бюджет клиента',
		'description'	=> 'Оптимизируем производство и модели под смету и требования объекта'
	],
	'advantage-person' => [
		'title'			=> 'Эксперты в благоустройстве',
		'description'	=> 'Более 20 лет создаем современные малые архитектурные формы'
	],
	'advantage-delivery' => [
		'title'			=> 'Индивидуальный подход',
		'description'	=> 'Мы всегда открыты к сотрудничеству и готовы выполнить любой запрос'
	],
];
?>
<?php if ($args['section']) { ?>
	<section class="bg-2">
		<div class="container">
		<?php } else { ?>
			<div class="wp-section bg-2">
			<?php } ?>
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h2>Наши преимущества</h2>
					</div>
				</div>
			</div>
			<div class="row advantages">
				<?php foreach ($data as $key => $item) { ?>
					<div class="advantage-item">
						<div class="block">
							<div class="advantage-item__img-wrap">
								<div class="advantage-item__img">
									<?= do_shortcode('[pillars_svg key="' . $key . '" type=""]') ?>
								</div>
							</div>
							<h5 class="advantage-item__title"><?= $item['title'] ?></h5>
							<div class="advantage-item__text"><?= $item['description'] ?></div>
						</div>
					</div>
				<?php } ?>
			</div>

			<?php if (!$args['section']) { ?>
			</div>
		<?php } else { ?>
		</div>
	</section>
<?php } ?>