<?php

namespace Bonnier\WP\BestAnswer\Admin;

use Bonnier\WP\BestAnswer;

class PostMetaBox
{
    /**
     * Setting for best answer marked
     */
    const SOLVED_BY_REPLY_SETTING_KEY = 'bbp_best_reply';

    /**
     * Register the meta box in Wordpress backend
     *
     * @return void
     */
    public static function register_meta_box()
    {
        if(current_user_can('manage_options'))
        {
            add_action('do_meta_boxes', function() {
                add_meta_box('_bbp_forum_description', 'Best Answer', [__CLASS__, 'meta_box_content'], 'reply','side','high');
            });

            add_action('save_post', [__CLASS__, 'save_meta_box_settings']);
        }
    }

    public static function meta_box_content()
    {
        $fieldOutput =  "<p>";
        $fieldOutput .= "<strong class='label' style='width:150px'>Mark as best answer</strong>";
        $fieldOutput .= "<label class='screen-reader-text' for='bbp_best_reply'>Mark as best answer</label>";
        $fieldOutput .= "<input type='checkbox' value='true' name='bbp_best_reply' id='bbp_best_reply' ".self::is_checked()." />";
        $fieldOutput .= "</p>";

        echo $fieldOutput;
    }

    public static function save_meta_box_settings()
    {
        if(isset($_POST[self::SOLVED_BY_REPLY_SETTING_KEY]))
        {
            $topicId = bbp_get_reply_topic_id(get_the_ID());

            update_post_meta(
                $topicId,
                self::SOLVED_BY_REPLY_SETTING_KEY,
                sanitize_text_field(get_the_ID())
           );
        }
    }

    private static function is_checked()
    {
        return ((int)self::get_setting_for_topic(self::SOLVED_BY_REPLY_SETTING_KEY) === get_the_ID()) ? 'checked' : '';
    }

    public static function get_setting($option)
    {
        return get_post_meta(get_the_ID(), $option, true);
    }

    public static function get_setting_for_topic($option)
    {
        return get_post_meta(bbp_get_reply_topic_id(get_the_ID()), $option, true);
    }
}