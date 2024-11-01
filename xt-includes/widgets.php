<?php

define('XT_WIDGET_EDIT_PAGE_COMMON', 'custom,header,footer,shares,share,albums,album,users,user,daogous,daogou,taobaos,paipais,invite,error404,helps,help');
define('XT_TAOBAO_CAT_TEMAI', 50100982);

function xt_search_keyword() {
    global $wp_query, $xt;
    $s = '';
    if ($xt->is_shares || $xt->is_albums || $xt->is_taobaos || $xt->is_shops || $xt->is_paipais || $xt->is_bijias || $xt->is_tuans) {
        $xt_share_param = isset($wp_query->query_vars['xt_param']) ? $wp_query->query_vars['xt_param'] : array();
        if (isset($xt_share_param['s'])) {
            $s = $xt_share_param['s'];
        } elseif (isset($xt_share_param['keyword'])) {
            $s = $xt_share_param['keyword'];
        } elseif (isset($xt_share_param['keyWord'])) {
            $s = $xt_share_param['keyWord'];
        }
    }
    return $s;
}

function xt_shareandtags($cid, $tags, $sortOrder) {
    global $wpdb;
    $tags = array_reverse($tags);
    $today_time = xt_get_todaytime();
    $fields = "wp_xt_share.id,wp_xt_share.title,wp_xt_share.pic_url,wp_xt_share.from_type";
    $order = "";
    $join = "";
    $groupby = "GROUP BY score";
    $where = " WHERE 1=1 ";
    if (!empty($cid) && $cid > 0) {
        $xt_catalog = xt_get_catalog($cid);
        if (!empty($xt_catalog)) {
            if (isset($xt_catalog->children) && !empty($xt_catalog->children)) {
                $join .= " INNER JOIN " . XT_TABLE_SHARE_CATALOG . " ON " . XT_TABLE_SHARE_CATALOG . ".id = " . XT_TABLE_SHARE . ".id ";
                $where .= " AND " . XT_TABLE_SHARE_CATALOG . ".cid in(" . $wpdb->escape($xt_catalog->children) . "," . $cid . ") ";
            } else {
                $join .= " INNER JOIN " . XT_TABLE_SHARE_CATALOG . " ON " . XT_TABLE_SHARE_CATALOG . ".id = " . XT_TABLE_SHARE . ".id ";
                $where .= $wpdb->prepare(" AND " . XT_TABLE_SHARE_CATALOG . ".cid=%d ", $cid);
            }
        }
    }
    switch ($sortOrder) {
        case 'newest' :
        default :
            $order = " ORDER BY " . XT_TABLE_SHARE . ".create_date DESC";
            break;
        case 'popular' :
            $day7_time = $today_time - 604800; //7 days
            $fields .= ",(UNIX_TIMESTAMP(" . XT_TABLE_SHARE . ".create_date) > $day7_time) AS time_sort ";
            $order = " ORDER BY time_sort DESC," . XT_TABLE_SHARE . ".fav_count DESC";
            break;
        case 'hot' :
            $day30_time = $today_time - 2592000; //30 days
            $fields .= ",(UNIX_TIMESTAMP(" . XT_TABLE_SHARE . ".create_date) > $day30_time) AS time_sort ";
            $order = " ORDER BY time_sort DESC," . XT_TABLE_SHARE . ".fav_count DESC";
            break;
    }
    $againsts = array();
    $results = array();
    $whenthen = array();
    if (!empty($tags)) {
        $length = count($tags);
        for ($i = 0; $i < $length; $i++) {
            $tag = $tags[$i];
            $segment = xt_segment_unicode($wpdb->escape($tag->title), '+');
            $againsts[] = "($segment)";
            $results[$i] = array(
                'tag' => $tag->title,
                'share' => false
            );
            $whenthen[] = " when (match(gm.content_match) against('$segment' IN BOOLEAN MODE)) = 1 then $i ";
        }
        $fields .= ",case " . implode('', $whenthen) . " END AS score ";
        $against = implode(' ', $againsts);
        $join .=" INNER JOIN wp_xt_share_match gm ON match(gm.content_match) against('$against' IN BOOLEAN MODE) AND gm.share_id=wp_xt_share.id ";
        $sql = "SELECT * FROM (SELECT $fields FROM wp_xt_share $join $where $order) AS temp $groupby";
        $shares = $wpdb->get_results($sql);
        unset($sql);
        if (!empty($shares)) {
            foreach ($shares as $share) {
                for ($j = 0; $j < 12; $j++) {
                    if ($share->score == $j) {
                        $results[$j]['share'] = $share;
                        continue;
                    }
                }
            }
        }
    }

    return array_reverse($results);
}

function xt_array_replace($search, $replace, $array) {
    if (is_array($array)) {
        foreach ($array as $k => $v) {
            $array[$k] = xt_array_replace($search, $replace, $v);
        }
    } else {
        $array = str_replace($search, $replace, $array);
    }
    return $array;
}

if (!function_exists('xt_replace_base')) {

    function xt_replace_widgetsandhtml($search, $replace) {
        global $wpdb;
        if (!empty($search) && !empty($replace)) {
            $ss = array();
            foreach ($search as $s) {
                $ss[] = "option_value LIKE '%" . $wpdb->escape($s) . "%'";
            }
            $sql = "SELECT * FROM $wpdb->options WHERE (" . implode(' OR ', $ss) . ") AND (option_name like '" . XT_OPTION_PAGE_WIDGETS_PRE . "%' OR option_name like '" . XT_OPTION_PAGE_HTML_PRE . "%');";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (!empty($results)) {
                foreach ($results as $result) {
                    update_option($result['option_name'], xt_array_replace($search, $replace, maybe_unserialize($result['option_value'])));
                }
            }
        }
    }

}
if (!function_exists('xt_replace_base')) {

    function xt_replace_base($global, $base, $daogou, $help) {
        global $wpdb;
        $home = home_url();
        if (substr($home, -strlen('/')) === '/') {
            $home = substr($home, strlen($home) - 1);
        }
        $home = $home . xt_index() . '/';
        $where = array();
        $search = array();
        $replace = array();
        $REPLACE = '';
        if ($global['base'] != $base) {
            $_base = $wpdb->escape($home . $global['base'] . '/');
            $where[] = " option_value LIKE '%" . ($_base) . "%' ";
            $search[] = $_base;
            $_newBase = $wpdb->escape($home . $base . '/');
            $replace[] = $_newBase;
            $REPLACE = " REPLACE(post_content,'{$_base}','{$_newBase}')";
        }
        if ($global['daogou'] != $daogou) {
            $_daogou = $wpdb->escape($home . $global['daogou'] . '/');
            $where[] = " option_value LIKE '%" . ($_daogou) . "%' ";
            $search[] = $_daogou;
            $_newDaogou = $wpdb->escape($home . $daogou . '/');
            $replace[] = $_newDaogou;
            if (!empty($REPLACE)) {
                $REPLACE = " REPLACE({$REPLACE},'{$_daogou}','{$_newDaogou}')";
            } else {
                $REPLACE = " REPLACE(post_content,'{$_daogou}','{$_newDaogou}')";
            }
        }
        if ($global['help'] != $help) {
            $_help = $wpdb->escape($home . $global['help'] . '/');
            $where[] = " option_value LIKE '%" . ($_help) . "%' ";
            $search[] = $_help;
            $_newHelp = $wpdb->escape($home . $daogou . '/');
            $replace[] = $_newHelp;
            if (!empty($REPLACE)) {
                $REPLACE = " REPLACE({$REPLACE},'{$_help}','{$_newHelp}')";
            } else {
                $REPLACE = " REPLACE(post_content,'{$_help}','{$_newHelp}')";
            }
        }
        if (!empty($where)) {
            $sql = "SELECT * FROM $wpdb->options WHERE (" . implode('OR', $where) . ") AND (option_name like '" . XT_OPTION_PAGE_WIDGETS_PRE . "%' OR option_name like '" . XT_OPTION_PAGE_HTML_PRE . "%');";
            $results = $wpdb->get_results($sql, ARRAY_A);
            if (!empty($results)) {
                foreach ($results as $result) {
                    update_option($result['option_name'], xt_array_replace($search, $replace, maybe_unserialize($result['option_value'])));
                }
            }
            $wpdb->query("UPDATE $wpdb->posts SET post_content = {$REPLACE};");
        }
    }

}

function xt_get_page_header() {
    global $wp_query, $xt_template_name;
    if ($xt_template_name == 'error404') {
        xt_set_404();
    }
    if (empty($xt_template_name)) {
        global $wp_query;
        $xt_template_name = $wp_query->post->ID;
    }
    if (!empty($xt_template_name)) {
        $headerAndFooters = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
        if (!empty($headerAndFooters) && isset($headerAndFooters[$xt_template_name])) {
            $header = $headerAndFooters[$xt_template_name]['header'];
            xt_dynamic_page(xt_init_page($header, '', true));
        } else {
            xt_dynamic_page(xt_init_page('header', '', true));
        }
    }
}

