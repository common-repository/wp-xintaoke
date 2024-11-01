<?php

/**
 * xt_core_load_session()
 *
 * Load up the XT session
 */
function xt_core_load_session() {
    if (!isset($_SESSION))
        $_SESSION = null;

    if ((!is_array($_SESSION)) xor (!$_SESSION))
        session_start();
}

function xt_core_constants_route() {
    global $xt;
    $xt->is_xintao = false;
    $xt->is_login = false;
    $xt->is_page = false;
    $xt->is_index = false;
    $xt->is_shares = false;
    $xt->is_share = false;
    $xt->is_albums = false;
    $xt->is_album = false;
    $xt->is_group = false;
    $xt->is_users = false;
    $xt->is_user = false;
    $xt->is_account = false;
    $xt->is_sidebar = false;
    $xt->is_taobaos = false;
    $xt->is_taobao = false;
    $xt->is_shops = false;
    $xt->is_paipais = false;
    $xt->is_bijias = false;
    $xt->is_tuans = false;
    $xt->is_temais = false;
    $xt->is_coupons = false;
    $xt->is_jump = false;
    $xt->is_invite = false;
    $xt->is_daogous = false;
    $xt->is_daogou = false;
    $xt->is_helps = false;
    $xt->is_help = false;
    $xt->is_brands = false;
    $xt->is_stars = false;
    $xt->is_activities = false;
    $xt->is_taoquan = false;
    $xt->is_quan = false;
    $xt->is_malls = false;
    $xt->is_error404 = false;
    $xt->is_sitemap = false;
}

/**
 * xt_core_constants()
 *
 * The core XT constants necessary to start loading
 */
function xt_core_constants() {
    if (!defined('XT_URL'))
        define('XT_URL', plugins_url('', __FILE__));
    global $xt;
    $xt->pages = array(
        'share' => 'share'
    );
    xt_core_constants_route();
    // Define Plugin version
    define('XT_VERSION', '1.0.0');
    define('XT_DB_VERSION', '100');
    define('XT_STATIC_VERSION', '20120428');


    // Define Debug Variables for developers
    define('XT_DEBUG', false);

    define('XT_PLUGIN_DIR', WP_PLUGIN_DIR . '/wp-xintaoke');

    define('XT_PLUGIN_URL', plugins_url('wp-xintaoke'));

    // Images URL
    define('XT_CORE_IMAGES_URL', XT_URL . '/xt-themes/images');
    define('XT_CORE_IMAGES_PATH', XT_FILE_PATH . '/xt-themes/images');

    // JS URL
    define('XT_CORE_JS_URL', XT_URL . '/xt-core/js');
    define('XT_CORE_JS_PATH', XT_FILE_PATH . '/xt-core/js');

    // CSS URL
    define('XT_CORE_CSS_URL', XT_URL . '/xt-core/css');
    define('XT_CORE_CSS_PATH', XT_FILE_PATH . '/xt-core/css');

    //theme 

    define('XT_THEME_URL', XT_URL . '/xt-themes');
    define('XT_THEME_PATH', XT_FILE_PATH . '/xt-themes');
    define('XT_THEME_LINKCOLOR', '#FF8CB5');
    //fanxian
    define('XT_FANXIAN_PRE', 'fx');
    define('XT_FANXIAN_OLD_PRE', 'xtfl');
    define('XT_NOFANXIAN_PRE', 'no');

    //connect
    define('XT_API_URL', 'http://plugin.xintaonet.com');
    define('XT_VERSION_URL', 'http://plugin.xintaonet.com/version.js');

    define('XT_WEIBO_PRE', 'sina');
    define('XT_WEIBO_KEY', 'xt_weibo_key');
    define('XT_WEIBO_TOKEN', 'xt_weibo_token');
    define('XT_WEIBO', 'xt_weibo');
    define('XT_WEIBO_SETTING', 'xt_weibo_setting');
    define('XT_WEIBO_AUTHORIZE_URL', 'https://api.weibo.com/oauth2/authorize');
    define('XT_WEIBO_TOKEN_URL', 'https://api.weibo.com/oauth2/access_token');

    define('XT_TAOBAO_PRE', 'taobao');
    define('XT_TAOBAO_KEY', 'xt_taobao_key');
    define('XT_TAOBAO_TOKEN', 'xt_taobao_token');
    define('XT_TAOBAO', 'xt_taobao');
    define('XT_TAOBAO_SETTING', 'xt_taobao_setting');
    define('XT_TAOBAO_AUTHORIZE_URL', 'https://oauth.taobao.com/authorize');
    define('XT_TAOBAO_TOKEN_URL', 'https://oauth.taobao.com/token');

    define('XT_QQ_PRE', 'qq');
    define('XT_QQ_KEY', 'xt_qq_key');
    define('XT_QQ_TOKEN', 'xt_qq_token');
    define('XT_QQ', 'xt_qq');
    define('XT_QQ_SETTING', 'xt_qq_setting');
    define('XT_QQ_AUTHORIZE_URL', 'https://graph.qq.com/oauth2.0/authorize');
    define('XT_QQ_TOKEN_URL', 'https://graph.qq.com/oauth2.0/token');

    define('XT_CITY_DEFAULT', 310000);
    define('XT_BIJIA_CATEGORY_DEFAULT', 113000000);

    if (!defined('XT_SITEMAP_INDEX_CHANGEFREQ')) {
        define('XT_SITEMAP_INDEX_CHANGEFREQ', 'daily');
    }
    if (!defined('XT_SITEMAP_INDEX_PRIORITY')) {
        define('XT_SITEMAP_INDEX_PRIORITY', '1.0');
    }
    if (!defined('XT_SITEMAP_SHARE_LIMIT')) {
        define('XT_SITEMAP_SHARE_LIMIT', 5000);
    }
    if (!defined('XT_SITEMAP_SHARE_CHANGEFREQ')) {
        define('XT_SITEMAP_SHARE_CHANGEFREQ', 'weekly');
    }
    if (!defined('XT_SITEMAP_SHARE_PRIORITY')) {
        define('XT_SITEMAP_SHARE_PRIORITY', '0.9');
    }
    if (!defined('XT_SITEMAP_POST_LIMIT')) {
        define('XT_SITEMAP_POST_LIMIT', 5000);
    }
    if (!defined('XT_SITEMAP_POST_CHANGEFREQ')) {
        define('XT_SITEMAP_POST_CHANGEFREQ', 'weekly');
    }
    if (!defined('XT_SITEMAP_POST_PRIORITY')) {
        define('XT_SITEMAP_POST_PRIORITY', '0.8');
    }
    if (!defined('XT_SITEMAP_ALBUM_LIMIT')) {
        define('XT_SITEMAP_ALBUM_LIMIT', 5000);
    }
    if (!defined('XT_SITEMAP_ALBUM_CHANGEFREQ')) {
        define('XT_SITEMAP_ALBUM_CHANGEFREQ', 'weekly');
    }
    if (!defined('XT_SITEMAP_ALBUM_PRIORITY')) {
        define('XT_SITEMAP_ALBUM_PRIORITY', '0.8');
    }
    if (!defined('XT_SITEMAP_USER_LIMIT')) {
        define('XT_SITEMAP_USER_LIMIT', 5000);
    }
    if (!defined('XT_SITEMAP_USER_CHANGEFREQ')) {
        define('XT_SITEMAP_USER_CHANGEFREQ', 'weekly');
    }
    if (!defined('XT_SITEMAP_USER_PRIORITY')) {
        define('XT_SITEMAP_USER_PRIORITY', '0.7');
    }
    if (!defined('XT_SITEMAP_OTHER_CHANGEFREQ')) {
        define('XT_SITEMAP_OTHER_CHANGEFREQ', 'daily');
    }
    if (!defined('XT_SITEMAP_OTHER_PRIORITY')) {
        define('XT_SITEMAP_OTHER_PRIORITY', '0.9');
    }
    // Require loading of deprecated functions for now. We will ween XT off
    // of this in future versions.
    define('XT_LOAD_DEPRECATED', true);

    //register_theme_directory(XT_PLUGIN_DIR . '/xt-theme/themes');

    xt_default_action();
    xt_default_filter();

    xt_core_constants_option();
}

