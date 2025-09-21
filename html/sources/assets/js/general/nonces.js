/**
 * Проверка актуальность ключей wp_nonce
 */
function wp_nonce_update() {
	const nonces = document.querySelectorAll('input[name*="_verify_key"]');
	if (nonces.length) {
		let form_data = new FormData();
		for (let i = 0; i < nonces.length; i++) {
			const nonce = nonces[i];

			console.log('nonce-input', nonce.name, nonce.value);

			form_data = new FormData();
			form_data.append('action', 'tp_nonce_update');
			form_data.append(nonce.name, nonce.value);

			tp_do_ajax({
				method: 'POST',
				url: window.wp_theplugin.ajax_url,
				data: form_data,
				contentType: 'application/x-www-form-urlencoded',
				success: function (request) {
					console.log('success wp nonce', request);
					data = JSON.parse(request);

					if (data.result != null) {
						if (data.result != '') {
							nonce.value = data.result;
							console.log('nonce-responce', nonce.name, nonce.value);
						}
					}
				}
			});
		}
	}
}

document.addEventListener('DOMContentLoaded', wp_nonce_update);
window.onblur = function () {
	wp_nonce_update();
}