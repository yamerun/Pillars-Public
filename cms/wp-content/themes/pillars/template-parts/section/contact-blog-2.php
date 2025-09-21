<div class="container">
	<div class="row">
		<div class="col-md-5">
			<div class="block g-block footer-block">
				<div class="footer-block__content">
					<h5 class="pillars-contact-list__title">Отдел продаж</h5>

					<dl class="pillars-contact-list">
						<dt></dt>
						<dd class="pillars-contact-list__subtitle">Телефон</dd>
						<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-phone') ?></dt>
						<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact]') ?></dd>
					</dl>

					<dl class="pillars-contact-list">
						<dt></dt>
						<dd class="pillars-contact-list__subtitle">E-mail</dd>
						<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
						<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email"]') ?></dd>
					</dl>
				</div>

				<div class="footer-block__footer pillars-contact-list__footer meniscus --bottom-right">
					<?php echo do_shortcode(sprintf(
						'[get-popup id="%s" form="%s" text="%s" class="btn-1" container="button" args="%s"]',
						'feedback',
						'feedback',
						'Связаться с нами',
						theplugin_array_to_args(['page_id' => get_the_ID()])
					)); ?>
				</div>
			</div>

			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Отдел кадров</h5>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">E-mail</dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
					<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email_hr"]') ?></dd>
				</dl>
			</div>
		</div>

		<div class="col-md-1"></div>

		<div class="col-md-4">
			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Производство</h5>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">E-mail</dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
					<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email_production"]') ?></dd>
				</dl>
			</div>

			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Отдел снабжения</h5>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">E-mail</dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
					<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email_supply"]') ?></dd>
				</dl>
			</div>
		</div>

		<div class="col-md-2"></div>

	</div>
</div>