<?php
/**
 * Plugin Name: Bonnier bbPress best answer
 * Version: 0.1.0
 * Plugin URI: https://github.com/BenjaminMedia/bbpress-best-answer
 * Description: This plugin gives you the ability to select a post in bbpress as the best answer
 * Author: Bonnier - Michael Sørensen
 * License: MIT
 */

namespace Bonnier\WP\BestAnswer;

/*use Bonnier\WP\WaOauth\Admin\PostMetaBox;
use Bonnier\WP\WaOauth\Assets\Scripts;
use Bonnier\WP\WaOauth\Http\Routes\OauthLoginRoute;
use Bonnier\WP\WaOauth\Http\Routes\UserUpdateCallbackRoute;
use Bonnier\WP\WaOauth\Models\User;
use Bonnier\WP\WaOauth\Settings\SettingsPage;*/

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

        $this->settings = new SettingsPage();
    }

    private function boostrap() {
        //Scripts::bootstrap();
        //PostMetaBox::register_meta_box();
    }

    /**
     * Returns the instance of this class.
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            global $bp_best_answer;
            $bp_best_answer = self::$instance;
            self::$instance->boostrap();

            /**
             * Run after the plugin has been loaded.
             */
            do_action('bp_best_answer_loaded');
        }

        return self::$instance;
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