function xt_core_constants_option() {
    //站点
    define('XT_OPTION_ENV', 'xt_option_env');
    define('XT_OPTION_INSTALLED', 'xt_option_installed');
    define('XT_OPTION_VERIFICATION', 'xt_option_verification');
    define('XT_OPTION_VERSION', 'xt_option_version');
    define('XT_OPTION_VERSION_DB', 'xt_option_version_db');
    define('XT_OPTION_MENUS', 'xt_option_menus');
    define('XT_OPTION_GLOBAL', 'xt_option_global');
    define('XT_OPTION_MAIL', 'xt_option_mail');
    define('XT_OPTION_FANXIAN', 'xt_option_fanxian');
    define('XT_OPTION_ROLE', 'xt_option_role');
    define('XT_OPTION_PAGES', 'xt_option_pages');
    define('XT_OPTION_PAGE_PRE', 'xt_option_page_');
    define('XT_OPTION_PAGE_WIDGETS_PRE', 'xt_option_page_widgets_');
    define('XT_OPTION_PAGE_HTML_PRE', 'xt_option_page_html_');
    define('XT_OPTION_PAGE_HEADERS', 'xt_option_page_headers');
    define('XT_OPTION_PAGE_FOOTERS', 'xt_option_page_footers');
    define('XT_OPTION_PAGE_HEADER_FOOTER', 'xt_option_page_header_footer');
    //MAIL
    define('XT_OPTION_MAIL_RETRIEVEPASSWORD_TITLE', 'xt_option_mail_retrievepassword_title');
    define('XT_OPTION_MAIL_RETRIEVEPASSWORD_MESSAGE', 'xt_option_mail_retrievepassword_message');

    define('XT_OPTION_CODE_ANALYTICS', 'xt_option_code_analytics');
    define('XT_OPTION_CODE_SHARE', 'xt_option_code_share');

    define('XT_OPTION_PLATFORM', 'xt_option_platform');
    define('XT_OPTION_CATALOG_SHARE', 'xt_option_catalog_share');
    define('XT_OPTION_CATALOG_ALBUM', 'xt_option_catalog_album');
    define('XT_OPTION_CATALOG_POST', 'xt_option_catalog_post');
    define('XT_OPTION_CATALOG_DAOGOU', 'xt_option_catalog_daogou');
    define('XT_OPTION_CATALOG_HELP', 'xt_option_catalog_help');
    define('XT_OPTION_TAOBAO_ITEMCAT', 'xt_option_taobao_itemcat');
    define('XT_OPTION_PAIPAI_ITEMCAT', 'xt_option_paipai_itemcat');
    define('XT_OPTION_YIQIFA_WEBSITE_CATEGORY', 'xt_option_yiqifa_website_category');
    define('XT_OPTION_YIQIFA_TUAN_WEBSITE', 'xt_option_yiqifa_tuan_website');
    define('XT_OPTION_YIQIFA_HOTACTIVITY_WEBSITE', 'xt_option_yiqifa_hotactivity_website');
    define('XT_OPTION_YIQIFA_HOTACTIVITY', 'xt_option_yiqifa_hotactivity');

    define('XT_OPTION_THEME_SETTING', 'xt_option_theme_setting');
    define('XT_OPTION_THEME_CUSTOM', 'xt_option_theme_custom');
    define('XT_OPTION_THEME', 'xt_option_theme');
    //Sitemap
    define('XT_OPTION_SITEMAP_INDEX', 'xt_option_sitemap_index');
    define('XT_OPTION_SITEMAP_INDEX_TIMESTAMP', 'xt_option_sitemap_index_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_INDEX', 'xt_option_sitemap_baidu_index');
    define('XT_OPTION_SITEMAP_BAIDU_INDEX_TIMESTAMP', 'xt_option_sitemap_baidu_index_timestamp');
    define('XT_OPTION_SITEMAP_SHARE', 'xt_option_sitemap_share');
    define('XT_OPTION_SITEMAP_SHARE_TIMESTAMP', 'xt_option_sitemap_share_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_SHARE', 'xt_option_sitemap_baidu_share');
    define('XT_OPTION_SITEMAP_BAIDU_SHARE_TIMESTAMP', 'xt_option_sitemap_baidu_share_timestamp');
    define('XT_OPTION_SITEMAP_POST', 'xt_option_sitemap_post');
    define('XT_OPTION_SITEMAP_POST_TIMESTAMP', 'xt_option_sitemap_post_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_POST', 'xt_option_sitemap_baidu_post');
    define('XT_OPTION_SITEMAP_BAIDU_POST_TIMESTAMP', 'xt_option_sitemap_baidu_post_timestamp');
    define('XT_OPTION_SITEMAP_ALBUM', 'xt_option_sitemap_album');
    define('XT_OPTION_SITEMAP_ALBUM_TIMESTAMP', 'xt_option_sitemap_album_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_ALBUM', 'xt_option_sitemap_baidu_album');
    define('XT_OPTION_SITEMAP_BAIDU_ALBUM_TIMESTAMP', 'xt_option_sitemap_baidu_album_timestamp');
    define('XT_OPTION_SITEMAP_USER', 'xt_option_sitemap_user');
    define('XT_OPTION_SITEMAP_USER_TIMESTAMP', 'xt_option_sitemap_user_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_USER', 'xt_option_sitemap_baidu_user');
    define('XT_OPTION_SITEMAP_BAIDU_USER_TIMESTAMP', 'xt_option_sitemap_baidu_user_timestamp');
    define('XT_OPTION_SITEMAP_OTHER', 'xt_option_sitemap_other');
    define('XT_OPTION_SITEMAP_OTHER_TIMESTAMP', 'xt_option_sitemap_other_timestamp');
    define('XT_OPTION_SITEMAP_BAIDU_OTHER', 'xt_option_sitemap_baidu_other');
    define('XT_OPTION_SITEMAP_BAIDU_OTHER_TIMESTAMP', 'xt_option_sitemap_baidu_other_timestamp');
    //个人信息option
    define('XT_USER_OLD_ID', 'xt_user_old_id');
    define('XT_USER_OLD_PARENTID', 'xt_user_old_parentid');
    define('XT_USER_GUID', 'xt_user_guid');
    define('XT_USER_INFO', 'xt_user_info');
    define('XT_USER_AVATAR', 'xt_user_avatar');
    define('XT_USER_FOLLOW', 'xt_user_follow');

    //返现
    define('XT_USER_FANXIAN_RATE', 'xt_user_fanxian_rate');

    //推广
    define('XT_USER_PARENT', 'xt_user_parent');

    define('XT_USER_GENDER', 'xt_user_gender');
    define('XT_USER_QQ', 'xt_user_qq');
    define('XT_USER_MOBILE', 'xt_user_mobile');
    define('XT_USER_ALIPAY', 'xt_user_alipay');
    define('XT_USER_ALIPAY_NAME', 'xt_user_alipay_name');
    define('XT_USER_BANK', 'xt_user_bank');
    define('XT_USER_BANK_CARD', 'xt_user_bank_card');
    define('XT_USER_BANK_NAME', 'xt_user_bank_name');

    define('XT_USER_AVATAR_DEFAULT', XT_CORE_IMAGES_URL . '/avatar_girl_default.jpg');

    //数量option
    define('XT_USER_COUNT', 'xt_user_count');

    define('XT_USER_COUNT_CASH', 'cash');
    define('XT_USER_COUNT_CASH_COST', 'cash_cast');
    define('XT_USER_COUNT_JIFEN', 'jifen');
    define('XT_USER_COUNT_JIFEN_COST', 'jifen_cost');

    define('XT_USER_COUNT_SHARE', 'share');
    define('XT_USER_COUNT_ALBUM', 'album');
    define('XT_USER_COUNT_FOLLOW', 'follow');
    define('XT_USER_COUNT_FANS', 'fans');

    define('XT_USER_COUNT_FAV_SHARE', 'fav_share');
    define('XT_USER_COUNT_FAV_ALBUM', 'fav_album');
    define('XT_USER_COUNT_FAV_TOPIC', 'fav_topic');
    define('XT_USER_COUNT_FAV_GROUP', 'fav_group');
    define('XT_USER_COUNT_FAV_BRAND', 'fav_brand');

    //Share
    define('XT_SHARE_DEFAULT_PIC', XT_CORE_IMAGES_URL . '/grey.gif');
    do_action('xt_core_constants_option');
}

