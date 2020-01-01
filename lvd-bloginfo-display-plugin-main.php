<?php
/**
 * Plugin Name: Blog Info Display
 * Author: Alexandru Boia
 * Author URI: http://alexboia.net
 * Version: 0.1.0
 * Description: Displays all the information provided by WordPress' get_bloginfo() function and dumps all the non-transient options. Strictly developed for learning purposes.
 * License: New BSD License
 * Text Domain: lvd-bloginfo-display
 */

define('LVD_BID_PLUGIN_ROOT', dirname(__FILE__));
define('LVD_BID_PLUGIN_VIEWS', LVD_BID_PLUGIN_ROOT . '/views');
define('LVD_BID_BLOGINFO_MENU_SLUG', 'lvd-bloginfo-display');
define('LVD_BID_BLOGOPTIONS_MENU_SLUG', 'lvd-bloginfo-options-display');

define('LVDBID_ACTION_DUMP_OPTION', 'lvdbid_dump_option');
define('LVD_BID_NONCE_DUMP_OPTION', 'lvdbid.nonce.dumpOption');

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
    if (!empty($page) && in_array($page, array(LVD_BID_BLOGINFO_MENU_SLUG, LVD_BID_BLOGOPTIONS_MENU_SLUG))) {
        wp_enqueue_style('lvdbid-plugin-css', 
            plugins_url('media/css/plugin.css', __FILE__), 
                array(), 
                '0.1.0', 
                'all');

        wp_enqueue_script('lvdbid-clipboard-js', 
            plugins_url('media/js/3rdParty/clipboard-js/clipboard.min.js', __FILE__), 
            array(), 
            '2.0.4', 
            true);

        wp_enqueue_script('lvdbid-plugin-js', 
            plugins_url('media/js/plugin.js', __FILE__), 
            array(), 
            '0.1.0', 
            true);

        if ($page == LVD_BID_BLOGOPTIONS_MENU_SLUG) {
            wp_enqueue_script('jquery-blockui-js', 
                plugins_url('media/js/3rdParty/jquery.blockUI.js', __FILE__), 
                array(), 
                '2.66.0', 
                true);

            wp_enqueue_script('lvdbid-show-all-options-js', 
                plugins_url('media/js/show-all-options.js', __FILE__), 
                array(), 
                '0.1.0', 
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

    add_submenu_page('lvd-bloginfo-display', 
        __('Debug blog options', 'lvd-bloginfo-display'), 
        __('Debug blog options', 'lvd-bloginfo-display'), 
            'manage_options', 
            LVD_BID_BLOGOPTIONS_MENU_SLUG, 
            'lvdbid_show_all_options');
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

function lvdbid_format_option_value($original) {
    $value = maybe_unserialize($original);
    return var_export($value, true);
}

function lvdbid_get_all_options() {
    $db = lvdbid_get_wpdb();
    $allOptions = $db->get_results(
        "SELECT option_name, option_value, autoload 
            FROM $db->options 
            WHERE option_name NOT LIKE '%_transient%' 
            ORDER BY option_name", ARRAY_A
    );

    if (!$allOptions) {
        $allOptions = array();
    }

    foreach ($allOptions as $key => $option) {
        $optionValue = $option['option_value'];
        $isCompositeValue = lvdbid_is_serialized_composite_type($optionValue);

        $allOptions[$key]['option_composite'] = $isCompositeValue;
        $allOptions[$key]['option_value'] = !$isCompositeValue 
            ? lvdbid_format_option_value($optionValue) 
            : null;
    }

    return $allOptions;
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
    $data->dumpOptionAction = LVDBID_ACTION_DUMP_OPTION;
    $data->dumpOptionNonce = wp_create_nonce(LVD_BID_NONCE_DUMP_OPTION);

    require LVD_BID_PLUGIN_VIEWS . '/lvdbid-show-all-options.php';
}

function lvdbid_dump_option() {
    if (!current_user_can('manage_options')) {
        lvdbid_send_forbidden_and_die();
    }

    if (!isset($_GET['lvdbid_nonce']) || !wp_verify_nonce($_GET['lvdbid_nonce'], LVD_BID_NONCE_DUMP_OPTION)) {
        lvdbid_bad_request_and_die();
    }

    $value = null;
    $option = isset($_GET['lvdbid_option']) 
        ? $_GET['lvdbid_option'] 
        : null;

    if (!empty($option)) {
        $value = get_option($option);
    }

    Kint::dump($value);
    die;
}

function lvbid_setup() {
    if (!class_exists('Kint')) {
        require_once dirname(__FILE__) .  '/lib/3rdParty/kint/kint.phar';
    }

    Kint::$expanded = true;
    Kint::$display_called_from = false;

    Kint\Renderer\RichRenderer::$folder = false;
    Kint\Renderer\RichRenderer::$theme = 'aante-light.css';
}

add_action('init', 'lvbid_setup');
add_action('admin_menu', 'lvdbid_add_admin_menu_links');
add_action('all_admin_notices', 'lvdbid_show_admin_notice');
add_action('admin_enqueue_scripts', 'lvdbid_add_stylesheets_scripts');
add_action('wp_ajax_' . LVDBID_ACTION_DUMP_OPTION, 'lvdbid_dump_option');