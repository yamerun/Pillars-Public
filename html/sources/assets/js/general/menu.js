/**
 * Открытие/скрытие дочерних элементов меню в мобильной версии
 *
 * @param {*} item
 */
function is_menu_item_childrens_show(e, item) {
	const wrapper = item.parentElement;
	if (window.innerWidth < window.wp_theplugin.break_sm) {
		e.preventDefault();

		if (wrapper.classList.contains('active')) {
			wrapper.classList.remove('active');
		} else {
			wrapper.classList.add('active');
		}
	} else {
		wrapper.classList.remove('active');
	}
}

const main_menu = document.getElementById('main_menu');
if (main_menu) {
	const main_menu_toggle = document.querySelector('.navbar-toggle[data-target="#main_menu"]');
	if (main_menu_toggle) {
		main_menu_toggle.addEventListener('click', function (e) {
			e.preventDefault();

			let z_index = 11;

			main_menu.style.zIndex = z_index;
			document.querySelector('body > header').style.zIndex = z_index;

			main_menu.querySelectorAll('.d-sm-block').forEach(element => {
				element.classList.remove('d-sm-block');
			});

			if (!main_menu_toggle.classList.contains('collapse')) {
				document.querySelector('body').classList.add('menu-collapse');

				// Получаем текущую высоту `body > header`
				let header_top = parseInt(document.querySelector('body > header').outerHeight);

				main_menu.style.top = header_top + 'px';
				main_menu.style.left = '0px';
				main_menu.style.height = 'calc(100vh - ' + header_top + 'px)';
				main_menu_toggle.classList.add('collapse');
			} else {
				main_menu.style.left = '-120vw';
				main_menu_toggle.classList.remove('collapse');
				document.querySelector('body').classList.remove('menu-collapse');
				document.querySelector('body > header').removeAttribute('style');
			}
		});

		window.addEventListener('resize', function (e) {
			if (window.innerWidth >= window.wp_theplugin.break_sm) {
				main_menu.removeAttribute('style');
			}
		});
	}

	const menu_item_childrens = main_menu.querySelectorAll('ul.navbar-nav > li.menu-item-has-children > a');
	if (menu_item_childrens.length) {
		for (let i = 0; i < menu_item_childrens.length; i++) {
			const menu_item_children = menu_item_childrens[i];
			menu_item_children.addEventListener('click', function (e) {
				is_menu_item_childrens_show(e, this);
			});
		}
	}

	const menu_item_groups = main_menu.querySelectorAll('ul.sub-class > li[class^="product_group-"] > span');
	if (menu_item_groups.length) {
		for (let i = 0; i < menu_item_groups.length; i++) {
			const menu_item_group = menu_item_groups[i];
			menu_item_group.addEventListener('click', function (e) {
				is_menu_item_childrens_show(e, this);
			});
		}
	}
}

const menu_video_links = document.querySelectorAll('.menu-item-3462');
if (menu_video_links.length) {
	for (let i = 0; i < menu_video_links.length; i++) {
		const menu_video_link = menu_video_links[i];

		menu_video_link.querySelector('a').setAttribute('target', '_blank');
		menu_video_link.querySelector('a').setAttribute('rel', 'nofollow');
	}
}

/**
 * Вывод списка категорий на указаной группе
 *
 * @param {*} menu_item
 */
function menu_catalog_wrapper(menu_item) {
	const menu_item_catalog = document.getElementById('menu-item-catalog');
	const menu_item_catalog_items = menu_item.querySelectorAll('.sub-menu li a[data-image]');
	const menu_item_catalog_container = menu_item_catalog.querySelector('.sub-menu-wrapper');

	let menu_item_category = '';

	menu_item.classList.add('active');

	menu_item_catalog_items.forEach(item => {
		menu_item_category += '<a href="' + item.href + '" class="catalog-item ' + item.parentElement.getAttribute('class') + '"><div class="catalog-item__cover"><div class="media-ratio"><img width="150" height="150" src="' + item.getAttribute('data-image') + '"></div></div><div class="catalog-item__title">' + item.textContent + '</div></a>';
	});

	setTimeout(function () {
		menu_item_catalog_container.classList.add('hide');
	}, 100);
	setTimeout(function () {
		menu_item_catalog_container.innerHTML = '';
		menu_item_catalog_container.insertAdjacentHTML('afterbegin', menu_item_category);
	}, 200);
	setTimeout(function () {
		menu_item_catalog_container.classList.remove('hide');
	}, 300);
}

/**
 *
 */
function menu_item_catalog_hide() {
	const menu_item_catalog = document.getElementById('menu-item-catalog');
	if (menu_item_catalog.classList.contains('show')) {
		menu_item_catalog.classList.remove('show');
	}
}

/**
 *
 */
const menu_item_catalog_groups = document.querySelectorAll('#menu-item-catalog .sub-menu.sub-class [class^="product_group-"]');
menu_catalog_wrapper(menu_item_catalog_groups[0]);

menu_item_catalog_groups.forEach(group => {
	group.addEventListener('mouseover', () => {
		if (!group.classList.contains('active')) {
			menu_item_catalog_groups.forEach(item => { item.classList.remove('active') });
			menu_catalog_wrapper(group);
		}
	});

	group.addEventListener('click', (e) => { e.preventDefault() });
});


const menu_item_catalog = document.getElementById('menu-item-catalog');
menu_item_catalog.addEventListener('mouseover', () => {
	if (window.innerWidth > window.wp_data.break_sm) {
		menu_item_catalog.classList.add('show');
	}
});

// Скрытие меню Каталога при различных сценариях
window.addEventListener('scroll', menu_item_catalog_hide);
window.addEventListener('resize', menu_item_catalog_hide);
tp_delegate(document.getElementById('main-menu'), 'mouseover', 'menu-item-has-children', function (e) {
	menu_item_catalog_hide();
});
document.addEventListener('click', function (e) {
	if (!e.target.closest('#menu-item-catalog')) {
		menu_item_catalog_hide();
	}
});