<section class="cookie">
	<div class="container">
		<div class="row cookie-wrapper">
			<div class="col-sm-10 col-9">
				<div class="block d-flex f-center txt-left">
					<p>Сайт Pillars использует cookie, чтобы сделать пользование сайтом проще. <a href="<?= get_privacy_policy_url() ?>" target="_blank">Узнайте больше про использование cookie.</a></p>
				</div>
			</div>
			<div class="col-sm-2 col-3">
				<form id="agree-cookie" class="block d-flex f-center txt-right" onSubmit="return false;">
					<button class="btn-1">Ok</button>
					<?php wp_nonce_field('cookie_verify_action', 'cookie_verify_key'); ?>
					<input name="action" type="hidden" value="theplugin_cookie_agree">
					<input name="form-type" type="hidden" value="agree">
				</form>
			</div>
		</div>
	</div>
</section>