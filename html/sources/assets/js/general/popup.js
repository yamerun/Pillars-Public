/************************************
 * POPUP
 ************************************/

const popup_delay = 400;
const popup_body = document.querySelector('body');
// Основной префик элементов модального окна
const popup_key = window.wp_data.popup_key;
const popup_class = '.' + popup_key;
// Список элементов, которым нужно добавлять отсутп при открытии модального окна
const popup_padding = document.querySelectorAll(popup_class + '__padding');
// Запрет на анимацию
let popup_unlock = true;


function tp_set_popup_wrapper(popup_id) {
	const popup_wrapper = document.getElementById(popup_id);

	if (!popup_wrapper) {
		const popup = document.createElement('div');

		popup.setAttribute('id', popup_id);
		popup.classList.add(popup_key);
		popup.innerHTML = window.wp_data.popup_wrapper.replace('#popup_id', '#' + popup_id);

		popup_body.append(popup);
	}
}

/**
 * Открытие переданного модального окна
 */
function tp_popup_open(popup_current) {
	// Проверяем наличие переданного модального окна и отстутвие запрета на анимацию
	if (popup_current && popup_unlock) {
		const popup_active = document.querySelector(popup_class + '.open');
		// Проверяем наличие открытых модальных окон
		if (popup_active) {
			tp_popup_close(popup_active, false);
		} else {
			tp_body_collapse();
		}
		popup_current.classList.add('open');
		// Добавляем событие закрытие модального окна при клике вне контента окна
		popup_current.addEventListener('click', function (e) {
			if (!e.target.closest(popup_class + '__content')) {
				tp_popup_close(e.target.closest(popup_class));
			}
		});
	}
}

/**
 * Закрытие переданного модального окна
 */
function tp_popup_close(popup_active, uncollapse = true) {
	if (popup_unlock) {
		popup_active.classList.remove('open');
		if (popup_active.querySelector('.video-wrapper > iframe')) {
			popup_active.querySelector('.video-wrapper > iframe').remove();
		}
		if (uncollapse) {
			tp_body_uncollapse();
		}
	}
}

/**
 *
 */
function tp_body_collapse() {
	// Определяем ширину вертикального скролла
	const scrollbar_width = window.innerWidth - document.querySelector('main').offsetWidth + 'px';

	if (popup_padding.length) {
		// Фиксированным элементам добавлем отступ на ширину скролла
		for (let i = 0; i < popup_padding.length; i++) {
			const el = popup_padding[i];
			el.style.paddingRight = scrollbar_width;
		}
	}

	// body добавлем отступ на ширину скролла
	popup_body.style.paddingRight = scrollbar_width;
	// body добавлем класс для блокировки скролла
	popup_body.classList.add('collapse');

	popup_unlock = false;
	setTimeout(function () { popup_unlock = true; }, popup_delay);
}

/**
 *
 */
function tp_body_uncollapse() {
	setTimeout(function () {
		if (popup_padding.length) {
			// Фиксированным элементам убираем справа отступ
			for (let i = 0; i < popup_padding.length; i++) {
				const el = popup_padding[i];
				el.style.paddingRight = '0px';
			}
		}
		// body убираем справа отступ
		popup_body.style.paddingRight = '0px';
		// body удаляем класс для блокировки скролла
		popup_body.classList.remove('collapse');
	}, popup_delay);

	popup_unlock = false;
	setTimeout(function () { popup_unlock = true; }, popup_delay);
}

/**
 * Добавляем событие закрытие модального окна при нажатии клавиши Esc
 */
document.addEventListener('keydown', function (e) {
	if (e.which === 27) {
		const popup_active = document.querySelector(popup_class + '.open');
		if (popup_active) {
			tp_popup_close(popup_active);
		}
	}
});


// Поиск элементов `{popup_key}__btn для` события открытия модального окна
tp_delegate(document.body, 'click', popup_key + '__btn', function (e) {
	e.preventDefault();

	const target = tp_get_target_by_class(e, popup_key + '__btn');
	let popup_id = '';
	// Проверяем наличие атрибута `data-id`
	if (target.hasAttribute('data-id')) {
		popup_id = target.getAttribute('data-id');
	} else {
		// Если нет атрибута, то проверяем url на наличие якоря
		popup_id = target.getAttribute('href').replace('#', '');
	}

	// Проверяем существования тела модального окна
	tp_set_popup_wrapper(popup_id);

	const popup_current = document.getElementById(popup_id);

	// Проверяем наличие атрибута `data-form` для подгрузки формы
	if (target.hasAttribute('data-form')) {
		// Проверяем существование формы
		if (!document.getElementById(target.getAttribute('data-form'))) {
			if (!popup_current.querySelector('.get-form')) {
				popup_current.querySelector(popup_class + '__close').insertAdjacentHTML('afterend', '<div class="get-form"></div>');
			}
			const popup_form = popup_current.querySelector('.get-form');
			popup_form.innerHTML = '';
			popup_form.classList.add('block-loading');

			let form_args = target.getAttribute('data-form_args') ?? null;

			// Загружаем форму
			tp_get_sections_request(popup_form, { type: "form", part: target.getAttribute('data-form'), path: "theme", args: form_args }, false, function (data) {
				if (data.params.part != undefined) {
					console.log('sections-request', data.params.part);
					let form_id = data.params.part;
					$('#' + form_id).find('input.mask-phone').maskPhone();
					$('#' + form_id).find('input.mask-date').maskDate();

					const phone_country_code = document.getElementById(form_id).querySelector('.phone-country-code');
					if (phone_country_code) {
						const phone_country_code_list = phone_country_code.querySelector('.phone-country-code__list');
						phone_country_code_list.innerHTML = '';
						form_phone_init_list_country(phone_country_code_list);
					}
				}
			});
		}

		wp_nonce_update();
	}

	tp_popup_open(popup_current);
});

// Поиск элементов `{popup_key}__close` для события закрытия модального окна
tp_delegate(document.body, 'click', popup_key + '__close', function (e) {
	e.preventDefault();

	const target = e.target;
	const popup_current = target.closest(popup_class);
	tp_popup_close(popup_current);
});
