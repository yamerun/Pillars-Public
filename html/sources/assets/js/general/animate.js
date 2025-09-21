/**
 * Функция скрытия/показа `element` с анимацией слайда "вверх-низ" по продолжительности `delay`
 * итерация анимации происходит через `requestAnimationFrame API`
 *
 * @param {*} element
 * @param {float} delay задаётся в секундах, по умолчанию 0.2
 * @param {*} is_slide флаг на принудительный тип анимации, `true` – скрытие, `false` – открытие
 * @returns {bool}
 */
function tp_slide_toggle_animate(element, delay = 0.1, is_slide = null, handle = null) {

	// Если у объекта есть класс `slide-toggle`, то приоставливаем обработку
	if (element.classList.contains('slide-toggle')) {
		return false;
	}

	// Добавляем объекту класс `slide-toggle`, что запретить другие анимации
	element.classList.add('slide-toggle');

	// Если это мобильное устройство или малый экран
	if (window.innerWidth < window.wp_theplugin.break_sm) {
		// То сокращаем длителность анимации вдвое
		delay = delay / 2;
	}

	// ID анимации для запуска/остановки
	let reqAnimationId;
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
	element.style.overflow = 'hidden';
	if (is_slide == null) {
		if (element.style.display == 'none') {
			slide_up = false;
			element.style.removeProperty('display');
			element.style.height = '0px';
		} else {

		}
	} else {
		slide_up = is_slide;
	}

	/**
	 * Функция итерации анимации
	 * @returns
	 */
	function slide_animate() {
		// console.warn(slide_up + ': ' + step + ' / ' + steps);
		// Условие завершения анимации – превышения шагов итерации общего количества
		if (step < steps) {
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

			// Запускаем анимацию рекурсивно
			reqAnimationId = requestAnimationFrame(slide_animate);
		} else {
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

			// Останавливаем анимацию
			if (reqAnimationId) {
				cancelAnimationFrame(reqAnimationId);
				// Если есть переменная `handle`
				if (handle) {
					// Проверяем является ли `handle` функцией
					if (typeof handle === 'function') {
						handle();
					}
				}
			}
			return true;
		}
	}

	// Останавливаем анимацию, если она была ранее запущена
	if (reqAnimationId) {
		cancelAnimationFrame(reqAnimationId)
	}

	// Запускаем анимацию
	reqAnimationId = requestAnimationFrame(slide_animate);
}

tp_delegate(document.body, 'click', 'tp-target-toggle', function (e) {
	e.preventDefault();

	const target = e.target;

	let id = target.getAttribute('href').replace('#', '');
	const el = document.getElementById(id);

	tp_slide_toggle_animate(el);
});