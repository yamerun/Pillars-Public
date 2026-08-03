/************************************
 * CATALOG POPUP
 ************************************/

const pillars_catalog_popup = document.querySelector('.pillars-catalog-popup');
document.querySelector('.pillars-catalog-popup__close').addEventListener('click', (e) => {
	e.preventDefault();
	pillars_catalog_popup.classList.remove('open');
	document.body.classList.remove('collapse');
});

document.querySelector('.pillars-catalog-popup__link').addEventListener('click', (e) => {
	fetch(
		wp_api_settings.root + wp_api_settings.namespace + '/catalog/',
		{
			method: 'GET',
			headers: {
				'X-WP-Nonce': wp_api_settings.nonce
			},
		})
		.then(response => response.json())
		.then(function (response) {
			console.log('catalog', response);
		});
});

/**
 * Добавляем событие закрытие модального окна при нажатии клавиши Esc
 */
document.addEventListener('keydown', function (e) {
	if (e.which === 27) {
		if (pillars_catalog_popup.classList.contains('open')) {
			pillars_catalog_popup.classList.remove('open');
			document.body.classList.remove('collapse');
		}
	}
});

setTimeout(() => {
	pillars_catalog_popup.classList.add('open');
	document.body.classList.add('collapse');
	let date = new Date();
	date.setDate(date.getDate() + 3);
	document.cookie = "pillars_view_catalog=1; domain=" + document.domain.match(/[^\.]*\.[^.]*$/)[0] + "; expires=" + date.toUTCString() + "; path=/";

	// Добавляем событие закрытие модального окна при клике вне контента окна
	pillars_catalog_popup.addEventListener('click', function (e) {
		if (!e.target.closest('.pillars-catalog-popup__wrapper')) {
			pillars_catalog_popup.classList.remove('open');
			document.body.classList.remove('collapse');
		}
	});
}, 10000);