function xt_get_page_body() {
    global $xt_template_name;
    if (!empty($xt_template_name)) {
        if (in_array($xt_template_name, array(
                    'xt-account',
                    'xt-sitemap'
                ))) {
            xt_load_template($xt_template_name);
        } else {
            xt_dynamic_page(xt_init_page($xt_template_name, '', true));
        }
    } else {
        global $wp_query;
        xt_dynamic_page(xt_init_page($wp_query->post->ID, '', true));
    }
}

function xt_get_page_footer() {
    global $xt_template_name;
    if (empty($xt_template_name)) {
        global $wp_query;
        $xt_template_name = $wp_query->post->ID;
    }
    if (!empty($xt_template_name)) {
        $headerAndFooters = get_option(XT_OPTION_PAGE_HEADER_FOOTER);
        if (!empty($headerAndFooters) && isset($headerAndFooters[$xt_template_name])) {
            $footer = $headerAndFooters[$xt_template_name]['footer'];
            xt_dynamic_page(xt_init_page($footer, '', true));
        } else {
            xt_dynamic_page(xt_init_page('footer', '', true));
        }
    }
}

function xt_is_header($page) {
    return strncmp($page, 'header', strlen('header')) === 0;
}

function xt_is_footer($page) {
    return strncmp($page, 'footer', strlen('footer')) === 0;
}

function xt_is_headerOrfooter($page) {
    return xt_is_header($page) || xt_is_footer($page);
}

function xt_index() {
    $index = xt_get_global('index');
    return empty($index) ? '' : $index;
}

function xt_base() {
    $base = xt_get_global('base');
    if (empty($base)) {
        $base = 'share';
    }
    return $base;
}

function xt_base_daogou() {
    $daogou = xt_get_global('daogou');
    if (empty($daogou)) {
        $daogou = 'daogou';
    }
    return xt_get_global('daogou');
}

function xt_base_help() {
    $help = xt_get_global('help');
    return $help ? $help : 'help';
}

if (!function_exists('xt_site_url')) {

    function xt_site_url($uri) {
        return home_url(xt_index() . '/' . xt_base() . '/' . $uri);
    }

}

function xt_url_decode($s) {
    return apply_filters('xt_url_decode', urldecode(urldecode($s)));
}

function xt_platform_authorize_url($platform = '', $state = '', $mode = '') {
    return xt_site_url('login?platform=' . $platform . '&mode=' . $mode . '&state=' . $state);
}

function xt_platform_taobao_authorize_url($state = '', $mode = '') {
    return xt_platform_authorize_url('taobao', $state, $mode);
}

function xt_platform_weibo_authorize_url($state = '', $mode = '') {
    return xt_platform_authorize_url('weibo', $state, $mode);
}

function xt_platform_qq_authorize_url($state = '', $mode = '') {
    return xt_platform_authorize_url('qq', $state, $mode);
}

function xt_jump_url($args) {
    $args = xt_core_jump_params_default($args);
    $args['url'] = base64_encode($args['url']);
    $args['title'] = urlencode($args['title']);
    return xt_site_url('jump-' . implode('-', $args));
}

function xt_core_jump_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 15) {
        $query['id'] = $array[0];
        $query['title'] = xt_url_decode($array[1]);
        $query['url'] = $array[2];
        $query['fx'] = $array[3];
        $query['share'] = $array[4];
        //保留9个扩展参数
        $query['extra_1'] = $array[5];
        $query['extra_2'] = $array[6];
        $query['extra_3'] = $array[7];
        $query['extra_4'] = $array[8];
        $query['extra_5'] = $array[9];
        $query['extra_6'] = $array[10];
        $query['extra_7'] = $array[11];
        $query['extra_8'] = $array[12];
        $query['extra_9'] = $array[13];
        //扩展参数结束
        $query['type'] = $array[14];
    } else {
        $query = xt_core_jump_params_default();
    }
    return apply_filters('xt_core_jump_params', $query);
}

function xt_core_jump_params_default($params = array()) {
    $query = array();
    $query['id'] = '';
    $query['title'] = '';
    $query['url'] = '';
    $query['fx'] = '';
    $query['share'] = '';

    //保留9个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    $query['extra_6'] = '';
    $query['extra_7'] = '';
    $query['extra_8'] = '';
    $query['extra_9'] = '';
    //扩展参数结束
    $query['type'] = 'taobao';
    if ($params) {
        $query = array_merge($query, $params);
        $query['title'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['title'])); //替换-
    }
    return array_slice($query, 0, 15);
}

function xt_get_shares_search_url($query = array()) {
    return xt_site_url('search-' . implode('-', xt_core_share_params_default($query)));
}

function xt_core_share_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 15) {
        $query['from_type'] = $array[0];
        $query['cid'] = $array[1];
        $query['s'] = xt_url_decode($array[2]);
        $query['price'] = $array[3];
        $query['sortOrder'] = $array[4];
        if (empty($query['sortOrder'])) {
            $query['sortOrder'] = 'newest';
        }
        //保留5个扩展参数
        $query['extra_1'] = $array[5];
        $query['extra_2'] = $array[6];
        $query['extra_3'] = $array[7];
        $query['extra_4'] = $array[8];
        $query['extra_5'] = $array[9];
        $query['extra_6'] = $array[10];
        $query['extra_7'] = $array[11];
        $query['extra_8'] = $array[12];
        $query['extra_9'] = $array[13];
        //扩展参数结束
        $query['page'] = $array[14];
    } else {
        $query = xt_core_share_params_default();
    }
    return apply_filters('xt_core_share_params', $query);
}

function xt_core_share_params_default($params = array()) {
    $query = array();
    $query['from_type'] = '';
    $query['cid'] = '';
    $query['s'] = '';
    $query['price'] = '';
    $query['sortOrder'] = 'newest';

    //保留5个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    $query['extra_6'] = '';
    $query['extra_7'] = '';
    $query['extra_8'] = '';
    $query['extra_9'] = '';
    //扩展参数结束
    $query['page'] = 1;
    if ($params) {
        if (isset($params['cid']) && (empty($params['cid']) || absint($params['cid']) == 0)) {
            $params['cid'] = '';
        }
        $query = array_merge($query, $params);
        if (empty($query['sortOrder'])) {
            $query['sortOrder'] = 'newest';
        }
        if (empty($query['page'])) {
            $query['page'] = 1;
        }
        $query['s'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['s'])); //替换-
    }
    return array_slice($query, 0, 15);
}

function xt_get_daogou_search_url($query = array()) {
    return xt_site_url('daogou-' . implode('-', xt_core_daogou_params_default($query)));
}

function xt_core_daogou_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 8) {
        $query['cid'] = $array[0];
        $query['s'] = xt_url_decode($array[1]);
        //保留9个扩展参数
        $query['extra_1'] = $array[2];
        $query['extra_2'] = $array[3];
        $query['extra_3'] = $array[4];
        $query['extra_4'] = $array[5];
        $query['extra_5'] = $array[6];
        //扩展参数结束
        $query['page'] = $array[7];
    } else {
        $query = xt_core_daogou_params_default();
    }
    return apply_filters('xt_core_daogou_params', $query);
}

function xt_core_daogou_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['s'] = '';

    //保留5个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page'] = 1;
    if ($params) {
        $query = array_merge($query, $params);
        $query['s'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['s'])); //替换-
    }
    return array_slice($query, 0, 8);
}

function xt_get_help_search_url($query = array()) {
    return xt_site_url('help-' . implode('-', xt_core_help_params_default($query)));
}

function xt_core_help_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 8) {
        $query['cid'] = $array[0];
        $query['s'] = xt_url_decode($array[1]);
        //保留9个扩展参数
        $query['extra_1'] = $array[2];
        $query['extra_2'] = $array[3];
        $query['extra_3'] = $array[4];
        $query['extra_4'] = $array[5];
        $query['extra_5'] = $array[6];
        //扩展参数结束
        $query['page'] = $array[7];
    } else {
        $query = xt_core_help_params_default();
    }
    return apply_filters('xt_core_help_params', $query);
}

function xt_core_help_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['s'] = '';

    //保留5个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page'] = 1;
    if ($params) {
        $query = array_merge($query, $params);
        $query['s'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['s'])); //替换-
    }
    return array_slice($query, 0, 8);
}

function xt_daogou_item_cat($cid = 0) {
    global $xt_daogou_itemcat;
    if (empty($xt_daogou_itemcat)) {
        if (!empty($cid) && $cid != -1) {
            $term = get_term($cid, 'daogou_category');
            if (!empty($term)) {
                $xt_daogou_itemcat = $term;
            }
        }
    }
    return $xt_daogou_itemcat;
}

