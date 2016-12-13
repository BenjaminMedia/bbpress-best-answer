<?php

/**
 * Returns an instance of the bp-wa-oauth plugin
 *
 * @return \Bonnier\WP\BestAnswer\Plugin|null
 */
function bp_best_answer()
{
    return isset($GLOBALS['bp_best_answer']) ? $GLOBALS['bp_best_answer'] : null;
}