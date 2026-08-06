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

/**
 * Оставить только цифры в переданном input
 *
 * @returns
 */
function numeric_only(e) {
	const input = e.target;
	input.value = input.value.replace(/[^0-9]/g, '');
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
		const wrapper = form.closest('section.cookie');

		form.classList.add('block-loading');

		let date = new Date();
		date.setDate(date.getDate() + 365);
		let val = 'ok for ' + date;
		document.cookie = "pillars_cookie_agree=" + val + "; domain=" + document.domain.match(/[^\.]*\.[^.]*$/)[0] + "; expires=" + date.toUTCString() + "; path=/";

		wrapper.remove();

		console.log('cookie', date);
		form.classList.remove('block-loading');
	});
}

const numeric_inputs = document.querySelectorAll('form.form-style input[pattern^="[0-9]"]');
if (numeric_inputs.length) {
	numeric_inputs.forEach(input => {
		input.addEventListener('blur', numeric_only);
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
