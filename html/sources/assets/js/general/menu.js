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
