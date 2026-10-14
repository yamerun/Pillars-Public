/**
 * Cookie Accept
 */
const cookiesection = document.getElementById('agree-cookie');
if (cookiesection) {
	cookiesection.querySelector('.accept').addEventListener('click', function (e) {
		const wrapper = cookiesection.closest('section.cookie');

		cookiesection.classList.add('block-loading');

		let date = new Date();
		date.setDate(date.getDate() + 365);
		let val = 'ok for ' + date;
		document.cookie = "pillars_cookie_agree=" + val + "; domain=" + document.domain.match(/[^\.]*\.[^.]*$/)[0] + "; expires=" + date.toUTCString() + "; path=/";

		const params = new FormData();

		cookiesection.querySelectorAll('input').forEach(input => {
			params.append(input.name, input.value);
		});

		console.log('cookie params', new URLSearchParams(params).toString());

		fetch(window.wp_data.ajax_url, {
			method: "post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(params).toString(),
		})
			.then(response => response.json())
			.then(function (data) {
				console.log('response cookie', data);

				if (data.message != null && data.type != null) {
					switch (data.type) {
						case 'ok':
							if (data.content) {
								document.head.insertAdjacentHTML('beforeEnd', data.content);
								load_metrika();
							}
							break;
						case 'error':
							break;
						case 'fail':
						case 'spam':

							break;
						default: ;
					}
				} else {
				}
			})
			.catch(function (error) { console.warn('error', error); });

		wrapper.remove();

		console.log('cookie accepted', date);
	});

	cookiesection.addEventListener('submit', function (e) {
		e.preventDefault();

		const form = e.target;
		const wrapper = form.closest('section.cookie');

		form.classList.add('block-loading');

		let date = new Date();
		date.setDate(date.getDate() + 365);
		let val = 'ok for ' + date;
		document.cookie = "pillars_cookie_agree=" + val + "; domain=" + document.domain.match(/[^\.]*\.[^.]*$/)[0] + "; expires=" + date.toUTCString() + "; path=/";

		wrapper.remove();

		console.log('cookie', date);
		form.classList.remove('block-loading');
	});
}
