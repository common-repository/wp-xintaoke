<?php

function xt_cron_autoshare() {
    global $wpdb;
    if (!IS_CLOUD)
        set_time_limit(0);
    $date = gmdate('Y-m-d H', current_time('timestamp'));
    $shares = $wpdb->get_results('SELECT * FROM ' . XT_TABLE_SHARE_CRON . ' WHERE create_date<=\'' . $date . '\' LIMIT 200');
    $deleteKeys = array();
    if (!empty($shares)) {
        foreach ($shares as $share) {
            $share_key = $share->share_key;
            $user_id = $share->user_id;
            $user_name = $share->user_name;
            $album_id = $share->album_id;
            $data = array();
            if (!empty($share->cache_data)) {
                $data = maybe_unserialize($share->cache_data);
                if (!empty($data)) {
                    if (xt_check_share($data['share_key'], $user_id)) {
                        $deleteKeys[] = $share_key;
                        continue;
                    }
                }
            }
            if (empty($data)) {
                $fetch = array();
                if (startsWith($share_key, 'tb')) {//taobao
                    $fetch = xt_share_fetch('http://item.taobao.com/item.htm?id=' . str_replace('tb_', '', $share_key));
                } elseif (startWith($share_key, 'pp')) {//paipai
                    $fetch = xt_share_fetch('http://auction1.paipai.com/' . str_replace('pp_', '', $share_key));
                }
                if (!empty($fetch)) {
                    $data = array(
                        'share_key' => $fetch['share_key'],
                        'title' => $fetch['title'],
                        'pic_url' => $fetch['pic_url'],
                        'price' => $fetch['price'],
                        'cid' => $fetch['cid'],
                        'cache_data' => $fetch['cache_data'],
                        'content' => '',
                        'from_type' => $fetch['from_type']
                    );
                }
            }
            if (!empty($data)) {
                xt_share_share(array(
                    'share_key' => $data['share_key'],
                    'title' => $data['title'],
                    'pic_url' => $data['pic_url'],
                    'price' => $data['price'],
                    'cid' => (int) $data['cid'],
                    'user_id' => (int) $user_id,
                    'user_name' => $user_name,
                    'cache_data' => $data['cache_data'],
                    'from_type' => $data['from_type'],
                    'data_type' => 1,
                    'content' => (isset($data['content'])) ? trim(strip_tags($data['content'])) : null,
                    'album_id' => (int) $album_id
                ));
            }
            $deleteKeys[] = $share_key;
        }
        if (!empty($deleteKeys)) {
            $_keys = array();
            foreach ($deleteKeys as $deleteKey) {
                $_keys[] = "'{$deleteKey}'";
            }
            $wpdb->query('DELETE FROM ' . XT_TABLE_SHARE_CRON . ' WHERE share_key in (' . implode(',', $_keys) . ')');
        }
        xt_catalogs_share(true); //reload catalogs and tags
        do_action('xt_page_updated', 'home'); //clear home cache
    }
}

function xt_total_share() {
    global $wpdb;
    $result = $wpdb->get_results('SELECT from_type AS platform,count(id) AS total FROM ' . XT_TABLE_SHARE . ' GROUP BY from_type', ARRAY_A);
    $_result = array(
        'total' => 0,
        'taobao' => 0,
        'paipai' => 0,
        'yiqifa' => 0
    );
    if (!empty($result)) {
        foreach ($result as $r) {
            $_result[$r['platform']] = $r['total'];
            $_result['total'] = $_result['total'] + $r['total'];
        }
    }
    return $_result;
}

function get_share_query_var($var) {
    global $xt_share_query;
    return $xt_share_query->get($var);
}

function get_share_queried_object() {
    global $xt_share_query;
    return $xt_share_query->get_queried_object();
}

function get_share_queried_object_id() {
    global $xt_share_query;
    return $xt_share_query->get_queried_object_id();
}

function set_share_query_var($var, $value) {
    global $xt_share_query;
    return $xt_share_query->set($var, $value);
}

