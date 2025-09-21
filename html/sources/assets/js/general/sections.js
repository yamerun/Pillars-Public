/**
 * Подгрузка фрагметов шаблона (секции, формы и пр.) через ajax-запрос
 *
 * @param {*} wrapper html-обёртка, после которой будет выведена секция
 * @param {*} params
 * @param {*} hide_notice
 * @returns
 */
function tp_get_sections_request(wrapper, params, hide_notice = true, callback = null) {

	let form_data = new FormData();
	for (let key in params) {
		form_data.append(key, params[key]);
	}

	form_data.append('referer', window.location.href);

	params = set_data_params_to_api(form_data);

	send_data_to_api(params, 'form', 'get-section', 'GET', function (data) {
		console.log('success request section');
		if (data.wrapper != null && data.type != null) {
			switch (data.type) {
				case 'ok':
					wrapper.insertAdjacentHTML('beforebegin', data.wrapper);
					wrapper.remove();
					if (typeof callback === 'function') {
						callback(data);
					}
					break;
				case 'error':
				case 'fail':
					let notice = new DOMParser().parseFromString(data.message, 'text/html').getElementsByTagName('div')[0];
					wrapper.prepend(notice);
					console.log('notice', data);
					if (hide_notice) {
						setTimeout(function () {
							tp_slide_toggle_animate(wrapper, 0.1, function () {
								wrapper.remove();
							});
						}, 3000);
					}
					break;
				default: break;
			}
		} else {
			wrapper.insertAdjacentHTML('afterbegin', window.wp_theplugin.notice_section);
		}
	});
}

/**
 * Подгрузка фрагметов шаблона (секции, формы и пр.) через ajax-запрос
 *
 * @param {*} wrapper
 * @returns
 */
function tp_get_sections(wrapper) {

	if (!wrapper.getAttribute('data-section'))
		return false;

	let data_section = JSON.parse(wrapper.getAttribute('data-section'));
	if (data_section.type != null && data_section.part != null) {

		tp_get_sections_request(wrapper, data_section);
	}

	return false;
}

document.addEventListener('DOMContentLoaded', function (e) {
	/**
	 * Если есть секции для подгрузки
	 */
	const get_sections = document.querySelectorAll('.get-sections__placeholder');
	if (get_sections.length) {
		for (let i = 0; i < get_sections.length; i++) {
			const get_section = get_sections[i];
			tp_get_sections(get_section);
		}
	}
});
