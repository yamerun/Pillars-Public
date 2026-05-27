<div class="call-me-action">
	<div class="call-me-action__container">
		<div class="call-me-action__wrapper">
			<div class="call-me-action__description">
				<h4>Бесплатная консультация</h4>
				<p>Подскажем, какие МАФ подойдут для вашей территории, сориентируем по ассортименту, вариантам исполнения и этапам заказа.</p>
			</div>
			<div class="call-me-action__button">
				<a class="btn-2 pillars-popup__btn" href="tel:<?= do_shortcode('[tp-get-contact wrapper="link"]') ?>" data-id="recall" data-form="form-recall" data-form_args="<?= theplugin_array_to_args(['page_id' => get_the_ID()]) ?>">
					<?= pillars_theme_get_svg_symbol('contact-icon-phone') ?>
					<span>Заказать звонок</span>
				</a>
			</div>
		</div>
	</div>
</div>