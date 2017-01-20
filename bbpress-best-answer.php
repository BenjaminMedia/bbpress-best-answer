<?php
/**
 * Plugin Name: Bonnier bbPress best answer
 * Version: 0.1.11
 * Plugin URI: https://github.com/BenjaminMedia/bbpress-best-answer
 * Description: This plugin gives you the ability to select a post in bbPress as the best answer
 * Author: Bonnier - Michael Sørensen
 * License: MIT
 */

namespace Bonnier\WP\BestAnswer;

use Bonnier\WP\BestAnswer\Admin\BulkAction;
use Bonnier\WP\BestAnswer\Admin\PostMetaBox;
use Bonnier\WP\BestAnswer\Forum\Reply;

// Do not access this file directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle autoload so we can use namespaces
spl_autoload_register(function ($className) {
    if (strpos($className, __NAMESPACE__) !== false) {
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        require_once(__DIR__ . DIRECTORY_SEPARATOR . Plugin::CLASS_DIR . DIRECTORY_SEPARATOR . $className . '.php');
    }
});

// Load plugin api
require_once (__DIR__ . '/'.Plugin::CLASS_DIR.'/api.php');

class Plugin
{
    /**
     * Text domain for translators
     */
    const TEXT_DOMAIN = 'bp-best-answer';

    const CLASS_DIR = 'src';

    /**
     * @var object Instance of this class.
     */
    private static $instance;

    public $settings;

    /**
     * @var string Filename of this class.
     */
    public $file;

    /**
     * @var string Basename of this class.
     */
    public $basename;

    /**
     * @var string Plugins directory for this plugin.
     */
    public $plugin_dir;

    /**
     * @var string Plugins url for this plugin.
     */
    public $plugin_url;

    /**
     * Do not load this more than once.
     */
    private function __construct()
    {
        // Set plugin file variables
        $this->file = __FILE__;
        $this->basename = plugin_basename($this->file);
        $this->plugin_dir = plugin_dir_path($this->file);
        $this->plugin_url = plugin_dir_url($this->file);

        // Load textdomain
        load_plugin_textdomain(self::TEXT_DOMAIN, false, dirname($this->basename) . '/languages');
    }

    private function boostrap()
    {
        PostMetaBox::register_meta_box();
        Reply::register();
        BulkAction::register();
    }

    /**
     * Returns the instance of this class.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            global $bbpress_best_answer;
            $bbpress_best_answer = self::$instance;
            self::$instance->boostrap();

            /**
             * Run after the plugin has been loaded.
             */
            do_action('bbpress_best_answer_loaded');
        }

        return self::$instance;
    }

    public function get_mark_best_answer_permalink()
    {
        echo Reply::generate_best_answer_url();
    }

    public function get_remove_best_answer_permalink()
    {
        echo Reply::generate_removal_url();
    }

    /**
     * Get the id of the best reply
     *
     * @return int|bool
     */
    public function get_best_answer_id()
    {
        return PostMetaBox::get_setting_for_topic(PostMetaBox::SOLVED_BY_REPLY_SETTING_KEY) ?: false;
    }

    public function show_tool()
    {
        if (current_user_can('publish_forums') || bbp_get_topic_author_id(bbp_get_reply_topic_id(get_the_ID())) === get_current_user_id()) {
            return true;
        }
        return false;
    }

    public function is_support_forum()
    {
        if (bpbbpst_get_forum_support_setting(bbp_get_forum_id()) > 2)
            return false;

        return true;
    }
}

/**
 * @return Plugin $instance returns an instance of the plugin
 */
function instance()
{
    return Plugin::instance();
}

add_action('plugins_loaded', __NAMESPACE__ . '\instance', 0);
