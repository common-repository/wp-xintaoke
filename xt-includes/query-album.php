<?php

function xt_total_album() {
    global $wpdb;
    $result = $wpdb->get_var('SELECT COUNT(*) AS total FROM ' . XT_TABLE_ALBUM);
    return $result;
}

function xt_have_albums() {
    global $xt_album_query;
    if ($xt_album_query) {
        return $xt_album_query->have_albums();
    }
    return false;
}

function xt_in_the_album_loop() {
    global $xt_album_query;
    return $xt_album_query->in_the_loop;
}

function xt_rewind_albums() {
    global $xt_album_query;
    return $xt_album_query->rewind_albums();
}

function xt_the_album() {
    global $xt_album_query;
    $xt_album_query->the_album();
}

function & query_albums($args = '') {
    unset($GLOBALS['xt_album_query']);
    $GLOBALS['xt_album_query'] = new XT_Album_Query();
    $result = $GLOBALS['xt_album_query']->query($args);
    return $result;
}

class XT_Album_Query {

    var $albums;
    var $album_count = 0;
    var $found_albums = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $album;
    var $paginate_links = '';

    function init() {
        unset($this->albums);
        $this->album_count = 0;
        $this->found_albums = 0;
        $this->current_album = -1;
        $this->in_the_loop = false;

        unset($this->album);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb, $xt;

        $this->init();

        $defaults = array(
            'cid' => '',
            'page' => 1,
            'album_per_page' => xt_albumperpage(),
            'user_id' => 0,
            'no_found_rows' => 0,
            's' => '',
            'isFavorite' => 0,
            'isShare' => 0,
            'sortOrder' => '',
            'album__in' => array()
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_albums', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $page = absint($page);
        $album_per_page = absint($album_per_page);

        $table = ($isFavorite ? XT_TABLE_FAVORITE : XT_TABLE_ALBUM);
        $fields = '*';
        $join = '';
        $where = '';
        $order = "ORDER BY " . XT_TABLE_ALBUM . ".update_date_gmt DESC";
        $groupby = '';

        if (!empty($cid) && $cid > 0) {
            global $xt_catalog;
            if (empty($xt_catalog) || $xt_catalog->id != $cid) {
                $xt_catalog = xt_get_catalog($cid);
            }
            if (!empty($xt_catalog)) {
                if (isset($xt_catalog->children) && !empty($xt_catalog->children)) {
                    $join .= " INNER JOIN " . XT_TABLE_ALBUM_CATALOG . " ON " . XT_TABLE_ALBUM_CATALOG . ".id = " . XT_TABLE_ALBUM . ".id ";
                    $where .= " AND " . XT_TABLE_ALBUM_CATALOG . ".cid in(" . $wpdb->escape($xt_catalog->children) . "," . $cid . ") ";
                    $groupby .="GROUP BY " . XT_TABLE_ALBUM . ".id";
                } else {
                    $join .= " INNER JOIN " . XT_TABLE_ALBUM_CATALOG . " ON " . XT_TABLE_ALBUM_CATALOG . ".id = " . XT_TABLE_ALBUM . ".id ";
                    $where .= $wpdb->prepare(" AND " . XT_TABLE_ALBUM_CATALOG . ".cid=%d ", $cid);
                }
            }
        } elseif ($cid == -1) {
            $join = '';
            $where = ' AND ' . XT_TABLE_ALBUM . '.id NOT IN (SELECT ID FROM ' . XT_TABLE_ALBUM_CATALOG . ')';
        }

        if ($isFavorite) {
            $join .= " INNER JOIN " . XT_TABLE_ALBUM . " ON  " . XT_TABLE_FAVORITE . ".id=" . XT_TABLE_ALBUM . ".id ";
        }
        if ($user_id > 0) {
            if ($isFavorite) {
                $where .= $wpdb->prepare(" AND " . XT_TABLE_FAVORITE . ".user_id = %d AND " . XT_TABLE_FAVORITE . ".type=2", $user_id);
            } else {
                $where .= $wpdb->prepare(" AND " . XT_TABLE_ALBUM . ".user_id = %d ", $user_id);
            }
        } elseif (!empty($s)) {
            $where .= " AND (" . XT_TABLE_ALBUM . ".title like '%" . $wpdb->escape($s) . "%' OR " . XT_TABLE_ALBUM . ".user_name like '%" . $wpdb->escape($s) . "%') ";
        }
        if (!empty($album__in)) {
            $album__in = implode(',', array_map('absint', $album__in));
            $where .= " AND " . XT_TABLE_ALBUM . ".id IN ($album__in)";
        }
        $today_time = xt_get_todaytime();
        switch ($sortOrder) {
            case 'newest' :
            default :
                $order = " ORDER BY " . XT_TABLE_ALBUM . ".update_date DESC";
                break;
            case 'popular' :
                $day7_time = $today_time - 604800; //7 days
                $fields .= ",(" . XT_TABLE_ALBUM . ".create_date > $day7_time) AS time_sort ";
                $order = " ORDER BY time_sort DESC," . XT_TABLE_ALBUM . ".fav_count DESC";
                break;
            case 'hot' :
                $day30_time = $today_time - 2592000; //30 days
                $fields .= ",(" . XT_TABLE_ALBUM . ".create_date > $day30_time) AS time_sort ";
                $order = " ORDER BY time_sort DESC," . XT_TABLE_ALBUM . ".fav_count DESC";
                break;
        }
        if (!$no_found_rows && $page && $album_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $album_per_page, $album_per_page);
        else {
            $limits = '';
        }
        if ($isFavorite) {
            $order = " ORDER BY " . XT_TABLE_FAVORITE . ".create_date DESC";
        }
        $found_rows = '';
        if (!$no_found_rows)
            $found_rows = 'SQL_CALC_FOUND_ROWS';
        $sql = "SELECT $found_rows $fields FROM {$table} {$join} WHERE 1=1 {$where} {$order}  {$limits}";
        $paged_albums = $wpdb->get_results($sql);
        $paged_albums = apply_filters_ref_array('xt_the_albums', array(
            $paged_albums,
            & $this
                ));
        $total_albums = -1;
        if (!$no_found_rows) {
            $total_albums = $wpdb->get_var('SELECT FOUND_ROWS()');
        }
        unset($sql);

        $this->found_albums = $total_albums;
        $this->albums = $paged_albums;
        $this->album_count = count($paged_albums);

        if ($total_albums > 1) {
            $total_page = ceil($total_albums / $album_per_page);
            $_base = '#%#%';
            if (isset($xt->is_albums) && $xt->is_albums) {
                $_base = xt_get_albums_search_url(array_merge($this->query_vars, array('page' => '%#%')));
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
                'current' => $page,
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1,
                'type' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_share' ? 'plain' : 'list'
                    ));
        }

        return array(
            'albums' => $paged_albums,
            'total' => $total_albums
        );
    }

