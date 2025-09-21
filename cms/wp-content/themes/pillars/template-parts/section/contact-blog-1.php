<div class="container">
	<div class="row">
		<div class="col-md-4">
			<div class="block g-block footer-block">
				<div class="footer-block__content">
					<h5 class="pillars-contact-list__title">Отдел продаж</h5>

					<?php /*
					<dl class="pillars-contact-list">
						<dt></dt>
						<dd class="pillars-contact-list__subtitle">Адрес</span></dd>
						<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-map') ?></dt>
						<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="raw" key="contacts_address_1"]') ?></dd>
					</dl>
					*/ ?>

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
		<div class="col-md-4">
			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Производство</h5>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">E-mail</dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
					<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email_production"]') ?></dd>
				</dl>

				<?php /* if (thetheme_get_phone_theme('contacts_phone_2')) { ?>
						<span class="icon"><?= pillars_theme_get_svg_symbol('phone') ?></span>
						<p><?= thetheme_get_phone_theme('contacts_phone_2') ?></p>
					<?php } */ ?>
			</div>

			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Отдел снабжения</h5>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">E-mail</dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-mail') ?></dt>
					<dd class="pillars-contact-list__content"><?= do_shortcode('[tp-get-contact type="email" key="contacts_email_supply"]') ?></dd>
				</dl>

				<?php /* if (thetheme_get_phone_theme('contacts_phone_2')) { ?>
						<span class="icon"><?= pillars_theme_get_svg_symbol('phone') ?></span>
						<p><?= thetheme_get_phone_theme('contacts_phone_2') ?></p>
					<?php } */ ?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="block g-block">
				<h5 class="pillars-contact-list__title">Реквизиты</h5>

				<div class="pillars-contact-list__content">ООО РК "ПИЛЛАРС плюс"</div>

				<div class="spacer"></div>

				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">ИНН:</div>
					<div class="pillars-contact-list__content">6658219297</div>
				</div>
				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">ОГРН:</div>
					<div class="pillars-contact-list__content">1056602855055</div>
				</div>

				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">Расчетный счет:</div>
					<div class="pillars-contact-list__content">40702810116540011233</div>
				</div>
				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">Банк:</div>
					<div class="pillars-contact-list__content">УРАЛЬСКИЙ БАНК ПАО СБЕРБАНК</div>
				</div>
				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">БИК:</div>
					<div class="pillars-contact-list__content">046577674</div>
				</div>
				<div class="d-flex">
					<div class="pillars-contact-list__subtitle">Корр. счет:</div>
					<div class="pillars-contact-list__content">30101810500000000674</div>
				</div>

				<div class="spacer"></div>

				<dl class="pillars-contact-list">
					<dt></dt>
					<dd class="pillars-contact-list__subtitle">Юридический адрес</span></dd>
					<dt class="pillars-contact-list__icon"><?= pillars_theme_get_svg_symbol('contact-icon-map') ?></dt>
					<dd class="pillars-contact-list__content">620028, Свердловская обл, <br>г. Екатеринбург, ул. Мельникова, <br>дом № 20, помещение 106.</dd>
				</dl>
			</div>
		</div>
		<div class="col-md-1"></div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="block wp-block">
				<?php /* $content = get_post(); echo $content->post_content; unset( $content ); */ ?>
			</div>
		</div>
	</div>
</div>