jQuery(function ($) {
	/**
	 * действие при нажатии на кнопку загрузки изображения
	 * вы также можете привязать это действие к клику по самому изображению
	 */
	$('.tp-wp-image-upload__button').click(function (event) {

		event.preventDefault();

		const button = $(this);

		const customUploader = wp.media({
			title: 'Выберите изображение',
			library: {
				// uploadedTo : wp.media.view.settings.post.id, // если для метобокса и хотим прилепить к текущему посту
				type: ['image']
			},
			button: {
				text: 'Выбрать изображение' // текст кнопки, по умолчанию "Вставить в запись"
			},
			multiple: false
		});

		// добавляем событие выбора изображения
		customUploader.on('select', function () {

			const image = customUploader.state().get('selection').first().toJSON();

			console.log(image);


			button.parent().prev().attr('src', image.url);
			button.prev().val(image.id);

			let attachments = customUploader.state().get('selection').map(
				function (attachment) {
					attachment.toJSON();
					return attachment.attributes;
				});

			console.log(attachments);

		});

		// и открываем модальное окно с выбором изображения
		customUploader.open();
	});
	/**
	 * удаляем значение произвольного поля
	 * если быть точным, то мы просто удаляем value у <input type="hidden">
	 */
	$('.tp-wp-image-remove__button').click(function (event) {

		event.preventDefault();

		if (true == confirm("Уверены?")) {
			const src = $(this).parent().prev().data('src');
			$(this).parent().prev().attr('src', src);
			$(this).prev().prev().val('');
		}
	});

	/**
 * действие при нажатии на кнопку загрузки изображения
 * вы также можете привязать это действие к клику по самому изображению
 */
	$('.tp-wp-file-upload__button').click(function (event) {

		event.preventDefault();

		const button = $(this);

		const customUploader = wp.media({
			title: 'Выберите изображение',
			library: {
				// uploadedTo : wp.media.view.settings.post.id, // если для метобокса и хотим прилепить к текущему посту
			},
			button: {
				text: 'Выбрать файл' // текст кнопки, по умолчанию "Вставить в запись"
			},
			multiple: false
		});

		// добавляем событие выбора изображения
		customUploader.on('select', function () {

			const file = customUploader.state().get('selection').first().toJSON();
			const wrapper = button.closest('.tp-wp-file');

			console.log(wrapper);

			console.log(file);

			wrapper.find('input[type="hidden"]').val(file.id);
			wrapper.find('.file-info [data-name="filename"]').attr('href', file.url);
			wrapper.find('.file-info [data-name="filename"]').html(file.filename);
			wrapper.find('.file-info [data-name="title"]').html(file.title);
		});

		// и открываем модальное окно с выбором изображения
		customUploader.open();
	});
	/**
	 * удаляем значение произвольного поля
	 * если быть точным, то мы просто удаляем value у <input type="hidden">
	 */
	$('.tp-wp-file-remove__button').click(function (event) {

		event.preventDefault();

		if (true == confirm("Уверены?")) {
			const wrapper = $(this).closest('.tp-wp-file');
			wrapper.find('input[type="hidden"]').val(0);
			wrapper.find('.file-info [data-name="filename"]').attr('href', '#');
			wrapper.find('.file-info [data-name="filename"]').html('–');
			wrapper.find('.file-info [data-name="title"]').html('–');
		}
	});
});


/*
var send_attachment_multiple = wp.media({
			title : 'Choose or Upload an Image',
			multiple: 'add',

			library: {
				type: [ 'image' ]
			},
		});

		mediaPopup = wp.media({
				title: "Select the " + label + " image",
				library: {type: "image"},
				multiple: true,
				button: {text: "Insert"}
			});

		mediaPopup.on("select", function () {
				var img = self.window.state().get("selection").toJSON();
				if (editorText.indexOf(placeholders[label]) > -1) {
					editorText = editorText.replace(placeholders[label], img.url);
				}
				else {
					editorText = editorText.replace(currentValues[label](), img.url);
				}
				wp.media.editor.insert(editorText);
		   });


			function open_media_window() {
				if (this.window === undefined) {
					this.window = wp.media({
							title: 'Select a Before and After image',
							library: {type: 'image'},
							multiple: true,
							button: {text: 'Insert'}
						});

					var self = this; // Needed to retrieve our variable in the anonymous function below

					//WHEN THE MAGIC STARTS
					this.window.on('select', function() {

						var attachments = self.state().get('selection').map(
						   function( attachment ) {
							   attachment.toJSON();
							   return attachment;
						  });


						var first = attachments[0]; //you can also iterate them with $.each(attachments, function( index, attachement ) {}
						var second = attachments[1];

						wp.media.editor.insert('[banda before="' + first.attributes.url + ' after="' + second.attributes.url + '"]');
					});
					//WHEN THE MAGIC ENDS
				}

				this.window.open();
				return false;
			}
*/