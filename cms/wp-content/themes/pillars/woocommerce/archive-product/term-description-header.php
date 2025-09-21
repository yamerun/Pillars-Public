<?php

/**
 * Archive Description in header
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
<div class="pillars-wc-term-description__header wp-block">
	<?= wc_format_content(wp_kses_post($args['description'])) ?>
</div>