function xt_help_item_cat($cid = 0) {
    global $xt_help_itemcat;
    if (empty($xt_help_itemcat)) {
        if (!empty($cid) && $cid != -1) {
            $term = get_term($cid, 'help_category');
            if (!empty($term)) {
                $xt_help_itemcat = $term;
            }
        }
    }
    return $xt_help_itemcat;
}

function xt_get_paipai_search_url($query = array()) {
    return xt_site_url('paipai-' . implode('-', xt_core_paipai_params_default($query)));
}

function xt_core_paipai_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 18) {
        $query['classId'] = $array[0];
        $query['keyWord'] = xt_url_decode($array[1]);
        $query['begPrice'] = $array[2];
        $query['endPrice'] = $array[3];
        $query['orderStyle'] = $array[4];
        $query['crMin'] = $array[5];
        $query['crMax'] = $array[6];
        $query['payType'] = $array[7];
        $query['property'] = $array[8];
        $query['hotClassId'] = $array[9];
        $query['level'] = $array[10];
        $query['materialId'] = $array[11];
        $query['activeId'] = $array[12];
        $query['address'] = $array[13];
        $query['adPosition'] = $array[14];
        $query['hongbaoTag'] = $array[15];
        $query['productId'] = $array[16];

        //扩展参数结束
        $query['pageIndex'] = $array[17];

        if ((empty($query['classId']) || $query['classId'] == 0) && empty($query['keyWord'])) {
            $query['classId'] = 20501;
        }
        if (empty($query['orderStyle'])) {
            $query['orderStyle'] = '128';
        }
        if (intval($query['pageIndex']) == 0) {
            $query['pageIndex'] = 1;
        }
        if (intval($query['pageIndex']) > 100) {
            $query['pageIndex'] = 100;
        }
    } else {
        $query = xt_core_paipai_params_default();
    }
    return apply_filters('xt_core_paipai_params', $query);
}

function xt_core_paipai_params_default($params = array()) {
    $query = array();
    $query['classId'] = '';
    $query['keyWord'] = '';
    $query['begPrice'] = '';
    $query['endPrice'] = '';
    $query['orderStyle'] = '';
    $query['crMin'] = '';
    $query['crMax'] = '';
    $query['payType'] = '';
    $query['property'] = '';
    $query['hotClassId'] = '';
    $query['level'] = '';
    $query['materialId'] = '';
    $query['activeId'] = '';
    $query['address'] = '';
    $query['adPosition'] = '';
    $query['hongbaoTag'] = '';
    $query['productId'] = '';

    //扩展参数结束
    $query['pageIndex'] = 1;
    if ($params) {
        $query = array_merge($query, $params);
        $query['keyWord'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyWord'])); //替换-
    }
    if ((empty($query['classId']) || $query['classId'] == 0) && empty($query['keyWord'])) {
        $query['classId'] = 20501;
    }
    if (empty($query['orderStyle'])) {
        $query['orderStyle'] = '128';
    }
    if ($query['pageIndex'] != '%#%' && intval($query['pageIndex']) == 0) {
        $query['pageIndex'] = 1;
    }
    if ($query['pageIndex'] != '%#%' && intval($query['pageIndex']) > 100) {
        $query['pageIndex'] = 100;
    }
    return array_slice($query, 0, 18);
}

function xt_get_temai_search_url($query = array()) {
    return xt_site_url('temai-' . implode('-', xt_core_temai_params_default($query)));
}

function xt_core_temai_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 8) {
        $query['cat'] = $array[0];
        $query['sort'] = $array[1];
        //保留5个扩展参数
        $query['extra_1'] = $array[2];
        $query['extra_2'] = $array[3];
        $query['extra_3'] = $array[4];
        $query['extra_4'] = $array[5];
        $query['extra_5'] = $array[6];
        //扩展参数结束
        $query['page_no'] = $array[7];
        if (empty($query['cat'])) {
            $query['cat'] = XT_TAOBAO_CAT_TEMAI;
        }
        if (intval($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (intval($query['page_no']) > 100) {
            $query['page_no'] = 100;
        }
    } else {
        $query = xt_core_temai_params_default();
    }
    return apply_filters('xt_core_temai_params', $query);
}

function xt_core_temai_params_default($params = array()) {
    $query = array();
    $query['cat'] = '';
    $query['sort'] = 's';

    //保留5个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        $query = array_merge($query, $params);
    }
    if (isset($query['cat']) && (empty($query['cat']) || absint($query['cat']) == 0)) {
        $query['cat'] = XT_TAOBAO_CAT_TEMAI;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) > 100) {
        $query['page_no'] = 100;
    }
    return array_slice($query, 0, 8);
}

function xt_get_coupon_search_url($query = array()) {
    return xt_site_url('coupon-' . implode('-', xt_core_coupon_params_default($query)));
}

function xt_core_coupon_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 12) {
        $query['cid'] = $array[0];
        $query['keyword'] = xt_url_decode($array[1]);
        $query['sort'] = $array[2];
        $query['start_commissionRate'] = $array[3];
        $query['end_commissionRate'] = $array[4];
        $query['shop_type'] = $array[5];
        //保留5个扩展参数
        $query['extra_1'] = $array[6];
        $query['extra_2'] = $array[7];
        $query['extra_3'] = $array[8];
        $query['extra_4'] = $array[9];
        $query['extra_5'] = $array[10];
        //扩展参数结束
        $query['page_no'] = $array[11];
        if (empty($query['cid']) && empty($query['keyword'])) {
            $query['cid'] = 16;
        }
        if (intval($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (intval($query['page_no']) > 99) {
            $query['page_no'] = 99;
        }
    } else {
        $query = xt_core_coupon_params_default();
    }
    return apply_filters('xt_core_coupon_params', $query);
}

function xt_core_coupon_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['keyword'] = '';
    $query['sort'] = '';
    $query['start_commissionRate'] = '';
    $query['end_commissionRate'] = '';
    $query['shop_type'] = '';

    //保留9个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        if (isset($params['cid']) && (empty($params['cid']) || absint($params['cid']) == 0)) {
            $params['cid'] = '';
        }
        $query = array_merge($query, $params);
        $query['keyword'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyword'])); //替换-
    }
    if (empty($query['cid']) && empty($query['keyword'])) {
        $query['cid'] = 16;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) > 99) {
        $query['page_no'] = 99;
    }
    return array_slice($query, 0, 12);
}

function xt_get_shop_search_url($query = array()) {
    return xt_site_url('shop-' . implode('-', xt_core_shop_params_default($query)));
}

function xt_core_shop_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 13) {
        $query['cid'] = $array[0];
        $query['keyword'] = xt_url_decode($array[1]);
        $query['start_credit'] = $array[2];
        $query['end_credit'] = $array[3];
        $query['sort_field'] = $array[4];
        $query['sort_type'] = $array[5];
        $query['only_mall'] = $array[6];
        //保留5个扩展参数
        $query['extra_1'] = $array[7];
        $query['extra_2'] = $array[8];
        $query['extra_3'] = $array[9];
        $query['extra_4'] = $array[10];
        $query['extra_5'] = $array[11];
        //扩展参数结束
        $query['page_no'] = $array[12];
        if (empty($query['cid']) && empty($query['keyword'])) {
            $query['cid'] = 14;
        }
        if (intval($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (intval($query['page_no']) > 10) {
            $query['page_no'] = 10;
        }
    } else {
        $query = xt_core_taobao_params_default();
    }
    return apply_filters('xt_core_shop_params', $query);
}

function xt_core_shop_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['keyword'] = '';
    $query['start_credit'] = '';
    $query['end_credit'] = '';
    $query['sort_field'] = '';
    $query['sort_type'] = '';
    $query['only_mall'] = '';

    //保留9个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        if (isset($params['cid']) && (empty($params['cid']) || absint($params['cid']) == 0)) {
            $params['cid'] = '';
        }
        $query = array_merge($query, $params);
        $query['keyword'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyword'])); //替换-
    }
    if (empty($query['cid']) && empty($query['keyword'])) {
        $query['cid'] = 14;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) > 10) {
        $query['page_no'] = 10;
    }
    return array_slice($query, 0, 13);
}

function xt_get_taobao_search_url($query = array()) {
    return xt_site_url('taobao-' . implode('-', xt_core_taobao_params_default($query)));
}