/**
 * xt_core_version_processing()
 */
function xt_core_constants_version_processing() {
    global $wp_version;

    $version_processing = str_replace(array(
        '_',
        '-',
        '+'
            ), '.', strtolower($wp_version));
    $version_processing = str_replace(array(
        'alpha',
        'beta',
        'gamma'
            ), array(
        'a',
        'b',
        'g'
            ), $version_processing);
    $version_processing = preg_split("/([a-z]+)/i", $version_processing, -1, PREG_SPLIT_DELIM_CAPTURE);

    array_walk($version_processing, create_function('&$v', '$v = trim($v,". ");'));

    define('IS_WP25', version_compare($version_processing[0], '2.5', '>='));
    define('IS_WP27', version_compare($version_processing[0], '2.7', '>='));
    define('IS_WP29', version_compare($version_processing[0], '2.9', '>='));
    define('IS_WP30', version_compare($version_processing[0], '3.0', '>='));
}

/**
 * xt_core_is_multisite()
 *
 * Checks if this is a multisite installation of WordPress
 *
 * @global object $wpdb
 * @return bool
 */
function xt_core_is_multisite() {
    global $wpdb;

    if (defined('IS_WPMU'))
        return IS_WPMU;

    if (isset($wpdb->blogid))
        $is_multisite = 1;
    else
        $is_multisite = 0;

    define('IS_WPMU', $is_multisite);

    return (bool) $is_multisite;
}