function & get_share_album(& $share = array()) {
    global $wpdb;
    $album = null;
    if (empty($share)) {
        if (isset($GLOBALS['share']))
            $share = & $GLOBALS['share']->id;
        else
            return $album;
    }else {
        if (is_object($share))
            $share = $share->id;
    }
    $share = (int) $share;
    if ($share > 0) {
        $album = $wpdb->get_row($wpdb->prepare("SELECT album.* FROM " . XT_TABLE_ALBUM . " AS album," . XT_TABLE_SHARE_ALBUM . " AS sa WHERE sa.id = %d AND sa.album_id=album.id ORDER BY sa.create_date DESC LIMIT 1 ", $share));
    }
    return $album;
}

function & get_share(& $share = array()) {
    global $wpdb;
    $null = null;

    if (empty($share)) {
        if (isset($GLOBALS['share']))
            $_share = & $GLOBALS['share'];
        else
            return $null;
    } else {
        if (is_object($share))
            $share_id = $share->id;
        else
            $share_id = $share;
        $share_id = (int) $share_id;
        $_share = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . XT_TABLE_SHARE . " WHERE id = %d LIMIT 1", $share_id));
        if (!$_share)
            return $null;
    }

    return $_share;
}

function & query_shares($query) {
    unset($GLOBALS['xt_share_query']);
    $GLOBALS['xt_share_query'] = new XT_Share_Query();
    $result = $GLOBALS['xt_share_query']->query($query);
    return $result;
}

function xt_reset_share_query() {
    unset($GLOBALS['xt_share_query']);
    $GLOBALS['xt_share_query'] = $GLOBALS['xt_the_share_query'];
    xt_reset_sharedata();
}

function xt_reset_sharedata() {
    global $xt_share_query;
    if (!empty($xt_share_query->share)) {
        $GLOBALS['share'] = $xt_share_query->share;
        xt_setup_sharedata($xt_share_query->share);
    }
}

function xt_have_shares() {
    global $xt_share_query;
    return $xt_share_query->have_shares();
}

function xt_in_the_share_loop() {
    global $xt_share_query;
    return $xt_share_query->in_the_loop;
}

function xt_rewind_shares() {
    global $xt_share_query;
    return $xt_share_query->rewind_shares();
}

function xt_the_share() {
    global $xt_share_query;
    $xt_share_query->the_share();
}

function xt_is_share_single() {
    global $xt_share_query;
    return $xt_share_query->is_single();
}

class XT_Share_Query {

    var $query;
    var $query_vars = array();
    var $queried_object;
    var $queried_object_id;
    var $shares;
    var $share_count = 0;
    var $found_shares = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $share;
    var $is_single = false;
    var $query_vars_hash = false;
    var $query_vars_changed = true;
    var $paginate_links = "";

    function init_query_flags() {
        $this->is_single = false;
    }

    function init() {
        unset($this->shares);
        unset($this->query);
        $this->query_vars = array();
        unset($this->queried_object);
        unset($this->queried_object_id);
        $this->found_shares = 0;
        $this->share_count = 0;
        $this->current_share = -1;
        $this->in_the_loop = false;

        unset($this->share);
        $this->paginate_links = '';

        $this->init_query_flags();
    }

    function parse_query_vars() {
        $this->parse_query();
    }

    function fill_query_vars($array) {
        $keys = array(
            'cid',
            's',
            'price',
            'sortOrder',
            'user_id',
            'from_type',
            'data_type',
            'id',
            'group_id',
            'fields',
            'page',
            'status',
            'no_found_rows',
            'share_per_page',
            'isHome',
            'isFavorite',
            'isShare',
            'album_id'
        );

        foreach ($keys as $key) {
            if (!isset($array[$key]))
                $array[$key] = '';
        }
        if ($array['fields'] == '') {
            $array['fields'] = XT_TABLE_SHARE . '.*';
        }
        return $array;
    }