function xt_core_taobao_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 18) {
        $query['cid'] = $array[0];
        $query['keyword'] = xt_url_decode($array[1]);
        $query['start_price'] = $array[2];
        $query['end_price'] = $array[3];
        $query['sort'] = $array[4];
        $query['start_commissionRate'] = $array[5];
        $query['end_commissionRate'] = $array[6];
        $query['mall_item'] = $array[7];
        //保留5个扩展参数
        $query['extra_1'] = $array[8];
        $query['extra_2'] = $array[9];
        $query['extra_3'] = $array[10];
        $query['extra_4'] = $array[11];
        $query['extra_5'] = $array[12];
        $query['extra_6'] = $array[13];
        $query['extra_7'] = $array[14];
        $query['extra_8'] = $array[15];
        $query['extra_9'] = $array[16];
        //扩展参数结束
        $query['page_no'] = $array[17];
        if (empty($query['cid']) && empty($query['keyword'])) {
            $query['cid'] = 16;
        }
        if (intval($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (intval($query['page_no']) > 10) {
            $query['page_no'] = 10;
        }
    } else {
        $query = xt_core_taobao_params_default();
    }
    return apply_filters('xt_core_taobao_params', $query);
}

function xt_core_taobao_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['keyword'] = '';
    $query['start_price'] = '';
    $query['end_price'] = '';
    $query['sort'] = '';
    $query['start_commissionRate'] = '';
    $query['end_commissionRate'] = '';
    $query['mall_item'] = '';

    //保留9个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    $query['extra_6'] = '';
    $query['extra_7'] = '';
    $query['extra_8'] = '';
    $query['extra_9'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        if (isset($params['cid']) && (empty($params['cid']) || absint($params['cid']) == 0)) {
            $params['cid'] = '';
        }
        $query = array_merge($query, $params);
        $query['keyword'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyword'])); //替换-
    }
    if (empty($query['cid']) && empty($query['keyword'])) {
        $query['cid'] = 16;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && intval($query['page_no']) > 10) {
        $query['page_no'] = 10;
    }
    return array_slice($query, 0, 18);
}

function xt_get_albums_search_url($query = array()) {
    return xt_site_url('album-' . implode('-', xt_core_album_params_default($query)));
}

function xt_core_album_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 9) {
        $query['cid'] = $array[0];
        $query['s'] = xt_url_decode($array[1]);
        $query['sortOrder'] = $array[2];
        if (empty($query['sortOrder'])) {
            $query['sortOrder'] = 'newest';
        }
        //保留5个扩展参数
        $query['extra_1'] = $array[3];
        $query['extra_2'] = $array[4];
        $query['extra_3'] = $array[5];
        $query['extra_4'] = $array[6];
        $query['extra_5'] = $array[7];
        //扩展参数结束
        $query['page'] = $array[8];
    } else {
        $query = xt_core_album_params_default();
    }
    return apply_filters('xt_core_album_params', $query);
}

function xt_core_album_params_default($params = array()) {
    $query = array();
    $query['cid'] = '';
    $query['s'] = '';
    $query['sortOrder'] = 'newest';

    //保留5个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page'] = 1;
    if ($params) {
        if (isset($params['cid']) && (empty($params['cid']) || absint($params['cid']) == 0)) {
            $params['cid'] = '';
        }
        $query = array_merge($query, $params);
        if (empty($query['sortOrder'])) {
            $query['sortOrder'] = 'newest';
        }
        if (empty($query['page'])) {
            $query['page'] = 1;
        }
        $query['s'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['s'])); //替换-
    }
    return array_slice($query, 0, 9);
}

function xt_get_bijia_search_url($query = array()) {
    return xt_site_url('bijia-' . implode('-', xt_core_bijia_params_default($query)));
}

function xt_core_bijia_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 12) {
        $query['catid'] = $array[0];
        $query['keyword'] = xt_url_decode($array[1]);
        $query['minprice'] = $array[2];
        $query['maxprice'] = $array[3];
        $query['orderby'] = $array[4];
        $query['webid'] = $array[5];
        //保留5个扩展参数
        $query['extra_1'] = $array[6];
        $query['extra_2'] = $array[7];
        $query['extra_3'] = $array[8];
        $query['extra_4'] = $array[9];
        $query['extra_5'] = $array[10];
        //扩展参数结束
        $query['page_no'] = $array[11];
        if (empty($query['catid']) && empty($query['keyword'])) {
            $query['catid'] = -1;
        }
        if (empty($query['orderby'])) {
            $query['orderby'] = 3;
        }
        if (absint($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (absint($query['page_no']) > 100) {
            $query['page_no'] = 100;
        }
    } else {
        $query = xt_core_bijia_params_default();
    }
    return apply_filters('xt_core_bijia_params', $query);
}

function xt_core_bijia_params_default($params = array()) {
    $query = array();
    $query['catid'] = '';
    $query['keyword'] = '';
    $query['minprice'] = '';
    $query['maxprice'] = '';
    $query['orderby'] = 3;
    $query['webid'] = '';

    //保留9个扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        if (isset($params['catid']) && (empty($params['catid']) || absint($params['catid']) == 0)) {
            $params['catid'] = '-1';
        }
        $query = array_merge($query, $params);
        $query['keyword'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyword'])); //替换-
    }
    if ((empty($query['catid']) && empty($query['keyword'])) || $query['catid'] == -1) {
        $query['catid'] = XT_BIJIA_CATEGORY_DEFAULT;
    }
    if (empty($query['orderby'])) {
        $query['orderby'] = 3;
    }
    if ($query['page_no'] != '%#%' && absint($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && absint($query['page_no']) > 100) {
        $query['page_no'] = 100;
    }
    return array_slice($query, 0, 12);
}

function xt_get_tuan_search_url($query = array()) {
    return xt_site_url('tuan-' . implode('-', xt_core_tuan_params_default($query)));
}

function xt_core_tuan_params($str) {
    $query = array();
    $array = explode('-', $str);
    if (count($array) == 12) {
        $query['catid'] = $array[0];
        $query['city_id'] = $array[1];
        $query['keyword'] = xt_url_decode($array[2]);
        $query['price'] = $array[3];
        $query['orderby'] = $array[4];
        $query['region_id'] = $array[5];
        //扩展参数
        $query['extra_1'] = $array[6];
        $query['extra_2'] = $array[7];
        $query['extra_3'] = $array[8];
        $query['extra_4'] = $array[9];
        $query['extra_5'] = $array[10];
        //扩展参数结束
        $query['page_no'] = $array[11];
        if (empty($query['city_id'])) {
            $query['city_id'] = XT_CITY_DEFAULT;
        }
        if (empty($query['orderby'])) {
            $query['orderby'] = 'desc,bought';
        }
        if (absint($query['page_no']) == 0) {
            $query['page_no'] = 1;
        }
        if (absint($query['page_no']) > 100) {
            $query['page_no'] = 100;
        }
    } else {
        $query = xt_core_tuan_params_default();
    }
    return apply_filters('xt_core_tuan_params', $query);
}

function xt_core_tuan_params_default($params = array()) {
    $query = array();
    $query['catid'] = '';
    $query['city_id'] = '';
    $query['keyword'] = '';
    $query['price'] = '';
    $query['orderby'] = 'desc,bought';
    $query['region_id'] = '';
    //保留扩展参数
    $query['extra_1'] = '';
    $query['extra_2'] = '';
    $query['extra_3'] = '';
    $query['extra_4'] = '';
    $query['extra_5'] = '';
    //扩展参数结束
    $query['page_no'] = 1;
    if ($params) {
        $query = array_merge($query, $params);
        $query['keyword'] = urlencode(str_replace(array(
                    '-',
                    '/'
                        ), array(
                    ' ',
                    ' '
                        ), $query['keyword'])); //替换-
    }
    if (empty($query['city_id'])) {
        $query['city_id'] = XT_CITY_DEFAULT;
    }
    if (empty($query['orderby'])) {
        $query['orderby'] = 'desc,bought';
    }
    if ($query['page_no'] != '%#%' && absint($query['page_no']) == 0) {
        $query['page_no'] = 1;
    }
    if ($query['page_no'] != '%#%' && absint($query['page_no']) > 100) {
        $query['page_no'] = 100;
    }
    return array_slice($query, 0, 12);
}

function xt_array_export($array, $level = 0, $array_key = '') {
    if (!is_array($array)) {
        return "'" . $array . "'";
    }
    //    if (is_array($array) && function_exists('var_export')) {
    //        exit('var_export');
    //        return var_export($array, true);
    //    }

    $space = '';
    for ($i = 0; $i <= $level; $i++) {
        $space .= "";
    }
    $evaluate = "Array$space(";
    $comma = $space;
    if (is_array($array)) {
        foreach ($array as $key => $val) {
            $key = is_string($key) ? '\'' . addcslashes($key, '\'\\') . '\'' : $key;
            $val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\'' . addcslashes($val, '\'\\') . '\'' : $val;
            if (is_array($val)) {
                if (!empty($array_key)) {
                    $key = $val[$array_key];
                }
                $evaluate .= "$comma$key=>" . xt_array_export($val, $level + 1);
            } else {
                $evaluate .= "$comma$key=>$val";
            }
            $comma = ",$space";
        }
    }
    $evaluate .= "$space)";
    return $evaluate;
}

function xt_search() {
    return array(
        'share' => array(
            'title' => '分享',
            'placeholder' => '请输入要搜索的分享关键词'
        ),
        'album' => array(
            'title' => '专辑',
            'placeholder' => '请输入要搜索的专辑关键词'
        ),
        'taobao' => array(
            'title' => '淘宝',
            'placeholder' => '输入关键词或粘贴宝贝网址，如：http://detail.tmall.com/item.htm?id=14280494531'
        ),
        'shop' => array(
            'title' => '店铺',
            'placeholder' => '请输入掌柜旺旺名或相关店铺信息'
        ),
        'paipai' => array(
            'title' => '拍拍',
            'placeholder' => '输入关键词或粘贴宝贝网址，如：http://auction1.paipai.com/4B45340500000000040100001CB19399'
        ),
        'bijia' => array(
            'title' => '全网',
            'placeholder' => '请输入要搜索的商品关键词'
        ),
        'tuan' => array(
            'title' => '团购',
            'placeholder' => '请输入要搜索的团购关键词'
        )
    );
}

function xt_credit_taobao() {
    return array(
        array('name' => '5goldencrown', 'title' => '五黄冠'),
        array('name' => '4goldencrown', 'title' => '四黄冠'),
        array('name' => '3goldencrown', 'title' => '三黄冠'),
        array('name' => '2goldencrown', 'title' => '两黄冠'),
        array('name' => '1goldencrown', 'title' => '一黄冠'),
        array('name' => '5crown', 'title' => '五冠'),
        array('name' => '4crown', 'title' => '四冠'),
        array('name' => '3crown', 'title' => '三冠'),
        array('name' => '2crown', 'title' => '两冠'),
        array('name' => '1crown', 'title' => '一冠'),
        array('name' => '5diamond', 'title' => '五钻'),
        array('name' => '4diamond', 'title' => '四钻'),
        array('name' => '3diamond', 'title' => '三钻'),
        array('name' => '2diamond', 'title' => '两钻'),
        array('name' => '1diamond', 'title' => '一钻'),
        array('name' => '5heart', 'title' => '五心'),
        array('name' => '4heart', 'title' => '四心'),
        array('name' => '3heart', 'title' => '三心'),
        array('name' => '2heart', 'title' => '两心'),
        array('name' => '1heart', 'title' => '一心')
    );
}

function xt_sort_temai() {
    global $xt_sort_temai;
    if (empty($xt_sort_temai)) {
        $xt_sort_temai = array(
            's' => array(
                'title' => '人气排序',
                'seo' => '人气最高'
            ),
            'p' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            'pd' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            ),
            'd' => array(
                'title' => '月销量从高到低',
                'seo' => '月销量最高'
            ),
            'pt' => array(
                'title' => '按发布时间排序',
                'seo' => '最新发布'
            )
        );
    }
    return $xt_sort_temai;
}

