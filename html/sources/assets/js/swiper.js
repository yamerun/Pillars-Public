const swiper_wrapper_pagination = '<div class="pillars-slider__pagination"></div>';
const swiper_wrapper_buttons = '<div class="pillars-slider__buttons"><div class="pillars-slider__button-prev"></div><div class="pillars-slider__button-next"></div></div>';

let swiper_args = {
	navigation: {
		nextEl: '.pillars-slider__button-next',
		prevEl: '.pillars-slider__button-prev'
	},
	pagination: {
		el: '.pillars-slider__pagination',
		// Буллеты
		type: 'bullets',
		clickable: true,
	},
	spaceBetween: 20,
};

document.addEventListener('DOMContentLoaded', function (e) {

	let informer_slider = document.querySelector('.informer-slider__container');
	if (informer_slider) {
		let informer_swiper = new Swiper('.informer-slider__container', {
			navigation: swiper_args.navigation,
			pagination: swiper_args.pagination,

			// Количество слайдов для показа
			slidesPerView: 1,
			// Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			watchOverflow: true,
			// Отступ между слайдами
			spaceBetween: swiper_args.spaceBetween,
			// Количество слайдов в пролистывании
			slidesPerGroup: 1,
			// Зациклить
			loop: false,
			// Отключение автопролистывания до границ слайда
			freeMode: false,
			speed: 500,

			autoplay: {
				delay: 5000,
			},
		});
	}

	let portfolio_slider = document.querySelector('.portfolio-slider__container');
	if (portfolio_slider) {
		let portfolio_swiper = new Swiper('.portfolio-slider__container', {
			navigation: swiper_args.navigation,
			pagination: swiper_args.pagination,

			// Количество слайдов для показа
			slidesPerView: 1,
			// Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			watchOverflow: true,
			// Отступ между слайдами
			spaceBetween: swiper_args.spaceBetween,
			// Количество слайдов в пролистывании
			slidesPerGroup: 1,
			// Зациклить
			loop: false,
			// Отключение автопролистывания до границ слайда
			freeMode: false,
			speed: 500,
		});
	}

	let news_slider = document.querySelector('.news-slider__container');
	if (news_slider) {
		let news_swiper = new Swiper('.news-slider__container', {
			navigation: swiper_args.navigation,
			pagination: swiper_args.pagination,

			// Количество слайдов для показа
			slidesPerView: 1,
			// Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			watchOverflow: true,
			// Отступ между слайдами
			spaceBetween: swiper_args.spaceBetween,
			// Количество слайдов в пролистывании
			slidesPerGroup: 1,
			// Зациклить
			loop: false,
			// Отключение автопролистывания до границ слайда
			freeMode: false,
			speed: 500,
			breakpoints: {
				480: {
					slidesPerView: 1,
					slidesPerGroup: 1,
				},
				768: {
					slidesPerView: 2,
					slidesPerGroup: 2,
				},
				1200: {
					slidesPerView: 3,
					slidesPerGroup: 3,
				},
			},
		});
	}


	let product_slider = document.querySelector('.product-slider__container');
	if (product_slider) {
		let product_swiper = new Swiper('.product-slider__container', {
			navigation: swiper_args.navigation,
			pagination: swiper_args.pagination,

			// Количество слайдов для показа
			slidesPerView: 2,
			// Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			watchOverflow: false,
			// Отступ между слайдами
			spaceBetween: swiper_args.spaceBetween,
			// Количество слайдов в пролистывании
			slidesPerGroup: 2,
			// Зациклить
			loop: false,
			// Отключение автопролистывания до границ слайда
			freeMode: false,
			speed: 500,
			breakpoints: {
				768: {
					slidesPerView: 3,
					slidesPerGroup: 3,
				},
				1200: {
					slidesPerView: 4,
					slidesPerGroup: 4,
				},
			},
		});
	}

	// Инициализация превью слайдера
	let product_gallery_thumb = document.querySelector('.pillars-wc-product-thumbnails__container');
	let sliderThumbs = {};
	if (product_gallery_thumb) {
		sliderThumbs = new Swiper('.pillars-wc-product-thumbnails__container', { // ищем слайдер превью по селектору
			// задаем параметры
			direction: 'vertical', // вертикальная прокрутка
			slidesPerView: 4, // показывать по 3 превью
			spaceBetween: 11, // расстояние между слайдами
			watchOverflow: true, // Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			navigation: swiper_args.navigation,
			freeMode: true, // при перетаскивании превью ведет себя как при скролле
			breakpoints: { // условия для разных размеров окна браузера
				0: { // при 0px и выше
					direction: 'horizontal', // горизонтальная прокрутка
				},
				768: { // при 768px и выше
					direction: 'vertical', // вертикальная прокрутка
				}
			},

			// можно прокручивать изображения колёсиком мыши
			mousewheel: true,

			// Обновление свайпера при открытие через табы или при изменении родительского/дочернего элемента
			// При изменении элементов слайдера
			observer: true,

			// При изменении родительского элемента
			observeParents: true,

			// При изменении дочернего элемента
			observeSlideChildren: true,
		});
	}

	let product_gallery_images = document.querySelector('.pillars-wc-product-gallery__container');
	// Инициализация слайдера изображений
	if (product_gallery_images) {
		let product_gallery_images_args = {
			// задаем кнопки навигации
			navigation: swiper_args.navigation,
			pagination: swiper_args.pagination,

			mousewheel: true, // можно прокручивать изображения колёсиком мыши
			// Управление клавиатурой
			keyboard: {
				enabled: true,
				// Только в пределах слайдера
				onlyInViewport: true,
				// pageUp, pageDown
				pageUpDown: true,
			},
			autoHeight: true,

			// Количество слайдов для показа
			slidesPerView: 1,
			// Деактивация слайдера при меньшем или равном количестве слайдов, чем в slidesPerView
			watchOverflow: true,
			// расстояние между слайдами
			spaceBetween: 0,

			grabCursor: true, // менять иконку курсора
			/*
			thumbs: { // указываем на превью слайдер
				swiper: sliderThumbs // указываем имя превью слайдера
			},
			*/

			// Обновление свайпера при открытие через табы или при изменении родительского/дочернего элемента
			// При изменении элементов слайдера
			observer: true,

			// При изменении родительского элемента
			observeParents: true,

			// При изменении дочернего элемента
			observeSlideChildren: true,

			autoplay: {
				delay: 10000,
			},
		};

		if (product_gallery_thumb) {
			product_gallery_images_args.thumbs = { // указываем на превью слайдер
				swiper: sliderThumbs // указываем имя превью слайдера
			};
		}

		const product_gallery_images = new Swiper('.pillars-wc-product-gallery__container', product_gallery_images_args);
		const product_gallery_images_container = document.querySelector('.pillars-wc-product-gallery__images');

		// При наведении мыши (mouseenter)
		product_gallery_images_container.addEventListener('mouseenter', function () {
			product_gallery_images.autoplay.stop(); // Останавливаем автопрокрутку
		});

		// Когда мышь уходит (mouseleave)
		product_gallery_images_container.addEventListener('mouseleave', function () {
			product_gallery_images.autoplay.start(); // Запускаем автопрокрутку снова
		});
	}

});