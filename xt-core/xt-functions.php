<?php

function xt_design_syspages() {
    global $wpdb;
    $SHARE_ID = $wpdb->get_var('SELECT id FROM ' . XT_TABLE_SHARE . ' ORDER BY id DESC LIMIT 1');
    $USER_ID = $wpdb->get_var("SELECT id FROM $wpdb->users ORDER BY id DESC LIMIT 1");
    $ALBUM_ID = $wpdb->get_var('SELECT id FROM ' . XT_TABLE_ALBUM . ' ORDER BY id DESC LIMIT 1');
    $DAOGOU_ID = $wpdb->get_var("SELECT id FROM $wpdb->posts WHERE post_status='publish' AND post_type='daogou' ORDER BY id DESC LIMIT 1");
    $HELP_ID = $wpdb->get_var("SELECT id FROM $wpdb->posts WHERE post_status='publish' AND post_type='help' ORDER BY id DESC LIMIT 1");
    $_sys_pages = xt_design_pages();
    if (!empty($SHARE_ID)) {
        $_sys_pages['share']['preview'] = str_replace('SHAREID', $SHARE_ID, $_sys_pages['share']['preview']);
    } else {
        $_sys_pages['share']['preview'] = 'null';
    }
    if (!empty($ALBUM_ID)) {
        $_sys_pages['album']['preview'] = str_replace('ALBUMID', $ALBUM_ID, $_sys_pages['album']['preview']);
    } else {
        $_sys_pages['album']['preview'] = 'null';
    }
    if (!empty($DAOGOU_ID)) {
        $_sys_pages['daogou']['preview'] = get_permalink($DAOGOU_ID);
    } else {
        $_sys_pages['daogou']['preview'] = 'null';
    }
    if (!empty($HELP_ID)) {
        $_sys_pages['help']['preview'] = get_permalink($HELP_ID);
    } else {
        $_sys_pages['help']['preview'] = 'null';
    }
    if (!empty($USER_ID)) {
        $_sys_pages['user']['preview'] = str_replace('USERID', $USER_ID, $_sys_pages['user']['preview']);
        $_sys_pages['invite']['preview'] = str_replace('USERID', $USER_ID, $_sys_pages['invite']['preview']);
    } else {
        $_sys_pages['user']['preview'] = 'null';
        $_sys_pages['invite']['preview'] = 'null';
    }
    return $_sys_pages;
}

function xt_filter_comments_template($file) {
    if (endsWith($file, 'xt-help_comments.php')) {
        return XT_PLUGIN_DIR . '/xt-themes/xt-help_comments.php';
    } elseif (endsWith($file, 'xt-daogou_comments.php')) {
        return XT_PLUGIN_DIR . '/xt-themes/xt-daogou_comments.php';
    }
    return $file;
}

function xt_filter_ids($ids) {
    if (!empty($ids)) {
        $ids = (str_replace(array(
                    '，',
                    ' '
                        ), array(
                    ',',
                    ','
                        ), $ids));
    }
    return $ids;
}

function xt_get_theme() {
    return get_option(XT_OPTION_THEME);
}

function xt_get_theme_custom() {
    return get_option(XT_OPTION_THEME_CUSTOM);
}

function xt_get_theme_setting() {
    $_theme = get_option(XT_OPTION_THEME_SETTING);
    if (empty($_theme) || !is_array($_theme)) {
        $_theme = array();
    }
    return array_merge(array(
                'linkColor' => '#eb1176',
                'navbarBackgroundHighlight' => '#eb1176',
                'navbarLinkColor' => '#ffffff',
                'grayScale' => 0,
                'grayScaleHome' => 1
                    ), $_theme);
}

function xt_action_admin_init() {
    if (!defined('DOING_AJAX') && !current_user_can('edit_posts')) {
        $user = wp_get_current_user();
        if ($user->exists()) {
            wp_redirect(xt_get_the_user_url($user->ID));
            exit();
        }
    }
}