    function get($query_var) {
        if (isset($this->query_vars[$query_var]))
            return $this->query_vars[$query_var];

        return '';
    }

    function next_album() {

        $this->current_album++;

        $this->album = $this->albums[$this->current_album];
        return $this->album;
    }

    function the_album() {
        global $xt_album;
        $this->in_the_loop = true;

        if ($this->current_album == -1) // loop has just started
            do_action_ref_array('xt_album_loop_start', array(
                & $this
            ));

        $xt_album = $this->next_album();
        xt_setup_albumdata($xt_album);
    }

    function have_albums() {
        if ($this->current_album + 1 < $this->album_count) {
            return true;
        } elseif ($this->current_album + 1 == $this->album_count && $this->album_count > 0) {
            do_action_ref_array('xt_album_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_albums();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_albums() {
        $this->current_album = -1;
        if ($this->album_count > 0) {
            $this->album = $this->albums[0];
        }
    }

}

function & xt_get_album($id = 0, $title = '') {
    if ($id == 0 && $title == '') {
        return $GLOBALS['xt_album'];
    }
    global $wpdb;
    $where = '';
    $value = '';
    if ($id > 0) {
        $where = ' id = %d ';
        $value = $id;
    } elseif (!empty($title)) {
        $where = ' title = %s ';
        $value = $title;
    }

    $album = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_ALBUM . ' WHERE ' . $where . ' limit 1', $value));
    return $album;
}

/**
 * Get  a album relationships.
 *
 */
function & xt_get_share_album($share_id, $album_id, $user_id) {
    global $wpdb;
    $album = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . XT_TABLE_SHARE_ALBUM . ' WHERE id = %d AND album_id = %d AND user_id = %d limit 1', $share_id, $album_id, $user_id));
    return $album;
}

function xt_album_delete($ids) {
    global $wpdb;
    $ids = explode(',', $ids);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            xt_delete_album(intval($id));
        }
    }
}

/**
 * deletes a album.
 *
 */
function xt_delete_album($id) {
    global $wpdb;
    $count = 0;
    $_album = xt_get_album($id);
    if (!empty($_album)) {
        //1.favorite
        $wpdb->delete(XT_TABLE_FAVORITE, array(
            'id' => $id,
            'type' => 2
        ));
        //2.share
        $wpdb->delete(XT_TABLE_SHARE_ALBUM, array(
            'album_id' => $id
        ));
        //3.catalog
        xt_delete_album_catalog(0, $id);
        //4.album
        $wpdb->delete(XT_TABLE_ALBUM, array(
            'id' => $id
        ));
        $count = xt_update_user_album_count($_album->user_id);
    }
    return $count;
}

function xt_update_user_album_count($user_id) {
    global $wpdb;
    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_ALBUM . " WHERE user_id = %d", $user_id));
    //update user option meta
    xt_update_user_count($user_id, XT_USER_COUNT_ALBUM, $count);
    return $count;
}

