<?php
$excerpt = trim(apply_filters('the_excerpt', get_the_excerpt()));
if ($excerpt == '<p>&nbsp;</p>') {
	$excerpt = '';
}

$btn = get_post_meta(get_the_ID(), '_partner_btn_action', true);
if (is_array($btn)) {
	$btn = do_shortcode(sprintf(
		'[get-popup id="%s" form="%s" text="%s" class="btn-2" container="a" args="%s"]',
		$btn['id'],
		$btn['form'],
		$btn['title'],
		theplugin_array_to_args(['page_id' => get_the_ID()])
	));
} else {
	$btn = '<a href="#form-partner" class="btn-2">Оставить заявку на сотрудничество</a>';
}
?>
<div class="row section-title-image__content --bottom">
	<div class="col-sm-6">
		<div class="block color-white">
			<h1><?php the_title(); ?></h1>
			<?= $excerpt ?>
			<?= $btn ?>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="block"></div>
	</div>
</div>