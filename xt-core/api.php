<?php

function xt_is_ready() {
    $app = xt_get_app_xt();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        return false;
    }
    return $app;
}

function xt_ajax_api_version() {
    $app = xt_get_app_xt();
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array(
            'XT_VERSION' => get_option(XT_OPTION_VERSION),
            'XT_DB_VERSION' => get_option(XT_OPTION_VERSION_DB),
            'XT_APPKEY' => $app['appKey'] ? 1 : 0
        )
    );
    if (isset($_REQUEST['jsoncallback'])) {
        exit('XT_VERSION(' . json_encode($result) . ')');
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_api_version', 'xt_ajax_api_version');
add_action('wp_ajax_nopriv_xt_ajax_api_version', 'xt_ajax_api_version');

function xt_ajax_api_app() {
    $app = xt_is_ready();
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (empty($app)) {
        $appKey = intval(trim($_POST['appKey']));
        $appSecret = esc_html(trim($_POST['appSecret']));
        if (!empty($appKey) && !empty($appSecret)) {
            $platform = get_option(XT_OPTION_PLATFORM);
            $app = $platform['xt'];
            $app['appKey'] = $appKey;
            $app['appSecret'] = $appSecret;
            $platform['xt'] = $app;
            update_option(XT_OPTION_PLATFORM, $platform);
            update_option(XT_OPTION_INSTALLED, 1);
//            $api = new XTClient($app['appKey'], $app['appSecret']);
//            $api->execute('updatePages', $_REQUEST);
            exit(json_encode($result));
        }
        $result['code'] = 500;
        $result['msg'] = '未指定appKey';
    } else {
        $result['code'] = 500;
        $result['msg'] = '当前站点已经加入新淘客WordPress平台(站点更换域名请联系客服)';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_api_app', 'xt_ajax_api_app');
add_action('wp_ajax_nopriv_xt_ajax_api_app', 'xt_ajax_api_app');

function xt_ajax_api() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $appKey = $_REQUEST['app_key'];
    $method = $_REQUEST['method'];
    $timestamp = $_REQUEST['timestamp'];
    $sign = $_REQUEST['sign'];
    if (empty($appKey)) {
        $result['code'] = 500;
        $result['msg'] = '未指定appKey';
        exit(json_encode($result));
    }
    if (empty($method)) {
        $result['code'] = 501;
        $result['msg'] = '未指定method';
        exit(json_encode($result));
    }
    if (!in_array($method, array(
                'addCustomPage',
                'getSysPages',
                'getPageHeaderAndFooter',
                'updatePageHeaderAndFooter',
                'getPageHeaders',
                'addPageHeader',
                'updatePageHeader',
                'getPageFooters',
                'addPageFooter',
                'updatePageFooter',
                'getPageHeader',
                'getPageFooter',
                'getCustomPages',
                'getCustomPage',
                'getOption',
                'updateOption',
                'addOption',
                'getCoreOptions',
                'getPageOptions',
                'updateThemes',
                'updatePages',
                'updateApp',
                'updateBase',
                'updateHelps',
                'clearHelps',
                'updateAutoCatalog',
                'autoShare',
                'refresh',
                'importUser',
                'refreshXTUser',
                'getUserName'
            ))) {
        $result['code'] = 502;
        $result['msg'] = '指定的method不存在';
        exit(json_encode($result));
    }
    if (empty($timestamp)) {
        $result['code'] = 503;
        $result['msg'] = '未指定时间参数';
        exit(json_encode($result));
    }
    $app = xt_get_app_xt();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret'])) {
        $result['code'] = 504;
        $result['msg'] = '尚未配置新淘网AppKey,AppSecret';
        exit(json_encode($result));
    }
    if ($app['appKey'] != $appKey) {
        $result['code'] = 505;
        $result['msg'] = 'AppKey不一致';
        exit(json_encode($result));
    }
    $api = new XTClient($app['appKey'], $app['appSecret']);
    $_sign = $api->generateSign($_REQUEST);
    if ($sign != $_sign) {
        $result['code'] = 506;
        $result['msg'] = '验证出错,请确认AppKey,AppSecret配置正确';
        exit(json_encode($result));
    }
    $result['result'] = $api->execute($method, $_REQUEST);
    exit(json_encode($result));
}

add_action('wp_ajax_xt_ajax_api', 'xt_ajax_api');
add_action('wp_ajax_nopriv_xt_ajax_api', 'xt_ajax_api');

class XTClient {

    public $appKey;
    public $appSecret;
    public $gatewayUrl = "http://plugin.xintaonet.com";
    protected $signMethod = "md5";
    protected $apiVersion = "1.0";
    protected $sdkVersion = "xt-sdk-php-20121224";

    function __construct($appKey, $appSecret) {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
    }

    public function generateSign($params) {
        ksort($params);
        $stringToBeSigned = $this->appSecret;
        foreach ($params as $k => $v) {
            if ("@" != substr($v, 0, 1) && $k != 'sign') {
                $v = stripslashes($v); //server need
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        $stringToBeSigned .= $this->appSecret;
        return strtoupper(md5($stringToBeSigned));
    }

    public function execute($method, $apiParams) {
        return $this->$method($apiParams);
    }

    public function addCustomPage($params = array()) {
        if (isset($params['title']) && !empty($params['title']) && isset($params['name']) && !empty($params['name'])) {
            $id = wp_insert_post(array(
                'post_title' => trim($params['title']),
                'post_type' => 'page',
                'post_name' => trim($params['name']),
                'comment_status' => 'closed',
                'ping_status' => 'closed',
                'post_content' => '<h1>新淘客自定义页面,请勿编辑该页面</h1>',
                'post_status' => 'publish',
                'post_author' => 0,
                'menu_order' => 0,
                'page_template' => 'xt-page.php'
                    ), true);
            if (is_wp_error($id)) {
                return array('error' => $id->get_error_message());
            }
            return get_post($id);
        }
        return false;
    }

    public function getSysPages($params = array()) {
        return xt_design_syspages();
    }

    public function getPageHeaderAndFooter($params = array()) {
        if (isset($params['page']) && !empty($params['page'])) {
            $headers = get_option(XT_OPTION_PAGE_HEADERS);
            $footers = get_option(XT_OPTION_PAGE_FOOTERS);
            $pages = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
            if (!empty($pages)) {
                $page = esc_attr($params['page']);
                if (isset($pages[$page])) {
                    $header = $pages[$page]['header'];
                    if (isset($headers[$header])) {
                        $headers[$header]['checked'] = true;
                    }
                    $footer = $pages[$page]['footer'];
                    if (isset($footers[$footer])) {
                        $footers[$footer]['checked'] = true;
                    }
                }
            }
            return array('headers' => $headers, 'footers' => $footers);
        }
        return false;
    }

    public function updatePageHeaderAndFooter($params = array()) {
        if (isset($params['page']) && !empty($params['page']) && isset($params['type']) && !empty($params['type']) && isset($params['header']) && !empty($params['header']) && isset($params['footer']) && !empty($params['footer'])) {
            $page = esc_attr($params['page']);
            $type = esc_attr($params['type']);
            $header = esc_attr($params['header']);
            $footer = esc_attr($params['footer']);
            $headers = get_option(XT_OPTION_PAGE_HEADERS);
            $footers = get_option(XT_OPTION_PAGE_FOOTERS);
            $pages = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
            if (!empty($page) && in_array($type, array('system', 'custom')) && (isset($headers[$header]) || $header == 'header') && (isset($footers[$footer]) || $footer == 'footer')) {
                if (!is_array($pages)) {
                    $pages = array();
                }
                if ($header == 'header' && $footer == 'footer') {
                    unset($pages[$page]);
                } else {
                    $pages[$page] = array('id' => $page, 'type' => $type, 'header' => $header, 'footer' => $footer);
                }
                update_option(XT_OPTION_PAGE_HEADER_FOOTER, $pages);
                return true;
            }
        }
        return false;
    }

    public function getPageHeader($params = array()) {
        if (isset($params['id']) && !empty($params['id'])) {
            $headers = get_option(XT_OPTION_PAGE_HEADERS);
            if (!empty($headers) && isset($headers[$params['id']])) {
                $header = $headers[$params['id']];
                return $header;
            }
        }
        return false;
    }

    public function getPageFooter($params = array()) {
        if (isset($params['id']) && !empty($params['id'])) {
            $footers = get_option(XT_OPTION_PAGE_FOOTERS);
            if (!empty($footers) && isset($footers[$params['id']])) {
                $footer = $footers[$params['id']];
                return $footer;
            }
        }
        return false;
    }

    public function getPageHeaders($params = array()) {
        $syses = xt_design_syspages();
        $headers = get_option(XT_OPTION_PAGE_HEADERS);
        $pages = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
        if (!empty($headers) && !empty($pages)) {
            $customIds = array();
            foreach ($pages as $page) {
                if ($page['type'] == 'system') {//system
                    $page['url'] = $syses[$page['id']]['preview'];
                    $page['title'] = $syses[$page['id']]['title'];
                } else {//custom
                    $customIds[] = $page['id'];
                }
                if (isset($headers[$page['header']])) {
                    $header = $headers[$page['header']];
                    if (isset($header['pages'])) {
                        $header['pages'][$page['id']] = $page;
                    } else {
                        $header['pages'] = array($page['id'] => $page);
                    }
                    $headers[$page['header']] = $header;
                }
            }
            if (!empty($customIds)) {
                $customs = array();
                query_posts(array('post_type' => 'page', 'post__in' => $customIds));
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        $customs[get_the_ID()] = array(
                            'url' => get_permalink(),
                            'title' => get_the_title()
                        );
                    }
                }
                if (!empty($customs)) {
                    foreach ($headers as $header) {
                        if (!empty($header['pages'])) {
                            foreach ($header['pages'] as $_page) {
                                if (isset($customs[$_page['id']])) {
                                    $custom = $customs[$_page['id']];
                                    $header['pages'][$_page['id']]['url'] = $custom['url'];
                                    $header['pages'][$_page['id']]['title'] = $custom['title'];
                                    $headers[$header['id']] = $header;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $headers;
    }

    public function addPageHeader($params = array()) {
        if (isset($params['title']) && !empty($params['title'])) {
            $title = esc_attr($params['title']);
            if (!empty($title)) {
                $headers = get_option(XT_OPTION_PAGE_HEADERS);
                if (!empty($headers)) {
                    foreach ($headers as $header) {
                        if ($header['title'] == $title) {
                            return array('error' => '页头名称重复');
                        }
                    }
                    $id = 'header' . (count($headers) + 1);
                    $headers[$id] = array('id' => $id, 'title' => $title);
                    update_option(XT_OPTION_PAGE_HEADERS, $headers);
                } else {
                    $headers['header1'] = array('id' => 'header1', 'title' => $title);
                    add_option(XT_OPTION_PAGE_HEADERS, $headers, '', 'no');
                }


                return true;
            }
        }
        return false;
    }

    public function addPageFooter($params = array()) {
        if (isset($params['title']) && !empty($params['title'])) {
            $title = esc_attr($params['title']);
            if (!empty($title)) {
                $footers = get_option(XT_OPTION_PAGE_FOOTERS);
                if (!empty($footers)) {
                    foreach ($footers as $footer) {
                        if ($footer['title'] == $title) {
                            return array('error' => '页尾名称重复');
                        }
                    }
                    $id = 'footer' . (count($footers) + 1);
                    $footers[$id] = array('id' => $id, 'title' => $title);
                    update_option(XT_OPTION_PAGE_FOOTERS, $footers);
                } else {
                    $footers['footer1'] = array('id' => 'footer1', 'title' => $title);
                    add_option(XT_OPTION_PAGE_FOOTERS, $footers, '', 'no');
                }
            }
        }
        return false;
    }

    public function updatePageHeader($params = array()) {
        if (isset($params['id']) && !empty($params['id']) && isset($params['title']) && !empty($params['title'])) {
            $id = esc_attr($params['id']);
            $title = esc_attr($params['title']);
            if (!empty($id) && !empty($title)) {
                $headers = get_option(XT_OPTION_PAGE_HEADERS);
                if (!empty($headers)) {
                    if (!isset($headers[$id])) {
                        return array('error' => '指定的页头不存在');
                    }
                    foreach ($headers as $header) {
                        if ($header['title'] == $title) {
                            return array('error' => '页头名称重复');
                        }
                    }
                    $headers[$id] = array('id' => $id, 'title' => $title);
                    update_option(XT_OPTION_PAGE_HEADERS, $headers);
                }
            }
        }
        return false;
    }

    public function updatePageFooter($params = array()) {
        if (isset($params['id']) && !empty($params['id']) && isset($params['title']) && !empty($params['title'])) {
            $id = esc_attr($params['id']);
            $title = esc_attr($params['title']);
            if (!empty($id) && !empty($title)) {
                $footers = get_option(XT_OPTION_PAGE_FOOTERS);
                if (!empty($footers)) {
                    if (!isset($footers[$id])) {
                        return array('error' => '指定的页尾不存在');
                    }
                    foreach ($footers as $footer) {
                        if ($footer['title'] == $title) {
                            return array('error' => '页尾名称重复');
                        }
                    }
                    $footers[$id] = array('id' => $id, 'title' => $title);
                    update_option(XT_OPTION_PAGE_FOOTERS, $footers);
                }
            }
        }
        return false;
    }

    public function getPageFooters($params = array()) {
        $syses = xt_design_syspages();
        $footers = get_option(XT_OPTION_PAGE_FOOTERS);
        $pages = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
        if (!empty($footers) && !empty($pages)) {
            $customIds = array();
            foreach ($pages as $page) {
                if ($page['type'] == 'system') {//system
                    $page['url'] = $syses[$page['id']]['preview'];
                    $page['title'] = $syses[$page['id']]['title'];
                } else {//custom
                    $customIds[] = $page['id'];
                }
                if (isset($footers[$page['footer']])) {
                    $footer = $footers[$page['footer']];
                    if (isset($footer['pages'])) {
                        $footer['pages'][$page['id']] = $page;
                    } else {
                        $footer['pages'] = array($page['id'] => $page);
                    }
                    $footers[$page['footer']] = $footer;
                }
            }
            if (!empty($customIds)) {
                $customs = array();
                query_posts(array('post_type' => 'page', 'post__in' => $customIds));
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        $customs[get_the_ID()] = array(
                            'url' => get_permalink(),
                            'title' => get_the_title()
                        );
                    }
                }
                if (!empty($customs)) {
                    foreach ($footers as $footer) {
                        if (!empty($footer['pages'])) {
                            foreach ($footer['pages'] as $_page) {
                                if (isset($customs[$_page['id']])) {
                                    $custom = $customs[$_page['id']];
                                    $footer['pages'][$_page['id']]['url'] = $custom['url'];
                                    $footer['pages'][$_page['id']]['title'] = $custom['title'];
                                    $footers[$footer['id']] = $footer;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $footers;
    }

    public function getCustomPages($params = array()) {
        $paged = isset($params['paged']) ? intval($params['paged']) : 1;
        (query_posts(array(
                    'post_type' => 'page',
                    'meta_key' => '_wp_page_template',
                    'meta_value' => 'xt-page.php',
                    'paged' => $paged,
                    'posts_per_page' => 20
                )));
        $pages = array();
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                $page = array();
                $page['id'] = get_the_ID();
                $page['title'] = get_the_title();
                $page['preview'] = get_permalink();
                $pages[] = $page;
            }
        }
        return $pages;
    }

    public function getCustomPage($params = array()) {
        if (isset($params['id']) && intval($params['id']) > 0) {
            $post = get_post(intval($params['id']));
            if (!empty($post) && $post->post_type == 'page') {
                $page_template = get_post_meta($post->ID, '_wp_page_template', true);
                if ($page_template == 'xt-page.php') {
                    $page = array();
                    $page['id'] = $post->ID;
                    $page['title'] = $post->post_title;
                    $page['permalink'] = get_permalink($post->ID);
                    return $page;
                }
            }
        }

        return array();
    }

    public function getCoreOptions($params = array()) {
        global $wpdb;
        $core_options = array(
            'siteurl',
            'blogname',
            'blogdescription',
            'users_can_register',
            'mailserver_url',
            'mailserver_login',
            'mailserver_pass',
            'mailserver_port',
            'permalink_structure',
            'category_base',
            'tag_base',
            'blog_charset',
            'active_plugins',
            'home',
            'template',
            'stylesheet',
            'default_role',
            'show_on_front',
            'show_avatars',
            'page_for_posts',
            'page_on_front',
            'wp_user_roles',
            'cron',
            'current_theme',
            'rewrite_rules',
            XT_OPTION_VERSION,
            XT_OPTION_VERSION_DB,
            XT_OPTION_MENUS,
            XT_OPTION_GLOBAL,
            XT_OPTION_FANXIAN,
            XT_OPTION_ROLE,
            XT_OPTION_PAGES,
            XT_OPTION_PLATFORM,
            XT_OPTION_CATALOG_SHARE,
            XT_OPTION_CATALOG_ALBUM,
            XT_OPTION_CATALOG_POST,
            XT_OPTION_CATALOG_DAOGOU,
            XT_OPTION_CATALOG_HELP,
            XT_OPTION_TAOBAO_ITEMCAT,
            XT_OPTION_PAIPAI_ITEMCAT,
            XT_OPTION_YIQIFA_WEBSITE_CATEGORY,
            XT_OPTION_YIQIFA_TUAN_WEBSITE,
            XT_OPTION_THEME_SETTING,
            XT_OPTION_THEME_CUSTOM,
            XT_OPTION_THEME,
            XT_OPTION_ENV
        );
        $core_options_in = "'" . implode("', '", $core_options) . "'";
        $options = $wpdb->get_results("SELECT option_name, option_value,autoload FROM $wpdb->options WHERE option_name IN ($core_options_in)");
        return $options;
    }

    public function updateBase($params = array()) {
        if (isset($params['base']) && !empty($params['base']) && isset($params['daogou']) && !empty($params['daogou']) && isset($params['help']) && !empty($params['help'])) {
            $global = get_option(XT_OPTION_GLOBAL);
            $oldBase = isset($global['base']) && !empty($global['base']) ? $global['base'] : 'share';
            $oldDaogou = isset($global['daogou']) && !empty($global['daogou']) ? $global['daogou'] : 'daogou';
            $oldHelp = isset($global['help']) && !empty($global['help']) ? $global['help'] : 'help';
            $newBase = trim($params['base']);
            $newDaogou = trim($params['daogou']);
            $newHelp = trim($params['help']);

            //replace option_value(widgets and html)
            xt_replace_base($global, $newBase, $newDaogou, $newHelp);

            $global['daogou'] = $newDaogou;
            $global['help'] = $newHelp;

            if (!isset($global['base']) || empty($global['base'])) {
                $global['base'] = $newBase;
            } elseif ($global['base'] != $newBase) {
                global $wpdb;
                $home_id = $wpdb->get_var("SELECT id FROM `" . $wpdb->posts . "` WHERE `post_name` = '" . $wpdb->escape($global['base']) . "'	AND `post_type` != 'revision'");
                if (!empty($home_id)) {
                    $newpost = array();
                    $newpost['ID'] = $home_id;
                    $newpost['post_name'] = $newBase;
                    wp_update_post($newpost);
                }
                $global['base'] = $newBase;
            }
            global $wpdb, $wp_rewrite;
            $index = '';
            if ($wp_rewrite->using_index_permalinks()) {
                $global['index'] = '/' . $wp_rewrite->index;
                $index = $wp_rewrite->index . '/';
            } else {
                $global['index'] = '';
            }
            update_option(XT_OPTION_GLOBAL, $global);
            xt_register_post_type();
            xt_register_taxonomy_for_object_types();
            $wp_rewrite->flush_rules(); //flush
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
                                $newUrl = str_replace(array(home_url($index . $oldDaogou), home_url($index . $oldBase), home_url($index . $oldHelp)), array(home_url($index . $newDaogou), home_url($index . $newBase), home_url($index . $newHelp)), $url);
                                update_post_meta($post->ID, '_menu_item_url', $newUrl);
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function getPageOptions($params = array()) {
        global $wpdb;
        $options = $wpdb->get_results("SELECT option_name, option_value,autoload FROM $wpdb->options WHERE option_name LIKE '" . XT_OPTION_PAGE_PRE . "%' AND option_name NOT LIKE '" . XT_OPTION_PAGE_HTML_PRE . "%'");
        return $options;
    }

    public function getOption($params = array()) {
        if (isset($params['keys']) && !empty($params['keys'])) {
            $keys = explode(',', $params['keys']);
            $result = array();
            foreach ($keys as $key) {
                $result[$key] = get_option($key);
            }
            return $result;
        }
        return array();
    }

    public function addOption($params = array()) {
        if (isset($params['key']) && !empty($params['key']) && isset($params['value'])) {
            $key = $params['key'];
            $value = $params['value'];
            $deprecated = '';
            $autoload = 'yes';
            if (isset($params['deprecated']) && !empty($params['deprecated'])) {
                $deprecated = $params['deprecated'];
            }
            if (isset($params['autoload']) && !empty($params['autoload'])) {
                $autoload = $params['autoload'] == 'no' ? 'no' : 'yes';
            }
            $value = maybe_unserialize(stripslashes($value));
            add_option($key, $value, $deprecated, $autoload);
            return array(
                $params['key'] => $value
            );
        }
        return array();
    }

    public function updateOption($params = array()) {
        if (isset($params['key']) && !empty($params['key']) && isset($params['value'])) {
            $key = $params['key'];
            $value = $params['value'];
            $value = maybe_unserialize(stripslashes($value));
            update_option($key, $value);
            return array(
                $params['key'] => $value
            );
        }
        return array();
    }

    public function updateApp($params = array()) {
        if (!empty($params)) {
            if (isset($params['appKey']) && !empty($params['appKey']) && isset($params['appSecret']) && !empty($params['appSecret'])) {
                $platform = get_option(XT_OPTION_PLATFORM);
                $platform['xt']['appKey'] = $params['appKey'];
                $platform['xt']['appSecret'] = $params['appSecret'];
                update_option(XT_OPTION_PLATFORM, $platform);
                return true;
            }
        }
        return false;
    }

    public function updateThemes($params = array()) {
        if (!empty($params)) {
            if (isset($params[XT_OPTION_THEME_SETTING]) && !empty($params[XT_OPTION_THEME_SETTING]) && isset($params[XT_OPTION_THEME]) && !empty($params[XT_OPTION_THEME])) {
                $setting = maybe_unserialize(stripslashes($params[XT_OPTION_THEME_SETTING]));
                if (is_array($setting)) {
                    update_option(XT_OPTION_THEME_SETTING, $setting);
                    update_option(XT_OPTION_THEME, stripslashes($params[XT_OPTION_THEME]));
                    update_option(XT_OPTION_THEME_CUSTOM, stripslashes($params[XT_OPTION_THEME_CUSTOM]));
                    return true;
                }
            }
        }
        return false;
    }

    public function updateHelps($params = array()) {
        $global = get_option(XT_OPTION_GLOBAL);
        if (!empty($params) && isset($params['helps']) && !empty($params['helps'])) {
            $categorys = array();
            if (!(isset($global['isHelp']) && $global['isHelp'])) {
                if (isset($params['cats']) && !empty($params['cats'])) {
                    $cats = maybe_unserialize(stripslashes($params['cats']));
                    if (is_array($cats) && !empty($cats)) {
                        foreach ($cats as $cat) {
                            $cid = wp_insert_category(array('taxonomy' => 'help_category', 'cat_name' => $cat['title'], 'category_nicename' => $cat['name']), true);
                            if (!is_wp_error($cid)) {
                                $categorys[$cat['name']] = $cid;
                            } else {
                                $term = get_term_by('slug', $cat['name'], 'help_category');
                                if ($term) {
                                    $categorys[$cat['name']] = (int) $term->term_id;
                                }
                            }
                        }
                        xt_action_refresh_help_category();
                    }
                }
            }
            $helps = maybe_unserialize(stripslashes($params['helps']));
            if (is_array($helps) && !empty($helps)) {
                global $wpdb;
                foreach ($helps as $help) {
                    if (isset($help['name']) && !empty($help['name']) && isset($help['title']) && !empty($help['title']) && isset($help['content']) && !empty($help['content'])) {
                        $name = $help['name'];
                        $title = $help['title'];
                        $content = $help['content'];
                        $isHot = isset($help['isHot']) && $help['isHot'];
                        $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key='xt_help_name' AND meta_value='" . $wpdb->escape($help['name']) . "'");
                        if ($count == 0) {
                            $post = array(
                                'post_title' => trim($title),
                                'post_type' => 'help',
                                'post_name' => trim($name),
                                'post_content' => $content,
                                'post_status' => 'publish',
                                'post_author' => 0,
                                'menu_order' => 0,
                            );
                            $id = wp_insert_post($post, true);
                            if (!is_wp_error($id)) {
                                wp_set_object_terms($id, $categorys[$help['cat']], 'help_category');
                                update_post_meta($id, 'xt_help_name', $help['name']);
                                if ($isHot) {
                                    update_post_meta($id, 'xt_help_hot', 1);
                                } else {
                                    update_post_meta($id, 'xt_help_hot', 0);
                                }
                            }
                        }
                    }
                }
                $global['isHelp'] = 1;
                update_option(XT_OPTION_GLOBAL, $global);
            }
        }
        return true;
    }

    public function clearHelps($params = array()) {
        query_posts(array(
            'post_type' => 'help',
            'meta_key' => 'xt_help_name',
            'meta_compare' => '!=',
            'meta_value' => '',
            'nopaging' => 1
        ));
        if (have_posts()) {
            while (have_posts()) {
                the_post();
                wp_delete_post(get_the_ID());
            }
        }
        $global = get_option(XT_OPTION_GLOBAL);
        $global['isHelp'] = 0;
        update_option(XT_OPTION_GLOBAL, $global);
        return true;
    }

    public function updatePages($params = array()) {
        if (!empty($params)) {
            $clears = array();
            foreach ($params as $key => $value) {
                if (strncmp($key, XT_OPTION_PAGE_WIDGETS_PRE, strlen(XT_OPTION_PAGE_WIDGETS_PRE)) === 0) {
                    $widgets = maybe_unserialize(stripslashes($value));
                    if (is_array($widgets)) {
                        if (in_array($key, array(
                                    XT_OPTION_PAGE_WIDGETS_PRE . 'header',
                                    XT_OPTION_PAGE_WIDGETS_PRE . 'footer'
                                ))) {
                            update_option($key, $widgets);
                        } else {
                            if (!add_option($key, $widgets, '', 'no')) {
                                update_option($key, $widgets);
                            }
                            $page = str_replace(XT_OPTION_PAGE_WIDGETS_PRE, '', $key);
                            if ($page == 'home' || is_numeric($page)) {
                                $clears[$page] = true;
                            }
                        }
                    }
                } elseif (strncmp($key, XT_OPTION_PAGE_PRE, strlen(XT_OPTION_PAGE_PRE)) === 0) {
                    $layouts = maybe_unserialize(stripslashes($value));
                    if (is_array($layouts)) {
                        $layouts = xt_layout_convert($layouts);
                        if (in_array($key, array(
                                    XT_OPTION_PAGE_PRE . 'header',
                                    XT_OPTION_PAGE_PRE . 'footer'
                                ))) {
                            update_option($key, $layouts);
                        } else {
                            if (!add_option($key, $layouts, '', 'no')) {
                                update_option($key, $layouts);
                            }
                            $page = str_replace(XT_OPTION_PAGE_PRE, '', $key);
                            if ($page == 'home' || is_numeric($page)) {
                                $clears[$page] = true;
                            }
                        }
                    }
                }
            }
            if (!empty($clears)) {
                foreach ($clears as $page => $flag) {
                    if ($flag) {
                        do_action('xt_page_updated', $page);
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function updateAutoCatalog($params = array()) {
        if (isset($params['type']) && !empty($params['type']) && isset($params['cid']) && !empty($params['cid']) && isset($params['cids'])) {
            $type = $params['type'];
            $cid = intval($params['cid']);
            $cids = $params['cids'];
            $catalog = xt_get_catalog($cid);
            if (!empty($catalog) && in_array($type, array('taobao', 'paipai'))) {
                $cids = array_map('intval', explode(',', $cids));
                global $wpdb;
                $olds = $wpdb->get_col('SELECT cid FROM ' . XT_TABLE_CATALOG_ITEMCAT . ' WHERE id=' . $cid . ' AND type=\'' . $type . '\'');
                $deleteCids = array_diff($olds, $cids);
                $addCids = array_diff($cids, $olds);
                if (!empty($deleteCids)) {
                    $wpdb->query('DELETE FROM ' . XT_TABLE_CATALOG_ITEMCAT . ' WHERE id=' . $cid . ' AND cid in (' . implode(',', $deleteCids) . ')');
                }
                if (!empty($addCids)) {
                    foreach ($addCids as $_cid) {
                        if ($_cid > 0)
                            $wpdb->insert(XT_TABLE_CATALOG_ITEMCAT, array('id' => $cid, 'cid' => $_cid, 'parent_id' => $catalog->parent, 'type' => $type));
                    }
                }
            }
            return false;
        }
    }

    public function autoShare($params = array()) {
        global $wpdb;
        $count = 0;
        if (!empty($params) && isset($params['albums']) && !empty($params['albums'])) {
            $albums = json_decode(stripslashes($params['albums']), true);
            if (!empty($albums) && is_array($albums)) {
                foreach ($albums as $album_id => $value) {
                    if (!empty($value) && is_array($value) && isset($value['cron']) && !empty($value['cron']) && isset($value['shares']) && !empty($value['shares'])) {
                        $album = xt_get_album($album_id);
                        if (!empty($album)) {
                            $cron = $value['cron'];
                            $shares = $value['shares'];
                            $user_id = $album->user_id;
                            $user_name = $album->user_name;
                            if (is_array($shares)) {
                                $values = array();
                                foreach ($shares as $share) {
                                    $share_key = $wpdb->escape($share['share_key']);
                                    $cache_data = $wpdb->escape($share['cache_data']);
                                    $values[] = "('{$share_key}','{$album_id}','{$user_id}','{$user_name}','{$cron}','{$cache_data}')";
                                }
                                if (!empty($values)) {
                                    $sql = "INSERT IGNORE INTO `" . XT_TABLE_SHARE_CRON . "`(`share_key`,`album_id`,`user_id`,`user_name`,`create_date`,`cache_data`) VALUES " . implode(',', $values);
                                    $count+=$wpdb->query($sql);
                                }
                            }
                        }
                    }
                }
            }
        } elseif (!empty($params) && isset($params['crons']) && !empty($params['crons'])) {
            $crons = json_decode(stripcslashes($params['crons']), true);
            $cron = $crons['cron'];
            $shares = $crons['shares'];
            $users = array();
            if (!empty($shares) && is_array($shares)) {
                $values = array();
                $admins = (get_users(array('role' => 'administrator', 'number' => 1)));
                if (!empty($admins)) {
                    $_user_name = $admins[0]->user_login;
                    if (!empty($admins[0]->display_name)) {
                        $_user_name = $admins[0]->display_name;
                    }
                    $users[0] = $_user_name;
                }
                foreach ($shares as $share) {
                    $share_key = $wpdb->escape($share['share_key']);
                    $cache_data = $wpdb->escape($share['cache_data']);
                    $user_id = absint($wpdb->escape($share['user_id']));
                    if ($user_id > 0 && !isset($users[$user_id])) {
                        $_user = new WP_User($user_id);
                        if ($_user->exists()) {
                            $_user_name = $_user->user_login;
                            if (!empty($_user->display_name)) {
                                $_user_name = $_user->display_name;
                            }
                            $users[$user_id] = $_user_name;
                        }
                    }
                    if (isset($users[$user_id]) && !empty($users[$user_id])) {
                        $user_name = $users[$user_id];
                        $values[] = "('{$share_key}','0','{$user_id}','{$user_name}','{$cron}','{$cache_data}')";
                    }
                }
                if (!empty($values)) {
                    $sql = "INSERT IGNORE INTO `" . XT_TABLE_SHARE_CRON . "`(`share_key`,`album_id`,`user_id`,`user_name`,`create_date`,`cache_data`) VALUES " . implode(',', $values);
                    $count+=$wpdb->query($sql);
                }
            }
        }
        if ($count > 0) {
            if (isset($params['now']) && !empty($params['now'])) {
                xt_cron_autoshare();
            }
            return $count;
        }
        return true;
    }

    public function refresh($params = array()) {
        xt_catalogs_share(true); //catalogs and tags
        return true;
    }

    public function refreshXTUser($params = array()) {
        if (!IS_CLOUD) {
            set_time_limit(0);
        }
        global $wpdb;
        $metas = $wpdb->get_results("SELECT * FROM $wpdb->usermeta WHERE meta_key='" . XT_USER_OLD_PARENTID . "'");
        if (!empty($metas)) {
            foreach ($metas as $meta) {
                $user_id = $meta->user_id;
                $parent_oldid = $meta->meta_value;
                if (!empty($parent_oldid)) {
                    $parentUsers = get_users(array(
                        'meta_key' => XT_USER_OLD_ID,
                        'meta_value' => $parent_oldid
                            ));
                    if (!empty($parentUsers)) {
                        $parentUser = $parentUsers[0];
                        if ($parentUser->exists()) {
                            update_user_meta($user_id, XT_USER_PARENT, array(
                                'id' => (string) $parentUser->ID,
                                'name' => $parentUser->user_login
                            ));
                        }
                    }
                }
            }
        }
    }

    public function importUser($params = array()) {
        if (!empty($params) && isset($params['users']) && !empty($params['users'])) {
            $users = json_decode(stripslashes($params['users']), true);
            if (!empty($users) && is_array($users)) {
                global $xt_during_user_creation;
                $xt_during_user_creation = true; //Multiple emails
                $success = array();
                foreach ($users as $user) {
                    $xt_id = $user['id'];
                    $_oldUsers = get_users(array(
                        'meta_key' => XT_USER_OLD_ID,
                        'meta_value' => $xt_id
                            ));
                    if (empty($_oldUsers)) {
                        $user_login = $user['name'];
                        $user_login = sanitize_user($user_login, true);
                        $user_login = apply_filters('pre_user_login', $user_login);
                        $user_login = trim($user_login);

                        if (username_exists($user_login)) {
                            $user_login = $user_login . '_' . rand(1, 10000);
                        }
                        $userdata = array(
                            'user_login' => $user_login,
                            'user_pass' => $user['password'],
                            'user_nicename' => $user['name'],
                            'nickname' => $user['name'],
                            'first_name' => $user['name'],
                            'display_name' => $user['name'],
                            'user_email' => $user['email']
                        );
                        $user_id = wp_insert_user($userdata);
                        if (is_numeric($user_id)) {
                            $xt_parentid = $user['parentid'];
                            $xt_rate = $user['rate'];
                            $xt_adrate = $user['ads'];
                            $xt_qq = $user['qq'];
                            $xt_mobile = $user['mobile'];
                            $xt_alipay = $user['alipay'];

                            update_user_option($user_id, 'default_password_nag', true, true);
                            update_user_meta($user_id, XT_USER_OLD_ID, $xt_id);
                            if (!empty($xt_parentid)) {
                                update_user_meta($user_id, XT_USER_OLD_PARENTID, $xt_parentid);
                            }
                            update_user_meta($user_id, XT_USER_FANXIAN_RATE, array(
                                'rate' => $xt_rate,
                                'ads' => $xt_adrate,
                                'share' => ''
                            ));
                            if (!empty($xt_qq)) {
                                update_user_meta($user_id, XT_USER_QQ, $xt_qq);
                            }
                            if (!empty($xt_mobile)) {
                                update_user_meta($user_id, XT_USER_MOBILE, $xt_mobile);
                            }
                            if (!empty($xt_alipay)) {
                                update_user_meta($user_id, XT_USER_ALIPAY, $xt_alipay);
                            }
                            $success[] = $xt_id;
                        }
                    } else {
                        $success[] = $xt_id;
                    }
                }
                $xt_during_user_creation = false;
                return $success;
            }
        }
        return array();
    }

    public function getUserName($params = array()) {
        if (!empty($params) && isset($params['id']) && !empty($params['id']) && absint($params['id']) > 0) {
            $user = new WP_User(absint($params['id']));
            if ($user->exists()) {
                $user_name = $user->user_login;
                if (!empty($user->display_name)) {
                    $user_name = $user->display_name;
                }
                return array(
                    'id' => $user->ID,
                    'user_name' => $user_name,
                );
            }
        }
        return array();
    }

}