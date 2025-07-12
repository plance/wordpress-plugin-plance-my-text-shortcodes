<?php
/**
 * Main plugin file.
 *
 * @package Plance\Plugin\My_Text_Shortcodes
 *
 * Plugin Name: My Text Shortcodes
 * Description: Creating text shortcodes, using friendly interface
 * Plugin URI:  https://plance.top/
 * Version:     1.1.0
 * Author:      plance
 * Author URI: http://plance.top/
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-text-shortcodes
 * Domain Path: /languages/
 */

namespace Plance\Plugin\My_Text_Shortcodes;

defined( 'ABSPATH' ) || exit;


/**
 * Bootstrap.
 */
require_once __DIR__ . '/bootstrap.php';

/**
 * Actions.
 */
require_once __DIR__ . '/actions.php';