function xt_sort_tuan() {
    global $xt_sort_tuan;
    if (empty($xt_sort_tuan)) {
        $xt_sort_tuan = array(
            'desc,bought' => array(
                'title' => '购买人数最多',
                'seo' => '销量最高'
            ),
            'desc,curPrice' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            ),
            'asc,curPrice' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            'asc,rebate' => array(
                'title' => '折扣从高到低',
                'seo' => '最优惠'
            )
        );
    }
    return $xt_sort_tuan;
}

function xt_sort_bijia() {
    global $xt_sort_bijia;
    if (empty($xt_sort_bijia)) {
        $xt_sort_bijia = array(
            '3' => array(
                'title' => '相关程度排序',
                'seo' => ''
            ),
            '1' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            '2' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            )
        );
    }
    return $xt_sort_bijia;
}

function xt_sort_coupon() {
    global $xt_sort_coupon;
    if (empty($xt_sort_coupon)) {
        $xt_sort_coupon = array(
            'price_desc' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            ),
            'price_asc' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            'credit_desc' => array(
                'title' => '信用等级从高到低',
                'seo' => '信用最好'
            ),
            'commissionRate_desc' => array(
                'title' => '佣金比率从高到低',
                'seo' => ''
            ),
            'volume_desc' => array(
                'title' => '成交量成高到低',
                'seo' => '销售最多'
            ),
        );
    }
    return $xt_sort_coupon;
}

function xt_sort_taobao() {
    global $xt_sort_taobao;
    if (empty($xt_sort_taobao)) {
        $xt_sort_taobao = array(
            'price_desc' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            ),
            'price_asc' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            'credit_desc' => array(
                'title' => '信用等级从高到低',
                'seo' => '信用最好'
            ),
            'commissionRate_desc' => array(
                'title' => '佣金比率从高到低',
                'seo' => ''
            ),
            'commissionNum_desc' => array(
                'title' => '成交量成高到低',
                'seo' => '销售最多'
            ),
        );
    }
    return $xt_sort_taobao;
}

function xt_sort_paipai() {
    global $xt_sort_paipai;
    if (empty($xt_sort_paipai)) {
        $xt_sort_paipai = array(
            '88' => array(
                'title' => 'CPS默认排序',
                'seo' => ''
            ),
            '89' => array(
                'title' => 'CPS商品销量排序',
                'seo' => '销售最多'
            ),
            '6' => array(
                'title' => '价格从低到高',
                'seo' => '最便宜'
            ),
            '7' => array(
                'title' => '价格从高到低',
                'seo' => '最贵'
            ),
            '9' => array(
                'title' => '信用从高到低',
                'seo' => '信用最好'
            ),
            '11' => array(
                'title' => '浏览量从高到低',
                'seo' => '浏览最多'
            ),
            '21' => array(
                'title' => '收藏量从高到低',
                'seo' => '收藏最多'
            ),
            '33' => array(
                'title' => '佣金从高到低',
                'seo' => ''
            )
        );
    }
    return $xt_sort_paipai;
}

function xt_property_paipai() {
    global $xt_property_paipai;
    if (empty($xt_property_paipai)) {
        $xt_property_paipai = array(
            '128' => array(
                'title' => '优质商品过滤',
                'seo' => '优质'
            ),
            '1' => array(
                'title' => '大卖场商品过滤',
                'seo' => '大卖场'
            ),
            '2' => array(
                'title' => '店铺推荐位商品过滤',
                'seo' => '店铺推荐'
            ),
            '4' => array(
                'title' => 'Qzone商品过滤',
                'seo' => 'Qzone'
            ),
            '8' => array(
                'title' => '网游7*24小时过滤',
                'seo' => '网游7*24小时'
            ),
            '16' => array(
                'title' => '免运费商品过滤',
                'seo' => '免运费'
            ),
            '32' => array(
                'title' => '移动电商商品标志',
                'seo' => '移动'
            ),
            '64' => array(
                'title' => '红包商品过滤',
                'seo' => '红包'
            ),
            '256' => array(
                'title' => '会员商品过滤[可以判断是否商城用户]',
                'seo' => '商城'
            ),
            '512' => array(
                'title' => '7天包退换诚保商品过滤',
                'seo' => '7天包退换诚保'
            ),
            '1024' => array(
                'title' => '14天包换诚保商品过滤',
                'seo' => '14天包换诚保'
            ),
            '2048' => array(
                'title' => 'QQVideo商品过滤',
                'seo' => 'QQVideo'
            ),
            '4096' => array(
                'title' => '收藏商品过滤',
                'seo' => '收藏'
            ),
            '0x2000' => array(
                'title' => '裳品廊认证商品',
                'seo' => '裳品廊'
            ),
            '0x4000' => array(
                'title' => '新手机商品',
                'seo' => '新手机'
            ),
            '0x8000' => array(
                'title' => '闪电发货商品',
                'seo' => '闪电发货'
            ),
            '0x10000' => array(
                'title' => '橱窗商品',
                'seo' => '橱窗'
            ),
            '0x20000' => array(
                'title' => '假1赔3商品',
                'seo' => '假1赔3'
            ),
            '0x40000' => array(
                'title' => '手机“官“字',
                'seo' => '手机'
            ),
            '0x80000' => array(
                'title' => '促销信息',
                'seo' => '促销'
            ),
            '0x100000' => array(
                'title' => '快冲入口白名单',
                'seo' => ''
            ),
            '0x200000' => array(
                'title' => '商品有赠品',
                'seo' => '送赠品'
            ),
            '0x400000' => array(
                'title' => '商品有集分宝',
                'seo' => '送集分宝'
            ),
            '0x800000' => array(
                'title' => '折扣',
                'seo' => '折扣'
            ),
            '0x1000000' => array(
                'title' => '尚品会',
                'seo' => '尚品会'
            ),
            '0x2000000' => array(
                'title' => '有发票商品',
                'seo' => '发票'
            ),
            '0x4000000' => array(
                'title' => '参加促销活动的商品',
                'seo' => '促销'
            ),
            '0x8000000' => array(
                'title' => '彩钻商品',
                'seo' => '彩钻商品'
            ),
        );
    }
    return $xt_property_paipai;
}

