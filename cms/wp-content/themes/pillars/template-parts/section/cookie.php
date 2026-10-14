<section class="cookie">
	<div class="container">
		<div class="row cookie-wrapper">
			<div class="col-sm-9 col-12">
				<div class="block d-flex f-center txt-left">
					<p>Сайт pillars.ru (оператор: ООО РК «ПИЛЛАРС плюс», ИНН 6658219297) использует cookies для веб-аналитики (Яндекс.Метрика). Подробнее — в <a href="<?= get_privacy_policy_url() ?>" target="_blank">Политике обработки персональных данных.</a></p>
				</div>
			</div>
			<div class="col-sm-2 col-6">
				<div id="agree-cookie" class="block d-flex f-center txt-right">
					<button class="btn-1 accept">Принять</button>
					<?php wp_nonce_field('cookie_verify_action', 'cookie_verify_key'); ?>
					<input name="action" type="hidden" value="theplugin_cookie_agree">
					<input name="form-type" type="hidden" value="agree">
				</div>
			</div>
			<div class="col-sm-1 col-6">
				<div id="decline-cookie" class="block d-flex f-center txt-right">
					<button class="btn-1 alt decline">Отклонить</button>
				</div>
			</div>
		</div>
	</div>
</section>