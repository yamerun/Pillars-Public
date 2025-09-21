/**
 * Добавление эффекта подгрузки изображений с анимацией ожидания skeleton
 */
const preload_items = document.querySelectorAll('.preload-items');

// Поиск контейеров, где на изображения нужно добавить эффект
if (preload_items.length) {
	for (let l = 0; l < preload_items.length; l++) {
		const preload_item = preload_items[l];

		// Поиск изображений только с ленивой загрузкой
		const loadimages = preload_item.querySelectorAll('img[loading="lazy"]');
		if (loadimages.length) {
			for (let i = 0; i < loadimages.length; i++) {
				const loadimage = loadimages[i];
				if (!loadimage.complete) {

					// Проверяем родителя изображения
					let parent_image = loadimage.parentElement;
					// Если это .media-ratio, то берём родитея выше по уровню
					if (parent_image.classList.contains('media-ratio')) {
						parent_image = parent_image.parentElement;
					}

					parent_image.classList.add('preload-item');
					loadimage.addEventListener('load', function (e) {
						parent_image.classList.remove('preload-item');
					});

					setTimeout(() => {
						parent_image.classList.remove('preload-item');
					}, 6000);
				}
			}
		}
	}
}

/**
 * Блокировка всплытия меню Каталога при наличии курсора над элементом меню
 */
document.addEventListener('DOMContentLoaded', function (e) {

	let catalog = document.getElementById('menu-item-catalog');
	catalog.classList.add('unhover');

	document.addEventListener('mousemove', function (event) {
		let coords = catalog.getBoundingClientRect();
		let mouseX = event.clientX;
		let mouseY = event.clientY;

		if (coords.top <= mouseY && mouseY <= coords.bottom && coords.left <= mouseX && mouseX <= coords.right) {
			setTimeout(function () {
				catalog.classList.remove('unhover');
			}, 500);
		} else {
			catalog.classList.remove('unhover');
		}
	}, { once: true });
});