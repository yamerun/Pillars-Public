const countries = [
	{ name: 'Россия', code: 7, iso: 'ru', alter: '', placeholder: '(912) 345-67-89', mask: '(^^^) ^^^-^^-^^' },
	{ name: 'Азербайджан', code: 994, iso: 'az', alter: 'Azərbaycan', placeholder: '40 123 45 67', mask: '^^ ^^^ ^^ ^^' },
	{ name: 'Армения', code: 374, iso: 'am', alter: 'Հայաստան', placeholder: '77 123456', mask: '^^ ^^^^^^' },
	{ name: 'Беларусь', code: 375, iso: 'by', alter: '', placeholder: '29 491-19-11', mask: '^^ ^^^-^^-^^' },
	{ name: 'Казахстан', code: 7, iso: 'kz', alter: 'Қазақстан', placeholder: '771 000 9998', mask: '^^^ ^^^ ^^^^' },
	{ name: 'Кыргызстан', code: 996, iso: 'kg', alter: '', placeholder: '700 123 456', mask: '^^^ ^^^ ^^^^' },
	{ name: 'Молдова', code: 373, iso: 'md', alter: '', placeholder: '621 12 345', mask: '^^^ ^^ ^^^' },
	{ name: 'Таджикистан', code: 992, iso: 'tj', alter: 'Тоҷикистон', placeholder: '917 12 3456', mask: '^^^ ^^ ^^^^' },
	{ name: 'Туркменистан', code: 993, iso: 'tm', alter: 'Türkmenistan', placeholder: '66 123456', mask: '^^ ^^^^^^' },
	{ name: 'Узбекистан', code: 998, iso: 'uz', alter: 'Oʻzbekiston', placeholder: '91 234 56 78', mask: '^^ ^^^ ^^ ^^' },
];

/**
 *
 * @param {*} country_list
 */
function form_phone_init_list_country(country_list) {
	// Заполняем выпадающий список
	countries.forEach(country => {
		const li = document.createElement('li');
		li.classList.add('phone-country-code__item');
		li.role = 'option';
		li.tabindex = '-1';
		li.dataset.dialCode = country.code;
		li.dataset.countryCode = country.iso;
		li.ariaSelected = false;

		li.innerHTML = `<div class="phone-country-code__flag --${country.iso}"></div><span class="phone-country-code__name">${country.name}</span><span class="phone-country-code__item-dial">+${country.code}</span>`;
		country_list.appendChild(li);
	});

	// Устанавливаем страну по умолчанию
	const phone = form_phone_get_container(country_list);
	form_phone_set_country(phone, countries[0]);
}

/**
 *
 * @param {*} phone
 * @param {*} country
 */
function form_phone_set_country(phone, country) {
	document.querySelectorAll('.phone-country-code__container .phone-country-code__flag').forEach(flag => {
		flag.parentElement.querySelector('.phone-country-code__dial').textContent = '+' + country.code;
		flag.removeAttribute('class');
		flag.classList.add('phone-country-code__flag', '--' + country.iso);

		const input = form_phone_get_input(flag.parentElement);
		input.dataset.mask = '+' + country.code + ' ' + country.mask;
		input.setAttribute('placeholder', '+' + country.code + ' ' + country.placeholder);
		input.style = 'padding-left: ' + (phone.offsetWidth - phone.querySelector('.phone-country-code__dial').offsetWidth) + 'px !important';
	});

	$('input.mask-phone, .mask-phone input').maskPhone();
}

/**
 *
 * @param {*} country_list
 * @returns
 */
function form_phone_get_container(country_list) {
	return country_list.closest('.phone-country-code').querySelector('.phone-country-code__container');
}

/**
 *
 * @param {*} phone
 * @returns
 */
function form_phone_get_input(phone) {
	const wrapper = phone.closest('.phone-country-code').parentElement;
	const input = wrapper.querySelector('input.mask-phone');

	return input;
}

document.addEventListener('DOMContentLoaded', () => {
	const phone_inputs = document.querySelectorAll('.phone-country-code__list');
	if (phone_inputs.length) {
		for (let i = 0; i < phone_inputs.length; i++) {
			const country_list = phone_inputs[i];
			country_list.innerHTML = '';
			form_phone_init_list_country(country_list);
		}
	}
});

// Закрытие списка при клике вне его
document.addEventListener('click', (e) => {
	const country_lists = document.querySelectorAll('.phone-country-code__list:not(.d-none)');
	if (country_lists.length) {
		for (let i = 0; i < country_lists.length; i++) {
			const country_list = country_lists[i];
			const wrapper = country_list.closest('.phone-country-code');
			if (!wrapper.contains(e.target)) {
				country_list.classList.add('d-none');
			}
		}

	}
});

tp_delegate(document.body, 'click', 'phone-country-code__item', function (e) {
	e.preventDefault();
	const item = tp_get_target_by_class(e, 'phone-country-code__item');
	const country_list = item.closest('.phone-country-code__list');
	const phone = form_phone_get_container(country_list);

	if (item) {
		const code = item.dataset.countryCode;
		const country = countries.find(c => c.iso === code);
		if (country) {
			form_phone_set_country(phone, country);
			let input = form_phone_get_input(phone);
			input.focus();
		}
		country_list.classList.add('d-none');
	}
});

