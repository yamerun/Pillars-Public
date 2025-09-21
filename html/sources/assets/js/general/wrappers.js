
tp_delegate(document.body, 'click', 'video-placeholder', function (e) {
	const target = tp_get_target_by_class(e, 'video-placeholder');

	if (!target.classList.contains('--link')) {
		e.preventDefault();

		const link = target.getAttribute('href');

		if (link) {

			const popup_id = 'video-wrapper';
			const is_clip = (link.includes('frame=clip')) ? true : false;

			// Проверяем существования тела модального окна
			tp_set_popup_wrapper(popup_id);
			const popup_current = document.getElementById(popup_id);

			if (!popup_current.querySelector('.video-wrapper')) {
				popup_current.querySelector('.' + window.wp_data.popup_key + '__close').insertAdjacentHTML('afterend', '<div class="video-wrapper"></div>');
			}
			const wrapper = popup_current.querySelector('.video-wrapper');
			wrapper.classList.add('block-loading');
			wrapper.innerHTML = '<iframe class="" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" frameborder="0" src="' + link + '"></iframe>';

			const iframe = wrapper.querySelector('iframe');
			iframe.addEventListener('load', function (e) {
				wrapper.classList.remove('block-loading');
			});

			popup_current.querySelector('.pillars-popup__content').classList.add('video-content');
			if (is_clip) {
				popup_current.querySelector('.video-content').classList.add('--clip');
				popup_current.querySelector('.video-content iframe').setAttribute('allow', 'autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;');
			}

			tp_popup_open(popup_current);
		}
	}
});

/**
 * Добавление в буфер обмена источника информации
 */
function tp_clipboard_add_referer_link() {
	let target_text = window.getSelection(),
		add_text = '<br><br>Источник: ' + document.location.href,
		out_text = target_text + add_text,
		clipboard = document.createElement('div');

	// Формируем элемент с ссылкой на страницу-источник
	clipboard.style.position = 'absolute';
	clipboard.style.left = '-99999px';
	document.body.appendChild(clipboard);
	clipboard.innerHTML = out_text;
	// Переназначаем скопированный текст через выделение
	target_text.selectAllChildren(clipboard);

	// Удаляем элемент с ссылкой на страницу-источник
	window.setTimeout(function () {
		document.body.removeChild(clipboard);
	}, 100);
}

/**
 * Блокировка контекстного меню на изображениях
 * @param {*} e
 * @returns
 */
function tp_disable_context(e) {
	var clickedEl = (e == null) ? event.srcElement.tagName : e.target.tagName;
	if (clickedEl == "IMG") {
		return false;
	}
}

// Событие при копировании контента на сайте
document.addEventListener('copy', tp_clipboard_add_referer_link);
// Событие на запрет контекстного меню на изображениях
document.oncontextmenu = tp_disable_context;


let mailtolinks = document.querySelectorAll('[href^="mailto:"]:not([class])');
if (mailtolinks.length) {
	for (let i = 0; i < mailtolinks.length; i++) {
		const mailtolink = mailtolinks[i];

		mailtolink.addEventListener('click', function (e) {
			e.preventDefault();

			if (navigator.clipboard) {

				let mail = mailtolink.getAttribute('href').replace('mailto:', '');

				navigator.clipboard.writeText(mail).then(function () {
					alert('Почта «' + mail + '» скопирована в буфер обмена.');
				}, function (err) {
					console.error('Произошла ошибка при копировании текста: ', err);
				});
			}
		});
	}
}