/**
 * xt_core_constants_table_names()
 *
 * List globals here for proper assignment
 *
 * @global string $table_prefix
 * @global object $wpdb
 */
function xt_core_constants_table_names() {
    global $table_prefix, $wpdb;

    // Use the DB method if it's around
    if (!empty($wpdb->prefix))
        $wp_table_prefix = $wpdb->prefix;

    // Fallback on the wp_config.php global
    else
    if (!empty($table_prefix))
        $wp_table_prefix = $table_prefix;

    // the XT meta prefix, used for the product meta functions.
    define('XT_META_PREFIX', '_xt_');

    // These tables are required, either for speed, or because there are no
    // existing WordPress tables suitable for the data stored in them.

    define('XT_TABLE_USER_NOTICE', "{$wp_table_prefix}xt_user_notice");
    define('XT_TABLE_USER_FOLLOW', "{$wp_table_prefix}xt_user_follow");
    define('XT_TABLE_USER_JIFEN_LOG', "{$wp_table_prefix}xt_user_jifen_log");
    define('XT_TABLE_USER_JIFEN_ORDER', "{$wp_table_prefix}xt_user_jifen_order");
    define('XT_TABLE_USER_JIFEN_ITEM', "{$wp_table_prefix}xt_user_jifen_item");

    define('XT_TABLE_CATALOG', "{$wp_table_prefix}xt_catalog");
    define('XT_TABLE_CATALOG_ITEMCAT', "{$wp_table_prefix}xt_catalog_itemcat");

    define('XT_TABLE_SHARE', "{$wp_table_prefix}xt_share");
    define('XT_TABLE_SHARE_ALBUM', "{$wp_table_prefix}xt_share_album");
    define('XT_TABLE_SHARE_CATALOG', "{$wp_table_prefix}xt_share_catalog");
    define('XT_TABLE_ALBUM_CATALOG', "{$wp_table_prefix}xt_album_catalog");
    define('XT_TABLE_SHARE_MATCH', "{$wp_table_prefix}xt_share_match");
    define('XT_TABLE_SHARE_COMMENT', "{$wp_table_prefix}xt_share_comment");
    define('XT_TABLE_SHARE_TAG', "{$wp_table_prefix}xt_share_tag");
    define('XT_TABLE_SHARE_TAG_CATALOG', "{$wp_table_prefix}xt_share_tag_catalog");
    define('XT_TABLE_SHARE_CRON', "{$wp_table_prefix}xt_share_cron");


    define('XT_TABLE_FAVORITE', "{$wp_table_prefix}xt_favorite");
    define('XT_TABLE_ALBUM', "{$wp_table_prefix}xt_album");

    define('XT_TABLE_TOPIC', "{$wp_table_prefix}xt_topic");
    define('XT_TABLE_GROUP', "{$wp_table_prefix}xt_group");
    define('XT_TABLE_BRAND', "{$wp_table_prefix}xt_brand");

    define('XT_TABLE_TAOBAO_ITEMCAT', "{$wp_table_prefix}xt_taobao_itemcat");
    define('XT_TABLE_TAOBAO_REPORT', "{$wp_table_prefix}xt_taobao_report");

    define('XT_TABLE_FANXIAN', "{$wp_table_prefix}xt_fanxian");
    define('XT_TABLE_TIXIAN', "{$wp_table_prefix}xt_tixian");

    define('XT_TABLE_PAIPAI_ITEMCAT', "{$wp_table_prefix}xt_paipai_itemcat");
    define('XT_TABLE_PAIPAI_REPORT', "{$wp_table_prefix}xt_paipai_report");

    define('XT_TABLE_YIQIFA_REPORT', "{$wp_table_prefix}xt_yiqifa_report");

    define('XT_TABLE_TERM_RELATIONSHIPS', "{$wp_table_prefix}xt_term_relationships");
    define('XT_TABLE_TERM_TAXONOMY', "{$wp_table_prefix}xt_term_taxonomy");
    define('XT_TABLE_TERMS', "{$wp_table_prefix}xt_terms");

    do_action('xt_core_constants_table_names');
}

/**
 * xt_core_constants_uploads()
 *
 * Set the Upload related constants
 */
function xt_core_constants_uploads() {
    
}

/* * *
 * xt_core_setup_globals()
 *
 * Initialize the xt query vars, must be a global variable as we
 * cannot start it off from within the wp query object.
 * Starting it in wp_query results in intractable infinite loops in 3.0
 */

function xt_core_setup_globals() {
    
}

