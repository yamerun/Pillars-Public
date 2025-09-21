<?php

function theplugin_set_captcha($captha_id = '')
{
	if (theplugin_get_theme_mod('yandex_smartcaptcha_key') != '' && theplugin_get_theme_mod('yandex_smartcaptcha_secret') != '') :
		if (!empty($captha_id)) {
			$captha_id = '-' . $captha_id;
		}
?>
		<!-- Google Recaptcha-->
		<input type="hidden" id="g-recaptcha-response<?= $captha_id ?>" name="g-recaptcha-response" />
		<script>
			grecaptcha.ready(function() {
				grecaptcha.execute('<?= theplugin_get_theme_mod('yandex_smartcaptcha_key') ?>', {
					action: 'homepage'
				}).then(function(token) {
					console.log(token);
					document.getElementById('g-recaptcha-response<?= $captha_id ?>').value = token;
				});
			});
		</script>
		<!-- End Google Recaptcha-->
<?php
		add_action('wp_enqueue_scripts', function () {
			wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . theplugin_get_theme_mod('yandex_smartcaptcha_key'));
		});
	endif;
}

function theplugin_get_captcha($token)
{
	define('roboTestLevel', '0.5');
	define('SECRET_KEY', theplugin_get_theme_mod('yandex_smartcaptcha_secret'));

	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . SECRET_KEY . "&response={$token}");
	$response = json_decode($response);

	if ($response->success == true && $response->score > roboTestLevel) {
		return true;
	} else {
		return false;
	}
}

/**
 * Вывод поля wpnonce с заданным ключом
 *
 * @param string $name
 * @param boolean $referer
 * @param boolean $echo
 * @return string
 */
function theplugin_set_form_nonce($name = '_wpnonce', $referer = true, $echo = true)
{
	if ($echo) {
		wp_nonce_field($name . '_verify_action', $name . '_verify_key', $referer, $echo);
		echo '<input name="form-type" type="hidden" value="' . $name . '">';
	} else {
		return wp_nonce_field($name . '_verify_action', $name . '_verify_key', $referer, $echo) . '<input name="form-type" type="hidden" value="' . $name . '">';
	}
}

/**
 * Код nonce для верификации сохранения мета-данных терминов таксономии
 *
 * @param [type] $tag
 * @param boolean $echo
 * @return string
 */
function theplugin_metabox_wp_nonce_field_by_taxonomy($tag, $echo = true)
{
	if (is_numeric($tag))
		$tag = get_term($tag);

	// Получаем тип таксономии для точной верификации
	if ($tag instanceof WP_Term) {
		$taxonomy	= $tag->taxonomy;
	} else {
		return '';
	}

	$nonce		= wp_nonce_field("theplugin_post_meta_{$taxonomy}_action", "theplugin_post_meta_{$taxonomy}_noncename", true, false);

	if (!$echo) {
		return $nonce;
	}

	echo $nonce;
}
