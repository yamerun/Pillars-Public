document.addEventListener('DOMContentLoaded', () => {
	const loop_product_links = document.querySelectorAll('.woocommerce-loop-product__link .cover .hover-light');
	if (loop_product_links.length) {
		let loop_product_length = 0;
		let random = 0;
		for (let i = 0; i < loop_product_links.length; i++) {
			loop_product_length += 4;
			if (loop_product_length > loop_product_links.length) {
				loop_product_length = loop_product_links.length;
			}

			random = Math.floor(Math.random() * (loop_product_length - i)) + i;
			const loop_product_link = loop_product_links[random].closest('.cover');
			loop_product_link.classList.add('--animate');

			console.log('loop-product', i, loop_product_length, random);

			i += 3;
		}

		console.log('loop-product', loop_product_links.length, loop_product_length);
	}
});