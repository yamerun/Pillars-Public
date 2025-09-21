<form id="get-mail" class="tp-form-style tp-form-js tp-form-valid tt-form-ajax" method="post" action="">
	<div class="form-field">
		<h4 class="form-title txt-center">Связаться с нами</h4>
	</div>
	<div class="form-field">
		<label for="feedback-person">Ваше имя<abbr title="required">*</abbr></label>
		<input id="feedback-person" name="feedback-person" type="text" pattern="[\d\D\s]{2,}" required="require">
	</div>
	<div class="form-field">
		<label for="feedback-email">Ваш email<abbr title="required">*</abbr></label>
		<input id="feedback-email" name="feedback-email" type="email" required="require">
	</div>
	<div class="form-field">
		<label for="feedback-subject">Тема<abbr></abbr></label>
		<input id="feedback-subject" name="feedback-subject" type="text">
	</div>
	<div class="form-field">
		<label for="feedback-message">Сообщение<abbr></abbr></label>
		<textarea id="feedback-message" name="feedback-message" pattern="[\d\D\s]{3,}"></textarea>
	</div>
	<div class="one-field">
		<label class="confirm-wrapper">
			<input type="checkbox" name="privacy-confirm" value="Accepted" required="require"><span></span>
			<p class="small">Я даю согласие на обработку моих персональных данных в соответствии с <a class="privacy-policy-link" href="<?= get_privacy_policy_url() ?>" target="_blank">политикой конфиденциальности</a>.</p>
		</label>
	</div>
	<div class="form-field txt-center"><button type="submit">Отправить</button></div>
	<input type="hidden" name="action" value="theplugin_mailer">
	<?php theplugin_set_form_nonce('feedback'); ?>
	<?php theplugin_set_captcha('-feedback'); ?>
</form>