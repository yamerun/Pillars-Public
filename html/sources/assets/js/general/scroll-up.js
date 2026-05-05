if (document.querySelector('.btn-up')) {
	const btnUp = {
		el: document.querySelector('.btn-up'),
		show() {
			// удалим у кнопки класс btn-up_hide
			this.el.classList.remove('btn-up__hide');
		},
		hide() {
			// добавим к кнопке класс btn-up_hide
			this.el.classList.add('btn-up__hide');
		},
		addEventListener() {
			// при прокрутке содержимого страницы
			window.addEventListener('scroll', () => {
				// определяем величину прокрутки
				const scrollY = window.scrollY || document.documentElement.scrollTop;
				// если страница прокручена больше чем на высоту экрана браузера, то делаем кнопку видимой, иначе скрываем
				scrollY > (document.documentElement.clientHeight - 200) ? this.show() : this.hide();
			});
			// при нажатии на кнопку .btn-up
			document.querySelector('.btn-up').onclick = () => {
				// переместим в начало страницы
				window.scrollTo({
					top: 0,
					left: 0,
					behavior: 'smooth'
				});
			}
		}
	}

	btnUp.addEventListener();
}

if (document.querySelector('.btn-recall-me')) {
	const btnRecall = {
		el: document.querySelector('.btn-recall-me'),
		show() {
			// удалим у кнопки класс btn-up_hide
			this.el.classList.remove('btn-recall-me__hide');
		},
		hide() {
			// добавим к кнопке класс btn-up_hide
			this.el.classList.add('btn-recall-me__hide');
		},
		addEventListener() {
			let btn = this.el;

			/*
			let btnDelay = 1000 * 60;
			let btnActive = false;

			function btnShow() {
				if (!btnActive) {
					if (btn.classList.contains('--active')) {
						btn.classList.remove('--active');
						setTimeout(btnShow, btnDelay);
					} else {
						btn.classList.add('--active');
						btnDelay += 1000 * 60;
						setTimeout(btnShow, 1000 * 6);
					}
				} else {
					setTimeout(btnShow, btnDelay);
				}
			}

			let btnTimer = setTimeout(btnShow, btnDelay);

			// при наведения курсора .btn-recall-me
			document.querySelector('.btn-recall-me').onmouseover = () => {
			if (!this.el.classList.contains('--active')) {
			this.el.classList.add('--active');
			}

			btnActive = true;
			clearInterval(btnTimer);
			}

			// при отведении курсора .btn-recall-me
			document.querySelector('.btn-recall-me').onmouseout = () => {
			if (this.el.classList.contains('--active')) {
			this.el.classList.remove('--active');
			}

			btnActive = false;
			btnTimer = setTimeout(btnShow, btnDelay);
			}
			*/

			setTimeout(function recallMe() {
				if (btn.classList.contains('--animate')) {
					btn.classList.remove('--animate');
					setTimeout(recallMe, 1000 * 15);
				} else {
					btn.classList.add('--animate');
					setTimeout(recallMe, 1000 * 3);
				}
			}, 1000 * 15);
		}
	}

	btnRecall.addEventListener();
}