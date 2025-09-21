/**
 * Функция получения объекта по переданному классу
 *
 * @param {event} e
 * @param {string} className
 * @returns
 */
function tp_get_target_by_class(e, className) {
	let node = e.target;
	// console.warn(node);
	if (!node.classList.contains(className)) {
		if (!node.closest('.' + className)) {
			return false;
		} else {
			node = node.closest('.' + className);
		}
	}

	return node;
}

/**
 * Функция делегирования события `eventType` на объект `element` с классов `className` для срабатывания функции `handler`
 *
 * @param {*} element
 * @param {*} eventType
 * @param {*} className
 * @param {*} handler
 */
function tp_delegate(element, eventType, className, handler) {
	element.addEventListener(eventType, (e) => {
		if (!tp_get_target_by_class(e, className)) {
			return false;
		}
		handler(e)
	})
}

function tp_get_offset_top(element) {
	let to = element.offsetTop;
	let node = element;

	// Перебираем до последнего родительского элемента для подсчёта Y-координаты element
	while (node.offsetParent && node.offsetParent != document.body) {
		node = node.offsetParent;
		to += node.offsetTop;
	}

	return to;
}

/**
 * Плавный скролл до `element` с учётом отсупа `header`
 *
 * @param {*} element
 */
function tp_scroll_to(element, add_height = 0) {
	let header = document.querySelector('body > header');
	let to = tp_get_offset_top(element);

	// Небольшой отступ и дополнительный отступ, если есть
	to -= (0 + add_height);

	// Если есть header, то вычитаем его положение из Y-координаты element
	if (header) {
		to -= header.offsetHeight;
	}

	window.scrollTo({
		top: to,
		behavior: 'smooth'
	});
}

/**
 * Функция скролла до элемента с ID от `scroll_tab`
 *
 * @param {*} scroll_tab
 */
function tp_scroll_tp_hash_target(scroll_tab) {

	// Если это элемент вызова модального окна
	if (scroll_tab.classList.contains('pillars-popup__btn') || scroll_tab.classList.contains('pillars-popup__close')) {
		return '';
	}

	let tabs_container = scroll_tab.closest('.tabs__container');
	let add_height = 0;
	let scroll_top = document.createElement('div');
	// Проверяем наличие контейнера табов
	if (tabs_container) {
		// Убираем классы активности элементов табов
		tabs_container.querySelectorAll('a.tab-item').forEach(element => {
			element.classList.remove('active');
		});
		// Добавляем класс активности на нажатый таб
		scroll_tab.classList.add('active');
		add_height = tabs_container.clientHeight;
		scroll_top = tabs_container.parentElement;
	} else {
		scroll_top = scroll_tab.parentElement;
	}

	const tab_target = scroll_tab.getAttribute('href').replace('#', '');
	if (document.getElementById(tab_target)) {
		tp_scroll_to(document.getElementById(tab_target), add_height);
	} else if (tab_target == '') {
		tp_scroll_to(scroll_top);
	}
}

/**
 * Функция отправки данных через ajax
 *
 * @param {*} request
 */
function tp_do_ajax(request) {
	var xhr = new XMLHttpRequest();

	if (request.method == "GET") {
		request.url += '?' + request.data;
		request.data = null;
	}
	xhr.open(request.method, request.url);

	if (typeof request.data != 'object') {
		xhr.setRequestHeader('Content-Type', request.contentType);
	}

	if (request.dataType != null) {
		xhr.setRequestHeader('Data-Type', request.contentType);
	}

	xhr.onerror = function () {
		console.warn('ajax request error');
	}

	if (request.log != null) {
		xhr.onprogress = function (event) {
			if (event.lengthComputable) {
				console.log(`Получено ${event.loaded} из ${event.total} байт`);
			} else {
				console.log(`Получено ${event.loaded} байт`); // если в ответе нет заголовка Content-Length
			}
		};
	}

	xhr.onreadystatechange = function () {
		if (xhr.readyState == 3) {
			// console.log(xhr.response);
		}

		if (xhr.readyState == 4) {
			if (xhr.status == 200) {
				request.success(xhr.response);
			} else {
				console.warn(`Ошибка ${xhr.status}: ${xhr.statusText}`);
			}
		}
	}

	xhr.send(request.data);
}