function xt_layout_convert($layout) {
    if (empty($layout)) {
        $layout = array();
    }
    if (is_array($layout) && !isset($layout['seos'])) {
        $layout = array(
            'seos' => array(
                'title' => '',
                'keywords' => '',
                'description' => ''
            ),
            'layouts' => $layout
        );
    }
    return $layout;
}

function xt_cache_covert($cache) {
    return $cache;
}

function xt_taobao_temai_cats() {
    return array(
        '服饰' => 50101034,
        '鞋包' => 50101011,
        '时尚' => 50100986,
        '运动' => 50101089,
        '居家' => 50101115,
        '其他' => 50101133
    );
}

if (!function_exists('xt_widget_category_taobao')) {

    function xt_widget_category_taobao($cat = '') {
        global $xt_market_taobao;
        if (empty($xt_market_taobao)) {
            $xt_market_taobao = include ('data-taobao.php');
        }

        if (!empty($cat)) {
            if (isset($xt_market_taobao[$cat])) {
                return apply_filters('xt_widget_category_taobao', $xt_market_taobao[$cat]);
            }
            return array();
        }

        return $xt_market_taobao;
    }

}
if (!function_exists('xt_dropdown_categories')) {

    function xt_dropdown_categories($args = '') {
        return wp_dropdown_categories($args);
    }

}

if (!function_exists('xt_dropdown_daogou_categories')) {

    function xt_dropdown_daogou_categories($args = array('taxonomy' => 'daogou_category')) {
        return wp_dropdown_categories(array_merge($args, array('taxonomy' => 'daogou_category')));
    }

}
if (!function_exists('xt_dropdown_help_categories')) {

    function xt_dropdown_help_categories($args = array('taxonomy' => 'help_category')) {
        return wp_dropdown_categories(array_merge($args, array('taxonomy' => 'help_category')));
    }

}

if (!function_exists('xt_get_option')) {

    function xt_get_option($key) {
        return get_option($key);
    }

}
if (!function_exists('xt_update_option')) {

    function xt_update_option($key, $value) {
        return update_option($key, $value);
    }

}
if (!function_exists('xt_add_option')) {

    function xt_add_option($key, $value = '', $deprecated = '', $autoload = 'yes') {
        return add_option($key, $value, $deprecated, $autoload);
    }

}

function xt_get_option_page($page) {
    $result = array();
    $result['widgets'] = xt_get_option(XT_OPTION_PAGE_WIDGETS_PRE . $page);
    $result['layouts'] = xt_get_option(XT_OPTION_PAGE_PRE . $page);
    return $result;
}

if (!function_exists('xt_design_pages')) {

    function xt_design_pages() {
        global $xt_design_syspages;
        if (empty($xt_design_syspages)) {
            $xt_design_syspages = array(
                'home' => array(
                    'id' => 'home',
                    'title' => '首页',
                    'preview' => home_url()
                ),
                'error404' => array(
                    'id' => 'error404',
                    'title' => '404错误页面',
                    'preview' => home_url('404')
                ),
                'shares' => array(
                    'id' => 'shares',
                    'title' => '分享列表页',
                    'preview' => xt_get_shares_search_url(),
                    'layouts_edit' => 0
                ),
                'share' => array(
                    'id' => 'share',
                    'title' => '分享详情页',
                    'preview' => xt_site_url('id-' . 'SHAREID')
                ),
                'albums' => array(
                    'id' => 'albums',
                    'title' => '专辑列表页',
                    'preview' => xt_get_albums_search_url(),
                    'layouts_edit' => 0
                ),
                'album' => array(
                    'id' => 'album',
                    'title' => '专辑详情页',
                    'preview' => xt_site_url('aid-' . 'ALBUMID'),
                    'layouts_edit' => 0
                ),
                'users' => array(
                    'id' => 'users',
                    'title' => '会员列表页',
                    'preview' => ''
                ),
                'user' => array(
                    'id' => 'user',
                    'title' => '会员详情页',
                    'preview' => xt_site_url('uid-USERID'),
                    'layouts_edit' => 0,
                    'widgets_edit' => 0
                ),
                'taobaos' => array(
                    'id' => 'taobaos',
                    'title' => '淘宝搜索页',
                    'preview' => xt_get_taobao_search_url(),
                    'layouts_edit' => 0
                ),
                'taobao' => array(
                    'id' => 'taobao',
                    'title' => '淘宝商品详情页',
                    'preview' => xt_site_url('taobao-NUMIID'),
                    'layouts_edit' => 0
                ),
                'shops' => array(
                    'id' => 'shops',
                    'title' => '淘宝店铺搜索页',
                    'preview' => xt_get_shop_search_url(),
                    'layouts_edit' => 0
                ),
                'paipais' => array(
                    'id' => 'paipais',
                    'title' => '拍拍搜索页',
                    'preview' => xt_get_paipai_search_url(),
                    'layouts_edit' => 0
                ),
                'bijias' => array(
                    'id' => 'bijias',
                    'title' => '全网搜索页',
                    'preview' => xt_get_bijia_search_url(),
                    'layouts_edit' => 0
                ),
                'tuans' => array(
                    'id' => 'tuans',
                    'title' => '团购搜索页',
                    'preview' => xt_get_tuan_search_url(),
                    'layouts_edit' => 0
                ),
                'temais' => array(
                    'id' => 'temais',
                    'title' => '淘宝特卖搜索页',
                    'preview' => xt_get_temai_search_url(),
                    'layouts_edit' => 0
                ),
                'coupons' => array(
                    'id' => 'coupons',
                    'title' => '淘宝折扣搜索页',
                    'preview' => xt_get_coupon_search_url(),
                    'layouts_edit' => 0
                ),
                'daogous' => array(
                    'id' => 'daogous',
                    'title' => '导购文章搜索页',
                    'preview' => xt_get_daogou_search_url(),
                    'layouts_edit' => 0
                ),
                'daogou' => array(
                    'id' => 'daogou',
                    'title' => '导购文章详情页',
                    'preview' => '',
                    'layouts_edit' => 0
                ),
                'helps' => array(
                    'id' => 'helps',
                    'title' => '帮助文章列表页',
                    'preview' => xt_get_help_search_url(),
                    'layouts_edit' => 0
                ),
                'help' => array(
                    'id' => 'help',
                    'title' => '帮助详情页',
                    'preview' => '',
                    'layouts_edit' => 0
                ),
                'brands' => array(
                    'id' => 'brands',
                    'title' => '天猫品牌街',
                    'preview' => xt_site_url('brands'),
                    'layouts_edit' => 0
                ),
                'stars' => array(
                    'id' => 'stars',
                    'title' => '明星店',
                    'preview' => xt_site_url('stars'),
                    'layouts_edit' => 0
                ),
                'activities' => array(
                    'id' => 'activities',
                    'title' => '特卖活动',
                    'preview' => xt_site_url('activities'),
                    'layouts_edit' => 0
                ),
                'taoquan' => array(
                    'id' => 'taoquan',
                    'title' => '淘宝优惠券',
                    'preview' => xt_site_url('taoquan'),
                    'layouts_edit' => 0
                ),
                'malls' => array(
                    'id' => 'malls',
                    'title' => '商城',
                    'preview' => xt_site_url('malls'),
                    'layouts_edit' => 0
                ),
                'invite' => array(
                    'id' => 'invite',
                    'title' => '邀请页',
                    'preview' => xt_site_url('invite-USERID'),
                    'layouts_edit' => 0
                )
            );
        }
        return $xt_design_syspages;
    }

}
if (!class_exists('XT_Widget')) {

    class XT_Widget {

        var $id_base; // Root id for all widgets of this type.
        var $name; // Name for this widget type.
        var $widget_options; // Option array passed to xt_register_page_widget()
        var $control_options; // Option array passed to
        // xt_register_widget_control()
        var $number = false; // Unique ID number of the current instance.
        var $id = false; // Unique ID string of the current instance
        // (id_base-number)
        var $updated = false; // Set true when we update the data after a POST

        // submit - makes sure we don't do it twice.
        // Member functions that you must over-ride.

        function widget($args, $instance) {
            die('function XT_Widget::widget() must be over-ridden in a sub-class.');
        }

        // Functions you'll need to call.
        function XT_Widget($id_base = false, $name, $widget_options = array(), $control_options = array()) {
            XT_Widget :: __construct($id_base, $name, $widget_options, $control_options);
        }

        function __construct($id_base = false, $name, $widget_options = array(), $control_options = array()) {
            $this->id_base = empty($id_base) ? preg_replace('/(xt_)?widget_/', '', strtolower(get_class($this))) : strtolower($id_base);
            $this->name = $name;
            $this->option_name = 'xt_widget_' . $this->id_base;
            $this->widget_options = wp_parse_args($widget_options, array(
                'classname' => $this->option_name
                    ));
            $this->control_options = wp_parse_args($control_options, array(
                'id_base' => $this->id_base
                    ));
        }

        // Private Functions. Don't worry about these.
        function _register() {
            global $xt_page_layouts;
            $settings = array();
            if (!empty($xt_page_layouts)) {
                foreach ($xt_page_layouts as $layout) {
                    $spans = $layout['layout'];
                    if (!empty($spans) && is_array($spans)) {
                        foreach ($spans as $span => $_widgets) {
                            if (!empty($_widgets))
                                $settings = array_merge($settings, array_keys($_widgets));
                        }
                    }
                }
            }

            $empty = true;
            $number = 1;
            if (is_array($settings)) {
                foreach ($settings as $setting) {
                    $ids = explode('-', $setting);
                    if ($ids[0] == $this->id_base) {
                        $this->_set($ids[1]);
                        $this->_register_one($ids[1]);
                        $empty = false;
                        $number++;
                    }
                }
            }
            if ($empty) {
                // If there are none, we register the widget's existence with a
                // generic template
                $this->_set(1);
                $this->_register_one();
            }
        }

        function _set($number) {
            $this->number = $number;
            $this->id = $this->id_base . '-' . $number;
        }

        function _get_display_callback() {
            return array(
                & $this,
                'display_callback'
            );
        }

        /**
         * Generate the actual widget content.
         * Just finds the instance and calls widget().
         * Do NOT over-ride this function.
         */
        function display_callback($args, $widget_args = 1) {
            if (is_numeric($widget_args))
                $widget_args = array(
                    'number' => $widget_args
                );
            global $xt_current_widget;
            $xt_current_widget = $this->id_base;
            $widget_args = wp_parse_args($widget_args, array(
                'number' => -1
                    ));
            $this->_set($widget_args['number']);
            $instance = xt_get_page_widgets_setting($this->id_base . '-' . $this->number);

            if (!empty($instance)) {
                // filters the widget's settings, return false to stop
                // displaying the widget
                $instance = apply_filters('xt_widget_display_callback', $instance, $this, $args);
                if (false !== $instance)
                    $this->widget($args, $instance);
            }
        }

        /**
         * Helper function: Registers a single instance.
         */
        function _register_one($number = -1) {
            xt_register_page_widget($this->id, $this->name, $this->_get_display_callback(), $this->widget_options, array(
                'number' => $number
            ));
        }

    }

}

