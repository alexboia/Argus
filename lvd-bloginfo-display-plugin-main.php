<?php
/**
 * Plugin Name: Blog Info Display
 * Author: Alexandru Boia
 * Author URI: http://alexboia.net
 * Version: 0.1.1
 * Description: Displays all the information provided by WordPress' get_bloginfo() function and dumps all the non-transient options. Strictly developed for learning purposes.
 * License: New BSD License
 * Text Domain: lvd-bloginfo-display
 */

/**
 * Copyright (c) 2019-2020 Alexandru Boia
 *
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 *	1. Redistributions of source code must retain the above copyright notice, 
 *		this list of conditions and the following disclaimer.
 *
 * 	2. Redistributions in binary form must reproduce the above copyright notice, 
 *		this list of conditions and the following disclaimer in the documentation 
 *		and/or other materials provided with the distribution.
 *
 *	3. Neither the name of the copyright holder nor the names of its contributors 
 *		may be used to endorse or promote products derived from this software without 
 *		specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY 
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, 
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */

define('LVD_BID_LOADED', true);
define('LVD_BID_VERSION', '0.1.1');
define('LVD_BID_PLUGIN_ROOT', dirname(__FILE__));
define('LVD_BID_PLUGIN_VIEWS', LVD_BID_PLUGIN_ROOT . '/views');
define('LVD_BID_BLOGINFO_MENU_SLUG', 'lvd-bloginfo-display');
define('LVD_BID_BLOGOPTIONS_MENU_SLUG', 'lvd-bloginfo-options-display');
define('LVD_BID_BLOGTRANSIENTS_MENU_SLUG', 'lvd-bloginfo-transients-display');

define('LVD_BID_ACTION_DUMP_OPTION', 'lvdbid_dump_option');
define('LVD_BID_NONCE_DUMP_OPTION', 'lvdbid.nonce.dumpOption');

define('LVD_BID_ACTION_DUMP_TRANSIENT', 'lvdbid_dump_transient');
define('LVD_BID_NONCE_DUMP_TRANSIENT', 'lvdbid.nonce.dumpTransient');

define('LVD_BID_NO_OPTION', '__LVDBID_NO_OPTION__');
define('LVD_BID_NO_TRANSIENT', '__LVDBID_NO_TRANSIENT__');

function lvdbid_get_wpdb() {
    return $GLOBALS['wpdb'];
}

function lvdbid_get_current_admin_page() {
    return isset($_GET['page']) ? $_GET['page'] : null;
}

function lvdbid_send_forbidden_and_die() {
    http_response_code(403);
    die;
}

function lvdbid_bad_request_and_die() {
    http_response_code(400);
    die;
}

function lvdbid_check_nonce($action) {
    return isset($_GET['lvdbid_nonce']) && wp_verify_nonce($_GET['lvdbid_nonce'], $action);
}

function lvdbid_is_serialized_composite_type($value) {
    return is_serialized($value) && (
        preg_match('/^a:[0-9]+:\{/', $value) === 1 || //is serialized array
        preg_match('/^O:[0-9]+:"[a-zA-Z0-9_]+":[0-9]+:\{/', $value) === 1 //OR is serialized object
    );
}

function lvdbid_create_admin_notice_link($slug) {
    return admin_url('admin.php?page=' . $slug . '&from=admin-notice');
}