function xt_default_action() {

    add_action('permalink_structure_changed', 'xt_action_permalink_structure_changed');
    add_action('update_option_blogname', 'xt_action_blogname', 10, 2);
    add_action('update_option_home', 'xt_action_home', 10, 2);
    add_action('delete_user', 'xt_action_delete_user');
    if (!in_array(get_option('default_role'), xt_roles())) {
        add_action('admin_notices', 'xt_action_role_notice');
    }
    add_action('publish_page', 'xt_action_update_option_pages');
    add_action('before_delete_post', 'xt_action_delete_option_pages');
    add_action('xt_cron_share_hook', 'xt_cron_autoshare');
    add_action('xt_cron_taobao_refreshtoken_hook', 'xt_taobao_refreshtoken');
    add_action('xt_cron_report_taobao_hook', 'xt_report_taobao');
    add_action('xt_cron_report_paipai_hook', 'xt_report_paipai');
    add_action('xt_cron_report_yiqifa_hook', 'xt_report_yiqifa');
    add_action('xt_cron_catalogs_share_hook', 'xt_catalogs_share_force');
    add_action('xt_cron_user_account_hook', 'xt_cron_user_account');
    add_action('xt_cron_yiqifa_hotactivity_hook', 'xt_cron_yiqifa_hotactivity');
    add_action('xt_cron_sitemap_hook', 'xt_cron_sitemap');


    add_action('init', 'xt_taobao_jssdk_cookie'); //TOP JSSDK COOKIE
    add_action('init', 'xt_action_header_charset');
    add_action('wp_login', 'xt_action_login');
    add_action('admin_init', 'xt_action_admin_init', 1);
    add_action('xt_check_comment_flood', 'xt_check_comment_flood_db', 10, 3);
    add_action('created_category', 'xt_action_refresh_category');
    add_action('edited_category', 'xt_action_refresh_category');
    add_action('delete_category', 'xt_action_refresh_category');

    add_action('created_daogou_category', 'xt_action_refresh_daogou_category');
    add_action('edited_daogou_category', 'xt_action_refresh_daogou_category');
    add_action('delete_daogou_category', 'xt_action_refresh_daogou_category');

    add_action('created_help_category', 'xt_action_refresh_help_category');
    add_action('edited_help_category', 'xt_action_refresh_help_category');
    add_action('delete_help_category', 'xt_action_refresh_help_category');

    add_action('xt_page_updated', 'xt_action_page_updated');
    add_action('wp_create_nav_menu', 'xt_action_nav_menu');
    add_action('wp_update_nav_menu', 'xt_action_nav_menu');
    add_action('wp_delete_nav_menu', 'xt_action_nav_menu');
    add_action('profile_update', 'xt_action_profile_update', 10, 2);
    do_action('xt_default_action');
}

function xt_cron_sitemap() {
    include XT_PLUGIN_DIR . '/xt-includes/sitemap.function.php';
    xt_sitemap_build_index();
    xt_sitemap_build_share();
    xt_sitemap_build_album();
    xt_sitemap_build_other();
    xt_sitemap_build_post();
    xt_sitemap_build_user();
}

function xt_action_header_charset() {
    header('Content-Type: text/html; charset=UTF-8');
}

function xt_action_profile_update($user_id, $old_user_data) {
    $user = new WP_User($user_id);
    if ($user->exists() && !empty($old_user_data)) {
        if (!empty($user->display_name)) {
            if ($user->display_name != $old_user_data->display_name) {//update display_name cache
                global $wpdb;
                $wpdb->query($wpdb->prepare('UPDATE ' . XT_TABLE_SHARE . ' SET user_name = %s WHERE user_id = %d', $user->display_name, $user_id)); //SHARE
                $wpdb->query($wpdb->prepare('UPDATE ' . XT_TABLE_ALBUM . ' SET user_name = %s WHERE user_id = %d ', $user->display_name, $user_id)); //ALBUM
                $wpdb->query($wpdb->prepare('UPDATE ' . XT_TABLE_SHARE_ALBUM . ' SET user_name = %s WHERE user_id = %d ', $user->display_name, $user_id)); //SHARE_ALBUM
                $wpdb->query($wpdb->prepare('UPDATE ' . XT_TABLE_SHARE_COMMENT . ' SET user_name = %s WHERE user_id = %d ', $user->display_name, $user_id)); //SHARE_COMMENT
            }
        }
    }
}

function xt_action_blogname($oldvalue, $newvalue) {
    global $wpdb;
    $wpdb->query("UPDATE $wpdb->posts SET post_content = REPLACE ( post_content, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' ),post_title = REPLACE ( post_title, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' );");
}

function xt_action_home($oldvalue, $newvalue) {
    if ($oldvalue != $newvalue) {
        global $wpdb;
        $wpdb->query("UPDATE $wpdb->posts SET post_content = REPLACE ( post_content, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' ),post_title = REPLACE ( post_title, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' );");
        //update menus
        $menus = get_terms('nav_menu', array('hide_empty' => true));
        if (!empty($menus)) {
            foreach ($menus as $menu) {
                $menu_id = $menu->term_id;
                $posts = wp_get_nav_menu_items($menu_id);
                if (!empty($posts)) {
                    foreach ($posts as $post) {
                        if ($post->type == 'custom') {
                            $url = $post->url;
                            $newUrl = str_replace($oldvalue, $newvalue, $url);
                            update_post_meta($post->ID, '_menu_item_url', $newUrl);
                        }
                    }
                }
            }
        }
        //update widgets,html
        xt_replace_widgetsandhtml(array($oldvalue), array($newvalue));
        //update post
        $wpdb->query("UPDATE $wpdb->posts SET post_content = REPLACE ( post_content, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' ),post_title = REPLACE ( post_title, '" . $wpdb->escape($oldvalue) . "', '" . $wpdb->escape($newvalue) . "' );");
    }
}

function xt_action_nav_menu($menu_id = null, $menu_data = null) {
    $menus = wp_get_nav_menus();
    if (!add_option(XT_OPTION_MENUS, $menus, '', 'no')) {
        update_option(XT_OPTION_MENUS, $menus);
    }
}

function xt_action_page_updated($page) {
    if ($page == 'home' || is_numeric($page)) {
        if (!add_option(XT_OPTION_PAGE_HTML_PRE . $page, '', '', 'no')) {
            update_option(XT_OPTION_PAGE_HTML_PRE . $page, array()); //clear cache	
        }
    }
}

