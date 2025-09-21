<?php

/**
 * Archive Description
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

$defaults = array(
	'description' => ''
);
$args = wp_parse_args($args, $defaults);
?>

<section>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block wp-block pillars-wc-term-description__general">
					<?= $args['description'] ?>
				</div>
			</div>
		</div><!-- .row -->
	</div>
</section>