/**
 * Singleton that registers and instantiates XT_Widget classes.
 */
class XT_Widget_Factory {

    var $widgets = array();

    function XT_Widget_Factory() {
        add_action('xt_widgets_init', array(
            & $this,
            '_register_widgets'
                ), 100);
    }

    function register($widget_class) {
        $this->widgets[$widget_class] = new $widget_class ();
    }

    function unregister($widget_class) {
        if (isset($this->widgets[$widget_class]))
            unset($this->widgets[$widget_class]);
    }

    function _register_widgets() {
        global $xt_registered_widgets;
        $keys = array_keys($this->widgets);
        $registered = array_keys($xt_registered_widgets);
        $registered = array_map('_xt_get_widget_id_base', $registered);

        foreach ($keys as $key) {
            // don't register new widget if old widget with the same id is
            // already registered
            if (in_array($this->widgets[$key]->id_base, $registered, true)) {
                unset($this->widgets[$key]);
                continue;
            }

            $this->widgets[$key]->_register();
        }
    }

}

/* Global Variables */

/**
 *
 * @ignore
 *
 *
 */
global $xt_registered_page, $xt_registered_widgets, $xt_registered_widget_controls, $xt_registered_widget_updates;

/**
 * Stores the pages
 */
$xt_registered_page = array();

/**
 * Stores the registered widgets.
 */
$xt_registered_widgets = array();

/**
 * Stores the registered widget control (options).
 */
$xt_registered_widget_controls = array();
$xt_registered_widget_updates = array();

/**
 * Private
 */
$GLOBALS['_xt_deprecated_widgets_callbacks'] = array();

/* Template tags & API functions */

/**
 * Register a widget
 *
 * Registers a XT_Widget widget
 *
 * @param string $widget_class
 *        	The name of a class that extends XT_Widget
 */
function xt_register_widget($widget_class) {
    global $xt_widget_factory;
    $xt_widget_factory->register($widget_class);
}

/**
 * Unregister a widget
 *
 * Unregisters a XT_Widget widget. Useful for unregistering default widgets.
 * Run within a function hooked to the widgets_init action.
 *
 * @param string $widget_class
 *        	The name of a class that extends XT_Widget
 */
function xt_unregister_widget($widget_class) {
    global $xt_widget_factory;
    $xt_widget_factory->unregister($widget_class);
}

function xt_register_page($args = array()) {
    global $xt_registered_page;
    $defaults = array(
        'name' => '自定义页面',
        'id' => 'home',
        'description' => '',
        'class' => '',
        'before_widget' => '<!--widget start--><div id="%1$s" class="xt-widget %2$s clearfix">',
        'after_widget' => "</div><!--widget end-->",
        'before_title' => '',
        'after_title' => ''
    );
    $page = wp_parse_args($args, $defaults);
    $xt_registered_page = $page;
    return $page['id'];
}

/**
 * Register widget for use in pages.
 *
 * The default widget option is 'classname' that can be override.
 *
 * The function can also be used to unregister widgets when $output_callback
 * parameter is an empty string.
 *
 *
 * @uses $xt_registered_widgets Uses stored registered widgets.
 * @uses $xt_register_widget_defaults Retrieves widget defaults.
 *      
 * @param int|string $id
 *        	Widget ID.
 * @param string $name
 *        	Widget display title.
 * @param callback $output_callback
 *        	Run when widget is called.
 * @param array|string $options
 *        	Optional. Widget Options.
 * @param mixed $params,...
 *        	Widget parameters to add to widget.
 * @return null Will return if $output_callback is empty after removing widget.
 */
function xt_register_page_widget($id, $name, $output_callback, $options = array()) {
    global $xt_registered_widgets, $xt_registered_widget_controls, $xt_registered_widget_updates, $_xt_deprecated_widgets_callbacks;
    $id = strtolower($id);

    if (empty($output_callback)) {
        unset($xt_registered_widgets[$id]);
        return;
    }

    $id_base = _xt_get_widget_id_base($id);

    $defaults = array(
        'classname' => $output_callback
    );
    $options = wp_parse_args($options, $defaults);
    $widget = array(
        'name' => $name,
        'id' => $id,
        'callback' => $output_callback,
        'params' => array_slice(func_get_args(), 4)
    );
    $widget = array_merge($widget, $options);
    if (is_callable($output_callback) && (!isset($xt_registered_widgets[$id]) || did_action('xt_widgets_init'))) {
        $xt_registered_widgets[$id] = $widget;
    }
}

/**
 * Display dynamic page.
 */
function xt_dynamic_page($isDynamic = true) {
    global $xt_current_page, $xt_need_cache;
    if (!$isDynamic) {
        return false;
    }
    $isCache = ($xt_current_page == 'home') || is_numeric($xt_current_page) ? true : false;

    ob_start();

    $did_one = false;

    xt_dynamic_layout();

    $out = ob_get_contents();
    ob_end_clean();
    if (defined('XT_PAGE_EDIT') && XT_PAGE_EDIT) { // no cache
        $isCache = false;
    }
    if ($isCache && $xt_need_cache) {
        $cache = array(
            'data' => $out,
            'time' => time()
        );
        if (!xt_add_option(XT_OPTION_PAGE_HTML_PRE . $xt_current_page, $cache, '', 'no')) {
            xt_update_option(XT_OPTION_PAGE_HTML_PRE . $xt_current_page, $cache);
        }
    }
    echo $out;
    return $did_one;
}

function xt_dynamic_layout() {
    global $xt_page_layouts, $xt_current_page, $xt_current_page_setting;
    $isEdit = defined('XT_PAGE_EDIT') && XT_PAGE_EDIT;
    $xt_current_page_setting = array(
        'layouts_edit' => 1,
        'widgets_edit' => 1
    );
    if ($isEdit) {
        $_sys_pages = xt_design_pages();
        if (isset($_sys_pages[$xt_current_page])) {
            $xt_current_page_setting = array_merge($xt_current_page_setting, $_sys_pages[$xt_current_page]);
        }
    }
    if (xt_is_headerOrfooter($xt_current_page)) {
        $xt_current_page_setting['layouts_edit'] = 0;
        if (empty($xt_page_layouts)) {
            $xt_page_layouts = array(
                array(
                    'name' => 'row-12',
                    'delete' => 1,
                    'layout' => array(
                        'span12' => array(
                        )
                    )
                )
            );
        }
    }
    if (!empty($xt_page_layouts)) {
        $_count = 0;
        foreach ($xt_page_layouts as $layout) {
            if (is_array($layout) && strstr($layout['name'], 'row')) {
                if ($isEdit) {
                    _xt_dynamic_layout_edit($layout);
                } else {
                    _xt_dynamic_layout($layout, $_count);
                }
                $_count++;
            }
        }
    }
    if ($isEdit && ($xt_current_page_setting['layouts_edit'])) {
        echo '<div class="row"><a id="X_AddLayout" class="add-row" href="javascript:;">添加布局单元</a></div>';
    }
}