function xt_action_permalink_structure_changed() {
    global $wp_rewrite, $wpdb;
    $global = get_option(XT_OPTION_GLOBAL);
    $oldIndex = $global['index'];
    if ($wp_rewrite->using_index_permalinks()) {
        $global['index'] = '/' . $wp_rewrite->index;
    } else {
        $global['index'] = '';
    }
    update_option(XT_OPTION_GLOBAL, $global); //global
    if ($oldIndex != $global['index']) {
        $wpdb->query("UPDATE $wpdb->options SET option_value='' WHERE option_name like '" . XT_OPTION_PAGE_HTML_PRE . "%' ");
    }
}

function xt_action_refresh_category() {
    $categories = get_terms("category", array(
        'get' => 'all'
            ));
    if (!add_option(XT_OPTION_CATALOG_POST, $categories, '', 'no'))
        update_option(XT_OPTION_CATALOG_POST, $categories);
}

function xt_action_refresh_daogou_category() {
    $categories = get_terms("daogou_category", array(
        'get' => 'all'
            ));
    if (!add_option(XT_OPTION_CATALOG_DAOGOU, $categories, '', 'no'))
        update_option(XT_OPTION_CATALOG_DAOGOU, $categories);
}

function xt_action_refresh_help_category() {
    $categories = get_terms("help_category", array(
        'get' => 'all'
            ));
    if (!add_option(XT_OPTION_CATALOG_HELP, $categories, '', 'no'))
        update_option(XT_OPTION_CATALOG_HELP, $categories);
}

function xt_default_filter() {
    add_filter('got_rewrite', 'xt_filter_got_rewrite');
    add_filter('xt_filter_ids', 'xt_filter_ids');
    add_filter('xt_comment_text', 'wptexturize');
    add_filter('xt_comment_text', 'convert_chars');
    add_filter('xt_the_user_pic', 'xt_filter_user_pic', 10, 2);
    //TODO暂时不开放
    //add_filter('xt_comment_text', 'make_clickable', 9);
    //add_filter('xt_comment_text', 'force_balance_tags', 25);
    add_filter('xt_comment_text', 'convert_smilies', 20);
    //add_filter('xt_comment_text', 'wpautop', 30);
    add_filter('xt_comment_text', 'convert_at', 30);
    add_filter('xt_comment_flood_filter', 'xt_throttle_comment_flood', 10, 3);
    add_filter('xt_get_comment_time_human', 'xt_format_time');
    add_filter('get_the_share_time_human', 'xt_format_time');

    add_filter('get_search_form', 'xt_search_form');

    add_filter('login_redirect', 'xt_login_redirect', 10, 3);

    add_filter('the_share_picurl', 'xt_share_picurl');

    add_filter('user_contactmethods', 'xt_contact_info');
    add_filter('comments_template', 'xt_filter_comments_template');
    add_filter('sanitize_user', 'xt_sanitize_user', 3, 3);

//    add_filter('retrieve_password_title', 'xt_filter_retrieve_password_title');
    add_filter('retrieve_password_message', 'xt_filter_retrieve_password_message');

    do_action('xt_default_filter');
}

function xt_filter_retrieve_password_message($message) {
    return str_replace(array('<', '>'), '', $message);
}

//function xt_mail_retrieve_password_title() {
//    $title = get_option(XT_OPTION_MAIL_RETRIEVEPASSWORD_TITLE);
//    if (empty($title)) {
//        $title = ('[{blogname}] 密码重设');
//    }
//    return $title;
//}
//
//function xt_mail_retrieve_password_message() {
//    $mesage = get_option(XT_OPTION_MAIL_RETRIEVEPASSWORD_MESSAGE);
//    if (empty($mesage)) {
//        $mesage = '';
//        $mesage = __('Someone requested that the password be reset for the following account:') . "\r\n";
//        $mesage .= "站点：{home}\r\n";
//        $mesage .= ('用户名： {userlogin}') . "\r\n";
//        $mesage .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n";
//        $mesage .= __('To reset your password, visit the following address:') . "\r\n";
//        $mesage .= "{url}\r\n";
//    }
//    return $mesage;
//}
//
//function xt_filter_retrieve_password_title($title = '') {
//    $title = xt_mail_retrieve_password_title();
//    return str_replace('{blogname}', get_bloginfo('name'), $title);
//}
//
//function xt_filter_retrieve_password_message($mesage = '', $key = '') {
//    if (strpos($_POST['user_login'], '@')) {
//        $user_data = get_user_by('email', trim($_POST['user_login']));
//    } else {
//        $login = trim($_POST['user_login']);
//        $user_data = get_user_by('login', $login);
//    }
//    $message = xt_mail_retrieve_password_message();
//    return str_replace(array("\r\n", "\n", "\r", '{blogname}', '{home}', '{userlogin}', '{url}'), array('<br/>', '<br/>', '<br/>',
//                get_bloginfo('name'),
//                home_url('/'), $user_data->user_login,
//                network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_data->user_login), 'login')), $message);
//}

function xt_filter_got_rewrite($got_rewrite) {
    if (IS_BAE) {
        return true;
    }
    return $got_rewrite;
}

function xt_sanitize_user($username, $raw_username, $strict) {
    if ($strict && $username != $raw_username) {
        $username = $raw_username;
        $username = wp_strip_all_tags($username);
        $username = remove_accents($username);
        // Kill octets
        $username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
        $username = preg_replace('/&.+?;/', '', $username); // Kill entities  
        // If strict, reduce to ASCII for max portability.
        $username = preg_replace('|[^a-z0-9 _.\-@\x80-\xff]|i', '', $username);

        $username = trim($username);
        // Consolidate contiguous whitespace
        $username = preg_replace('|\s+|', ' ', $username);
    }
    return $username;
}

