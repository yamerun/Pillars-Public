<?php

/**
 * Product Loop Start for Filter
 *
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;
?>

<div id="<?= $args['slug'] ?>" class="pillars-wc-products__section row-sm">
	<?php if (isset($args['link']) && !isset($args['hide_title'])) { ?>
		<div class="block wp-block">
			<h2 class="m-unbottom" data-value="<?= $args['slug'] ?>"><?= $args['name'] ?></h2>
		</div>
	<?php } ?>