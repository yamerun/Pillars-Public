document.addEventListener('DOMContentLoaded', function (e) {

	let currencySymbol = '';
	if (window.wp_data.currencysymbol) {
		currencySymbol = window.wp_data.currencysymbol;
	} else if (document.querySelector('.woocommerce-Price-currencySymbol')) {
		currencySymbol = document.querySelector('.woocommerce-Price-currencySymbol').innerHTML;
	}
	console.log('currencySymbol', currencySymbol);

	let selectors = document.querySelectorAll('select[name^="attribute_pa_"]');
	if (selectors.length) {
		for (let i = 0; i < selectors.length; i++) {
			const selector = selectors[i];

			let options = selector.querySelectorAll('option[data-description]');
			if (options.length) {
				const label = selector.closest('form').querySelector('label[for="' + selector.id + '"]');
				label.classList.add('pillars-tip__label');
				label.innerHTML += '<div class="pillars-tip__wrapper"><div class="pillars-tip__icon"></div><div class="pillars-tip__info"></div></div>';
				const tip = label.querySelector('.pillars-tip__info');
				for (let j = 0; j < options.length; j++) {
					const option = options[j];
					tip.innerHTML += '<p>' + option.getAttribute('data-description') + '</p>';
					console.log(option.text, option.getAttribute('data-description'));
				}
			}
		}
	}

	/**
	 *
	 */
	tp_delegate(document.body, 'click', 'pillars-wc-product-gallery__image-link', function (e) {
		const link = tp_get_target_by_class(e, 'pillars-wc-product-gallery__image-link');

		console.log('image-link', link);

		if (link.closest('.pillars-wc-product-thumbnails__wrapper')) {
			e.preventDefault();
			console.log('image-link', 'prevent');
		}
	});

	Fancybox.bind('[data-fancybox]', {
		// Your custom options for a specific gallery
	});

	Fancybox.bind('[data-fancybox="usage-gallery"]', {
		// Your custom options for a specific gallery
	});

	/**
	* Обновление цены на странице Товара
	* @param {*} form
	*/
	function pillars_product_calc_price(form) {

		let price_unit = parseInt(form.getAttribute('data-price'));
		let price_html = form.querySelector('.woocommerce-variation-price .price');

		if (price_unit && currencySymbol && price_html) {
			let price = price_html.innerText.replace(/[^\d.,\s]/g, '');
			let qty = parseInt(form.querySelector('input[name="quantity"]').value);
			let discounts;
			let sale;

			if (document.querySelector('.pillars-wc-quantity__discounts.product-id-' + form.querySelector('[name="product_id"]').value)) {
				discounts = document.querySelector('.pillars-wc-quantity__discounts.product-id-' + form.querySelector('[name="product_id"]').value).getAttribute('data-product_discounts');
				discounts = JSON.parse(discounts);
			} else {
				discounts = {
					count: 0,
					per: 0
				};
			}

			// console.log(discounts);

			let price_adds = form.querySelectorAll('.pillars-wc-product__price-add');
			if (price_adds.length) {
				for (let i = 0; i < price_adds.length; i++) {
					price_unit += parseInt(price_adds[i].getAttribute('data-price'));
				}
			}

			if (discounts.count <= qty && discounts.per != 0) {
				sale = Math.floor(price_unit * parseInt(discounts.per) / 100);
				price_unit = price_unit - sale;
			}

			let price_new = tp_format_price(price_unit * qty);
			price_html.innerHTML = price_html.innerHTML.replace(price, price_new);

			if (sale) {
				if (!form.querySelector('.woocommerce-variation-price .price-discounts')) {
					price_html.insertAdjacentHTML('afterEnd', '<span class="price-discounts"></span>');
				}
				form.querySelector('.woocommerce-variation-price .price-discounts').innerHTML = tp_format_price(sale * qty) + currencySymbol;
			} else if (form.querySelector('.woocommerce-variation-price .price-discounts')) {
				form.querySelector('.woocommerce-variation-price .price-discounts').remove();
			}
		}
	}

	/**
	 * Обвноление Корзины при смене количества товара
	 *
	 * @param {*} form
	 */
	function pillars_update_cart_totals(form) {
		let update = form.querySelector('[name="update_cart"]');
		update.removeAttribute('disabled');
		update.setAttribute('aria-disabled', false);

		update.dispatchEvent(new CustomEvent('click', { 'bubbles': true }));
		console.log('update', update);

		// document.body.dispatchEvent(new CustomEvent('updated_cart_totals', { 'bubbles': true }));
		// document.querySelector('.wc-proceed-to-checkout button').setAttribute('disabled', true);

		// input.change();
		$('[name="update_cart"]').trigger('click');
	}

	/**
	 *
	 */
	tp_delegate(document.body, 'click', 'pillars-wc-quantity__minus', function (e) {
		const btn = tp_get_target_by_class(e, 'pillars-wc-quantity__minus');
		const input = btn.closest('.pillars-wc-quantity__wrapper').querySelector('input.qty');

		let value = parseInt(input.value);

		value -= 1;
		if (value < input.getAttribute('min')) {
			value = input.getAttribute('min');
		}

		input.value = value;
		let form = input.closest('form');
		let event = new CustomEvent('change', { 'bubbles': true });

		input.dispatchEvent(event);

		if (form.classList.contains('cart')) {
			pillars_product_calc_price(form);
		} else if (form.querySelector('[name="update_cart"]')) {
			pillars_update_cart_totals(form);
		}
	});

	/**
	 *
	 */
	tp_delegate(document.body, 'click', 'pillars-wc-quantity__plus', function (e) {
		const btn = tp_get_target_by_class(e, 'pillars-wc-quantity__plus');
		const input = btn.closest('.pillars-wc-quantity__wrapper').querySelector('input.qty');

		let value = parseInt(input.value);

		value += 1;
		if (input.getAttribute('max') && value > input.getAttribute('max')) {
			value = input.getAttribute('max');
		}

		input.value = value;
		let form = input.closest('form');
		let event = new CustomEvent('change', { 'bubbles': true });

		input.dispatchEvent(event);

		if (form.classList.contains('cart')) {
			pillars_product_calc_price(form);
		} else if (form.querySelector('[name="update_cart"]')) {
			pillars_update_cart_totals(form);
		}
	});

	/**
	 * Обновление стоимости товара для подсчёта
	 * @param {*} form
	 */
	function pillars_set_price_data(form) {

		let price_html = form.querySelector('.woocommerce-variation-price');
		let variation_id = form.querySelector('input[name="variation_id"]');
		let display_price = 0;

		if (variation_id) {
			let product_data = JSON.parse(form.getAttribute('data-product_variations'));
			for (let i = 0; i < product_data.length; i++) {
				if (product_data[i].variation_id == variation_id.value) {
					display_price = product_data[i].display_price;
					break;
				}
			}

			form.setAttribute('data-price', display_price);
		}

		if (price_html) {
			if (!price_html.querySelector('.woocommerce-Price-amount')) {
				price_html.innerHTML = '<span class="price"><span class="woocommerce-Price-amount amount"><bdi>0<span class="woocommerce-Price-currencySymbol">' + currencySymbol + '</span></bdi></span></span>';
			}
		} else {
			return;
		}

		price_html = price_html.querySelector('.price');
		if (!variation_id) {
			display_price = parseInt(price_html.innerText.replace(/[^\d.,]/g, ''));
		}

		if (display_price) {
			form.setAttribute('data-price', display_price);
			setTimeout(function () {
				pillars_product_calc_price(form);
			}, 10);
		}
	}

	/**
	 * Обновление класса переключателя выпадающего списка
	 * @param {*} toggle
	 */
	function pillars_set_select_toggle(toggle) {
		if (toggle.classList.contains('active')) {
			toggle.classList.remove('active');
		} else {
			const toggles = document.querySelectorAll('.pillars-select__toggle');
			if (toggles.length) {
				for (let i = 0; i < toggles.length; i++) {
					toggles[i].classList.remove('active');
				}
			}

			toggle.classList.add('active');
		}
	}

	/**
	 * Добавление стилизации select в вариативных товарах
	 * @param {*} select
	 * @param {*} update_price
	 */
	function pillars_select_customize_dropdown(select, update_price = true) {
		let wrapper = '';
		let toggle = '';
		let dropdown = '';
		const options = select.getElementsByTagName('option');

		if (!select.parentElement.querySelector('.pillars-select[data-id="' + select.id + '"]')) {
			wrapper = document.createElement('div');
			toggle = document.createElement('div');
			dropdown = document.createElement('div');

			toggle.className = 'pillars-select__toggle';
			wrapper.append(toggle);

			dropdown.className = 'pillars-select__dropdown';
			wrapper.append(dropdown);

			wrapper.className = 'pillars-select';
			wrapper.setAttribute('data-id', select.id);
			select.after(wrapper);

			select.classList.add('visuallyhidden');
		}

		let permalinks = [];
		if (select.getAttribute('data-permalinks')) {
			permalinks = JSON.parse(select.getAttribute('data-permalinks'));
		}

		wrapper = select.parentElement.querySelector('.pillars-select[data-id="' + select.id + '"]');
		wrapper.querySelector('.pillars-select__toggle').innerHTML = '';
		dropdown = wrapper.querySelector('.pillars-select__dropdown');
		dropdown.innerHTML = ''

		if (options.length) {
			let ul = document.createElement('div');
			ul.className = 'pillars-select__options';
			ul.innerHTML = '';
			dropdown.append(ul);

			ul = dropdown.querySelector('.pillars-select__options');

			for (let j = 0; j < options.length; j++) {
				const option = options[j];
				let li = document.createElement('div');

				li.className = 'pillars-select__option';
				li.innerHTML = option.innerHTML;
				li.setAttribute('value', option.getAttribute('value'));

				if (option.hasAttribute('data-color')) {
					li.insertAdjacentHTML('afterBegin', '<span class="pillars-select__option-color" style="background:' + option.getAttribute('data-color') + ';"></span>');
				}

				if (option.hasAttribute('data-image')) {
					li = document.createElement('a');
					li.className = 'pillars-select__option';
					li.innerHTML = option.innerHTML;
					li.setAttribute('href', permalinks[option.getAttribute('value')]);
					li.insertAdjacentHTML('afterBegin', '<span class="pillars-select__option-image"><img src="' + option.getAttribute('data-image') + '" width="60" height="60"></span>');
					wrapper.classList.add('--image');
				}

				if (option.hasAttribute('data-price')) {
					let price_add = JSON.parse(option.getAttribute('data-price'));
					li.insertAdjacentHTML('beforeEnd', '<span class="pillars-select__option-price" data-price="' + price_add.price + '">+' + price_add.price_html + '</span>');
				}

				if (option.getAttribute('value') == select.value) {
					let selected = li.cloneNode(true);
					wrapper.querySelector('.pillars-select__toggle').append(selected);
				}

				ul.append(li);
			}
		}

		// Обновление стоимости товара за едницу для подсчёта
		if (update_price) {
			setTimeout(function () {
				pillars_set_price_data(select.closest('form'));
			}, 500);

		}
	}

	/**
	 * Вывод стилизации выпадающего списка родственных товара
	 */
	if (document.querySelector('select#product_siblings')) {
		const product_siblings = document.querySelector('select#product_siblings');
		pillars_select_customize_dropdown(product_siblings, false);
		product_siblings.addEventListener('change', function (e) {
			e.preventDefault();
			let permalinks = JSON.parse(this.getAttribute('data-permalinks'));
			location.replace(permalinks[this.value]);
		});
	}

	/**
	 * Вывод стилизации выпадающего списка с/без подстветкой
	 */
	if (document.querySelector('select#product_backlight')) {
		const product_backlight = document.querySelector('select#product_backlight');
		pillars_select_customize_dropdown(product_backlight, false);
		product_backlight.addEventListener('change', function (e) {
			e.preventDefault();
			let permalinks = JSON.parse(this.getAttribute('data-permalinks'));
			location.replace(permalinks[this.value]);
		});
	}

	const attrs_selects = document.querySelectorAll('form.variations_form select');
	if (attrs_selects.length) {
		let observer = new MutationObserver(mutations => {
			for (let mutation of mutations) {
				pillars_select_customize_dropdown(mutation.target);
			}
		});

		let config = { attributes: true, childList: true, characterData: true };

		for (let i = 0; i < attrs_selects.length; i++) {
			const attrs_select = attrs_selects[i];

			const options = attrs_select.getElementsByTagName('option');

			console.log('options', options);

			observer.observe(attrs_select, config);
		}

		setTimeout(function () {
			let i = attrs_selects.length - 1;
			pillars_set_price_data(attrs_selects[i].closest('form'));
		}, 100);

	}

	/**
	 * Открытие/закрытие списка опций атрибутов
	 */
	tp_delegate(document.body, 'click', 'pillars-select__toggle', function (e) {
		e.preventDefault();
		const toggle = tp_get_target_by_class(e, 'pillars-select__toggle');
		pillars_set_select_toggle(toggle);
	});

	/**
	 * Измение значений опций атрибутов с обновлением параметров
	 */
	tp_delegate(document.body, 'click', 'pillars-select__option', function (e) {
		const option = tp_get_target_by_class(e, 'pillars-select__option');

		if (!option.closest('.pillars-select__toggle') && option.getAttribute('value')) {

			e.preventDefault();

			const wrapper = option.closest('.pillars-select');
			const select = document.getElementById(wrapper.getAttribute('data-id'));

			select.value = option.getAttribute('value');

			if (option.querySelector('.pillars-select__option-price')) {
				if (wrapper.parentElement.querySelector('.pillars-wc-product__price-add')) {
					wrapper.parentElement.querySelector('.pillars-wc-product__price-add').innerHTML = option.querySelector('.pillars-select__option-price').innerHTML;
					wrapper.parentElement.setAttribute('data-price', option.querySelector('.pillars-select__option-price').getAttribute('data-price'));
				} else {
					wrapper.insertAdjacentHTML('afterEnd', '<div class="pillars-wc-product__price-add" data-price="' + option.querySelector('.pillars-select__option-price').getAttribute('data-price') + '">' + option.querySelector('.pillars-select__option-price').innerHTML + '</div>');
				}
			} else if (wrapper.parentElement.querySelector('.pillars-wc-product__price-add')) {
				wrapper.parentElement.querySelector('.pillars-wc-product__price-add').remove();
			}

			let event;

			if (document.createEvent) { // Если браузер поддерживает метод, далее инициируем событие
				event = document.createEvent('HTMLEvents');
				event.initEvent('change', true, false);
				select.dispatchEvent(event);
			} else if (document.createEventObject) {  // Если старый IE, используем другой подход
				event = document.createEventObject();
				select.fireEvent('onchange', event);
			}

			wrapper.querySelector('.pillars-select__toggle').classList.remove('active');
		} else {
			// console.log('no toggle', option);
		}
	});

	document.addEventListener('mouseup', function (e) {
		if (!e.target.matches('.pillars-select__toggle.active, .pillars-select__toggle.active *')) {
			const toggles = document.querySelectorAll('.pillars-select__toggle.active');
			if (toggles.length) {
				for (let i = 0; i < toggles.length; i++) {
					toggles[i].classList.remove('active');
				}
			}
		}
	});

	/**
	 * Обновления количества товаров в Корзине
	 */
	function pl_get_count_wc_cart() {
		const params = new URLSearchParams();
		params.append('action', 'pillars_wc_cart_count');

		fetch(window.wp_data.ajax_url, {
			method: "post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(params).toString(),
		})
			.then(response => response.json())
			.then(function (response) {
				console.log('pillars_wc_cart_count', response);
				if (response.content != null && response.type != null) {
					switch (response.type) {
						case 'ok':
							document.getElementById('pillars_wc_cart_contents_count').innerHTML = response.content.count;
							break;
						default: break;
					}
				}
			})
			.catch(function (error) { console.warn(error); });;
	}

	window.addEventListener(
		'visibilitychange',
		function () {
			if (document.hidden === true) return;

			pl_get_count_wc_cart();
		},
		false
	);

	/**
	 * Событие для отправки данных для добавления товара в Корзину
	 */
	tp_delegate(document.body, 'submit', 'cart', function (e) {
		e.preventDefault();

		const form = e.target;
		let form_data = new FormData(form);
		const popup_id = 'add-to-cart-popup';

		form_data.append('action', 'pillars_add_to_cart');

		// Проверяем существования тела модального окна
		tp_set_popup_wrapper(popup_id);
		const popup_current = document.getElementById(popup_id);

		if (!popup_current.querySelector('.get-form')) {
			popup_current.querySelector('.' + window.wp_data.popup_key + '__close').insertAdjacentHTML('afterend', '<div class="get-form"></div>');
		}
		const wrapper = popup_current.querySelector('.get-form');
		wrapper.innerHTML = '';

		fetch(window.wp_data.ajax_url, {
			method: "post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(form_data).toString(),
		})
			.then(response => response.json())
			.then(function (response) {
				if (response?.status == 'ok') {
					console.log(response.message);

					tp_popup_open(popup_current);

					const data = response.message;

					if (data.wrapper != null && data.type != null) {
						switch (data.type) {
							case 'ok':
								wrapper.insertAdjacentHTML('beforeend', data.wrapper);
								pl_get_count_wc_cart();
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
				}
			})
			.catch(function (error) { console.warn(error); });
	});
});