function xt_write_pic($pic, $title, $width = 0, $height = 0, $classname = '', $errorPic = '', $echo = true) {
    $html = '<script type="text/javascript">XT_writePic(\'' . $pic . '\',\'' . esc_html($title) . '\',\'' . $width . '\',\'' . $height . '\',\'' . $classname . '\',\'' . $errorPic . '\');</script>';
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function xt_refresh_url($url) {
    $rs = parse_url($url);
    $host = isset($rs['host']) ? $rs['host'] : "none";
    switch ($host) {
        case 's.click.taobao.com' :
            $url = add_query_arg(array(
                'unid' => xt_outercode()
                    ), $url);
            break;
        case 'www.taobao.com' :
            $url = add_query_arg(array(
                'unid' => xt_outercode()
                    ), $url);
            break;
        case 'te.paipai.com' :
            $url = add_query_arg(array(
                'outinfo' => xt_outercode()
                    ), $url);
            break;
        case 'p.yiqifa.com' :
        case 'g.yiqifa.com' :
            $url = urldecode(add_query_arg(array(
                        'e' => xt_outercode()
                            ), $url));
            break;
    }
    return $url;
}

function xt_outercode() {
    global $xt_share_guid;
    $user = wp_get_current_user();
    $outercode = '';
    if (!empty($xt_share_guid) && strlen($xt_share_guid) == 4) { //share and buy
        if (xt_is_fanxian()) {
            $outercode = XT_FANXIAN_PRE . $xt_share_guid . '8';
            if ($user->exists()) {
                $_guid = xt_user_guid();
                $outercode .= $_guid;
            }
            return $outercode;
        } else {
            return XT_NOFANXIAN_PRE . $user->ID;
        }
    } else { // buy
        if ($user->exists()) {
            if (xt_is_fanxian()) {
                return XT_FANXIAN_PRE . $user->ID;
            } else {
                return XT_NOFANXIAN_PRE . $user->ID;
            }
        }
    }

    return 'xintaoke';
}

function xt_taobao_spm() {
    
}

function xt_iconv($source, $in, $out) {
    $in = strtoupper($in);
    $out = strtoupper($out);
    if ($in == "UTF8") {
        $in = "UTF-8";
    }
    if ($out == "UTF8") {
        $out = "UTF-8";
    }
    if ($in == $out) {
        return $source;
    }

    if (function_exists('mb_convert_encoding')) {
        return mb_convert_encoding($source, $out, $in);
    } elseif (function_exists('iconv')) {
        return iconv($in, $out . "//IGNORE", $source);
    }
    return $source;
}

function xt_html($text) {
    return htmlentities(stripslashes($text), ENT_QUOTES, 'UTF-8');
}

function xt_pic_site($picurl) {
    if (strpos($picurl, 'taobaocdn.com')) { //淘宝
        return 'taobao';
    } elseif (strpos($picurl, 'paipaiimg.com')) {
        return 'paipai';
    } elseif (strpos($picurl, '360buyimg.com')) {
        return '360buy';
    } elseif (strpos($picurl, 'ddimg.cn')) {
        return 'dangdang';
    } elseif (strpos($picurl, 'vanclimg.com')) {
        return 'vancl';
    }
    return '';
}

function xt_filter_user_pic($picurl, $size = 50) {
    if (!empty($picurl)) {
        if ($size == 180) {
            if (strpos($picurl, 'sinaimg.cn')) {
                return str_replace('/50/', '/180/', $picurl);
            } elseif (strpos($picurl, 'tbcdn.cn')) {
                return str_replace('avatar-120', 'avatar-160', $picurl);
            } elseif (strpos($picurl, 'qlogo.cn')) {
                return str_replace('30LOGO', '100', $picurl . 'LOGO');
            } elseif (strpos($picurl, 'taobaocdn.com')) {
                return $picurl . '_160x160.jpg';
            }
        }
    }
    return $picurl;
}

function xt_pic_url($picurl, $size, $site = '') {
    $_picurl = '';
    if (!empty($picurl)) {
        if (empty($site)) {
            $site = xt_pic_site($picurl);
        }
        switch ($site) {
            case 'taobao' :
                if ($size > 310) {
                    $_picurl = $picurl . '_450x10000.jpg';
                } elseif ($size >= 300) {
                    $_picurl = $picurl . '_310x310.jpg';
                } elseif ($size >= 200) {
                    $_picurl = $picurl . '_220x10000.jpg';
                } elseif ($size >= 160) {
                    $_picurl = $picurl . '_160x160.jpg';
                } elseif ($size >= 80) {
                    $_picurl = $picurl . '_80x1000.jpg';
                }
                break;
            case 'paipai' :
                if ($size > 310) {
                    $_picurl = str_replace('.jpg', '.300x300.jpg', $picurl);
                } elseif ($size >= 300) {
                    $_picurl = str_replace('.jpg', '.300x300.jpg', $picurl);
                } elseif ($size >= 200) {
                    $_picurl = str_replace('.jpg', '.200x200.jpg', $picurl);
                } elseif ($size >= 160) {
                    $_picurl = str_replace('.jpg', '.160x160.jpg', $picurl);
                } elseif ($size >= 80) {
                    $_picurl = str_replace('.jpg', '.80x80.jpg', $picurl);
                }

                break;
            case '360buy' : //n3(130x130),n4(100x100),n5(50x50),n6(240x240)
                if ($size > 310) {
                    $_picurl = str_replace($picurl, '/n[0-9]/', '/n1/'); //350x350
                } elseif ($size >= 300) {
                    $_picurl = str_replace($picurl, '/n[0-9]/', '/n1/'); //350x350
                } elseif ($size >= 200) {
                    $_picurl = str_replace($picurl, '/n[0-9]/', '/n7/'); //220x220
                } elseif ($size >= 160) {
                    $_picurl = str_replace($picurl, '/n[0-9]/', '/n2/'); //160x160
                } elseif ($size >= 80) {
                    $_picurl = str_replace($picurl, '/n[0-9]/', '/n4/'); //100x100
                }
                break;
            case 'dangdang' :
                if ($size > 310) {
                    $_picurl = str_replace($picurl, '_[a-z].jpg', '_o.jpg'); //440x440
                } elseif ($size >= 300) {
                    $_picurl = str_replace($picurl, '_[a-z].jpg', '_o.jpg'); //440x440
                } elseif ($size >= 200) {
                    $_picurl = str_replace($picurl, '_[a-z].jpg', '_b.jpg'); //200x200
                } elseif ($size >= 160) {
                    $_picurl = str_replace($picurl, '_[a-z].jpg', '_l.jpg'); //150x150
                } elseif ($size >= 80) {
                    $_picurl = str_replace($picurl, '_[a-z].jpg', '_t.jpg'); //70x70
                }
                break;
            case 'vancl' :
                if ($size > 310) {
                    
                } elseif ($size >= 300) {
                    
                } elseif ($size >= 200) {
                    
                } elseif ($size >= 160) {
                    
                } elseif ($size >= 80) {
                    
                }
                break;
        }
    }
    return $_picurl;
}

if (!function_exists('xt_contact_info')) {

    function xt_contact_info($contactmethods) {
        $contacts = array(
            XT_USER_GENDER => '性别',
            XT_USER_QQ => 'QQ',
            XT_USER_MOBILE => '手机'
        );
        if (xt_is_fanxian()) {
            $contacts[XT_USER_ALIPAY] = '支付宝';
            $contacts[XT_USER_ALIPAY_NAME] = '支付宝实名';
            if (xt_is_fanxian_bank()) {
                $contacts[XT_USER_BANK] = '开户银行';
                $contacts[XT_USER_BANK_CARD] = '银行卡号';
                $contacts[XT_USER_BANK_NAME] = '开户名';
            }
        }
        return $contacts;
    }

}

function xt_share_picurl($picurl) {
    if (empty($picurl)) {
        $picurl = XT_SHARE_DEFAULT_PIC;
    }
    return $picurl;
}

function xt_action_login($user_login) {
    global $user_ID;
    if ($user_ID) {
        
    }
}

function xt_login_redirect($redirect_to, $request, $user) {
    if (!empty($user) && isset($user->roles) && is_array($user->roles)) {
        //		if (in_array("administrator", $user->roles)) {
        //			return home_url("/wp-admin/");
        //		} else {
        return xt_get_the_user_url($user->ID);
        //		}
    }
}

function convert_at($data) {
    return preg_replace('/@(\S+?)\:/', '<a href="javascript:;" class="xt-red">@\\1</a> :', $data);
}

function xt_notify_moderator($id = '') {
    
}

function xt_notify_shareauthor($id = '', $type = '') {
    
}

function xt_get_var($query = null, $x = 0, $y = 0) {
    global $wpdb;
    if (method_exists($wpdb, 'dbcr_query')) {
        $wpdb->dbcr_query($query, false);
        // Extract var out of cached results based x,y vals
        if (!empty($wpdb->last_result[$y])) {
            $values = array_values(get_object_vars($wpdb->last_result[$y]));
        }
        // If there is a value return it else return null
        return (isset($values[$x]) && $values[$x] !== '') ? $values[$x] : null;
    }
    return $wpdb->get_var($query, $x, $y);
}

/**
 * format time
 *
 * @param string|int $time
 * @return string
 */
function xt_format_time($time, $gmt = false) {
    if (empty($time)) {
        return $time;
    }

    if (!is_numeric($time)) {
        if (PHP_VERSION < 5) {
            $matchs = array();
            preg_match_all('/(\S+)/', $time, $matchs);
            if ($matchs[0]) {
                $Mtom = array(
                    'Jan' => '01',
                    'Feb' => '02',
                    'Mar' => '03',
                    'Apr' => '04',
                    'May' => '05',
                    'Jun' => '06',
                    'Jul' => '07',
                    'Aug' => '08',
                    'Sep' => '09',
                    'Oct' => '10',
                    'Nov' => '11',
                    'Dec' => '12'
                );
                $time = $matchs[0][5] . $Mtom[$matchs[0][1]] . $matchs[0][2] . ' ' . $matchs[0][3];
            }
        }
        $t = strtotime($time);
    } else {
        $t = $time;
    }
    $current_time = strtotime(current_time('mysql'));
    $differ = $current_time - $t;

    $year = date('Y', $current_time);

    if (($year % 4) == 0 && ($year % 100) > 0) {
        //闰年
        $days = 366;
    } elseif (($year % 100) == 0 && ($year % 400) == 0) {
        //闰年
        $days = 366;
    } else {
        $days = 365;
    }
    if ($differ <= 60) {
        //小于1分钟
        if ($differ <= 0) {
            $differ = 1;
        }
        $format_time = sprintf('%d秒前', $differ);
    } elseif ($differ > 60 && $differ <= 60 * 60) {
        //大于1分钟小于1小时
        $min = floor($differ / 60);
        $format_time = sprintf('%d分钟前', $min);
    } elseif ($differ > 60 * 60 && $differ <= 60 * 60 * 24) {
        if (date('Y-m-d', $current_time) == date('Y-m-d', $t)) {
            //大于1小时小于当天
            $format_time = sprintf('今天 %s', date('H:i', $t));
        } else {
            //大于1小时小于24小时
            $format_time = sprintf('%s月%s日 %s', date('n', $t), date('j', $t), date('H:i', $t));
        }
    } elseif ($differ > 60 * 60 * 24 && $differ <= 60 * 60 * 24 * $days) {
        if (date('Y', $current_time) == date('Y', $t)) {
            //大于当天小于当年
            $format_time = sprintf('%s月%s日 %s', date('n', $t), date('j', $t), date('H:i', $t));
        } else {
            //大于当天不是当年
            $format_time = sprintf('%s年%s月%s日 %s', date('Y', $t), date('n', $t), date('j', $t), date('H:i', $t));
        }
    } else {
        //大于今年
        $format_time = sprintf('%s年%s月%s日 %s', date('Y', $t), date('n', $t), date('j', $t), date('H:i', $t));
    }
    return $format_time;
}

function xt_segment($text, $num = 10) {
    $list = array();
    if (empty($text))
        return $list;
    try {
        if (function_exists("scws_open")) {
            $sh = scws_open();
            scws_set_charset($sh, 'utf8');
            scws_set_dict($sh, XT_PLUGIN_DIR . '/xt-core/sdks/scws/dict.utf8.xdb');
            scws_set_rule($sh, XT_PLUGIN_DIR . '/xt-core/sdks/scws/rules.utf8.ini');
            scws_set_ignore($sh, true);
            scws_send_text($sh, $text);
            $words = scws_get_tops($sh, $num);
            scws_close($sh);
        } else {
            require_once XT_PLUGIN_DIR . '/xt-core/sdks/pscws4/pscws4.class.php';
            $pscws = new PSCWS4();
            $pscws->set_dict(XT_PLUGIN_DIR . '/xt-core/sdks/scws/dict.utf8.xdb');
            $pscws->set_rule(XT_PLUGIN_DIR . '/xt-core/sdks/scws/rules.utf8.ini');
            $pscws->set_ignore(true);
            $pscws->send_text($text);
            $words = $pscws->get_tops($num);
            $pscws->close();
        }
    } catch (Exception $e) {
        
    }

    foreach ($words as $word) {
        $list[] = $word['word'];
    }

    return $list;
}

function xt_segments($arr, $num = 10) {
    $list = array();
    if (empty($text))
        return $list;

    $words = array();

    if (function_exists("scws_open")) {
        $sh = scws_open();
        scws_set_charset($sh, 'utf8');
        scws_set_dict($sh, XT_PLUGIN_DIR . '/xt-core/sdks/scws/dict.utf8.xdb');
        scws_set_rule($sh, XT_PLUGIN_DIR . '/xt-core/sdks/scws/rules.utf8.ini');
        scws_set_ignore($sh, true);
        foreach ($arr as $key => $text) {
            scws_send_text($sh, $text);
            $words[] = scws_get_tops($sh, $num);
        }
        scws_close($sh);
    } else {
        require_once XT_PLUGIN_DIR . '/xt-core/sdks/pscws4/pscws4.class.php';
        $pscws = new PSCWS4();
        $pscws->set_dict(XT_PLUGIN_DIR . '/xt-core/sdks/scws/dict.utf8.xdb');
        $pscws->set_rule(XT_PLUGIN_DIR . '/xt-core/sdks/scws/rules.utf8.ini');
        $pscws->set_ignore(true);
        foreach ($arr as $key => $text) {
            $pscws->send_text($text);
            $words[] = $pscws->get_tops($num);
        }
        $pscws->close();
    }

    for ($i = 0; $i < $num; $i++) {
        foreach ($words as $item) {
            if (isset($item[$i])) {
                $word = $item[$i]['word'];
                if (isset($list[$word]))
                    $list[$word]++;
                else
                    $list[$word] = 1;
            }
        }
    }

    $list = array_slice($list, 0, $num);
    return array_keys($list);
}

function xt_segment_unicode($str, $pre = '') {
    $arr = array();
    $str_len = mb_strlen($str, 'UTF-8');
    for ($i = 0; $i < $str_len; $i++) {
        $s = mb_substr($str, $i, 1, 'UTF-8');
        if ($s != ' ' && $s != '　') {
            $arr[] = $pre . 'ux' . xt_utf8_unicode($s);
        }
    }
    $arr = array_unique($arr);
    return implode(' ', $arr);
}

function xt_utf8_unicode($char) {
    switch (strlen($char)) {
        case 1 :
            return ord($char);
        case 2 :
            $n = (ord($char[0]) & 0x3f) << 6;
            $n += ord($char[1]) & 0x3f;
            return $n;
        case 3 :
            $n = (ord($char[0]) & 0x1f) << 12;
            $n += (ord($char[1]) & 0x3f) << 6;
            $n += ord($char[2]) & 0x3f;
            return $n;
        case 4 :
            $n = (ord($char[0]) & 0x0f) << 18;
            $n += (ord($char[1]) & 0x3f) << 12;
            $n += (ord($char[2]) & 0x3f) << 6;
            $n += ord($char[3]) & 0x3f;
            return $n;
    }
}

function xt_get_todaytime() {
    static $today_time = NULL;
    if ($today_time === NULL)
        $today_time = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - date('Z');
    return $today_time;
}

add_action('wp_loaded', 'xt_flush_rules');

// flush_rules() if our rules are not yet included
function xt_flush_rules() {
    global $wp_rewrite;
    $index = '';
    if ($wp_rewrite->using_index_permalinks()) {
        $index = $wp_rewrite->index . '/';
    }
    $rules = get_option('rewrite_rules');
    $base = xt_base();
    if (!isset($rules[$index . '(' . $base . ')/([a-zA-Z]+)-?(.*)$'])) {
        $global = get_option(XT_OPTION_GLOBAL);
        if (empty($index)) {
            $global['index'] = '';
        } else {
            $global['index'] = '/' . $wp_rewrite->index;
        }
        update_option(XT_OPTION_GLOBAL, $global);
        $wp_rewrite->flush_rules();
    }
}

/**
 * xt_rewrite_rules function.
 * Adds in new rewrite rules for categories, products, category pages, and ambiguities (either categories or products)
 * Also modifies the rewrite rules for product URLs to add in the post type.
 *
 * @since 3.8
 * @access public
 * @param array $rewrite_rules
 * @return array - the modified rewrite rules
 */
function xt_rewrite_rules($rewrite_rules) {
    global $wp_rewrite;
    $index = '';
    if ($wp_rewrite->using_index_permalinks()) {
        $index = $wp_rewrite->index . '/';
    }
    $newrules = array();
    $base = xt_base();
    $newrules[$index . '(' . $base . ')/([a-zA-Z]+)-?(.*)$'] = 'index.php?pagename=$matches[1]&xt_action=$matches[2]&xt_param=$matches[3]';
    return $newrules + $rewrite_rules;
}

add_filter('rewrite_rules_array', 'xt_rewrite_rules');

/**
 * xt_query_vars function.
 * adds in the post_type and xt_item query vars
 *
 */
function xt_query_vars($vars) {
    // post_type is used to specify that we are looking for products
    $vars[] = "xt_action";
    // xt_item is used to find items that could be either a product or a product category, it defaults to category, then tries products
    $vars[] = "xt_param";
    return $vars;
}

add_filter('query_vars', 'xt_query_vars');

function xt_get_platform($key) {
    global $xt;
    if (empty($xt->platform)) {
        $xt->platform = get_option(XT_OPTION_PLATFORM);
    }
    if (!empty($xt->platform)) {
        if (isset($xt->platform[$key]))
            return $xt->platform[$key];
    }
    return null;
}

function xt_get_app_xt() {
    return xt_get_platform('xt');
}

function xt_get_app_taobao() {
    return xt_get_platform('taobao');
}

function xt_get_app_paipai() {
    return xt_get_platform('paipai');
}

function xt_get_app_yiqifa() {
    return xt_get_platform('yiqifa');
}

function xt_get_app_weibo() {
    return xt_get_platform('weibo');
}

function xt_get_app_qq() {
    return xt_get_platform('qq');
}

function xt_is_self($user_id = 0) {
    if ($user_id == 0) {
        return false;
    }
    $user = wp_get_current_user();
    if ($user->exists()) {
        return $user->ID == $user_id;
    }
    return false;
}

function xt_is_404() {
    global $xt;
    return $xt->is_error404;
}

function xt_get_global($key) {
    global $xt;
    if (empty($xt->option)) {
        $xt->option = get_option(XT_OPTION_GLOBAL);
    }
    if (!empty($xt->option)) {
        if (isset($xt->option[$key]))
            return $xt->option[$key];
    }
    return 0;
}

function xt_user_default_description() {
    return xt_get_global('userDescription');
}

function xt_is_fanxian() {
    return xt_get_global('isFanxian');
}

function xt_is_fanxian_bank() {
    return xt_get_global('isFanxianBank');
}

function xt_is_forcelogin() {
    return xt_get_global('isForceLogin');
}

function xt_is_s8() {
    return xt_get_global('isS8');
}

function xt_is_taobaoPopup() {
    return xt_get_global('isTaobaoPopup');
}

function xt_is_displaycomment() {
    return xt_get_global('isDisplayComment');
}

function xt_albumdisplay() {
    return xt_get_global('albumDisplay');
}

function xt_is_scroll() {
    return 1;
    //return xt_get_global('isScroll');
}

function xt_code_analytics() {
    return get_option(XT_OPTION_CODE_ANALYTICS);
}

function xt_code_share() {
    return get_option(XT_OPTION_CODE_SHARE);
}

function xt_bdshare() {
    return xt_get_global('bdshare');
}

function xt_bulletin() {
    return xt_get_global('bulletin');
}

function xt_loading() {
    return xt_get_global('loading');
}

function xt_prices() {
    return xt_get_global('prices');
}

function xt_followlimit() {
    return xt_get_global('followLimit');
}

function xt_shareperpage() {
    return 20;
}

function xt_albumperpage() {
    return 20;
}

function xt_userperpage() {
    return 20;
}

function xt_get_multicashback($user, $rate, $sharerate, & $multi = array()) {
    if ($user->exists()) {
        $parents = get_user_meta($user->ID, XT_USER_PARENT, true);
        if (!empty($parents)) {
            $parent = new WP_User($parents['id']);
            if ($parent->exists()) { //当前推广人的推广人存在
                $adrate = xt_get_adrate($parent);
                $_temp = $rate + $sharerate + $adrate;
                foreach ($multi as $_m) {
                    $_temp += $_m['adrate'];
                }
                if (!empty($multi)) { //多级
                    if (xt_is_role_multicashback($parent->roles)) {
                        $multi[] = array(
                            'id' => $parent->ID,
                            'name' => $parent->user_login,
                            'sub_id' => $user->ID,
                            'sub_name' => $user->user_login,
                            'adrate' => $adrate
                        );
                    }
                } else { //一级
                    $multi[] = array(
                        'id' => $parent->ID,
                        'name' => $parent->user_login,
                        'sub_id' => $user->ID,
                        'sub_name' => $user->user_login,
                        'adrate' => $adrate
                    );
                }
                if ($adrate > 0 && xt_fanxian_is_multi()) {
                    if (xt_is_role_multicashback($parent->roles)) {
                        xt_get_multicashback($parent, $rate, $sharerate, $multi);
                    }
                }
            }
        }
    }
    return $multi;
}

function xt_is_role_multicashback($roles) {
    $xt_roles = get_option(XT_OPTION_ROLE);
    if (empty($xt_roles)) {
        return false;
    }
    foreach ($roles as $r) {
        if (isset($xt_roles[$r])) {
            if ($xt_roles[$r]['ismulti']) {
                return true;
            }
        }
    }
    return false;
}

function xt_get_rate($user = 0) {
    if (xt_is_fanxian()) {
        $isforce = false;
        if (!(is_object($user) && is_a($user, 'WP_User'))) {
            if ($user > 0) {
                $user = new WP_User($user);
                $isforce = true;
            } else {
                $user = wp_get_current_user();
            }
        } else {
            $isforce = true;
        }
        if ($user->exists()) {
            $rate = xt_get_user_rate($user->ID);
            if ($rate === '') {
                return xt_get_role_rate($user->roles);
            }
            return $rate;
        } else {
            if ($isforce) { //如果指定了会员,但不存在,则返回0 
                return 0;
            }
            return xt_fanxian_default_rate();
        }
    }
    return 0;
}

function xt_get_user_rate($user_id, $type = 'rate') {
    $rates = get_user_meta($user_id, XT_USER_FANXIAN_RATE, true);
    if (!empty($rates) && is_array($rates)) {
        $rates = array_merge(array('rate' => '', 'ads' => '', 'share' => ''), $rates);
        return $rates[$type];
    }
    return '';
}

function xt_get_role_rate($roles) {
    $xt_roles = get_option(XT_OPTION_ROLE);
    if (empty($xt_roles)) {
        return xt_fanxian_default_rate();
    }
    $_rate = 0;
    $_isset = false;
    foreach ($roles as $r) {
        if (isset($xt_roles[$r])) {
            if ($xt_roles[$r]['rate'] != -1) {
                $_isset = true;
            }
            $_rate = max(intval($xt_roles[$r]['rate']), $_rate);
        }
    }
    if ($_rate == 0 && !$_isset) {
        $_rate = xt_fanxian_default_rate();
    }
    return $_rate;
}

function xt_get_adrate($user = 0, $ismulti = false) {
    if (xt_is_fanxian() && xt_fanxian_is_ad()) {
        $isforce = false;
        if (!(is_object($user) && is_a($user, 'WP_User'))) {
            if ($user > 0) {
                $user = new WP_User($user);
                $isforce = true;
            } else {
                $user = wp_get_current_user();
            }
        } else {
            $isforce = true;
        }
        if ($user->exists()) {
            $rate = xt_get_user_rate($user->ID, 'ads');
            if ($rate === '') {
                return xt_get_role_adrate($user->roles, $ismulti = false);
            }
            return $rate;
        } else {
            if ($isforce) { //如果指定了会员,但不存在,则返回0 
                return 0;
            }
            return xt_fanxian_default_adrate();
        }
    }
    return 0;
}

function xt_get_sharerate($user = 0) {
    if (xt_is_fanxian() && xt_fanxian_is_share()) {
        $isforce = false;
        if (!(is_object($user) && is_a($user, 'WP_User'))) {
            if ($user > 0) {
                $user = new WP_User($user);
                $isforce = true;
            } else {
                $user = wp_get_current_user();
            }
        } else {
            $isforce = true;
        }
        if ($user->exists()) {
            $rate = xt_get_user_rate($user->ID, 'share');
            if ($rate === '') {
                return xt_get_role_sharerate($user->roles);
            }
            return $rate;
        } else {
            if ($isforce) { //如果指定了会员,但不存在,则返回0 
                return 0;
            }
            return xt_fanxian_default_sharerate();
        }
    }
    return 0;
}

function xt_get_role_sharerate($roles) {
    $xt_roles = get_option(XT_OPTION_ROLE);
    if (empty($xt_roles)) {
        return xt_fanxian_default_sharerate();
    }
    $_rate = 0;
    $_isset = false;
    foreach ($roles as $r) {
        if (isset($xt_roles[$r])) {
            if (isset($xt_roles[$r]['sharerate']) && $xt_roles[$r]['sharerate'] != -1) {
                $_isset = true;
            }
            $_sharerate = isset($xt_roles[$r]['sharerate']) ? $xt_roles[$r]['sharerate'] : 0;
            $_rate = max(intval($_sharerate), $_rate);
        }
    }
    if ($_rate == 0 && !$_isset) {
        $_rate = xt_fanxian_default_sharerate();
    }
    return $_rate;
}

function xt_get_role_adrate($roles, $ismulti = false) {
    $xt_roles = get_option(XT_OPTION_ROLE);
    if (empty($xt_roles)) {
        return xt_fanxian_default_adrate();
    }
    $_rate = 0;
    $_isset = false;
    foreach ($roles as $r) {
        if (isset($xt_roles[$r])) {
            if ($xt_roles[$r]['adrate'] != -1) {
                $_isset = true;
            }
            //            if ($ismulti) { //如果是多级分成,则查找角色内的支持多级分成的角色进行比较
            //                if ($xt_roles[$r]['ismulti']) {
            //                    $_rate = max(intval($xt_roles[$r]['adrate']), $_rate);
            //                }
            //            } else {
            $_rate = max(intval($xt_roles[$r]['adrate']), $_rate);
            //            }
        }
    }
    if ($_rate == 0 && !$ismulti && !$_isset) {
        $_rate = xt_fanxian_default_adrate();
    }
    return $_rate;
}

function xt_get_fanxian($key) {
    global $xt;
    if (empty($xt->fanxian)) {
        $xt->fanxian = get_option(XT_OPTION_FANXIAN);
    }
    if (!empty($xt->fanxian)) {
        if (isset($xt->fanxian[$key]))
            return $xt->fanxian[$key];
    }
    return 0;
}

function xt_fanxian_is_sharebuy() {
    return apply_filters('xt_fanxian_is_sharebuy', 1);
}

function xt_fanxian_is_multi() {
    return xt_get_fanxian('isMulti');
}

function xt_fanxian_is_pendingtixian() {
    return xt_get_fanxian('isPendingTixian');
}

function xt_fanxian_is_autocash() {
    return xt_get_fanxian('isAutoCash');
}

function xt_fanxian_is_ad() {
    return xt_get_fanxian('isAd');
}

function xt_fanxian_is_share() {
    return xt_get_fanxian('isShare');
}

function xt_fanxian_default_rate() {
    return xt_get_fanxian('rate_cashback');
}

function xt_fanxian_default_sharerate() {
    return xt_get_fanxian('rate_share');
}

function xt_fanxian_default_adrate() {
    return xt_get_fanxian('rate_ad');
}

function xt_fanxian_cashback() {
    return xt_get_fanxian('cashback');
}

function xt_fanxian_registe_cash() {
    return xt_get_fanxian('registe_cash');
}

function xt_fanxian_registe_jifen() {
    return xt_get_fanxian('registe_jifen');
}

function xt_load_template($template, $load = true, $isDie = false) {
    global $wp_query;
    if (!endsWith($template, '.php')) {
        $template = $template . '.php';
    }

    $_template = locate_template($template);
    if ('' == $_template) {
        if (file_exists(XT_THEME_PATH . '/' . $template)) {
            status_header(200);
            $wp_query->is_page = true;
            $_template = XT_THEME_PATH . '/' . $template;
        }
    }
    if ($load && '' != $_template) {
        require $_template;
    }
    if ($isDie)
        die;
    return $_template;
}

function xt_set_404() {
    global $wp_query, $xt;
    $xt->is_xintao = true;
    $xt->is_error404 = true;
    $wp_query->set_404();
    $wp_query->is_page = false;
}

function xt_clear_404() {
    global $wp_query, $xt;
    $xt->is_error404 = false;
    $wp_query->is_404 = false;
}

function xt_core_set_globals() {
    global $xt, $wp, $xt_template_name;
    xt_core_constants_route();
    $xt->option = get_option(XT_OPTION_GLOBAL);
    //TODO slug
    if (!is_admin()) {
        if (isset($wp->query_vars['pagename'])) {
            $base = xt_base();
            if ($wp->query_vars['pagename'] == $base) {
                $xt->is_xintao = true;
                if (isset($wp->query_vars['xt_action'])) {
                    $action = $wp->query_vars['xt_action'];
                    $param = $wp->query_vars['xt_param'];
                    switch ($action) {
                        case 'login' :
                            $xt->is_login = true;
                            break;
                        case 'search' :
                            $xt->is_shares = true;
                            $wp->query_vars['xt_param'] = xt_core_share_params($param);
                            break;
                        case 'album' :
                            $xt->is_albums = true;
                            $wp->query_vars['xt_param'] = xt_core_album_params($param);
                            break;
                        case 'user' :
                            $xt->is_users = true;
                            break;
                        case 'id' :
                            $xt->is_share = true;
                            break;
                        case 'aid' :
                            $xt->is_album = true;
                            break;
                        case 'gid' :
                            $xt->is_group = true;
                            break;
                        case 'uid' :
                            $xt->is_user = true;
                            break;
                        case 'account' :
                            $xt->is_account = true;
                            break;
                        case 'invite' :
                            $xt->is_invite = true;
                            break;
                        case 'taobao' :
                            if (!empty($param) && is_numeric($param)) {
                                $xt->is_taobao = true;
                                $wp->query_vars['xt_param'] = ($param);
                            } else {
                                $xt->is_taobaos = true;
                                $wp->query_vars['xt_param'] = xt_core_taobao_params($param);
                            }

                            break;
                        case 'shop' :
                            $xt->is_shops = true;
                            $wp->query_vars['xt_param'] = xt_core_shop_params($param);
                            break;
                        case 'paipai' :
                            $xt->is_paipais = true;
                            $wp->query_vars['xt_param'] = xt_core_paipai_params($param);
                            break;
                        case 'bijia' :
                            $xt->is_bijias = true;
                            $wp->query_vars['xt_param'] = xt_core_bijia_params($param);
                            break;
                        case 'tuan' :
                            $xt->is_tuans = true;
                            $wp->query_vars['xt_param'] = xt_core_tuan_params($param);
                            break;
                        case 'temai' :
                            $xt->is_temais = true;
                            $wp->query_vars['xt_param'] = xt_core_temai_params($param);
                            break;
                        case 'coupon' :
                            $xt->is_coupons = true;
                            $wp->query_vars['xt_param'] = xt_core_coupon_params($param);
                            break;
                        case 'daogou' :
                            $xt->is_daogous = true;
                            $wp->query_vars['xt_param'] = xt_core_daogou_params($param);
                            break;
                        case 'help' :
                            $xt->is_helps = true;
                            $wp->query_vars['xt_param'] = xt_core_help_params($param);
                            break;
                        case 'brands' :
                            $xt->is_brands = true;
                            break;
                        case 'stars' :
                            $xt->is_stars = true;
                            break;
                        case 'activities' :
                            $xt->is_activities = true;
                            break;
                        case 'taoquan' :
                            $xt->is_taoquan = true;
                            break;
                        case 'malls' :
                            $xt->is_malls = true;
                            break;
                        case 'jump' :
                            $xt->is_jump = true;
                            $wp->query_vars['xt_param'] = xt_core_jump_params($param);
                            break;
                        case 'sitemap' :
                        case 'sitemapshare' :
                        case 'sitemappost' :
                        case 'sitemapalbum' :
                        case 'sitemapuser' :
                        case 'sitemapother' :
                            $xt->is_sitemap = true;
                            break;
                        default :
                            xt_set_404();
                            break;
                    }
                } else {
                    $wp->query_vars['xt_action'] = 'error404';
                    xt_set_404();
                }
            } elseif (xt_is_page($wp->query_vars['pagename'])) {
                $xt->is_xintao = true;
                $xt->is_page = true;
            } elseif ($wp->query_vars['pagename'] == xt_base_daogou()) {
                $xt->is_xintao = true;
                $xt->is_daogous = true;
                $wp->query_vars['xt_param'] = xt_core_daogou_params(array());
            } elseif ($wp->query_vars['pagename'] == xt_base_help()) {
                $xt->is_xintao = true;
                $xt->is_helps = true;
                $wp->query_vars['xt_param'] = xt_core_help_params(array());
            } else {
                $xt->is_xintao = true;
                xt_set_404();
            }
        } elseif (isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == 'daogou') {
            $xt->is_xintao = true;
            $xt->is_daogou = true;
        } elseif (isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == 'help') {
            $xt->is_xintao = true;
            $xt->is_help = true;
        } elseif (isset($wp->query_vars['name']) && !empty($wp->query_vars['name'])) {
            $xt->is_xintao = false;
        } else {
            $xt->is_xintao = true;
            xt_set_404();
        }
        if ($xt->is_login) {
            $xt_template_name = 'xt-login';
        } elseif ($xt->is_account) {
            $xt_template_name = 'xt-account';
        } elseif ($xt->is_sitemap) {
            $xt_template_name = 'xt-sitemap';
        } else {
            if ($xt->is_index) {
                $xt_template_name = 'home';
            } elseif ($xt->is_shares) {
                $xt_template_name = 'shares';
            } elseif ($xt->is_share) {
                $xt_template_name = 'share';
            } elseif ($xt->is_albums) {
                $xt_template_name = 'albums';
            } elseif ($xt->is_album) {
                $xt_template_name = 'album';
            } elseif ($xt->is_users) {
                $xt_template_name = 'users';
            } elseif ($xt->is_user) {
                $xt_template_name = 'user';
            } elseif ($xt->is_taobaos) {
                $xt_template_name = 'taobaos';
            } elseif ($xt->is_taobao) {
                $xt_template_name = 'taobao';
            } elseif ($xt->is_shops) {
                $xt_template_name = 'shops';
            } elseif ($xt->is_paipais) {
                $xt_template_name = 'paipais';
            } elseif ($xt->is_bijias) {
                $xt_template_name = 'bijias';
            } elseif ($xt->is_tuans) {
                $xt_template_name = 'tuans';
            } elseif ($xt->is_temais) {
                $xt_template_name = 'temais';
            } elseif ($xt->is_coupons) {
                $xt_template_name = 'coupons';
            } elseif ($xt->is_invite) {
                $xt_template_name = 'invite';
            } elseif ($xt->is_daogous) {
                $xt_template_name = 'daogous';
            } elseif ($xt->is_daogou) {
                $xt_template_name = 'daogou';
            } elseif ($xt->is_helps) {
                $xt_template_name = 'helps';
            } elseif ($xt->is_help) {
                $xt_template_name = 'help';
            } elseif ($xt->is_brands) {
                $xt_template_name = 'brands';
            } elseif ($xt->is_stars) {
                $xt_template_name = 'stars';
            } elseif ($xt->is_activities) {
                $xt_template_name = 'activities';
            } elseif ($xt->is_taoquan) {
                $xt_template_name = 'taoquan';
            } elseif ($xt->is_malls) {
                $xt_template_name = 'malls';
            } elseif ($xt->is_error404) {
                $xt_template_name = 'error404';
            }
        }
    }
}

// Parse the URI and set globals
add_action('parse_request', 'xt_core_set_globals');
add_action('wp', 'xt_screens', 4);

function xt_screens() {
    global $xt, $wp_query, $xt_catalog, $xt_user, $xt_album, $xt_taobao_item, $xt_template_name;
    if (xt_is_404()) {
        if (is_home()) {
            xt_clear_404();
            $xt->is_xintao = true;
            $xt->is_index = true;
            $xt_template_name = 'home';
        } elseif (is_front_page()) {
            if (is_page(xt_base()) && (!isset($wp_query->query_vars['xt_action']))) {
                xt_clear_404();
                $xt->is_xintao = true;
                $xt->is_index = true;
                $xt_template_name = 'home';
            } elseif (!isset($wp_query->query_vars['xt_action'])) {
                $xt->is_xintao = false;
            }
        }
    }
    if (is_404() && !$xt->is_xintao) {
        $xt->is_xintao = true;
        xt_set_404();
    }
    if ($xt->is_xintao) {
        if (isset($_GET['invite']) && absint($_GET['invite']) > 0) {
            setcookie(XT_USER_PARENT, (string) $_GET['invite'], time() + 1296000, COOKIEPATH, COOKIE_DOMAIN);
        }
        $xt_share_param = isset($wp_query->query_vars['xt_param']) ? $wp_query->query_vars['xt_param'] : '';
        if ($xt->is_shares) {
            query_shares($xt_share_param);
            if (isset($xt_share_param['s']) && !empty($xt_share_param['s'])) { //设置搜索词
                $wp_query->set('s', $xt_share_param['s']);
            }
            if (absint($xt_share_param['cid']) > 0) {
                $_term = xt_get_catalog($xt_share_param['cid']);
                if (!empty($_term)) {
                    $xt_catalog = $_term;
                }
            }
        } elseif ($xt->is_albums) {
            query_albums($xt_share_param);
            if (isset($xt_share_param['s']) && !empty($xt_share_param['s'])) { //设置搜索词
                $wp_query->set('s', $xt_share_param['s']);
            }
        } elseif ($xt->is_share) {
            $xt_user = null;
            if (absint($xt_share_param) > 0) {
                query_shares(array(
                    'id' => absint($xt_share_param)
                ));
                if (xt_have_shares()) {
                    xt_the_share();
                    $xt_user = new WP_User(get_the_share_userid());
                    if (!empty($xt_user) && $xt_user->ID != 0) {
                        xt_setup_single_userdata();
                    } else {
                        xt_set_404();
                    }
                } else {
                    xt_set_404();
                }
            } else {
                xt_set_404();
            }
        } elseif ($xt->is_user) {
            $xt_user = null;
            if (absint($xt_share_param) > 0) {
                $uid = absint($xt_share_param);
                $_user = wp_get_current_user();
                if ($_user->exists()) {
                    if ($_user->ID == $uid) {
                        $xt_user = $_user;
                    }
                }
                if (empty($xt_user) || $xt_user->ID == 0) {
                    $_user = new WP_User($uid);
                    if ($_user && $_user->ID != 0) {
                        $xt_user = $_user;
                    }
                }
                if (!empty($xt_user) && $xt_user->ID != 0) {
                    xt_setup_single_userdata();
                }
            }
            if (empty($xt_user) || $xt_user->ID == 0)
                xt_set_404();
        }
        elseif ($xt->is_account) {
            $xt_user = wp_get_current_user();
            if ($xt_user->exists()) {
                //xt_setup_single_userdata();
            } else {
                xt_set_404();
                wp_safe_redirect(site_url('wp-login.php'));
                exit();
            }
        } elseif ($xt->is_album) {
            $xt_user = null;
            $xt_album = null;
            if (absint($xt_share_param) > 0) {
                $xt_album = xt_get_album($xt_share_param);
                if (!empty($xt_album)) {
                    $_user = wp_get_current_user();
                    if ($_user->exists()) {
                        if ($_user->ID == $xt_album->user_id) {
                            $xt_user = $_user;
                        } else {
                            $xt_user = new WP_User($xt_album->user_id);
                        }
                    } else {
                        $xt_user = new WP_User($xt_album->user_id);
                    }
                    if (!empty($xt_user) && $xt_user->ID > 0) {
                        xt_setup_single_userdata();
                        //						query_albums(array (
                        //							'page' => 1,
                        //							'album_per_page' => 20,
                        //							'user_id' => $xt_album->user_id
                        //						));
                        //当前专辑所属用户的前20个专辑
                        query_shares(array(
                            'album_id' => absint($xt_share_param),
                            'page' => 1,
                            'share_per_page' => 40,
                            'user_id' => $xt_album->user_id
                        ));
                    }
                    //当前专辑内的宝贝
                }
                if (empty($xt_album) || empty($xt_user) || $xt_user->ID == 0)
                    xt_set_404();
            }
        }
        elseif ($xt->is_page) {
            
        } elseif ($xt->is_invite) {
            $xt_user = null;
            if (absint($xt_share_param) > 0) {
                $xt_user = new WP_User(absint($xt_share_param));
            }
            if (empty($xt_user) || $xt_user->ID == 0) {
                xt_set_404();
            } else {
                $user = wp_get_current_user();
                if (!$user->exists()) {
                    //15days
                    setcookie(XT_USER_PARENT, (string) $xt_user->ID, time() + 1296000, COOKIEPATH, COOKIE_DOMAIN);
                }
            }
        } elseif ($xt->is_taobao) {
            $xt_taobao_item = xt_taobao_item($xt_share_param, "detail_url,num_iid,title,nick,props_name,cid,pic_url,num,location,price,post_fee,express_fee,ems_fee,item_img,prop_imgs");
            if (is_wp_error($xt_taobao_item)) {
                xt_set_404();
            }
        }
        if (xt_is_404()) {
            $xt_template_name = 'error404';
        }
        if ($xt->is_login) {
            xt_load_template('xt-login', true, true);
        } elseif ($xt->is_jump) {
            xt_load_template('xt-jump', true, true);
        } elseif ($xt->is_sitemap) {
            xt_load_template('xt-sitemap', true, true);
        } else {
            xt_load_template('xt-template', true, true);
        }
    }
}

//function xt_do_404_redirect() {
//    global $wp_query;
//    if (xt_is_404()) {
//        $wp_query->is_404 = true;
//    }
//}
//
//add_action('template_redirect', 'xt_do_404_redirect');

function xt_get_the_username($user = 0) {
    global $wpdb;
    if (!$user) {
        $user = wp_get_current_user();
    }
    if (!empty($user)) {
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        return apply_filters('xt_the_username', $wpdb->escape($user->display_name));
    }
    return "";
}

function xt_the_username($user = 0) {
    echo xt_get_the_username($user);
}

function xt_update_user_count($user_id, $metakey, $count) {
    $counts = get_user_meta($user_id, XT_USER_COUNT, true);
    if (!isset($counts[$metakey])) {
        $counts[$metakey] = 0;
    }
    $counts[$metakey] = $count;
    update_user_meta($user_id, XT_USER_COUNT, $counts);
}

function xt_setup_single_userdata($user = 0) {
    global $wpdb, $xt_user_meta, $xt_user, $xt_user_counts, $xt_user_avatar;
    if (!$user) {
        $user = $xt_user;
    }
    if ($user->ID == 0) {
        xt_set_404();
    }

    if (!empty($user)) {
        if (empty($user->display_name))
            $user->display_name = $user->user_login;
        $user->display_name = $wpdb->escape($user->display_name);
        $xt_user_meta = get_user_meta($user->ID);
        $xt_user_counts = xt_default_counts();
        if (isset($xt_user_meta[XT_USER_COUNT])) {
            $xt_user_counts = array_merge($xt_user_counts, unserialize($xt_user_meta[XT_USER_COUNT][0]));
        }
        if (isset($xt_user_meta[XT_USER_AVATAR])) {
            $xt_user_avatar = $xt_user_meta[XT_USER_AVATAR][0];
        }
    }

    return true;
}

function xt_default_counts() {
    return array(
        XT_USER_COUNT_CASH => 0,
        XT_USER_COUNT_CASH_COST => 0,
        XT_USER_COUNT_JIFEN => 0,
        XT_USER_COUNT_JIFEN_COST => 0,
        XT_USER_COUNT_SHARE => 0,
        XT_USER_COUNT_ALBUM => 0,
        XT_USER_COUNT_FOLLOW => 0,
        XT_USER_COUNT_FANS => 0,
        XT_USER_COUNT_FAV_SHARE => 0,
        XT_USER_COUNT_FAV_ALBUM => 0,
        XT_USER_COUNT_FAV_TOPIC => 0,
        XT_USER_COUNT_FAV_GROUP => 0,
        XT_USER_COUNT_FAV_BRAND => 0
    );
}

function xt_get_meta_userinfo($user_id, $key = 0) {
    $user_info = get_user_meta($user_id, XT_USER_INFO);
    $xt_user_info = array(
        XT_USER_INFO_PIC => ''
    );
    if (!empty($user_info))
        $xt_user_info = array_merge($xt_user_info, $user_info[0]);
    if ($key) {
        if (isset($xt_user_info[$key])) {
            return $xt_user_info[$key];
        }
        return '';
    }
    return $xt_user_info;
}

function xt_update_meta_userinfo($user_id, $key, $value) {
    $user_info = get_user_meta($user_id, XT_USER_INFO);
    $user_info = $user_info[0];
    if ($key) {
        $user_info[$key] = $value;
        update_user_meta($user_id, XT_USER_INFO, $user_info);
    }
}

function xt_follow_by_id($user_id, $f_user_id = 0) {
    global $wpdb;
    $user_id = (int) $user_id;
    $f_user_id = (int) $f_user_id;
    if ($f_user_id == 0) {
        $f_user_id = get_current_user_id();
    }
    if (!$f_user_id) {
        return false;
    }
    $create_time = current_time('mysql');
    $data = compact('user_id', 'f_user_id', 'create_time');
    $wpdb->hide_errors();
    $wpdb->insert(XT_TABLE_USER_FOLLOW, $data);
    $wpdb->show_errors();
    $id = (int) $wpdb->insert_id;
    $count = xt_update_user_follow($user_id, $f_user_id);
    $fans_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . XT_TABLE_USER_FOLLOW . " WHERE user_id=%d AND f_user_id=%d", $f_user_id, $user_id));
    return $fans_count;
}