function xt_register_post_type() {
    $taglabels = array(
        'name' => __('Tags'),
        'singular_name' => __('Tag'),
        'search_items' => __('Search Tags'),
        'popular_items' => __('Search Tags'),
        'all_items' => __('All Tags'),
        'edit_item' => __('Edit Tag'),
        'update_item' => __('Update Tag'),
        'add_new_item' => __('Adding Tags'),
        'new_item_name' => __('New Tag Name'),
        'separate_items_with_commas' => __('Separate tags with commas'),
        'add_or_remove_items' => __('Add or remove tags'),
        'choose_from_most_used' => __('Choose from the most used tags'),
    );
    register_taxonomy('daogou_category', 'daogou', array(
        'hierarchical' => true,
        'query_var' => 'daogou_category_name',
        'rewrite' => false,
        'show_admin_column' => true,
    ));
    register_taxonomy('daogou_tag', 'daogou', array(
        'hierarchical' => false,
        'query_var' => 'daogou_tag',
        'rewrite' => false,
        'labels' => $taglabels,
        'show_admin_column' => true,
    ));
    register_taxonomy('help_category', 'help', array(
        'hierarchical' => true,
        'query_var' => 'help_category_name',
        'rewrite' => false,
        'show_admin_column' => true,
    ));
    register_taxonomy('help_tag', 'help', array(
        'hierarchical' => false,
        'query_var' => 'help_tag',
        'rewrite' => false,
        'labels' => $taglabels,
        'show_admin_column' => true,
    ));
    register_post_type('daogou', array(
        'labels' => array(
            'name' => '导购文章',
            'singular_name' => '导购文章',
            'add_new' => '新建导购',
            'all_items' => '所有导购',
            'add_new_item' => '新建导购',
            'edit_item' => '编辑导购',
            'new_item' => '新建导购',
            'view_item' => '浏览该导购页面',
            'search_items' => '搜索导购',
            'not_found' => '未找到符合的导购文章',
            'not_found_in_trash' => '未找到符合的导购文章',
            'menu_name' => '导购文章',
            'name_admin_bar' => '导购文章'
        ),
        'public' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => true,
        'rewrite' => array(
            'slug' => xt_base_daogou(),
            'with_front' => false
        ),
        'query_var' => true,
        'delete_with_user' => true,
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'trackbacks',
            'comments'
        ),
        'taxonomies' => array(
            'daogou_category',
            'daogou_tag'
        )
    ));
    register_post_type('help', array(
        'labels' => array(
            'name' => '帮助',
            'singular_name' => '帮助',
            'add_new' => '新建帮助',
            'all_items' => '所有帮助',
            'add_new_item' => '新建帮助',
            'edit_item' => '编辑帮助',
            'new_item' => '新建帮助',
            'view_item' => '浏览该帮助页面',
            'search_items' => '搜索帮助',
            'not_found' => '未找到符合的帮助文章',
            'not_found_in_trash' => '未找到符合的帮助文章',
            'menu_name' => '帮助文章',
            'name_admin_bar' => '帮助文章'
        ),
        'public' => true,
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'hierarchical' => true,
        'rewrite' => array(
            'slug' => xt_base_help(),
            'with_front' => false
        ),
        'query_var' => true,
        'delete_with_user' => true,
        'supports' => array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'trackbacks',
            'comments'
        ),
        'taxonomies' => array(
            'help_category',
            'help_tag'
        )
    ));
}

function xt_default_custom() {
    xt_register_post_type();
    add_action('add_meta_boxes', 'xt_meta_box');
    add_action('save_post', 'xt_meta_box_save');
    add_image_size('xt-daogou-post-thumbnail', 180, 180, false);
}

add_action('init', 'xt_default_custom');
add_action('init', 'xt_register_taxonomy_for_object_types');

function xt_register_taxonomy_for_object_types() {
    register_taxonomy_for_object_type('daogou_category', 'daogou');
    register_taxonomy_for_object_type('daogou_tag', 'daogou');
    register_taxonomy_for_object_type('help_category', 'help');
    register_taxonomy_for_object_type('help_tag', 'help');
}

function xt_meta_box() {
    add_meta_box('X_Posts', '导购商品', 'xt_meta_box_post', 'daogou', 'normal', 'high', array());
    add_meta_box('X_Helps', '是否常见问题', 'xt_meta_box_help', 'help', 'normal', 'high', array());
}

function xt_meta_box_save($post_id) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if (isset($_POST['xt_meta_box_post']) && wp_verify_nonce($_POST['xt_meta_box_post'], plugin_basename(__FILE__))) {
        if (!current_user_can('edit_post', $post_id))
            return;
        $post_ID = $_POST['post_ID'];
        $items = $_POST['items'];
        update_post_meta($post_ID, 'xt_items', $items);
    }elseif (isset($_POST['xt_meta_box_help']) && wp_verify_nonce($_POST['xt_meta_box_help'], plugin_basename(__FILE__))) {
        $post_ID = $_POST['post_ID'];
        $isHot = isset($_POST['xt_help_hot']) && $_POST['xt_help_hot'] ? 1 : 0;
        update_post_meta($post_ID, 'xt_help_hot', $isHot);
    }
}