    function parse_query($query = '') {
        if (!empty($query)) {
            $this->init();
            $this->query = $this->query_vars = wp_parse_args($query);
        } elseif (!isset($this->query)) {
            $this->query = $this->query_vars;
        }

        $this->query_vars = $this->fill_query_vars($this->query_vars);
        $qv = & $this->query_vars;
        $this->query_vars_changed = true;
        $this->query_vars_hash = false;

        $qv['cid'] = intval($qv['cid']);
        $qv['s'] = trim($qv['s']);
        $qv['price'] = trim($qv['price']);
        $qv['sortOrder'] = trim($qv['sortOrder']);
        $qv['user_id'] = absint($qv['user_id']);
        $qv['user_id'] = absint($qv['user_id']);
        $qv['id'] = absint($qv['id']);
        $qv['group_id'] = absint($qv['group_id']);
        $qv['fields'] = trim($qv['fields']);
        $qv['page'] = absint($qv['page']) > 0 ? absint($qv['page']) : 1;
        $qv['status'] = absint($qv['status']);
        $qv['no_found_rows'] = (bool) $qv['no_found_rows'];
        $qv['share_per_page'] = absint($qv['share_per_page']);
        $qv['isFavorite'] = empty($qv['isFavorite']) ? false : true;
        $qv['isShare'] = empty($qv['isShare']) ? false : true;
        $qv['isHome'] = empty($qv['isHome']) ? false : true;
        $qv['album_id'] = absint($qv['album_id']);

        if ($qv['share_per_page'] == 0)
            $qv['share_per_page'] = xt_shareperpage();

        if (!empty($qv['id'])) {
            $this->is_single = true;
        }
        $this->query_vars_hash = md5(serialize($this->query_vars));
        $this->query_vars_changed = false;
        do_action_ref_array('xt_share_parse_query', array(
            & $this
        ));
    }

    function get($query_var) {
        if (isset($this->query_vars[$query_var]))
            return $this->query_vars[$query_var];

        return '';
    }

    function set($query_var, $value) {
        $this->query_vars[$query_var] = $value;
    }

    function & get_shares() {

        $this->parse_query();

        do_action_ref_array('pre_get_shares', array(
            & $this
        ));

        $q = & $this->query_vars;

        $q = $this->fill_query_vars($q);

        $hash = md5(serialize($this->query_vars));
        if ($hash != $this->query_vars_hash) {
            $this->query_vars_changed = true;
            $this->query_vars_hash = $hash;
        }
        unset($hash);

        $result = $this->_get_shares($q);

        $this->found_shares = (int) $result['total'];
        $this->shares = $result['share'];
        $this->share_count = count($this->shares);

        if (!$q['no_found_rows'] && ($this->share_count > 1 || $q['page'] > 1)) {
            global $xt;
            $total_page = ceil($this->found_shares / (int) $this->get('share_per_page'));
            $_base = '#%#%';
            if (isset($xt->is_shares) && $xt->is_shares) {
                $_base = xt_get_shares_search_url(array_merge($q, array('page' => '%#%')));
            } else {
                if (isset($_GET['page']) && $_GET['page'] == 'xt_menu_share') {
                    $_base = add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                }
            }
            $this->paginate_links = paginate_links(array(
                'base' => $_base,
                'format' => '',
                'end_size' => 3,
                'total' => $total_page,
                'current' => $q['page'],
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1,
                'type' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_share' ? 'plain' : 'list'
                    ));
        }
        return $this->shares;
    }