function lvdbid_add_stylesheets_scripts($hookSuffix) {
    $page = lvdbid_get_current_admin_page();

    //add the plug-in scripts and styles only 
    //  for the pages they are actually used on
    if (!empty($page) && in_array($page, array(LVD_BID_BLOGINFO_MENU_SLUG, LVD_BID_BLOGOPTIONS_MENU_SLUG, LVD_BID_BLOGTRANSIENTS_MENU_SLUG))) {
        wp_enqueue_style('lvdbid-plugin-css', 
            plugins_url('media/css/plugin.css', __FILE__), 
                array(), 
                LVD_BID_VERSION, 
                'all');

        wp_enqueue_script('lvdbid-clipboard-js', 
            plugins_url('media/js/3rdParty/clipboard-js/clipboard.min.js', __FILE__), 
            array(), 
            '2.0.4', 
            true);

        wp_enqueue_script('lvdbid-plugin-js', 
            plugins_url('media/js/plugin.js', __FILE__), 
            array(), 
            LVD_BID_VERSION, 
            true);

        wp_enqueue_script('jquery-blockui-js', 
            plugins_url('media/js/3rdParty/jquery.blockUI.js', __FILE__), 
            array(), 
            '2.66.0', 
            true);

        if ($page == LVD_BID_BLOGOPTIONS_MENU_SLUG) {
            wp_enqueue_script('lvdbid-show-all-options-js', 
                plugins_url('media/js/show-all-options.js', __FILE__), 
                array(), 
                LVD_BID_VERSION, 
                true);
        }

        if ($page == LVD_BID_BLOGTRANSIENTS_MENU_SLUG) {
            wp_enqueue_script('lvdbid-show-all-transients-js', 
                plugins_url('media/js/show-all-transients.js', __FILE__), 
                array(), 
                LVD_BID_VERSION, 
                true);
        }
    }
}

function lvdbid_show_admin_notice() {
    $page = lvdbid_get_current_admin_page();

    //do not show the admin notice on the 
    //  two pages the notice is nagging the user 
    //  to visit
    if (empty($page) || !in_array($page, array(LVD_BID_BLOGINFO_MENU_SLUG, LVD_BID_BLOGOPTIONS_MENU_SLUG))) {
        $hasVisited = get_user_meta(get_current_user_id(), 
            'lvdbid_has_visited_notice_link', 
            true);

        //also, do not show the admin notice 
        //  if the user has already clicked on it
        //  ($hasVisited === 'yes')
        if ($hasVisited !== 'yes') {
            $data = new stdClass();
            $data->blogInfoUrl = lvdbid_create_admin_notice_link(LVD_BID_BLOGINFO_MENU_SLUG);
            $data->optionsInfoUrl = lvdbid_create_admin_notice_link(LVD_BID_BLOGOPTIONS_MENU_SLUG);
            $data->transientsInfoUrl = lvdbid_create_admin_notice_link(LVD_BID_BLOGTRANSIENTS_MENU_SLUG);
            require LVD_BID_PLUGIN_VIEWS . '/lvdbid-admin-notice.php';
        }
    }
}

function lvdbid_add_admin_menu_links() {
    add_menu_page(__('Debug blog information', 'lvd-bloginfo-display'), //page title
        __('Debug blog information', 'lvd-bloginfo-display'), //menu item label
            'manage_options', //capability required to access the menu entry
            LVD_BID_BLOGINFO_MENU_SLUG, //menu item slug
            'lvdbid_show_blog_info', //callback function
            'dashicons-performance'); //menu item icon

    add_submenu_page(LVD_BID_BLOGINFO_MENU_SLUG, 
        __('Debug blog options', 'lvd-bloginfo-display'), 
        __('Debug blog options', 'lvd-bloginfo-display'), 
            'manage_options', 
            LVD_BID_BLOGOPTIONS_MENU_SLUG, 
            'lvdbid_show_all_options');

    add_submenu_page(LVD_BID_BLOGINFO_MENU_SLUG, 
        __('Debug blog transients', 'lvd-bloginfo-display'),
        __('Debug blog transients', 'lvd-bloginfo-display'),
            'manage_options',
            LVD_BID_BLOGTRANSIENTS_MENU_SLUG,
            'lvdbid_show_all_transients');
}