function _xt_dynamic_layout($layout, $_count = 0) {
    global $xt_page_widgets;
    //$row = xt_is_headerOrfooter($xt_current_page) ? 'row-fluid' : 'row';
    echo '<div class="row-fluid X_Layout ' . $layout['name'] . ' xt-first-child">';
    $spans = $layout['layout'];
    $xt_page_widgets = array();
    if (!empty($spans) && is_array($spans)) {
        $__count = 0;
        foreach ($spans as $span => $_widgets) {
            if (!empty($_widgets)) {
                $xt_page_widgets = $_widgets;
            } else {
                $xt_page_widgets = array();
            }
            echo '<div class="' . $span . ' X_Region X_Region_' . ucfirst($span) . ' xt-first-child" data-value="' . $span . '">';
            xt_dynamic_region($span);
            echo '</div>';
            $__count++;
        }
    }
    echo '</div>';
}

function xt_dynamic_region($layout) {
    global $xt_current_page, $xt_registered_page, $xt_registered_widgets, $xt_page_widgets, $xt_current_page_setting;
    if (empty($xt_page_widgets)) {
        return true;
    }
    $did_one = false;
    foreach ($xt_page_widgets as $id => $_widget) {
        if (!isset($xt_registered_widgets[$id])) {
            continue;
        }
        $isAdd = 1;
        if (!($xt_current_page_setting['widgets_edit'])) {
            $isAdd = 0;
        }
        $params = array_merge(array(
            array_merge($xt_registered_page, array(
                'layout' => $layout,
                'widget_id' => $id,
                'widget_name' => $xt_registered_widgets[$id]['name'],
                'add' => $isAdd,
                'edit' => $_widget['edit'],
                'delete' => $_widget['delete']
            ))
                ), (array) $xt_registered_widgets[$id]['params']);
        // Substitute HTML id and class attributes into before_widget
        $classname_ = '';
        foreach ((array) $xt_registered_widgets[$id]['classname'] as $cn) {
            if (is_string($cn))
                $classname_ .= '_' . $cn;
            elseif (is_object($cn))
                $classname_ .= '_' . get_class($cn);
        }
        $classname_ = ltrim($classname_, '_');
        $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
        $params = apply_filters('xt_dynamic_page_params', $params);
        $callback = $xt_registered_widgets[$id]['callback'];
        do_action('xt_dynamic_page', $xt_registered_widgets[$id]);
        if (is_callable($callback)) {
            call_user_func_array($callback, $params);
            $did_one = true;
        }
    }
    return $did_one;
}

function _xt_get_widget_id_base($id) {
    return preg_replace('/-[0-9]+$/', '', $id);
}

function xt_init_page($page = 'home', $page_name = '首页', $cache = false) {
    global $xt_registered_widgets, $xt_page_widgets_setting, $xt_page_layouts, $xt_page_widgets, $xt_current_page, $xt_widget_factory, $xt_need_cache;
    $xt_current_page = $page;
    $isEdit = defined('XT_PAGE_EDIT') && XT_PAGE_EDIT;
    if (!$isEdit && $cache && (($xt_current_page == 'home') || is_numeric($xt_current_page))) {
        $cache = xt_get_option(XT_OPTION_PAGE_HTML_PRE . $page);
        if (!empty($cache)) {
            if (time() < ($cache['time'] + 3600) && !empty($cache['data'])) {
                $cache = xt_cache_covert($cache);
                $time = gmdate('Y-m-d H:i:s', ($cache['time'] + (xt_get_option('gmt_offset') * 3600)));
                echo '<!--CACHE[' . $time . '] START-->' . $cache['data'] . '<!--CACHE[' . $time . '] END-->';
                return false;
            }
        }
    }
    xt_register_page(array(
        'name' => $page_name,
        'id' => $page,
        'description' => '',
        'class' => ''
    ));
    $xt_registered_widgets = array();
    $xt_widget_factory = new XT_Widget_Factory();
    $widgetsAndLayouts = xt_get_option_page($page);
    $xt_page_widgets_setting = $widgetsAndLayouts['widgets'];
    $xt_page_layouts = xt_layout_convert($widgetsAndLayouts['layouts']);
    $xt_page_layouts = $xt_page_layouts['layouts'];

    $xt_need_cache = true;
    if (!$isEdit && empty($xt_page_layouts)) {
        if (!xt_is_headerOrfooter($xt_current_page)) {
            $isInstalled = get_option(XT_OPTION_INSTALLED);
            $xt_need_cache = false;
            $xt_page_layouts = array(
                array(
                    'name' => 'row-12',
                    'delete' => 0,
                    'layout' => array(
                        'span12' => array(
                            'text-2' => array(
                                'edit' => 0,
                                'delete' => 0
                            )
                        )
                    )
                )
            );
            $text = '<div class="well xt-unvalid"><h1>尚未装修该页面</h1><p>登录<a href="http://plugin.xintaonet.com/" target="_blank">新淘客平台</a>-页面管理-设计！</p></div>';
            if (!$isInstalled) {
                $text = '<div class="well xt-unvalid"><h1>尚未初始化站点</h1><p>注册，登录<a href="http://plugin.xintaonet.com/" target="_blank">新淘客平台</a>添加验证自己的网站即可初始化！</p></div>';
            }
            $xt_page_widgets_setting = array(
                'text-2' => array(
                    'title' => '',
                    'text' => $text,
                    'filter' => 0,
                    'widefat' => 0,
                    'widget' => 1,
                    'widget_id' => 'text-2',
                    'widget_name' => 'text'
                )
            );
        }
    }
    $_widgets = xt_widgets();
    foreach ($_widgets as $_widget) {
        xt_register_widget($_widget);
    }
    do_action('xt_widgets_init');
    return true;
}

function xt_widgets() {
    $widgets = array(
        'XT_Widget_Text',
        'XT_Widget_Alert',
        'XT_Widget_Nav',
        'XT_Widget_Blog',
        'XT_Widget_Carousel',
        'XT_Widget_Grid',
        'XT_Widget_Grid_Album',
        'XT_Widget_ShareAndTags',
        'XT_Widget_HeaderLove',
        'XT_Widget_SideCat',
        'XT_Widget_Topic',
        'XT_Widget_DaogouList',
        'XT_Widget_Catalog_Share',
        'XT_Widget_Fanxian_Tab',
        'XT_Widget_Custom',
        'XT_Widget_Searchbox',
        'XT_Widget_LogoSearchbox',
        'XT_Widget_MeilishuoSearchbox',
        'XT_Widget_TaobaoGuide',
        'XT_Widget_Toolbar',
        'XT_Widget_Daogou_Category',
        'XT_Widget_Taobao_Chongzhi'
    );
    $sys_widgets = array(
        'XT_Widget_User',
        'XT_Widget_Share',
        'XT_Widget_Shares',
        'XT_Widget_Album',
        'XT_Widget_Albums',
        'XT_Widget_Taobaos',
        'XT_Widget_Taobao',
        'XT_Widget_TaobaoRecommend',
        'XT_Widget_Shops',
        'XT_Widget_Paipais',
        'XT_Widget_Bijias',
        'XT_Widget_Tuans',
        'XT_Widget_Temais',
        'XT_Widget_Coupons',
        'XT_Widget_Invite',
        'XT_Widget_Daogou',
        'XT_Widget_Daogous',
        'XT_Widget_Help',
        'XT_Widget_Helps',
        'XT_Widget_Brands',
        'XT_Widget_Stars',
        'XT_Widget_Activities',
        'XT_Widget_Taoquan',
        'XT_Widget_Malls',
    );
    $page_widgets = array(
        'XT_Widget_Recommend_Daogou',
        'XT_Widget_Recommend_Taobaos',
        'XT_Widget_Album_Album',
        'XT_Widget_Album_UserAlbum',
        'XT_Widget_Help_Category'
    );
    return apply_filters('xt_widgets', array_merge($widgets, $sys_widgets, $page_widgets));
}

// after xt_init_page
function xt_get_page_widgets_setting($widget_id = 0) {
    global $xt_page_widgets_setting;
    if (!did_action('xt_widgets_init')) {
        wp_die('页面模块信息尚未初始化');
    }
    if (!empty($widget_id)) {
        if (isset($xt_page_widgets_setting[$widget_id])) {
            return $xt_page_widgets_setting[$widget_id];
        }
        return array();
    }
    return $xt_page_widgets_setting;
}