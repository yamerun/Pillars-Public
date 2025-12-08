// Примагничивание панели навигации по секциям Товарам
if (document.querySelector('.pillars-wc-product-tabs__nav')) {
	const box = document.querySelector('.pillars-wc-product-tabs__nav').getBoundingClientRect();
	const elY = box.top + window.pageYOffset;
	let elOffset = 0;

	const product_tabs_nav = {
		el: document.querySelector('.pillars-wc-product-tabs__nav'),
		show() {
			// закрепляем панель
			this.el.classList.add('--fixed');
			// Если экран браузера под мобильный формат, то добавялем отсуп шапки сайта
			if (window.innerWidth < window.wp_theplugin.break_sm) {
				elOffset = document.querySelector('body > header').offsetHeight;
				this.el.style.top = elOffset + 'px';
			} else {
				this.el.style.top = '0px';
				elOffset = 0;
			}
		},
		hide() {
			// открепляем панель
			this.el.classList.remove('--fixed');
			if (window.innerWidth < window.wp_theplugin.break_sm) {
				this.el.style.top = '0px';
				elOffset = 0;
			}
		},
		addEventListener() {
			window.addEventListener('scroll', () => {
				// определяем величину прокрутки
				const scrollY = window.scrollY || document.documentElement.scrollTop;
				// если страница прокручена больше чем положение панели навигации, то примагничиванием к верху окна браузера
				scrollY > elY ? this.show() : this.hide();

				// Если есть элементы секций из навигации
				const navbar = this.el;
				const tabs_items = this.el.querySelectorAll('.pillars-wc-product-tabs__item');
				if (tabs_items.length) {
					// Задаём первичный выбор секции
					let nav = tabs_items[0];
					// Ищем секцию в поле видимости браузера
					tabs_items.forEach(element => {
						element.classList.remove('active');
						if (element.getAttribute('data-id')) {
							const id = element.getAttribute('data-id');
							if (document.getElementById(id)) {
								const section = document.getElementById(id).getBoundingClientRect();
								if (scrollY > (section.top + window.pageYOffset - navbar.offsetHeight - elOffset)) {
									nav = element;
								}
							}
						}
					});

					// Задаём класс активности элемента навигации для видимой секции
					nav.classList.add('active');
				}
			});
		}
	}

	product_tabs_nav.addEventListener();
}