<?php

/**
 * Template Name: Производство
 */

get_header();

?>

<section class="section-title-image firstscreen">
	<div class="container section-title-image__container">
		<div class="row section-title-image__cover">
			<div class="col-12">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9250, '1536x1536') ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row section-title-image__content">
			<div class="col-sm-6">
				<div class="block"></div>
			</div>
			<div class="col-sm-6">
				<div class="block color-white">
					<h1><?php the_title(); ?></h1>
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h3>Мы предоставляем такие услуги:</h3>
				</div>
			</div>
		</div>

		<div class="row f-row-reverse-sm">
			<div class="col-sm-7">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9251, 'large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-4">
				<div class="block">
					<div class="spacer"></div>

					<h4>Лазерная резка металла</h4>
					<p>Лазерная резка — это высокоточный метод обработки металла, который позволяет нам создавать детали сложной формы. Мы работаем с металлом толщиной до 10 мм и используем рабочее поле размером 1,5x3 метра. Осуществляем резку труб диаметром до 230 мм и длиной до 6 метров. Готовы рассчитать стоимость и предоставить детальный расчет по вашему проекту.</p>

					<div class="spacer-double"></div>

					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="button" args="%s"]',
						'contract-production',
						'contract-production',
						'Рассчитать стоимость',
						theplugin_array_to_args(['type' => 'Лазерная резка металла'])
					)); ?>

					<div class="spacer-double"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-7">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9252, 'large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-4">
				<div class="block">
					<div class="spacer"></div>

					<h4>ЧПУ токарная обработка металла</h4>
					<p>Наши токарные станки с ЧПУ позволяют обрабатывать любые металлические детали с высокой точностью. От простых элементов до сложных деталей — мы гарантируем качество и строгое соблюдение технических параметров. Свяжитесь с нами, чтобы рассчитать стоимость и обсудить ваш заказ.</p>

					<div class="spacer-double"></div>

					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="button" args="%s"]',
						'contract-production',
						'contract-production',
						'Рассчитать стоимость',
						theplugin_array_to_args(['type' => 'ЧПУ токарная обработка металла'])
					)); ?>

					<div class="spacer-double"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row f-row-reverse-sm">
			<div class="col-sm-7">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9255, 'large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-4">
				<div class="block">
					<div class="spacer"></div>

					<h4>ЧПУ фрезерная обработка металла и дерева</h4>
					<p>Фрезерные станки с ЧПУ на 4 оси позволяют нам работать как с металлом, так и с деревом. Мы обрабатываем материалы с высокой точностью, создавая детали, соответствующие вашим чертежам. У нас есть возможность работы с пластиком, что расширяет спектр наших услуг.</p>

					<div class="spacer-double"></div>

					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="button" args="%s"]',
						'contract-production',
						'contract-production',
						'Рассчитать стоимость',
						theplugin_array_to_args(['type' => 'ЧПУ фрезерная обработка металла и дерева'])
					)); ?>

					<div class="spacer-double"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-7">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9254, 'large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-4">
				<div class="block">
					<div class="spacer"></div>

					<h4>Порошково-полимерная покраска металла</h4>
					<p>Мы предоставляем услуги порошково-полимерной покраски, что позволяет надежно защитить металлические изделия от коррозии и придать им эстетичный внешний вид. Используем собственную покрасочную печь и камеры для равномерного нанесения покрытия. Максимальные размеры изделий для покраски: ширина 1800 мм, длина 6000 мм и высота 3000 мм.</p>

					<div class="spacer-double"></div>

					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="button" args="%s"]',
						'contract-production',
						'contract-production',
						'Рассчитать стоимость',
						theplugin_array_to_args(['type' => 'Порошково-полимерная покраска металла'])
					)); ?>

					<div class="spacer-double"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row f-row-reverse-sm">
			<div class="col-sm-7">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(9253, 'large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-4">
				<div class="block">
					<div class="spacer"></div>

					<h4>Изготовление металлоконструкций любой сложности</h4>
					<p>Мы специализируемся на производстве металлоконструкций любой сложности. Независимо от масштаба и сложности проекта, мы гарантируем высокое качество изготовления и своевременное выполнение работы. Обсудите ваш проект с нашими специалистами и получите расчет стоимости изготовления.</p>

					<div class="spacer-double"></div>

					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="button" args="%s"]',
						'contract-production',
						'contract-production',
						'Рассчитать стоимость',
						theplugin_array_to_args(['type' => 'Изготовление металлоконструкций любой сложности'])
					)); ?>

					<div class="spacer-double"></div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>