function xt_unfollow_by_id($user_id, $f_user_id = 0) {
    global $wpdb;
    $user_id = (int) $user_id;
    $f_user_id = (int) $f_user_id;
    if ($f_user_id == 0) {
        $f_user_id = get_current_user_id();
    }
    if (!$f_user_id) {
        return false;
    }
    $data = compact('user_id', 'f_user_id');
    $wpdb->delete(XT_TABLE_USER_FOLLOW, $data);
    $count = xt_update_user_follow($user_id, $f_user_id);
    return $count;
}

function xt_update_user_follow($user_id, $f_user_id) {
    global $wpdb;
    $fans_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM " . XT_TABLE_USER_FOLLOW . " WHERE user_id=%d", $user_id));
    xt_update_user_count($user_id, XT_USER_COUNT_FANS, $fans_count);
    $follows = $wpdb->get_col($wpdb->prepare("SELECT user_id FROM " . XT_TABLE_USER_FOLLOW . " WHERE f_user_id=%d", $f_user_id));
    update_user_meta($f_user_id, XT_USER_FOLLOW, $follows);
    xt_update_user_count($f_user_id, XT_USER_COUNT_FOLLOW, count($follows));
}

function xt_atme($content, $type) {
    global $wpdb;
    $atme_list = array();
    $pattern = "/@([^\f\n\r\t\v@ ]{2,50}?)(?:\:| )/";
    preg_match_all($pattern, $content, $atme_list);
    if (!empty($atme_list[1])) {
        $atme_list[1] = array_unique($atme_list[1]);
        $users = array();
        foreach ($atme_list[1] as $user) {
            if (!empty($user)) {
                $users[] = $user;
            }
        }
        $users = array_unique($users);
        $item_list_tmp = '';
        foreach ($users as $user) {
            if ($user !== '') {
                $user = $wpdb->escape($user);
                $item_list_tmp .= $item_list_tmp ? ",'$user'" : "'$user'";
            }
        }
        if (!empty($item_list_tmp)) {
            $res = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE display_name IN ($item_list_tmp)"));
            foreach ($res as $_id) {
                xt_update_user_notice($_id, $type);
            }
        }
    }
}

