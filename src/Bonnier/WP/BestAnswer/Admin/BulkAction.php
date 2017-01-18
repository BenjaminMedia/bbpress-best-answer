<?php

namespace Bonnier\WP\BestAnswer\Admin;

use Bonnier\WP\BestAnswer;

class BulkAction
{
    /**
     * Setting for best answer marked
     */
    const SOLVED_BY_REPLY_SETTING_KEY = '_bbp_best_answer_id';

    // This is a key, for another plugin! Do NOT change.
    const SOLVED_THREAD_SETTING_KEY = '_bpbbpst_support_topic';

    /**
     * Register the meta box in Wordpress backend
     *
     * @return void
     */
    public static function register()
    {
        // Validate permission
        if (current_user_can('publish_forums')) {

            add_filter('bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'my_custom_bulk_actions']);
            add_filter('handle_bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'my_bulk_action_handler'], 10, 3);
            add_action('admin_notices', [__CLASS__, 'my_bulk_action_admin_notice']);


        }
    }

    public static function my_custom_bulk_actions($actions)
    {
        $actions['resolved'] = pll__('Set status to Resloved');
        return $actions;
    }

    public static function my_bulk_action_handler($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'resolved') {
            return $redirect_to;
        }

        foreach ($post_ids as $post_id) {
            // we need to check the topic belongs to a support featured forum
            $forum_id = bbp_get_topic_forum_id( $post_id );
            if ( empty( $forum_id ) || ( 3 == bpbbpst_get_forum_support_setting( $forum_id )) ) {
                continue;
            }
            if ( 2 == bpbbpst_get_forum_support_setting( $forum_id )) {
                continue;
            }
            update_post_meta( $post_id, self::SOLVED_THREAD_SETTING_KEY, '2' );
        }

        $redirect_to = add_query_arg( 'bulk_resolved_posts', count( $post_ids ), $redirect_to );
        return $redirect_to;
    }

    public static function my_bulk_action_admin_notice()
    {
        if (!empty($_REQUEST['bulk_resolved_posts'])) {
            $resolvedPostCount = (int)$_REQUEST['bulk_resolved_posts'];

            printf( '<div id="message" class="updated fade">' .
                _n( '%s Topics has been set to resolved.',
                    '%s Topics has been set to resolved.',
                    $resolvedPostCount,
                    'bbpress-best-answer'
                ) . '</div>', $resolvedPostCount );
        }
    }
}