function xt_meta_box_post() {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'xt_meta_box_post');
    ?>
    <style type="text/css">
        .xt-items li{padding:0px;margin:0px;display: inline;float: left;margin-right: 17px;width: 152px;position: relative;cursor: default;}
        .xt-items li b{display:none;width:5px;height:5px;}
        .xt-items li div>a{color: #6C6C6C;width: 150px;height: 150px;display: block;overflow: hidden;border: 1px solid #E5E5E5;line-height: 150px;_font-size: 110px;text-align: center;}
        .xt-items li h4{line-height: 18px;text-align: center;font-weight: normal;margin: 4px 0;height: 20px;overflow: hidden;width: 152px;font-size: 100%;}
        .xt-items li p{text-align:center;color:#6C6C6C;font-family:'\5fae\8f6f\96c5\9ed1';}
        .xt-items li.xt-current b,.xt-items li.xt-hover b{display: block;width: 88px;height: 25px;color: white;text-align: center;line-height: 24px;position: absolute;top: 126px;right: 1px;z-index: 2;font-weight: normal;background: black;opacity: 0.5;filter: alpha(opacity=50);cursor: pointer;}
        .xt-items li.xt-current span,.xt-items li.xt-hover span{width: 150px;height: 150px;display: block;position: absolute;border: 3px solid #BAD4AF;top: -2px;left: -2px;z-index: 1;}
        #X_Items_Box_Url_Prompt_Text{color: #BBB;position: absolute;font-size: 1.7em;padding: 8px 10px;cursor: text;vertical-align: middle;margin-top:15px;}
    </style>
    <?php
    $xt_items = get_post_meta($post->ID, 'xt_items', true);
    if (empty($xt_items)) {
        $xt_items = array();
        for ($i = 0; $i < 4; $i++) {
            $xt_items[$i] = array(
                'title' => '',
                'url' => '',
                'pic' => '',
                'price' => '',
                'type' => '',
                'key' => '',
                'guid' => xt_user_guid()
            );
        }
    }
    $xt_count = 0;
    ?>
    <ul id="X_Items" class="xt-items clear">
        <?php foreach ($xt_items as $xt_item): ?>
            <li id="X_Items_<?php echo $xt_count; ?>">
                <b>编辑商品信息</b><div><span></span><a href="javascript:;"><?php echo!empty($xt_item['pic']) ? ('<img src="' . $xt_item['pic'] . '"') : '' ?></a><h4><a href="javascript:;"><?php echo $xt_item['title'] ?></a></h4><p><?php echo $xt_item['price'] ?></p></div>
                <input type="hidden" class="xt-item-title" name="items[<?php echo $xt_count; ?>][title]" value="<?php echo esc_html($xt_item['title']) ?>" />
                <input type="hidden" class="xt-item-url" name="items[<?php echo $xt_count; ?>][url]" value="<?php echo $xt_item['url'] ?>" />
                <input type="hidden" class="xt-item-pic" name="items[<?php echo $xt_count; ?>][pic]" value="<?php echo $xt_item['pic'] ?>" />
                <input type="hidden" class="xt-item-price" name="items[<?php echo $xt_count; ?>][price]" value="<?php echo $xt_item['price'] ?>" />
                <input type="hidden" class="xt-item-type" name="items[<?php echo $xt_count; ?>][type]" value="<?php echo $xt_item['type'] ?>" />
                <input type="hidden" class="xt-item-key" name="items[<?php echo $xt_count; ?>][key]" value="<?php echo $xt_item['key'] ?>" />
                <input type="hidden" class="xt-item-guid" name="items[<?php echo $xt_count; ?>][guid]" value="<?php echo $xt_item['guid'] ?>" />
            </li>
            <?php
            $xt_count++;
        endforeach;
        ?>
        <br class="clear">
    </ul>
    <div id="X_Items_Box" style="display:none;">
        <label class="hide-if-no-js" style="" id="X_Items_Box_Url_Prompt_Text" for="X_Items_Box_Url">在此输入商品链接</label>
        <input type="text" id="X_Items_Box_Url" style="background-color: white;border-color: #CCC;padding: 3px 8px;font-size: 1.7em;line-height: 100%;width: 100%;outline: none;margin-top:15px;" value="" autocomplete="off">
        <p class="submit" style="margin:20px auto;text-align:center;">
            <input type="submit" id="X_Items_Box_Submit" class="button-primary" value="确定">
            <span><img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-feedback"></span>
            <a class="button" id="X_Items_Box_Clear" href="javascript:;" >清空</a>
        </p>
    </div>
    <?php
    global $xt;
    $_global = get_option(XT_OPTION_GLOBAL);
    echo '<script type="text/javascript">var XT = ' . json_encode(array(
        'siteurl' => site_url(),
        'pluginurl' => XT_PLUGIN_URL,
        'ajaxurl' => admin_url('admin-ajax.php'),
        'loginurl' => site_url('wp-login.php'),
        'searchshareurl' => xt_get_shares_search_url(array(
            's' => 'SEARCH'
        )),
        'userId' => get_current_user_id(),
        'token' => wp_create_nonce('token'),
        'option' => $_global,
    )) . ';</script>';
    ?>
    <script type="text/javascript" src="<?php echo XT_CORE_JS_URL . '/xt-post.min.js'; ?>"></script>
    <?php
}

function xt_meta_box_help() {
    global $post;
    wp_nonce_field(plugin_basename(__FILE__), 'xt_meta_box_help');
    $isHot = get_post_meta($post->ID, 'xt_help_hot', true);
    ?>
    <label for = "xt_help_hot" class = "selectit"><input name = "xt_help_hot" type = "checkbox" id = "xt_help_hot" value = "1" <?php echo $isHot ? 'checked = "checked"' : '' ?>> 设置为常见问题</label>
    <?php
}

function xt_cat_switch_themes() {
    return true;
}

function xt_size($layout = 'span12', $size = '') {
    switch ($layout) {
        case 'span12' :
            if ($size == 'big') {
                return 4;
            } elseif ($size == 'small') {
                return 6;
            }
            break;
        case 'span3' :
            return 1;
            break;
        case 'span9' :
            global $xt_current_widget;
            if ($xt_current_widget == 'pagerecommendtaobaos') {
                return 4;
            }
            return 3;
            break;
    }

    return 5;
}

function xt_roles() {
    return array(
        'administrator',
        'editor',
        'author',
        'contributor',
        'subscriber',
        'pending'
    );
}

function xt_platforms() {
    return array(
        'xt',
        'taobao',
        'paipai',
        'yiqifa',
        'weibo',
        'qq'
    );
}

function xt_platforms_desc($platform) {
    $platforms = array(
        'xt' => '站内',
        'taobao' => '淘宝',
        'paipai' => '拍拍',
        'yiqifa' => '亿起发'
    );
    if (isset($platforms[$platform])) {
        return $platforms[$platform];
    }
    return '其他商城';
}