    function _get_shares($args) {
        global $wpdb;
        $id = isset($args['id']) && absint($args['id']) ? absint($args['id']) : 0;
        $album_id = isset($args['album_id']) && absint($args['album_id']) ? absint($args['album_id']) : 0;

        if ($id > 0) { //single
            $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . XT_TABLE_SHARE . " g WHERE g.id = %d", $id));
            if (!empty($result)) {
                return array(
                    'share' => array(
                        $result
                    ),
                    'total' => 1
                );
            } else {
                return array(
                    'share' => array(),
                    'total' => 0
                );
            }
        }

        $user_id = $args['user_id'];
        $sortOrder = $args['sortOrder'];
        $cid = $args['cid'];
        $share_per_page = $args['share_per_page'];
        $page = $args['page'];
        $s = $args['s'];
        $price = $args['price'];
        $isFavorite = $args['isFavorite'];
        $isHome = $args['isHome'];

        $fields = $args['fields'];

        $table = ($album_id > 0 ? XT_TABLE_SHARE_ALBUM : ($isFavorite ? XT_TABLE_FAVORITE : XT_TABLE_SHARE));
        $join = '';
        $where = '';
        $groupby = '';
        $order = '';
        $pagination = '';
        $found_rows = '';

        if (!empty($cid) && $cid > 0) {
            global $xt_catalog;
            if (empty($xt_catalog) || $xt_catalog->id != $cid) {
                $xt_catalog = xt_get_catalog($cid);
            }
            if (!empty($xt_catalog)) {
                if (isset($xt_catalog->children) && !empty($xt_catalog->children)) {
                    $join .= " INNER JOIN " . XT_TABLE_SHARE_CATALOG . " ON " . XT_TABLE_SHARE_CATALOG . ".id = " . XT_TABLE_SHARE . ".id ";
                    $where .= " AND " . XT_TABLE_SHARE_CATALOG . ".cid in(" . $wpdb->escape($xt_catalog->children) . "," . $cid . ") ";
                    $groupby .="GROUP BY " . XT_TABLE_SHARE . ".id";
                } else {
                    $join .= " INNER JOIN " . XT_TABLE_SHARE_CATALOG . " ON " . XT_TABLE_SHARE_CATALOG . ".id = " . XT_TABLE_SHARE . ".id ";
                    $where .= $wpdb->prepare(" AND " . XT_TABLE_SHARE_CATALOG . ".cid=%d ", $cid);
                }
            }
        } elseif ($cid == -1) {
            $join = '';
            $where = ' AND ' . XT_TABLE_SHARE . '.id NOT IN (SELECT ID FROM ' . XT_TABLE_SHARE_CATALOG . ')';
        }

        if (!empty($user_id) && $user_id > 0) {
            if ($isHome) {
                global $xt_pageuser_follows;
                if (!empty($xt_pageuser_follows) && is_array($xt_pageuser_follows)) {
                    $followIds = implode(',', $xt_pageuser_follows);
                    $where .= " AND " . XT_TABLE_SHARE . ".user_id in (" . $followIds . ") ";
                }
            } elseif ($isFavorite) {
                $where .= $wpdb->prepare(" AND " . XT_TABLE_FAVORITE . ".user_id = %d AND " . XT_TABLE_FAVORITE . ".type=1", $user_id);
            } elseif ($album_id > 0) {
                $where .= $wpdb->prepare(" AND " . XT_TABLE_SHARE_ALBUM . ".user_id = %d ", $user_id);
            } else {
                $where .= $wpdb->prepare(" AND " . XT_TABLE_SHARE . ".user_id = %d ", $user_id);
            }
        }
        if ($isFavorite) {
            $join .= " INNER JOIN " . XT_TABLE_SHARE . " ON  " . XT_TABLE_FAVORITE . ".id=" . XT_TABLE_SHARE . ".id ";
        } elseif (!empty($s)) {
            $match_key = xt_segment_unicode($wpdb->escape($s), '+');
            $join .= " INNER JOIN " . XT_TABLE_SHARE_MATCH . " gm ON match(gm.content_match) against('" . $match_key . "' IN BOOLEAN MODE) AND gm.share_id=" . XT_TABLE_SHARE . ".id ";
        } elseif ($album_id > 0) {
            $where .= $wpdb->prepare(" AND " . XT_TABLE_SHARE_ALBUM . ".album_id = %d ", $album_id);
            $join .= " INNER JOIN " . XT_TABLE_SHARE . " ON  " . XT_TABLE_SHARE_ALBUM . ".id=" . XT_TABLE_SHARE . ".id ";
        }
        $fields .= " ,$wpdb->usermeta.meta_value as user_avatar";
        $join .= " LEFT JOIN $wpdb->usermeta ON $wpdb->usermeta.user_id = " . XT_TABLE_SHARE . ".user_id AND $wpdb->usermeta.meta_key='" . XT_USER_AVATAR . "' ";
        if (!empty($price)) {
            $prices = xt_prices();
            switch ($price) {
                case 'low' :
                    $where .= " AND " . XT_TABLE_SHARE . ".price <= " . $prices['low']['end'];
                    break;
                case 'medium' :
                    $where .= " AND " . XT_TABLE_SHARE . ".price >= " . $prices['medium']['start'] . " AND " . XT_TABLE_SHARE . ".price <= " . $prices['medium']['end'];
                    break;
                case 'high' :
                    $where .= " AND " . XT_TABLE_SHARE . ".price >= " . $prices['high']['start'] . " AND " . XT_TABLE_SHARE . ".price <= " . $prices['high']['end'];
                    break;
                case 'higher' :
                    $where .= " AND " . XT_TABLE_SHARE . ".price >= " . $prices['higher']['start'];
                    break;
            }
        }
        $today_time = xt_get_todaytime();
        if ($isHome) {
            $order = " ORDER BY " . XT_TABLE_SHARE . ".create_date DESC";
        } elseif ($isFavorite) {
            $order = " ORDER BY " . XT_TABLE_FAVORITE . ".create_date DESC";
        } else {
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
        }

        if ($share_per_page && $page)
            $pagination = $wpdb->prepare("LIMIT %d, %d", intval(($page - 1) * $share_per_page), intval($share_per_page));
        if (!empty($where)) {
            $where = ' WHERE 1=1 ' . $where;
        }
        if (!$args['no_found_rows'])
            $found_rows = 'SQL_CALC_FOUND_ROWS';
        $sql = "SELECT {$found_rows} {$fields} FROM {$table} {$join} {$where} {$groupby} {$order} {$pagination}";
        $paged_share_sql = apply_filters('xt_share_get_paged_share_sql', $sql, $sql);
        $paged_share = $wpdb->get_results($paged_share_sql);

        $total_share = -1;
        if (!$args['no_found_rows']) {
            $total_share = $wpdb->get_var('SELECT FOUND_ROWS()');
            $max_num_pages = ceil($total_share / $args['share_per_page']);
        }
        unset($sql);

        return array(
            'share' => $paged_share,
            'total' => $total_share
        );
    }

    function next_share() {

        $this->current_share++;

        $this->share = $this->shares[$this->current_share];
        return $this->share;
    }

    function the_share() {
        global $share;
        $this->in_the_loop = true;

        if ($this->current_share == -1) // loop has just started
            do_action_ref_array('xt_share_loop_start', array(
                & $this
            ));

        $share = $this->next_share();
        xt_setup_sharedata($share);
    }

    function have_shares() {
        if ($this->current_share + 1 < $this->share_count) {
            return true;
        } elseif ($this->current_share + 1 == $this->share_count && $this->share_count > 0) {
            do_action_ref_array('xt_share_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_shares();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_shares() {
        $this->current_share = -1;
        if ($this->share_count > 0) {
            $this->share = $this->shares[0];
        }
    }

    function & query($query) {
        $this->init();
        $this->query = $this->query_vars = wp_parse_args($query);
        return $this->get_shares();
    }

    function get_queried_object() {
        if (isset($this->queried_object))
            return $this->queried_object;

        $this->queried_object = null;
        $this->queried_object_id = 0;

        return $this->queried_object;
    }

    function get_queried_object_id() {
        $this->get_queried_object();

        if (isset($this->queried_object_id)) {
            return $this->queried_object_id;
        }

        return 0;
    }

    function __construct($query = '') {
        if (!empty($query)) {
            $this->query($query);
        }
    }

    function set_found_shares($q) {
        global $wpdb;

        if ($q['no_found_rows'])
            return;

        $this->found_shares = $wpdb->get_var(apply_filters_ref_array('found_shares_query', array(
                    'SELECT FOUND_ROWS()',
                    & $this
                )));
        $this->found_shares = apply_filters_ref_array('found_shares', array(
            $this->found_shares,
            & $this
                ));

        $this->max_num_pages = ceil($this->found_shares / $q['share_per_page']);
    }

    /**
     * Is the query for a single share?
     *
     * @return bool
     */
    function is_single() {
        return $this->is_single;
    }

}

function xt_update_user_share_count($user_id) {
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_SHARE . " WHERE user_id = %d", $user_id));
    //update user option meta
    xt_update_user_count($user_id, XT_USER_COUNT_SHARE, $count);
    return $count;
}

