<?php
$code = theplugin_get_theme_mod('yandex_map_company');
$code || exit;
?>
<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<?= theplugin_yandex_reviews_widget(theplugin_get_theme_mod('yandex_map_company')) ?>
				</div>
			</div>
		</div>
	</div>
</section>