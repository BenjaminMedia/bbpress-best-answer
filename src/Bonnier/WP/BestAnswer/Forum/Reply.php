<?php

namespace Bonnier\WP\BestAnswer\Forum;

use Bonnier\WP\BestAnswer;
use Bonnier\WP\BestAnswer\Admin\PostMetaBox;
use WP_Query;

class Reply
{
    const BEST_ANSWER_GET_PARAMETER = 'best_answer';

    public static function register()
    {
        add_filter( 'query_vars', [__CLASS__, 'add_query_vars_filter']);
        add_filter( 'parse_query', [__CLASS__, 'parse_best_answer']);
    }

    public static function parse_best_answer(WP_Query $query) {
        $bestAnswer = $query->get(self::BEST_ANSWER_GET_PARAMETER);

        if(!is_numeric($bestAnswer))
        {
            return;
        }

        self::set_best_answer($bestAnswer);

        // redirect without best_answer param
        global $wp;
        wp_redirect(home_url(add_query_arg([], $wp->request)));
    }

    public static function add_query_vars_filter($vars)
    {
        $vars[] = self::BEST_ANSWER_GET_PARAMETER;
        return $vars;
    }

    /**
     * @return string
     */
    public static function generate_url()
    {
        return add_query_arg([
            self::BEST_ANSWER_GET_PARAMETER => bbp_get_reply_id(),
        ], bbp_topic_permalink(bbp_get_reply_topic_id(get_the_ID())));
    }

    /**
     * @param $reply
     * @return void
     */
    private static function set_best_answer($reply)
    {
        $topicId = bbp_get_reply_topic_id($reply);

        update_post_meta(
            $topicId,
            PostMetaBox::SOLVED_BY_REPLY_SETTING_KEY,
            sanitize_text_field($reply)
        );
    }
}