function lvdbid_check_and_mark_if_user_came_from_admin_notice() {
    //if the user is visiting one of our current admin pages
    //  via our admin notice, set the notice as being clicked,
    //  so we won't show it any further
    if (isset($_GET['from']) && $_GET['from'] === 'admin-notice') {
        $hasVisited = get_user_meta(get_current_user_id(), 
            'lvdbid_has_visited_notice_link', 
            true);

        if (empty($hasVisited)) {
            add_user_meta(get_current_user_id(), 
                'lvdbid_has_visited_notice_link', 
                'yes', 
                true);
        }
    }
}

function lvdbid_get_blog_info_keys() {
    //bloginfo keys, as specified here: https://developer.wordpress.org/reference/functions/get_bloginfo/
    return array(
        'name' => array(
            'desc' => 'Site title (set in Settings > General)',
            'provider' => "get_option('blogname')"
        ),
        'description' => array(
            'desc' => 'Site tagline (set in Settings > General)',
            'provider' => "get_option('blogdescription')"
        ),
        'wpurl' => array(
            'desc' => 'The WordPress address (URL) (set in Settings > General)',
            'provider' => 'site_url()'
        ),
        'url' => array(
            'desc' => 'The Site address (URL) (set in Settings > General)',
            'provider' => 'home_url()'
        ),
        'admin_email' => array(
            'desc' => 'Admin email (set in Settings > General)',
            'provider' => "get_option('admin_email')"
        ),
        'charset' => array(
            'desc' => 'The "Encoding for pages and feeds" (set in Settings > Reading)',
            'provider' => "get_option('blog_charset')"
        ),
        'version' => array(
            'desc' => 'The current WordPress version',
            'provider' => 'global $wp_version'
        ),
        'html_type' => array(
            'desc' => 'The content-type (default: "text/html")',
            'provider' => "get_option('html_type')"
        ),
        'language' => array(
            'desc' => 'Language code for the current site',
            'provider' => 'N/A'
        ),
        'stylesheet_url' => array(
            'desc' => 'URL to the stylesheet for the active theme. An active child theme will take precedence over this value',
            'provider' => "get_stylesheet_uri()"
        ),
        'stylesheet_directory' => array(
            'desc' => 'Directory path (URL) for the active theme. An active child theme will take precedence over this value',
            'provider' => "get_stylesheet_directory_uri()"
        ),
        'template_url' => array(
            'desc' => 'URL of the active theme’s directory. An active child theme will NOT take precedence over this value',
            'provider' => "get_template_directory_uri()"
        ),
        'template_directory' => array(
            'desc' => 'URL of the active theme’s directory. An active child theme will NOT take precedence over this value',
            'provider' => "get_template_directory_uri()"
        ),
        'pingback_url' => array(
            'desc' => 'The pingback XML-RPC file URL (xmlrpc.php)',
            'provider' => "site_url('xmlrpc.php')"
        ),
        'atom_url' => array(
            'desc' => 'The Atom feed URL (/feed/atom)',
            'provider' => "get_feed_link('atom')"
        ),
        'comments_atom_url' => array(
            'desc' => 'The comments Atom feed URL (/comments/feed)',
            'provider' => "get_feed_link('comments_atom')"
        ),
        'rdf_url' => array(
            'desc' => 'The RDF/RSS 1.0 feed URL (/feed/rdf)',
            'provider' => "get_feed_link('rdf')"
        ),
        'rss_url' => array(
            'desc' => 'The RSS 0.92 feed URL (/feed/rss)',
            'provider' => "get_feed_link('rss')"
        ),
        'rss2_url' => array(
            'desc' => 'The RSS 2.0 feed URL (/feed)',
            'provider' => "get_feed_link('rss2')"
        ),
        'comments_rss2_url' => array(
            'desc' => 'The comments RSS 2.0 feed URL (/comments/feed)',
            'provider' => "get_feed_link('comments_rss2')"
        )
    );
}

function lvdbid_show_blog_info() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    lvdbid_check_and_mark_if_user_came_from_admin_notice();

    $data = new stdClass();
    $data->pageTitle = get_admin_page_title();
    $data->blogInfoKeys = lvdbid_get_blog_info_keys();

    require LVD_BID_PLUGIN_VIEWS . '/lvdbid-show-blog-info.php';
}

