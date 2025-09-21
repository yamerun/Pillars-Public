<?php
$defaults = array(
	'type' 		=> '',
	'title'		=> '',
	'message' 	=> '',
	'console'	=> '',
	'class'		=> '',
	'icon'		=> ''
);
$args = wp_parse_args($args, $defaults);

$classes = array('tp-get-notice', $args['type'], $args['class']);
?>

<div class="<?= implode(' ', $classes) ?>">
	<div class="tp-get-notice__icon"><?= $args['icon'] ?></div>
	<div class="tp-get-notice__body">
		<div class="tp-get-notice__title"><?= $args['title'] ?></div>
		<?= $args['message'] ?>
	</div>
</div>
<?= $args['console'] ?>