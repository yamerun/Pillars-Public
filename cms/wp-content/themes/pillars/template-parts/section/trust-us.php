<?php
$defaults = array(
	'section' => false
);
$args = wp_parse_args($args, $defaults);
?>
<?php if ($args['section']) { ?>
	<section>
		<div class="container">
		<?php } ?>

		<div class="row">
			<div class="col-12">
				<div class="block">
					<h2>Нам доверяют</h2>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<?= do_shortcode('[pillars-video-placeholder url="https://vk.com/video-211381188_456239114" cover_id="8710" caption="Отзыв о благоустройстве территории санатория «Русь» г. Геленджик"]') ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<?= do_shortcode('[pillars-video-placeholder url="https://vk.com/video-211381188_456239113" cover_id="9164" caption="Отзыв о благоустройстве территории ГРЦ «Альбатрос», Геленджик"]') ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<?= do_shortcode('[pillars-video-placeholder url="https://vk.com/video-211381188_456239111" cover_id="9163" caption="Отзыв о благоустройстве территории возле ТРЦ «VEER Mall»"]') ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<?= do_shortcode('[pillars-video-placeholder url="https://vk.com/video-211381188_456239158" cover_id="9162" caption="Отзыв о благоустройстве парка культуры и отдыха г. Березовский"]') ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<?= do_shortcode('[pillars-video-placeholder url="https://vk.com/video-211381188_456239227" cover_id="9456" caption="Отзыв о благоустройстве территории ТЦ МЕГА, г. Бугуруслан"]') ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="block">
					<a class="video-placeholder --link" href="https://vk.com/video/playlist/-211381188_1" target=_blank>
						<p>Ещё больше отзывов и других видео о компании вы можете посмотреть на нашем канале VK Видео</p>
					</a>
				</div>
			</div>
		</div>

		<?php if ($args['section']) { ?>
		</div>
	</section>
<?php } ?>