function xt_update_album_count($album_id, $_share = array()) {
    global $wpdb;
    $share_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_SHARE_ALBUM . " WHERE album_id = %d", $album_id));
    $pic_url = array();
    $share_urls = $wpdb->get_results($wpdb->prepare("SELECT " . XT_TABLE_SHARE . ".pic_url FROM " . XT_TABLE_SHARE_ALBUM . " INNER JOIN " . XT_TABLE_SHARE . " ON " . XT_TABLE_SHARE . ".id=" . XT_TABLE_SHARE_ALBUM . ".id WHERE album_id = %d ORDER BY " . XT_TABLE_SHARE_ALBUM . ".create_date_gmt DESC LIMIT 9", $album_id));
    if (!empty($share_urls)) {
        foreach ($share_urls as $_u) {
            $pic_url[] = $_u->pic_url;
        }
    }
    $pic_url = implode(',', $pic_url);
    $update_date = current_time('mysql');
    $update_date_gmt = current_time('mysql', 1);
    $wpdb->update(XT_TABLE_ALBUM, compact('share_count', 'pic_url', 'update_date', 'update_date_gmt'), array(
        'id' => $album_id
    ));
    return $share_count;
}

function xt_delete_share_album($id, $album_id, $user_id) {
    global $wpdb;
    do_action('xt_delete_share_album', $id);
    if (!$wpdb->delete(XT_TABLE_SHARE_ALBUM, array(
                'id' => $id,
                'album_id' => $album_id,
                'user_id' => $user_id
            )))
        return false;

    do_action('xt_deleted_share_album', $id);
    $count = xt_update_album_count($album_id);
    return $count;
}

/**
 * Inserts a album to the database.
 *
 * @param array $albumdata Contains information on the album.
 * @return int The new album's ID.
 */
function xt_insert_share_album($albumdata) {
    global $wpdb;
    extract(stripslashes_deep($albumdata), EXTR_SKIP);
    $_share = get_share($id);
    if (!empty($_share)) {
        $data = compact('user_id', 'user_name', 'id', 'album_id', 'create_date', 'create_date_gmt');
        if ($wpdb->insert(XT_TABLE_SHARE_ALBUM, $data)) {
            $count = xt_update_album_count($album_id, $_share);
            return $id;
        }
    }
    return 0;
}

/**
 * Adds a new share2album to the database.
 *
 *
 * @param array $albumdata Contains information on the share2album.
 * @return int The ID of the album after adding.
 */
function xt_new_share_album($albumdata) {

    $albumdata['user_id'] = (int) $albumdata['user_id'];
    $albumdata['user_name'] = $albumdata['user_name'];
    $albumdata['id'] = $albumdata['id'];
    $albumdata['album_id'] = $albumdata['album_id'];
    $albumdata['create_date'] = current_time('mysql');
    $albumdata['create_date_gmt'] = current_time('mysql', 1);

    $id = xt_insert_share_album($albumdata);

    return $id;
}

/**
 * Inserts a album to the database.
 *
 * The available album data key names are  'create_date','parent', 'status', and 'user_id'.
 *
 *
 * @param array $albumdata Contains information on the album.
 * @return int The new album's ID.
 */
function xt_insert_album($albumdata) {
    global $wpdb;
    extract(stripslashes_deep($albumdata), EXTR_SKIP);

    if (!isset($create_date))
        $create_date = current_time('mysql');

    $data = compact('user_id', 'user_name', 'title', 'content', 'create_date', 'create_date_gmt', 'update_date', 'update_date_gmt');
    if ($wpdb->insert(XT_TABLE_ALBUM, $data)) {
        $count = xt_update_user_album_count($user_id);
        return $wpdb->insert_id;
    }
    return 0;
}

/**
 * Adds a new album to the database.
 *
 *
 * @param array $albumdata Contains information on the album.
 * @return int The ID of the album after adding.
 */
function xt_new_album($albumdata) {

    $albumdata['user_id'] = (int) $albumdata['user_id'];
    $albumdata['user_name'] = $albumdata['user_name'];
    $albumdata['title'] = strip_tags($albumdata['title']);
    $albumdata['content'] = strip_tags(isset($albumdata['content']) ? $albumdata['content'] : '');
    $albumdata['create_date'] = current_time('mysql');
    $albumdata['create_date_gmt'] = current_time('mysql', 1);
    $albumdata['update_date'] = current_time('mysql');
    $albumdata['update_date_gmt'] = current_time('mysql', 1);

    $id = xt_insert_album($albumdata);

    return $id;
}

function xt_setup_albumdata($album) {
    do_action_ref_array('xt_the_album', array(
        & $album
    ));
    return true;
}

//Define GLOBAL query
global $xt_the_album_query;
$xt_the_album_query = new XT_Album_Query();
$xt_album_query = & $xt_the_album_query;