function lvdbid_get_option_name($option) {
    return is_array($option) 
        ? $option['option_name'] 
        : $option;
}

function lvdbid_format_option_value($original) {
    $value = maybe_unserialize($original);
    return var_export($value, true);
}

function lvdbid_is_site_transient_option($option) {
    $optionName = lvdbid_get_option_name($option);
    return preg_match('/^_site_transient_/', $optionName);
}

function lvdbid_is_transient_timeout_option($option) {
    $optionName = lvdbid_get_option_name($option);
    return preg_match('/^_transient_timeout_/', $optionName) 
        || preg_match('/^_site_transient_timeout_/', $optionName);
}

function lvdbid_get_transient_name($option) {
    $siteTransientReplCount = 0;
    $optionName = lvdbid_get_option_name($option);

    $optionName = preg_replace('/^_site_transient_(.*)$/', '$1', 
        $optionName, 
        -1, 
        $siteTransientReplCount);
    
    if ($siteTransientReplCount < 1) {
        $optionName = preg_replace('/^_transient_(.*)$/', '$1', $optionName);
    }

    return $optionName;
}

function lvdbid_process_option($option) {
    $optionValue = $option['option_value'];
    $isCompositeValue = lvdbid_is_serialized_composite_type($optionValue);

    $option['option_transient_name'] = lvdbid_get_transient_name($option);
    $option['option_composite'] = $isCompositeValue;
    $option['option_value'] = !$isCompositeValue 
        ? lvdbid_format_option_value($optionValue) 
        : null;
    
    return $option;
}

function lvdbid_process_options_list($optionsList) {
    foreach ($optionsList as $key => $option) {
        $optionsList[$key] = lvdbid_process_option($option);
    }
    return $optionsList;
}

function lvdbid_process_transients_list($optionsList) {
    $timeouts = array();
    $transientsList = array();
    
    foreach ($optionsList as $option) {
        if (lvdbid_is_transient_timeout_option($option)) {
            $forTransientKey = str_replace('_timeout', '', $option['option_name']);
            $timeouts[$forTransientKey] = !empty($option['option_value']) 
                ? date('Y-m-d H:i:s', $option['option_value'])
                : null;
        } else {
            $transientsList[] = lvdbid_process_option($option);
        }
    }

    foreach ($transientsList as $key => $transient) {
        $transientName = $transient['option_name'];
        $transient['option_expiration'] = isset($timeouts[$transientName])
            ? $timeouts[$transientName]
            : null;
        $transientsList[$key] = $transient;
    }

    return $transientsList;
}

function lvdbid_get_all_options() {
    $db = lvdbid_get_wpdb();
    $allOptions = $db->get_results(
        "SELECT option_name, option_value, autoload 
            FROM $db->options 
            WHERE (option_name NOT LIKE '_transient_%'
                AND option_name NOT LIKE '_transient_timeout_%'
                AND option_name NOT LIKE '_site_transient_%'
                AND option_name NOT LIKE '_site_transient_timeout_%')
            ORDER BY option_name", ARRAY_A
    );

    if (!$allOptions) {
        $allOptions = array();
    }

    return lvdbid_process_options_list($allOptions);
}

function lvdbid_show_all_options() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    lvdbid_check_and_mark_if_user_came_from_admin_notice();

    $data = new stdClass();
    $data->allOptions = lvdbid_get_all_options();
    $data->pageTitle = get_admin_page_title();

    $data->ajaxUrl = get_admin_url(null, 'admin-ajax.php', 'admin');
    $data->dumpOptionAction = LVD_BID_ACTION_DUMP_OPTION;
    $data->dumpOptionNonce = wp_create_nonce(LVD_BID_NONCE_DUMP_OPTION);

    require LVD_BID_PLUGIN_VIEWS . '/lvdbid-show-all-options.php';
}