function xt_check_share($key, $user_id) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SELECT share_key FROM " . XT_TABLE_SHARE . " WHERE share_key = %s AND user_id=%d ", $key, $user_id));
}

function xt_share_delete($ids) {
    global $wpdb;
    $ids = explode(',', $ids);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            $id = intval($id);
            //1.match
            $wpdb->delete(XT_TABLE_SHARE_MATCH, array(
                'share_id' => $id
            ));
            //2.catalog
            $wpdb->delete(XT_TABLE_SHARE_CATALOG, array(
                'id' => $id
            ));
            //3.album
            $wpdb->delete(XT_TABLE_SHARE_ALBUM, array(
                'id' => $id
            ));
            //4.comment
            $wpdb->delete(XT_TABLE_SHARE_COMMENT, array(
                'share_id' => $id
            ));
            //5.favorite
            $wpdb->delete(XT_TABLE_FAVORITE, array(
                'id' => $id,
                'type' => 1
            ));
            //6.share
            $wpdb->delete(XT_TABLE_SHARE, array(
                'id' => $id
            ));
        }
    }
}

function xt_insert_share_match($sharematchdata) {
    global $wpdb;
    extract(stripslashes_deep($sharematchdata), EXTR_SKIP);

    $data = compact('share_id', 'content_match');
    $wpdb->insert(XT_TABLE_SHARE_MATCH, $data);
}

