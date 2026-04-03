<?php
$section = get_post_meta(get_the_ID(), '_advantage_section', true);
$section = ($section) ? $section : 'advantage';
get_template_part('template-parts/section/' . $section, null, ['section' => true]);
