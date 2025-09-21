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
	let flag = phone.querySelector('.phone-country-code__flag');
	const input = form_phone_get_input(phone);
	flag.removeAttribute('class');
	flag.classList.add('phone-country-code__flag', '--' + country.iso);
	phone.querySelector('.phone-country-code__dial').textContent = '+' + country.code;

	input.setAttribute('placeholder', '+' + country.code + ' ' + country.placeholder);
	input.dataset.mask = '+' + country.code + ' ' + country.mask;
	input.style = 'padding-left: ' + (phone.offsetWidth - phone.querySelector('.phone-country-code__dial').offsetWidth) + 'px !important';
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