function xt_get_sharecount_bytagandcid($tag, $cid) {
    global $wpdb;
    $cid = absint($cid);
    if ($cid > 0 && !empty($tag)) {
        $catalog = xt_get_catalog($cid);
        if (!empty($catalog)) {
            $match_key = xt_segment_unicode($wpdb->escape($tag), '+');
            $sql = "SELECT COUNT(*) FROM wp_xt_share INNER JOIN wp_xt_share_catalog ON wp_xt_share_catalog.id = wp_xt_share.id INNER JOIN wp_xt_share_match gm ON match(gm.content_match) against('$match_key' IN BOOLEAN MODE) AND gm.share_id=wp_xt_share.id WHERE wp_xt_share_catalog.cid=$cid ";
            if (isset($catalog->children) && !empty($catalog->children)) {
                $sql = "SELECT COUNT(*) FROM wp_xt_share INNER JOIN wp_xt_share_catalog ON wp_xt_share_catalog.id = wp_xt_share.id INNER JOIN wp_xt_share_match gm ON match(gm.content_match) against('$match_key' IN BOOLEAN MODE) AND gm.share_id=wp_xt_share.id WHERE wp_xt_share_catalog.cid in(" . $wpdb->escape($catalog->children) . "," . $cid . ") GROUP BY " . XT_TABLE_SHARE . ".id";
            }
            return $wpdb->get_var($sql);
        }
    }
    return 1;
}

function xt_update_share_tags($tags, $cids = array()) {
    global $wpdb;
    foreach ($tags as $tag) {
        //第一步:判断标签是否已存在
        //存在该标签:1.修改计数2.如指定cid,判断是否已有关系,无则插入关系表,有则不处理
        //不存在该标签:1.插入标签2.如指定cid,则插入关系表
        $sql = $wpdb->prepare("SELECT id,sort FROM " . XT_TABLE_SHARE_TAG . " WHERE title =%s", $tag);
        $tagRow = $wpdb->get_row($sql);
        if (!empty($tagRow)) { //当前标签已存在
            $tag_id = $tagRow->id;
            $tag_sort = $tagRow->sort;
            $wpdb->query($wpdb->prepare("UPDATE " . XT_TABLE_SHARE_TAG . " SET count=(count+1) WHERE id =%d", $tag_id));
            if (!empty($cids)) {
                foreach ($cids as $cid) {
                    if (!xt_tag_catalog_exit(absint($tag_id), absint($cid))) {
                        xt_new_tag_catalog(array('id' => absint($tag_id), 'cid' => absint($cid), 'sort' => $tag_sort));
                    } else {
                        $wpdb->query("UPDATE " . XT_TABLE_SHARE_TAG_CATALOG . " SET count=count+1 WHERE id=" . absint($tag_id) . " AND cid=" . absint($cid));
                    }
                }
            }
        } else {
            if ($tag_id = xt_new_tag(array('title' => $tag))) {
                if (!empty($cids)) {
                    foreach ($cids as $cid) {
                        xt_new_tag_catalog(array('id' => absint($tag_id), 'cid' => absint($cid), 'sort' => (xt_tag_default_sort($tag))));
                    }
                }
            }
        }
    }
}