tp_delegate(document.body, 'click', 'phone-country-code__container', function (e) {
	e.preventDefault();
	const item = tp_get_target_by_class(e, 'phone-country-code__container');
	const list = item.closest('.phone-country-code').querySelector('.phone-country-code__list');

	const wrapper = item.closest('.phone-country-code').parentElement;
	const input = form_phone_get_input(item.closest('.phone-country-code'));
	input.blur();
	wrapper.classList.add('active');
	list.classList.toggle('d-none');
});

/*
document.addEventListener('DOMContentLoaded', () => {
	const phoneInput = document.getElementById('phone-input');
	const countryDropdown = document.querySelector('.country-dropdown');
	const countryList = document.querySelector('.country-list');
	const selectedCountry = document.querySelector('.selected-country');
	const selectedFlag = selectedCountry.querySelector('.flag-icon');
	const selectedCode = selectedCountry.querySelector('.country-code');

	// Инициализация
	function init() {
		// Заполняем выпадающий список
		countries.forEach(country => {
			const li = document.createElement('li');
			li.dataset.code = country.code;
			li.dataset.iso = country.iso;
			li.innerHTML = `<span class="flag-icon">${country.flag}</span> <span>${country.name}</span> <span class="country-code">${country.code}</span>`;
			countryList.appendChild(li);
		});

		// Устанавливаем страну по умолчанию
		setCountry(countries[0]);
	}

	// Установка выбранной страны
	function setCountry(country) {
		selectedFlag.textContent = country.flag;
		selectedCode.textContent = country.code;
		selectedFlag.style.backgroundImage = `url(https://flagcdn.com/24x18/${country.iso}.png)`;
		phoneInput.value = country.code + ' ';
		phoneInput.focus();
	}

	// Обработка клика по выпадающему списку
	countryDropdown.addEventListener('click', (e) => {
		countryList.classList.toggle('hidden');
		e.stopPropagation();
	});

	// Обработка выбора страны из списка
	countryList.addEventListener('click', (e) => {
		const selectedLi = e.target.closest('li');
		if (selectedLi) {
			const code = selectedLi.dataset.code;
			const country = countries.find(c => c.code === code);
			if (country) {
				setCountry(country);
			}
			countryList.classList.add('hidden');
		}
	});

	// Закрытие списка при клике вне его
	document.addEventListener('click', (e) => {
		if (!countryDropdown.contains(e.target)) {
			countryList.classList.add('hidden');
		}
	});

	// Форматирование номера телефона
	phoneInput.addEventListener('input', (e) => {
		let value = e.target.value.trim();
		const currentCode = selectedCode.textContent.trim();

		// Если код страны не совпадает, переключаем
		const matchingCountry = countries.find(c => value.startsWith(c.code));
		if (matchingCountry && matchingCountry.code !== currentCode) {
			setCountry(matchingCountry);
			// Устанавливаем курсор в правильную позицию после смены кода
			requestAnimationFrame(() => {
				phoneInput.selectionStart = phoneInput.value.length;
				phoneInput.selectionEnd = phoneInput.value.length;
			});
		}

		// Удаляем все, кроме цифр
		value = value.replace(/\D/g, '');

		// Если значение не пустое, добавляем + и код страны
		if (value.length > 0) {
			e.target.value = '+' + value;
		}
	});

	init();
});

document.addEventListener('DOMContentLoaded', () => {
	const phoneInput = document.getElementById('phone');

	// Функция для очистки номера от всего, кроме цифр
	const getCleanNumber = (value) => {
		return value.replace(/\D/g, '');
	};

	// Функция для форматирования номера телефона
	const formatPhoneNumber = (value) => {
		const cleaned = getCleanNumber(value);
		let formatted = '';

		if (cleaned.length > 0) {
			formatted += '+7';
		}
		if (cleaned.length > 1) {
			formatted += ' (' + cleaned.substring(1, 4);
		}
		if (cleaned.length > 4) {
			formatted += ') ' + cleaned.substring(4, 7);
		}
		if (cleaned.length > 7) {
			formatted += '-' + cleaned.substring(7, 9);
		}
		if (cleaned.length > 9) {
			formatted += '-' + cleaned.substring(9, 11);
		}

		return formatted;
	};

	// Обработчик события ввода
	phoneInput.addEventListener('input', (e) => {
		const value = e.target.value;
		const formattedValue = formatPhoneNumber(value);
		e.target.value = formattedValue;
	});

	// Обработчик события нажатия клавиши для предотвращения удаления маски
	phoneInput.addEventListener('keydown', (e) => {
		const value = e.target.value;
		const cleanNumber = getCleanNumber(value);

		// Разрешаем Backspace, стрелки и другие служебные клавиши
		if (e.key === 'Backspace' || e.key.startsWith('Arrow')) {
			return;
		}

		// Запрещаем ввод, если достигнута максимальная длина (11 цифр)
		if (cleanNumber.length >= 11) {
			e.preventDefault();
		}
	});

	// Установка начального значения маски при фокусе
	phoneInput.addEventListener('focus', () => {
		if (phoneInput.value === '') {
			phoneInput.value = '+7';
		}
	});

	// Удаление маски, если поле осталось пустым после потери фокуса
	phoneInput.addEventListener('blur', () => {
		if (getCleanNumber(phoneInput.value).length === 1) {
			phoneInput.value = '';
		}
	});
});
*/