function lvdbid_dump_option() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    if (!lvdbid_check_nonce(LVD_BID_NONCE_DUMP_OPTION)) {
        lvdbid_bad_request_and_die();
    }

    $optionValue = null;
    $optionName = isset($_GET['lvdbid_option']) 
        ? $_GET['lvdbid_option'] 
        : null;

    if (!empty($optionName)) {
        $optionValue = get_option($optionName, LVD_BID_NO_OPTION);
    }

    if ($optionValue !== LVD_BID_NO_OPTION) {
        Kint::dump($optionValue);
    } else {
        require LVD_BID_PLUGIN_VIEWS . '/lvdbid-option-not-found.php';
    }

    die;
}

function lvdbid_get_all_transients() {
    $db = lvdbid_get_wpdb();
    $allOptions = $db->get_results(
        "SELECT option_name, option_value 
            FROM $db->options 
            WHERE (option_name LIKE '_transient_%'
                OR option_name LIKE '_transient_timeout_%'
                OR option_name LIKE '_site_transient_%'
                OR option_name LIKE '_site_transient_timeout_%')
            ORDER BY option_name", ARRAY_A
    );

    if (!$allOptions) {
        $allOptions = array();
    }

    return lvdbid_process_transients_list($allOptions);
}

function lvdbid_show_all_transients() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    lvdbid_check_and_mark_if_user_came_from_admin_notice();

    $data = new stdClass();
    $data->allTransients = lvdbid_get_all_transients();
    $data->pageTitle = get_admin_page_title();

    $data->ajaxUrl = get_admin_url(null, 'admin-ajax.php', 'admin');
    $data->dumpTransientAction = LVD_BID_ACTION_DUMP_TRANSIENT;
    $data->dumpTransientNonce = wp_create_nonce(LVD_BID_NONCE_DUMP_TRANSIENT);

    require LVD_BID_PLUGIN_VIEWS . '/lvdbid-show-all-transients.php';
}

function lvdbid_dump_transient() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    if (!lvdbid_check_nonce(LVD_BID_NONCE_DUMP_TRANSIENT)) {
        lvdbid_bad_request_and_die();
    }

    $transientValue = null;
    $transientOptionaName = isset($_GET['lvdbid_transient']) 
        ? $_GET['lvdbid_transient'] 
        : null;

    if (!empty($transientOptionaName)) {
        $transientName = lvdbid_get_transient_name($transientOptionaName);
        $transientValue = !lvdbid_is_site_transient_option($transientOptionaName)
            ? get_transient($transientName)
            : get_site_transient($transientName);
    }

    if ($transientValue !== false) {
        Kint::dump($transientValue);
    } else {
        require LVD_BID_PLUGIN_VIEWS . '/lvdbid-transient-not-found.php';
    }

    die;
}

function lvbid_setup() {
    if (!class_exists('Kint')) {
        require_once dirname(__FILE__) .  '/lib/3rdParty/kint/kint.phar';
    }

    //Configure Kint core:
    //  - render the tree already expanded
    //  - do not include the footer that shows a trace of where the Kint::dump was called
    Kint::$expanded = true;
    Kint::$display_called_from = false;

    //Configure the Rich renderer:
    //  - do not group the items in a toolbar at the bottom of the mage
    //  - change the theme
    Kint\Renderer\RichRenderer::$folder = false;
    Kint\Renderer\RichRenderer::$theme = 'aante-light.css';
}

add_action('init', 'lvbid_setup');
add_action('admin_menu', 'lvdbid_add_admin_menu_links');
add_action('all_admin_notices', 'lvdbid_show_admin_notice');
add_action('admin_enqueue_scripts', 'lvdbid_add_stylesheets_scripts');
add_action('wp_ajax_' . LVD_BID_ACTION_DUMP_OPTION, 'lvdbid_dump_option');
add_action('wp_ajax_' . LVD_BID_ACTION_DUMP_TRANSIENT, 'lvdbid_dump_transient');