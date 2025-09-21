<?php

/**
 * Siblings products in form add to cart
 *
 * @package    Pillars
 * @version 0.0.1
 */

defined('ABSPATH') || exit;

if ($args['values'] && $args['label']) {
	$permalinks = array();
	foreach ($args['values'] as $id => $item) {
		$permalinks[$id] = get_permalink($id);
	}
?>
	<div class="form-style cart">
		<table class="variations" role="presentation">
			<tbody>
				<tr>
					<th class="label"><label for="product_siblings"><?= esc_html($args['label']) ?></label></th>
					<td class="value">
						<select id="product_siblings" class="" data-permalinks="<?= esc_attr(theplugin_json_encode($permalinks)) ?>">
							<?= pillars_get_options_wrapper($args['values'], $args['id']) ?>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
<?php
}
