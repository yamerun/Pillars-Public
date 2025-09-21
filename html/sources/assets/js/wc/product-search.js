/**
 * Глобальный параметр поиска товара
 */
let search_products_term = '';

/**
 * Поиск товара через API
 *
 * @param {*} input
 * @param {*} search_wrapper
 */
function pl_search_product_api(input, search_wrapper) {
	const form = input.closest('form');
	let term = input.value;

	if (term.length > 2 /* && term != search_products_term */) {

		// search_products_term = term;

		form.classList.add('search-active');

		const params = new URLSearchParams();
		params.append('t', term);
		params.append('search_verify_key', document.getElementById('search_verify_key').value);
		params.append('action', 'pillars_search_product');

		send_data_to_api(params, '', 'search-product', 'GET', function (data) {
			console.log('success request search', data);
			if (data.status != null) {
				let box = form.getBoundingClientRect();

				search_wrapper.style.display = 'inline-block';
				search_wrapper.style.position = 'absolute';
				search_wrapper.style.top = box.top + document.body.scrollTop + form.offsetHeight + 'px';
				search_wrapper.style.left = box.left + document.body.scrollLeft + 'px';

				switch (data.status) {
					case 'ok':
						search_wrapper.innerHTML = '<ul>' + data.wrapper + '</ul>';
						break;
					default:
						if (data.notice != null) {
							search_wrapper.innerHTML = data.notice;
						} else {
							search_wrapper.innerHTML = 'Ошибка запроса поиска.<br>Пожалуйста, обновите страницу.';
						}
						break;
				}
				form.classList.remove('search-active');
			}
		});
	} else {
		search_wrapper.style.display = 'none';
		search_wrapper.innerHTML = '';
	}
}

const search_products_inputs = document.querySelectorAll('.form-search-products__input');
if (search_products_inputs.length) {

	const search_id = 'pillars-search-products-result';
	let search_products_view = false;

	if (!document.getElementById(search_id)) {
		const wrapper = document.createElement('div');
		wrapper.setAttribute('id', search_id);
		document.body.append(wrapper);
	}

	const search_wrapper = document.getElementById(search_id);
	search_wrapper.style.display = 'none';
	search_wrapper.innerHTML = '';

	search_wrapper.addEventListener('mouseover', () => { search_products_view = true; });
	search_wrapper.addEventListener('mouseout', () => { search_products_view = false; });

	for (let s = 0; s < search_products_inputs.length; s++) {
		const search_products_input = search_products_inputs[s];

		search_products_input.addEventListener('focus', () => {
			pl_search_product_api(search_products_input, search_wrapper);
		});

		search_products_input.addEventListener('keyup', () => {
			pl_search_product_api(search_products_input, search_wrapper);
		});

		search_products_input.addEventListener('blur', () => {
			if (!search_products_view) {
				search_products_input.value = '';
				search_products_term = '';
				search_wrapper.style.display = 'none';
				search_wrapper.innerHTML = '';
			}
		});
	}

	/**
	 * Анимация текста placeholder
	 */
	let products_input_placeholder = search_products_inputs[0];
	let placeholder_step = 0;
	let placeholder_input = '';
	const placeholder_texts = ['Качели', 'Парклеты', 'Скамейки', 'Перголы', 'Кашпо', 'Шары', 'Столы'];
	let placeholder_text = 0;
	const placeholder_speed = 300;

	function pl_products_input_placeholder() {
		placeholder_input += placeholder_texts[placeholder_text].charAt(placeholder_step);
		products_input_placeholder.setAttribute('placeholder', 'Например: ' + placeholder_input);
		placeholder_step++;
		if ((placeholder_texts[placeholder_text].length + 10) < placeholder_step) {
			placeholder_step = 0;
			placeholder_input = '';
			placeholder_text++;
			if (placeholder_texts.length <= placeholder_text) {
				placeholder_text = 0;
			}
		}
		setTimeout(pl_products_input_placeholder, placeholder_speed);
	}

	pl_products_input_placeholder();
}