function xt_update_user_notice($user_id, $type) {
    global $wpdb;
    $user_id = (int) $user_id;
    $type = (int) $type;
    if (!$user_id || !$type)
        return;
    $num = 1;
    $create_time = current_time('mysql');
    $data = compact('user_id', 'type', 'num', 'create_time');
    $wpdb->hide_errors();
    $wpdb->insert(XT_TABLE_USER_NOTICE, $data);
    $wpdb->show_errors();
    $id = (int) $wpdb->insert_id;
    if (!$id) {
        $wpdb->query($wpdb->prepare("UPDATE " . XT_TABLE_USER_NOTICE . " SET num=num+1,create_time='" . current_time('mysql') . "' WHERE user_id=%d AND type=%d", $user_id, $type));
    }
    return $id;
}

function xt_action_delete_user($user_id) {
    if (empty($user_id) || !is_user_logged_in() || !current_user_can('manage_options'))
        return false;
    //TODO 
}

function xt_action_role_notice() {
    ?>	
    <div class="error fade">
        <p><?php echo '新用户默认角色必须设置为wordpress内置的角色,如:订阅者(subscriber),<a href="' . admin_url('options-general.php') . '">修改新用户默认角色</a>'; ?></p>
    </div>
    <?php
}

function xt_action_update_option_pages($post_ID) {
    $template = get_post_meta($post_ID, '_wp_page_template', true);
    if ($template == 'xt-page.php') {
        $post = get_post($post_ID);
        if (!empty($post)) {
            $pages = get_option(XT_OPTION_PAGES);
            $pages[$post_ID] = $post->post_name;
            update_option(XT_OPTION_PAGES, $pages);
        }
    }
}

