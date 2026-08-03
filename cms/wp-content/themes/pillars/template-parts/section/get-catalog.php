<div id="catalog-popup" class="pillars-catalog-popup">
	<div class="pillars-catalog-popup__container">
		<div class="pillars-catalog-popup__wrapper">
			<a href="#catalog-popup" class="pillars-catalog-popup__close"></a>
			<div class="pillars-catalog-popup__cover">
				<div class="media-ratio">
					<img width="480" height="330" src="<?= get_template_directory_uri() ?>/assets/images/catalog-popup.webp" alt="">
				</div>
			</div>
			<div class="pillars-catalog-popup__content">
				<div class="pillars-catalog-popup__title">Получить каталог продукции</div>
				<a href="<?= site_url('/files/catalog.pdf') ?>" class="pillars-catalog-popup__link" target="_blank" rel="nofollow" onclick="ym(60911305,'reachGoal','DOWNLOAD_CATALOG');">Скачать pdf-каталог</a>
			</div>
		</div>
	</div>
</div>

<?php theplugin_file_get_content_css_by_theme_print('assets/css/catalog-popup.min.css', 'pillars-catalog-popup'); ?>
<?php theplugin_file_get_content_js_by_theme_print('assets/js/catalog-popup.min.js', 'pillars-catalog-popup'); ?>