function xt_insert_share($sharedata) {
    global $wpdb;
    extract(stripslashes_deep($sharedata), EXTR_SKIP);
    $guid = xt_user_guid($user_id);
    $data = compact('title', 'share_key', 'pic_url', 'price', 'cid', 'guid', 'user_id', 'user_name', 'tags', 'cache_data', 'content', 'from_type', 'data_type', 'create_date', 'create_date_gmt', 'update_date', 'update_date_gmt');
    if ($wpdb->insert(XT_TABLE_SHARE, $data)) {
        $id = $wpdb->insert_id;
        //match
        xt_insert_share_match(array(
            'share_id' => $id,
            'content_match' => xt_segment_unicode($title)
        ));

        if ($cid > 0) {
            $cids = array();
            //Auto Catalog(By cid)
            if (in_array($from_type, array('taobao', 'paipai'))) {
                $cids = xt_get_catalogs_by_cid($cid, $from_type);
                if (!empty($cids)) {
                    if (!empty($tags)) {
                        xt_update_share_tags(explode(' ', $tags), $cids);
                    }
                }
            }
            //Auto Catalog(By Tags)
//            if (empty($cids)) {
//                if (!empty($tags)) {
//                    $cids = xt_get_catalogs_by_tags(explode(' ', $tags));
//                }
//            }

            if (!empty($cids)) {
                foreach ($cids as $_cid) {
                    xt_new_share_catalog(array(
                        'id' => absint($id),
                        'cid' => absint($_cid)
                    ));
                }
            }
        } else {
            if (!empty($tags)) {
                xt_update_share_tags(explode(' ', $tags));
            }
        }

        $count = xt_update_user_share_count($user_id);
        return $id;
    }
    return 0;
}

function xt_new_share($sharedata) {
    $sharedata['title'] = $sharedata['title'];
    $sharedata['share_key'] = $sharedata['share_key'];
    $sharedata['price'] = $sharedata['price'];
    $sharedata['pic_url'] = $sharedata['pic_url'];
    $sharedata['cid'] = (int) $sharedata['cid'];
    $sharedata['user_id'] = (int) $sharedata['user_id'];
    $sharedata['user_name'] = $sharedata['user_name'];

    $tags = array_unique(xt_segment($sharedata['title'], 10));
    $sharedata['tags'] = implode(' ', $tags);

    $sharedata['content'] = $sharedata['content'];
    $sharedata['cache_data'] = $sharedata['cache_data'];
    $sharedata['from_type'] = $sharedata['from_type'];
    $sharedata['data_type'] = (int) $sharedata['data_type'];

    $sharedata['create_date'] = current_time('mysql');
    $sharedata['create_date_gmt'] = current_time('mysql', 1);
    $sharedata['update_date'] = current_time('mysql');
    $sharedata['update_date_gmt'] = current_time('mysql', 1);

    $id = xt_insert_share($sharedata);

    return $id;
}

function xt_share_share($data) {
    $sharedata = array();
    $sharedata['share_key'] = $data['share_key'];
    $sharedata['title'] = $data['title'];
    $sharedata['pic_url'] = $data['pic_url'];
    $sharedata['price'] = $data['price'];
    $sharedata['cid'] = (int) $data['cid'];
    $sharedata['user_id'] = (int) $data['user_id'];
    $sharedata['user_name'] = $data['user_name'];
    $sharedata['cache_data'] = $data['cache_data'];
    $sharedata['from_type'] = $data['from_type'];
    $sharedata['data_type'] = (int) $data['data_type'];
    $sharedata['content'] = (isset($data['content'])) ? trim(strip_tags($data['content'])) : null;

    $share_id = xt_new_share($sharedata);
    if ($share_id > 0 && isset($data['album_id'])) {
        $album_id = $albumdata['album_id'] = $data['album_id'];
        if ($albumdata['album_id'] == 0) { //insert default album
            if (isset($data['album_title']) && !empty($data['album_title'])) {
                $albumdata['album_title'] = $data['album_title'];
                $album_id = xt_new_album(array(
                    'title' => $albumdata['album_title'],
                    'user_id' => $sharedata['user_id'],
                    'user_name' => $sharedata['user_name'],
                    'content' => ''
                        ));
            }
        }

        if ($share_id > 0 && $album_id > 0) {
            xt_new_share_album(array(
                'id' => $share_id,
                'album_id' => $album_id,
                'user_name' => $sharedata['user_name'],
                'user_id' => $sharedata['user_id']
            ));
        }
    }
    return $share_id;
}

function xt_setup_sharedata($share) {
    global $xt_authordata;

    if (xt_is_share_single())
        $xt_authordata = get_userdata($share->user_id);
    else
        $xt_authordata = null;

    $share->cache_data = unserialize($share->cache_data);

    do_action_ref_array('xt_the_share', array(
        & $share
    ));

    return true;
}

//Define GLOBAL query
global $xt_the_share_query;
$xt_the_share_query = new XT_Share_Query();
$xt_share_query = & $xt_the_share_query;