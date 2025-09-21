<?php

/**
 * Archive Video Reviews
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

$defaults = array(
	'reviews' => array()
);
$args = wp_parse_args($args, $defaults);
if ($args['reviews'] && is_array($args['reviews'])) {
?>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="block">
						<h2>Видеообзор</h2>
					</div>
				</div>
				<?php foreach ($args['reviews'] as $video) { ?>
					<div class="col-sm-4">
						<div class="block">
							<?= do_shortcode('[pillars-video-placeholder url="' . $video['url'] . '" cover_id="' . $video['cover_id'] . '" size="hqdefault"]') ?>
						</div>
					</div>
				<?php } ?>
			</div><!-- .row -->
		</div>
	</section><!-- term-videos -->
<?php }
