<?php

/**
 * Template Name: О нас
 */

get_header();

?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-5">
				<div class="block">
					<h1><?php the_title(); ?></h1>
					<p>Компания Pillars уже более 20 лет занимается производством световых и несветовых изделий для городского благоустройства, интерьеров и открытых площадок.</p>
					<p>Многолетний опыт работы в отрасли, наличие собственного производства в Екатеринбурге и понимание особенностей технологии ротационного формования позволяет нам оперативно изготавливать любые объемы товаров из каталога или разрабатывать по запросу эксклюзивные продукты с гарантией высочайшего качества.</p>
				</div>
			</div>
			<div class="col-sm-2"></div>
			<div class="col-sm-5">
				<div class="block">
					<a class="video-placeholder --click" href="<?= theplugin_get_video_embed_link('https://vk.com/video-211381188_456239079') ?>">
						<div class="video-placeholder__cover image-radius">
							<div class="media-ratio">
								<?= wp_get_attachment_image(7468, 'large') ?>
							</div>
						</div>
						<div class="video-placeholder__btn">
							<?= do_shortcode('[pillars_svg key="video-play"]') ?>
						</div>
					</a>
				</div>
			</div>

			<div class="col-12">
				<div class="block spacer"></div>
			</div>

			<div class="col-sm-3">
				<div class="block counters__border">
					<div class="counters__title">+20</div>
					<p class="counters__desc">Лет на рынке городского благоустройства</p>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block counters__border">
					<div class="counters__title">+400</div>
					<p class="counters__desc">Уникальных изделий в каталоге</p>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block counters__border">
					<div class="counters__title">+1000</div>
					<p class="counters__desc">Реализованых проектов с нашими изделиями</p>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="block">
					<div class="counters__title">+2000</div>
					<p class="counters__desc">Изделий производим ежегодно</p>
				</div>
			</div>
		</div>
	</div>
</section>

<?= do_shortcode('[tp-get-part part="advantage" args="section:true,"]') ?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<h2>Наша миссия и ценности</h2>
				</div>
			</div>
		</div>
		<div class="row f-row-reverse-sm">
			<div class="col-sm-6">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(8671, 'medium_large') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="block border-line txt-center">
					<h3>Миссия</h3>
					<p class="txt-center">Меняем повседневность городских пространств, благодаря созданию привлекательной, комфортной и экологически чистой городской среды для проживания, работы и отдыха людей. Мы создаём высококачественные и функциональные малые архитектурные формы, которые воплощают в себе инновационный дизайн, уникальность и удовлетворяют потребности наших клиентов</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="block">
					<div class="image-radius">
						<div class="media-ratio">
							<?= wp_get_attachment_image(7364, 'full') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="block border-line txt-center">
					<h3>Ценности</h3>
					<div></div>
					<ul class="marker --values">
						<li>Команда</li>
						<li>Качество</li>
						<li>Надёжность</li>
						<li>Клиентоориентированность</li>
						<li>Безопасность</li>
						<li>Инновации</li>
						<li>Профессионализм</li>
						<li>Целеустремленность</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="block spacer show-sm"></div>
				<div class="block spacer-double hide-sm"></div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-5" style="align-content: space-between;">
				<div class="block">
					<h2>Качество и надёжность</h2>
					<p>Каждая партия выпускаемой продукции проходит многоступенчатый контроль качества на всех этапах производства. Все изделия проходят 48-часовое тестирование, в процессе которого отбраковывается некачественный товар.</p>
				</div>
				<div class="block certificates">
					<h4 class="certificates__title">Сертификаты</h4>
					<div class="certificates__certificate">
						<a data-fancybox="eac" href="<?= get_template_directory_uri() ?>/assets/images/Certificate_pillars_EAC.png">
							<img width="210" height="300" src="<?= get_template_directory_uri() ?>/assets/images/Certificate_pillars_EAC_preview.png">
						</a>
						<a data-fancybox="eac" href="<?= wp_get_attachment_image_url(9343, 'full') ?>">
							<img width="210" height="300" src="<?= wp_get_attachment_image_url(9343, 'medium') ?>">
						</a>
					</div>
					<div class="certificates__eac">
						<img width="78" src="<?= get_template_directory_uri() ?>/assets/images/svg-icon-eac.svg">
						<img height="78" src="<?= get_template_directory_uri() ?>/assets/images/svg-icon-made-in-russia-ru.svg">
					</div>
				</div>
			</div>
			<div class="col-sm-1"></div>
			<div class="col-sm-6">
				<div class="block image-split">
					<div class="image-caption">
						<div class="image-caption__img">
							<div class="media-ratio">
								<img src="https://pillars.ru/wp-content/uploads/2023/10/advantage-01.png">
							</div>
						</div>
						<div class="image-caption__desc">Специалисты в производстве</div>
					</div>
					<?= wp_get_attachment_image(2041, 'medium', false, ['class' => 'hide-sm']) ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="block spacer-double"></div>
			</div>
		</div>

		<?= do_shortcode('[tp-get-part part="trust-us"]') ?>

		<div class="row">
			<div class="col-12">
				<div class="block spacer-double"></div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="block">
					<h2>Наши клиенты</h2>
				</div>
				<div class="clients row">
					<?php
					foreach (array(8796, 8794, 5020, 5019, 5018, 8797, 8795, 8793, 8791, 8792, 8790, 9909, 5016, 5015, 5014, 5013, 5012, 8799, 8798, 8801, 9910) as $id) {
						$name = strtr(get_the_title($id), ['Администрация города' => '']);
						if (mb_strpos($name, '–') !== false) {
							$name = stristr($name, '–', true);
						}
						$name = trim($name);
					?>
						<div class="client-item">
							<div class="client-item__logo">
								<?= wp_get_attachment_image($id, 'full') ?>
							</div>
							<div class="client-item__name">
								<?= $name ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

		</div>
	</div>
</section>

<?php get_footer(); ?>