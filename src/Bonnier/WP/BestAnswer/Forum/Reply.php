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
        add_filter('query_vars', [__CLASS__, 'add_query_vars_filter']);
        add_filter('parse_query', [__CLASS__, 'parse_best_answer']);
        add_filter('parse_query', [__CLASS__, 'parse_remove_answer']);
    }

    public static function parse_remove_answer(WP_Query $query)
    {
        $removeAnswer = (int) $query->get(self::REMOVE_ANSWER_GET_PARAMETER);

        if(!static::has_access($removeAnswer))
            return;

        if(!empty($removeAnswer) && is_numeric($removeAnswer)) {
            if(!bpbbpst_get_forum_support_setting(bbp_get_reply_forum_id($removeAnswer)) > 2)
                return;

            self::remove_best_answer($removeAnswer);
            self::redirect_back();
        }
    }

    public static function parse_best_answer(WP_Query $query)
    {
        $bestAnswer = (int) $query->get(self::BEST_ANSWER_GET_PARAMETER);

        // If the user dose not have access
        if(!static::has_access($bestAnswer))
            return;

        if(!empty($bestAnswer) && is_numeric($bestAnswer)) {
            if(bpbbpst_get_forum_support_setting(bbp_get_reply_forum_id($bestAnswer)) > 2)
                return;

            self::set_best_answer($bestAnswer);
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

    /**
     * Redirects back without parameters
     */
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

    public static function has_access($replyId)
    {
        if(current_user_can('manage_options')
            || bbp_get_topic_author_id(bbp_get_reply_topic_id($replId))  === get_current_user_id()) {
            return true;
        }

        return false;
    }
}