/**
 * Функция скрытия/показа `element` с анимацией слайда "вверх-низ" по продолжительности `delay`
 *
 * @param {*} element
 * @param {float} delay задаётся в секундах, по умолчанию 0.2
 * @returns {bool}
 */
function tp_slide_toggle(element, delay = 0.2) {

	// Если у объекта есть класс `slide-toggle`, то приоставливаем обработку
	if (element.classList.contains('slide-toggle')) {
		return false;
	}

	// Добавляем объекту класс `slide-toggle`, что запретить другие анимации
	element.classList.add('slide-toggle');

	// Переменная с набором style-свойств объекта
	let css_styles = window.getComputedStyle(element);
	// Сохраняем в отдельные переменные style-свойств объекта для анимации margin-top и margin-bottom
	let margin_top = css_styles.marginTop || '0px';
	let margin_bottom = css_styles.marginBottom || '0px';
	// Флаг анимации на скрытие/показа объекта, по умолчание скрытие
	let slide_up = true;
	// Шаг анимации в миллисекундах
	const timestep = 10;
	// Длительность анимации в миллисекундах
	const timeout = delay * 1000;
	// Задаём нулевой шаг итерации
	let step = 0;
	// Общее количество шагов итерации
	let steps = Math.round(timeout / timestep);

	// Определяем вид анимации, если объект скрыт, то флаг ставим на показ
	if (element.style.display == 'none') {
		slide_up = false;
		element.style.removeProperty('display');
		element.style.height = '0px';
	} else {
		element.style.overflow = 'hidden';
	}

	/**
	 * Формируем таймер анимации с длительностью итерации `timestep`
	 */
	let slides = setInterval(function () {
		// console.warn(step + ' / ' + steps);
		// Условие завершения анимации – превышения шагов итерации общего количества
		if (step >= steps) {
			// Корректировка style-свойств в зависимости от флага аниации
			if (slide_up) {
				// Скрытие
				element.style.marginTop = margin_top;
				element.style.marginBottom = margin_bottom;
				element.style.display = 'none';
			} else {
				// Показ
				element.style.removeProperty('overflow');
				element.style.removeProperty('margin-top');
				element.style.removeProperty('margin-bottom');
			}
			// Стираем style-свойств высота объекта
			element.style.removeProperty('height');
			// Убираем класс `slide-toggle` на снятие запрета других анимаций
			element.classList.remove('slide-toggle');
			// Удалеям таймер
			clearInterval(slides);

			return true;
		} else {
			step++;

			// Корректировка style-свойств в зависимости от флага аниации
			if (slide_up) {
				// Скрытие
				element.style.marginTop = parseInt(margin_top) * (1 - step / steps) + 'px';
				element.style.marginBottom = parseInt(margin_bottom) * (1 - step / steps) + 'px';
				element.style.height = element.scrollHeight * (1 - step / steps) + 'px';
			} else {
				// Показ
				element.style.marginTop = parseInt(margin_top) * (step / steps) + 'px';
				element.style.marginBottom = parseInt(margin_bottom) * (step / steps) + 'px';
				element.style.height = element.scrollHeight * (step / steps) + 'px';
			}
		}
	}, timestep);
}

function tp_pageloader_item(wrapper) {
	const container = wrapper.parentElement;

	if (container.classList.contains('pageloader__item')) {
		container.querySelector('.pageloader__container').remove();
		container.classList.remove('pageloader__item');
	} else {
		let loader = document.createElement('div');
		loader.classList.add('pageloader__container');
		loader.innerHTML = '<div class="pageloader"></div>';
		container.prepend(loader);
		container.classList.add('pageloader__item');
	}
}

/**
 * Добавляем событие для отслеживания клика по элементам для возможного скролла
 */
document.addEventListener('click', function (e) {
	let target = e.target;
	if (target.tagName.toLowerCase() != 'a') {
		target = target.closest('a');
	}

	if (target) {
		if (target.tagName.toLowerCase() == 'a' && target.getAttribute('href')) {
			if (target.getAttribute('href').indexOf('#') != -1) {
				console.log('#', target, window.innerWidth, window.wp_data.break_sm);
				if (window.wp_data.break_sm >= window.innerWidth) {
					e.preventDefault();
					tp_scroll_tp_hash_target(target);
				}
			}
		}
	}
});