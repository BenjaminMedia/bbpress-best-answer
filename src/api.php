<?php

/**
 * Returns an instance of the bp-wa-oauth plugin
 *
 * @return \Bonnier\WP\BestAnswer\Plugin|null
 */
function bbpress_best_answer()
{
    return isset($GLOBALS['bbpress_best_answer']) ? $GLOBALS['bbpress_best_answer'] : null;
}