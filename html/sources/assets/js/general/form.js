tp_delegate(document.body, 'click', 'form-style__input', function (e) {
	e.preventDefault();

	const input = tp_get_target_by_class(e, 'form-style__input');
	const wrapper = input.parentElement;

	wrapper.classList.add('active');
	input.addEventListener('blur', is_show_label, { once: true });
});

tp_delegate(document.body, 'focusin', 'form-style__input', function (e) {
	e.preventDefault();

	const input = tp_get_target_by_class(e, 'form-style__input');
	const wrapper = input.parentElement;

	wrapper.classList.add('active');
	input.addEventListener('blur', is_show_label, { once: true });
});

function is_show_label(e) {
	const wrapper = this.parentElement;
	if (this.value != '' || this.classList.contains('mask-phone')) {
		wrapper.classList.add('active');
	} else {
		wrapper.classList.remove('active');
	}
}

tp_delegate(document.body, 'submit', 'form-ajax', function (e) {
	e.preventDefault();

	const form = e.target;
	const wrapper = form.parentElement;
	let form_data = new FormData(form);
	const id = form.id;
	let informer = null;

	let files = form.querySelectorAll('input[type="file"]');
	if (files.length) {
		for (let i = 0; i < files.length; i++) {
			const file = files[i];
			form_data.append(file.name, file.files[0]);
		}
	}

	if (!wrapper.querySelector('.pillars-informer[data-id="' + id + '"]')) {
		informer = document.createElement('div');
		informer.classList.add('pillars-informer');
		informer.setAttribute('data-id', id);
		wrapper.insertBefore(informer, form);
	} else {
		informer = wrapper.querySelector('.pillars-informer[data-id="' + id + '"]');
	}

	form.classList.add('block-loading');

	fetch(window.wp_data.ajax_url, {
		method: "post",
		body: form_data,
		// headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		// body: new URLSearchParams(form_data).toString(),
	})
		.then(response => response.json())
		.then(function (data) {
			console.log('response', data);
			form.classList.remove('block-loading');

			if (data.message != null && data.type != null) {
				switch (data.type) {
					case 'ok':
						form.remove();
						break;
					case 'error':
						break;
					case 'fail':
					case 'spam':
						form.remove();
						break;
					default: ;
				}
				informer.innerHTML = data.message;
			} else {
				informer.innerHTML = window.wp_theplugin.notice_error;
			}
		})
		.catch(function (error) { console.warn('error', error); });
});

/**
 * Cookie Accept
 */
const cookiesection = document.getElementById('agree-cookie');
if (cookiesection) {
	cookiesection.addEventListener('submit', function (e) {
		e.preventDefault();

		const form = e.target;
		let form_data = new FormData(form);
		const wrapper = form.closest('section.cookie');

		// TODO упрощенная анимация отправки
		form.classList.add('block-loading');

		fetch(window.wp_data.ajax_url, {
			method: "post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(form_data).toString(),
		})
			.then(response => response.json())
			.then(function (data) {
				console.log('cookie', data);
				form.classList.remove('block-loading');

				if (data.message != null && data.type != null) {
					switch (data.type) {
						case 'ok':
							wrapper.remove();
							break;
						case 'error':
						case 'fail':
						case 'spam':
							// TODO функционал простого нотиса
							alert(data.message);
							break;
						default: ;
					}
				}
			})
			.catch(function (error) { console.warn('cookie-error', error); });
	});
}

/**
 * Log Action
 */
$(document).on("click", 'form.form-style button[type="submit"]', function (e) {
	let form = $(this).closest('form');

	console.log('SEND', $(this), form.serialize() + '&action=pillars_senddata');

	$.ajax({
		type: 'post',
		url: window.wp_theplugin.ajax_url,
		data: form.serialize() + '&action=pillars_senddata',
		traditional: true,
		success: function (data) {
			console.log('SEND DATA', data);
		},
		error: function (error) {
			console.log('ERRORS SEND DATA', error);
		},
	});
});
