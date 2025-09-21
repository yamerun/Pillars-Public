$.fn.scrollToObj = function () {
	$('html, body').animate({ scrollTop: $(this).offset().top - $('body > header').outerHeight() - 20 }, '100');
}

// Mask-input script
$.mask.definitions["9"] = null;
$.mask.definitions["^"] = "[0-9]";
// Phone
$.fn.maskPhone = function () {
	if ($(this).attr('data-mask')) {
		$(this).mask($(this).attr('data-mask'));
	} else {
		$(this).mask('+^ (^^^) ^^^-^^-^^');
	}
}
$('input.mask-phone, .mask-phone input').maskPhone();
// Date
$.fn.maskDate = function () { $(this).mask('^^.^^.20^^'); }
$('input.mask-date, .mask-date input').maskDate();

/**
 * Проверка корректности даты
 */
$(document).on('change', 'input.mask-date, .mask-date input', function () {
	let input = $(this).val().split('.');
	let message = $(this).siblings('.input-message');
	let day = input[0];
	let month = input[1] - 1;
	let year = input[2];
	let date = new Date(year, month, day);
	let today = new Date();

	$(this).removeClass('--invalid');
	message.removeAttr('data-invalid');

	if (year != date.getFullYear() || month != date.getMonth() || day != date.getDate()) {
		message.attr('data-invalid', 'Некорректная дата.');
	} else if (today >= date) {
		message.attr('data-invalid', 'Дата должна быть в будущем.');
	}

	if (message.attr('data-invalid')) {
		$(this).addClass('--invalid');
		$(this).val('');
	};
});

// Проверка существования переменной name в get-запросе
$.fn.getRequestUrl = function (name) {
	if (name = (new RegExp('[?&]' + encodeURIComponent(name) + '=([^&]*)')).exec(location.search))
		return decodeURIComponent(name[1]);
}

// Вывод информера
$.fn.getFormError = function () { $(this).html('<div class="informer"><img src="' + $('#themes').attr('data-uri') + '/assets/images/icon-mail-fail.png">Ошибка загрузки формы. Пожалуйста, обновите страницу и попробуйте ещё раз открыть форму.</div>'); }

/**
 * Filter Attribute
 */
// Задаём изначальное значение табов
if ($('.pillars-tabs__item > a[data-id="#"]').length) {
	$('.pillars-tabs__item > a[data-id="#"]').each(function () {
		$(this).closest('.pillars-tabs__item').addClass('--active');
	});

	$('.pillars-tabs__item > a[data-id]').each(function () {
		let $target = $(this).attr('data-id');
		if ($target != '#') {
			if (!$('#' + $target)) {
				$(this).closest('.pillars-tabs__item').remove();
			}
		}
	});
}

$(document).on('click', '.pillars-tabs__item > a', function (e) {
	let $tab = $(this).closest('.pillars-tabs__item');

	if (!$tab.hasClass('--no-tab')) {

		e.preventDefault();

		if (!$tab.hasClass('--active')) {

			let $id = $(this).attr('data-id') ?? $(this).attr('href').replace('#', '');

			$(this).closest('.pillars-tabs__wrapper').find('.pillars-tabs__item > a').each(function () {

				$(this).closest('.pillars-tabs__item').removeClass('--active');

				let $target = $(this).attr('data-id') ?? $(this).attr('href').replace('#', '');

				let $list = null;
				if ($target == '#' || $target == '') {
					$list = '';
				} else if ($('#' + $target)) {
					$list = $('#' + $target);
				}

				if ($list != null && $list.length) {
					if ($id == '#' || $id == '') {
						if (!$list.is(':visible')) { $list.slideDown(300); }
					} else {
						if ($target == $id) {
							if (!$list.is(':visible')) { $list.slideDown(300); }
						} else {
							$list.slideUp(300);
						}
					}
				}
			});
		}

		$tab.addClass('--active');
	}
});