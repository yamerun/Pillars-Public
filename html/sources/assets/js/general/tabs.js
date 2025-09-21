tp_delegate(document.body, 'click', 'pillars-tabs__item', function (e) {

	const tab = tp_get_target_by_class(e, 'pillars-tabs__item');
	const nav = tab.closest('nav');

	if (nav.getAttribute('data-tab_group')) {

		if (!tab.classList.contains('--no-tab')) {

			e.preventDefault();

			const link = tab.querySelector('a');
			const id = link.getAttribute('href').indexOf('#');

			if (id !== -1) {
				let hash = link.getAttribute('href').substring(id + 1);

				if (hash) {
					if (document.getElementById(hash)) {
						const element = document.getElementById(hash);
						const group = element.getAttribute('data-tab_group');
						const items = document.querySelectorAll('[data-tab_group="' + group + '"]:not(nav)');

						nav.querySelectorAll('.pillars-tabs__item').forEach(elm => {
							elm.classList.remove('--active');
						});

						for (let i = 0; i < items.length; i++) {
							if (items[i].id == element.id) {
								if (element.style.display != undefined && element.style.display == 'none') {
									tp_slide_toggle_animate(element, 0.2, false);
								}
								tab.classList.add('--active');
							} else {
								if (element.style.display == undefined || element.style.display != 'none') {
									tp_slide_toggle_animate(items[i], 0.2, true);
								}

							}
						}
					}
				} else {

				}

				console.log(hash);
			}
		}
	}

});