function xt_action_delete_option_pages($post_ID) {
    $template = get_post_meta($post_ID, '_wp_page_template', true);
    if ($template == 'xt-page.php') {
        $pages = get_option(XT_OPTION_PAGES);
        if (isset($pages[$post_ID])) {
            unset($pages[$post_ID]);
            update_option(XT_OPTION_PAGES, $pages);
        }
    }
    delete_option(XT_OPTION_PAGE_PRE . $post_ID);
    delete_option(XT_OPTION_PAGE_WIDGETS_PRE . $post_ID);
    delete_option(XT_OPTION_PAGE_HTML_PRE . $post_ID);
}

function xt_is_page($pagename, $post_ID = 0) {
    if ($post_ID > 0) {
        $pages = get_option(XT_OPTION_PAGES);
        return isset($pages[$post_ID]);
    } elseif (!empty($pagename)) {
        $pages = get_option(XT_OPTION_PAGES);
        if (!empty($pages)) {
            foreach ($pages as $page => $_pagename) {
                if ($_pagename == $pagename) {
                    return true;
                }
            }
        }
    }
    return false;
}

function xt_fanxian_invite($user_id) {
    if (intval($user_id) > 0) {
        if (isset($_COOKIE[XT_USER_PARENT])) {
            $parent = new WP_User(intval($_COOKIE[XT_USER_PARENT]));
            if ($parent->exists()) {
                update_user_meta($user_id, XT_USER_PARENT, array(
                    'id' => (string) $parent->ID,
                    'name' => $parent->user_login
                ));
            }
        }
    }
}

