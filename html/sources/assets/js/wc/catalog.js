document.addEventListener('DOMContentLoaded', () => {
	const loop_product_links = document.querySelectorAll('.woocommerce-loop-product__link .cover');
	if (loop_product_links.length) {
		// Преобразуем NodeList в массив
		const loop_product_items = Array.from(loop_product_links);
		const product_group_size = 4;
		const product_groups = [];
		// Разбиваем массив на группы
		for (let i = 0; i < loop_product_items.length; i += product_group_size) {
			product_groups.push(loop_product_items.slice(i, i + product_group_size));
		}

		// Запускаем процесс с задержкой
		product_groups.forEach((group, index) => {
			setTimeout(() => {
				// Случайный выбор элемента из текущей группы
				const randomIndex = Math.floor(Math.random() * group.length);
				const loop_product_link = group[randomIndex];
				loop_product_link.closest('.cover').classList.add('--animate');

				const hover_image = loop_product_link.querySelector('.hover-light[data-image]');
				if (hover_image) {
					loop_product_link.insertAdjacentHTML('beforeEnd', loop_product_link.querySelector('.hover-light[data-image]').getAttribute('data-image'));
				}
			}, index * 3000); // Задержка в 1 секунду на каждую группу
		});
	}
});