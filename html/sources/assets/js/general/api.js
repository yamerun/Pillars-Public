/**
 * Перевод данных из `FormData` в `URLSearchParams` для отпавки по api
 *
 * @param {*} params
 * @returns
 */
function set_data_params_to_api(params) {
	const data = new URLSearchParams();
	for (const pair of params) {
		data.append(pair[0], pair[1]);
	}

	return data;
}

/**
 * Отправки данных по Pillars API
 *
 * @param {*} data
 * @param {*} path
 * @param {*} route
 */
function send_data_to_api(data, path, route, method = 'GET', callback = null) {
	let params = {
		method: method,
		headers: {
			// 'Content-Type': 'application/json',
			'X-WP-Nonce': wp_api_settings.nonce
		},
	};

	if (path) {
		path += '/';
	}

	let url = wp_api_settings.root + wp_api_settings.namespace + '/' + route + '/' + path;

	if (method == 'GET') {
		url += '?' + data.toString();
	}

	if (method == 'POST') {
		params.body = data;
	}

	console.log('api url', url);
	console.log('api params', params);

	let set_data_to = fetch(url, params)
		.then(response => response.json())
		.then(function (response) {
			// TODO возможно нужна будет проверка cookie
			console.log(response);

			if (response?.status == 'ok') {
				// TODO создать шаблон для вывода notice
				console.log(response.message);

				if (typeof callback === 'function') {
					callback(response.message);
				}

				if (response.message?.redirect != null) {
					setTimeout(function () {
						window.location.href = response.message.redirect;
					}, 1000);
				}
			}
		})
		.catch(function (error) { console.warn(error); });
}

/**
 * Отправки данных формы по Pillars API
 *
 * @param {*} form
 * @param {*} path
 * @param {*} route
 */
function send_form_data_to_api(form, path, route) {

	const wrapper = form.parentElement;
	let form_data = new FormData(form);
	const data = set_data_params_to_api(form_data);

	send_data_to_api(data, path, route, 'POST');
}