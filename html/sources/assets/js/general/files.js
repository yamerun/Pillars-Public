tp_delegate(document.body, 'change', 'input-file', function (e) {
	e.preventDefault();

	const input = tp_get_target_by_class(e, 'input-file');
	const wrapper = input.closest('.file-wrapper');

	if (input.files.length == 1) {
		let file = input.files[0];
		let types = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
		let size = 8388608; // 8 Mb
		let error = [];
		let message = wrapper.querySelector('.input-message');
		let type = file.name.split('.').pop().toLowerCase();

		console.log('file', file, type, types, types.indexOf(type), file.size > 0, file.size <= size);

		input.classList.remove('--invalid');
		message.removeAttribute('data-invalid');

		if (file.size) {
			if (file.size > size) {
				error.push('файл не должен превышать ' + parseInt(size / 1024 / 1024) + ' Мб');
			}
		} else {
			error.push('файл должен иметь размер');
		}

		if (types.length && types.indexOf(type) === -1) {
			error.push('выберете файл следующих форматов: ' + types.join(', '));
		}

		console.log('error', error.length, error);

		if (error.length) {
			input.value = '';

			input.classList.add('--invalid');
			message.setAttribute('data-invalid', error.join(', '));
			wrapper.querySelector('.input-file-text').innerHTML = input.getAttribute('placeholder');
		} else {
			wrapper.querySelector('.input-file-text').innerHTML = file.name + '<span class="input-file-text__remove" aria-label="Удалить этот файл"></span>';
		}
	}
});

tp_delegate(document.body, 'click', 'input-file-text__remove', function (e) {
	e.preventDefault();

	const link = tp_get_target_by_class(e, 'input-file-text__remove');
	const wrapper = link.closest('.file-wrapper');

	if (wrapper.querySelector('.input-file')) {
		const input = wrapper.querySelector('.input-file');
		input.value = '';
		wrapper.querySelector('.input-file-text').innerHTML = input.getAttribute('placeholder');
	}
});