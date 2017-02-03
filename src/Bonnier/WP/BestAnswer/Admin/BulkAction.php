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
        // Check users permission
        if (current_user_can('publish_forums')) {

            add_filter('bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'set_as_resolved']);
            add_filter('bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'set_as_unresolved']);
            add_filter('handle_bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'set_as_resolved_handler'], 10, 3);
            add_filter('handle_bulk_actions-edit-'.bbp_get_topic_post_type(), [__CLASS__, 'set_as_unresolved_handler'], 10, 3);
            add_action('admin_notices', [__CLASS__, 'my_bulk_action_admin_notice']);

        }
    }

    public static function set_as_resolved($actions)
    {
        $actions['resolved'] = pll__('Set status to Resolved');
        return $actions;
    }

    public static function set_as_unresolved($actions)
    {
        $actions['unresolved'] = pll__('Set status to Unresolved');
        return $actions;
    }

    public static function set_as_unresolved_handler($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'unresolved') {
            return $redirect_to;
        }

        // source: https://github.com/imath/buddy-bbPress-Support-Topic/blob/master/includes/admin.php

        foreach ($post_ids as $post_id) {
            // we need to check the topic belongs to a support featured forum
            $forum_id = bbp_get_topic_forum_id( $post_id );
            if ( empty( $forum_id ) || ( 3 == bpbbpst_get_forum_support_setting( $forum_id )) ) {
                continue;
            }
            update_post_meta( $post_id, self::SOLVED_THREAD_SETTING_KEY, '1' );
        }

        $redirect_to = add_query_arg( 'bulk_unresolved_posts', count( $post_ids ), $redirect_to );
        return $redirect_to;
    }

    public static function set_as_resolved_handler($redirect_to, $doaction, $post_ids) {
        if ($doaction !== 'resolved') {
            return $redirect_to;
        }

        // source: https://github.com/imath/buddy-bbPress-Support-Topic/blob/master/includes/admin.php

        foreach ($post_ids as $post_id) {
            // we need to check the topic belongs to a support featured forum
            $forum_id = bbp_get_topic_forum_id( $post_id );
            if ( empty( $forum_id ) || ( 3 == bpbbpst_get_forum_support_setting( $forum_id )) ) {
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

            $translation = ($resolvedPostCount <= 1) ? pll__('Topic has been set to resolved') : pll__('Topics has been set to resolved');
            printf('<div id="message" class="updated fade">%s ' . $translation . '</div>', $resolvedPostCount);
        }
    }
}