function xt_fanxian_cancel($platform, $id) {
    global $wpdb;
    $table = '';
    $field_tradeId = '';
    switch ($platform) {
        case 'taobao' :
            $table = XT_TABLE_TAOBAO_REPORT;
            $field_tradeId = 'trade_id';
            break;
        case 'paipai' :
            $table = XT_TABLE_PAIPAI_REPORT;
            $field_tradeId = 'dealId';
            break;
        case 'yiqifa' :
            $table = XT_TABLE_YIQIFA_REPORT;
            $field_tradeId = 'yiqifaId';
            break;
    }
    if (!empty($table)) {
        $old = $wpdb->get_row('SELECT * FROM ' . $table . ' WHERE id=' . intval($id));
        if (!empty($old)) {
            //update order
            if ($wpdb->update($table, array(
                        'user_id' => 0,
                        'user_name' => ''
                            ), array(
                        'id' => $id
                    ))) {
                //delete fanxian
                $wpdb->delete(XT_TABLE_FANXIAN, array(
                    'trade_id' => $old->$field_tradeId
                ));
            }
        }
    }
}

function xt_cron() {
    if (!wp_next_scheduled('xt_cron_share_hook')) {
        wp_schedule_event(time(), 'minutes_2', 'xt_cron_share_hook');
    }

    if (!wp_next_scheduled('xt_cron_taobao_refreshtoken_hook')) {
        wp_schedule_event(time(), 'hourly', 'xt_cron_taobao_refreshtoken_hook');
    }
    if (!wp_next_scheduled('xt_cron_catalogs_share_hook')) {
        wp_schedule_event(time(), 'hourly', 'xt_cron_catalogs_share_hook');
    }
    if (!wp_next_scheduled('xt_cron_report_taobao_hook')) {
        wp_schedule_event(time(), 'hourly', 'xt_cron_report_taobao_hook');
    }
    if (!wp_next_scheduled('xt_cron_report_paipai_hook')) {
        wp_schedule_event(time() + 5 * 60, 'hourly', 'xt_cron_report_paipai_hook');
    }
    if (!wp_next_scheduled('xt_cron_report_yiqifa_hook')) {
        wp_schedule_event(time() + 10 * 60, 'hourly', 'xt_cron_report_yiqifa_hook');
    }
    if (!wp_next_scheduled('xt_cron_user_account_hook')) {
        wp_schedule_event(time(), 'daily', 'xt_cron_user_account_hook');
    }
    if (!wp_next_scheduled('xt_cron_yiqifa_hotactivity_hook')) {
        wp_schedule_event(time(), 'daily', 'xt_cron_yiqifa_hotactivity_hook');
    }
    if (!wp_next_scheduled('xt_cron_sitemap_hook')) {
        wp_schedule_event(time(), 'daily', 'xt_cron_sitemap_hook');
    }
}

add_filter('cron_schedules', 'xt_interval_minutes2');
add_filter('cron_schedules', 'xt_interval_minutes10');

// add once 2 minute interval to wp schedules
function xt_interval_minutes2($interval) {

    $interval['minutes_2'] = array('interval' => 2 * 60, 'display' => 'Once 2 minutes');

    return $interval;
}

// add once 10 minute interval to wp schedules
function xt_interval_minutes10($interval) {

    $interval['minutes_10'] = array('interval' => 10 * 60, 'display' => 'Once 10 minutes');

    return $interval;
}

add_action('init', 'xt_cron');

//Util
function endsWith($haystack, $needle) {
    return strlen($needle) === 0 || substr($haystack, -strlen($needle)) === $needle;
}

function startsWith($haystack, $needle) {
    return strncmp($haystack, $needle, strlen($needle)) === 0;
}