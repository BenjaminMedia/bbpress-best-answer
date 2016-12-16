<?php

namespace Bonnier\WP\BestAnswer\Forum;

use Bonnier\WP\BestAnswer;
use Bonnier\WP\BestAnswer\Admin\PostMetaBox;
use WP_Query;

class Reply
{
    const BEST_ANSWER_GET_PARAMETER = 'best_answer';
    const REMOVE_ANSWER_GET_PARAMETER = 'remove_answer';

    public static function register()
    {
        add_filter( 'query_vars', [__CLASS__, 'add_query_vars_filter']);
        add_filter( 'parse_query', [__CLASS__, 'parse_best_answer']);
    }

    public static function parse_best_answer(WP_Query $query)
    {
        $bestAnswer = (int)$query->get(self::BEST_ANSWER_GET_PARAMETER);
        $removeBestAnswer = (int)$query->get(self::REMOVE_ANSWER_GET_PARAMETER);

        if(!empty($bestAnswer))
        {
            if(!is_numeric($bestAnswer))
            {
                return;
            }

            self::set_best_answer($bestAnswer);

            // redirect without param
            self::redirect_back();
        }

        if(!empty($removeBestAnswer))
        {
            if(!is_numeric($removeBestAnswer))
            {
                return;
            }

            self::remove_best_answer($removeBestAnswer);

            // redirect without param
            self::redirect_back();
        }
    }

    public static function add_query_vars_filter($vars)
    {
        $vars[] = self::BEST_ANSWER_GET_PARAMETER;
        $vars[] = self::REMOVE_ANSWER_GET_PARAMETER;
        return $vars;
    }

    public static function generate_best_answer_url()
    {
        return self::generate_url(self::BEST_ANSWER_GET_PARAMETER);
    }

    public static function generate_removal_url()
    {
        return self::generate_url(self::REMOVE_ANSWER_GET_PARAMETER);
    }

    private static function generate_url($parameter)
    {
        return add_query_arg([
            $parameter => bbp_get_reply_id(),
        ], bbp_topic_permalink(bbp_get_reply_topic_id(get_the_ID())));
    }

    private static function redirect_back()
    {
        global $wp;
        wp_redirect(home_url(add_query_arg([], $wp->request)));
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

    private static function remove_best_answer($reply)
    {
        $topicId = bbp_get_reply_topic_id($reply);

        delete_post_meta(
            $topicId,
            PostMetaBox::SOLVED_BY_REPLY_SETTING_KEY,
            sanitize_text_field($reply)
        );
    }
}