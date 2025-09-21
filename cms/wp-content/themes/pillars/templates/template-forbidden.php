<?php

/**
 * Template Name: Access Denied
 */

get_header('ais');

/**
 * Hook: pillars_account_forbidden.
 *
 * @hooked pillars_account_forbidden_before - 5
 * @hooked pillars_account_forbidden_notice - 10
 * @hooked pillars_account_forbidden_form_sign - 15
 * @hooked pillars_account_forbidden_after - 20
 */
do_action('pillars_account_forbidden');

// setWPNonce('schedule_crm');
get_footer('ais');
