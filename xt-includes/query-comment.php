<?php

function xt_have_comments() {
    global $xt_comment_query;
    return $xt_comment_query->have_comments();
}

function xt_in_the_comment_loop() {
    global $xt_comment_query;
    return $xt_comment_query->in_the_loop;
}

function xt_rewind_comments() {
    global $xt_comment_query;
    return $xt_comment_query->rewind_comments();
}

function xt_the_comment() {
    global $xt_comment_query;
    $xt_comment_query->the_comment();
}

function xt_check_comment($author, $comment, $user_ip) {
    global $wpdb;

    if (1 == get_option('comment_moderation'))
        return false; // If moderation is set to manual

    $comment = apply_filters('xt_comment_text', $comment);

    // Check # of external links
    if ($max_links = get_option('comment_max_links')) {
        $num_links = preg_match_all('/<a [^>]*href/i', $comment, $out);
        if ($num_links >= $max_links)
            return false;
    }

    $mod_keys = trim(get_option('moderation_keys'));
    if (!empty($mod_keys)) {
        $words = explode("\n", $mod_keys);

        foreach ((array) $words as $word) {
            $word = trim($word);

            // Skip empty lines
            if (empty($word))
                continue;

            // Do some escaping magic so that '#' chars in the
            // spam words don't break things:
            $word = preg_quote($word, '#');

            $pattern = "#$word#i";
            if (preg_match($pattern, $author))
                return false;
            if (preg_match($pattern, $comment))
                return false;
            if (preg_match($pattern, $user_ip))
                return false;
        }
    }
    return true;
}

function & xt_get_comment(& $comment, $output = OBJECT) {
    global $wpdb;
    $null = null;

    if (empty($comment)) {
        if (isset($GLOBALS['xt_comment']))
            $_comment = & $GLOBALS['xt_comment'];
        else
            $_comment = null;
    }
    elseif (is_object($comment)) {
        $_comment = $comment;
    } else {
        if (isset($GLOBALS['xt_comment']) && ($GLOBALS['xt_comment']->id == $comment)) {
            $_comment = & $GLOBALS['xt_comment'];
        } else {
            $_comment = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . XT_TABLE_SHARE_COMMENT . " WHERE id = %d LIMIT 1", $comment));
            if (!$_comment)
                return $null;
        }
    }

    $_comment = apply_filters('xt_get_comment', $_comment);

    if ($output == OBJECT) {
        return $_comment;
    } elseif ($output == ARRAY_A) {
        $__comment = get_object_vars($_comment);
        return $__comment;
    } elseif ($output == ARRAY_N) {
        $__comment = array_values(get_object_vars($_comment));
        return $__comment;
    } else {
        return $_comment;
    }
}

function & query_comments($args = '') {
    unset($GLOBALS['xt_comment_query']);
    $GLOBALS['xt_comment_query'] = new XT_Comment_Query();
    $_result = $GLOBALS['xt_comment_query']->query($args);
    return $_result;
}

class XT_Comment_Query {

    var $comments;
    var $comment_count = 0;
    var $found_comments = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $comment;
    var $paginate_links = '';

    function init() {
        unset($this->comments);
        $this->comment_count = 0;
        $this->found_comments = 0;
        $this->current_comment = -1;
        $this->in_the_loop = false;

        unset($this->comment);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;

        $this->init();

        $defaults = array(
            'id' => '',
            'page' => 1,
            'comment_per_page' => 10,
            'share_id' => 0,
            'user_id' => '',
            's' => '',
            'status' => 1,
            'is_user' => 1
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_comments', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $share_id = absint($share_id);
        $page = absint($page);
        $comment_per_page = absint($comment_per_page);

        $fields = XT_TABLE_SHARE_COMMENT . '.*';
        $join = '';
        $where = ' status=' . absint($status);

        if ($is_user) {
            $fields .= " ,$wpdb->usermeta.meta_value as avatar";
            $join = " LEFT JOIN $wpdb->usermeta ON $wpdb->usermeta.user_id = " . XT_TABLE_SHARE_COMMENT . ".user_id AND $wpdb->usermeta.meta_key='" . XT_USER_AVATAR . "' ";
        }
        if ($page && $comment_per_page)
            $limits = $wpdb->prepare("LIMIT %d, %d", ($page - 1) * $comment_per_page, $comment_per_page);
        else {
            $limits = '';
        }

        if (!empty($share_id))
            $where .= $wpdb->prepare(' AND share_id = %d', $share_id);
        if ('' !== $user_id)
            $where .= $wpdb->prepare(' AND user_id = %d', $user_id);
        if (!empty($s)) {
            $where .= " AND (" . XT_TABLE_SHARE_COMMENT . ".content like '%" . $wpdb->escape($s) . "%' OR " . XT_TABLE_SHARE_COMMENT . ".user_name like '%" . $wpdb->escape($s) . "%') ";
        }
        $sql = "SELECT $fields FROM " . XT_TABLE_SHARE_COMMENT . " $join WHERE $where ORDER BY create_date_gmt DESC $limits";
        $paged_comments = $wpdb->get_results($sql);
        $paged_comments = apply_filters_ref_array('xt_the_comments', array(
            $paged_comments,
            & $this
                ));
        $total_sql = "SELECT COUNT(*) FROM " . XT_TABLE_SHARE_COMMENT . " WHERE $where";
        $total_comments = $wpdb->get_var($total_sql);
        unset($sql, $total_sql);

        $this->found_comments = $total_comments;
        $this->comments = $paged_comments;
        $this->comment_count = count($paged_comments);

        if ($total_comments > 1 || $page > 1) {
            $total_page = ceil($total_comments / $comment_per_page);
            $this->paginate_links = paginate_links(array(
                'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_share' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
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
            'comments' => $paged_comments,
            'total' => $total_comments
        );
    }

    function get($query_var) {
        if (isset($this->query_vars[$query_var]))
            return $this->query_vars[$query_var];

        return '';
    }

    function get_search_sql($string, $cols) {
        $string = esc_sql(like_escape($string));

        $searches = array();
        foreach ($cols as $col)
            $searches[] = "$col LIKE '%$string%'";

        return ' AND (' . implode(' OR ', $searches) . ')';
    }

    function next_comment() {

        $this->current_comment++;

        $this->comment = $this->comments[$this->current_comment];
        return $this->comment;
    }

    function the_comment() {
        global $xt_comment;
        $this->in_the_loop = true;

        if ($this->current_comment == -1) // loop has just started
            do_action_ref_array('xt_comment_loop_start', array(
                & $this
            ));

        $xt_comment = $this->next_comment();
        xt_setup_commentdata($xt_comment);
    }

    function have_comments() {
        if ($this->current_comment + 1 < $this->comment_count) {
            return true;
        } elseif ($this->current_comment + 1 == $this->comment_count && $this->comment_count > 0) {
            do_action_ref_array('xt_comment_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_comments();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_comments() {
        $this->current_comment = -1;
        if ($this->comment_count > 0) {
            $this->comment = $this->comments[0];
        }
    }

}

function xt_allow_comment($commentdata) {
    global $wpdb;
    extract($commentdata, EXTR_SKIP);

    // Simple duplicate check
    $dupe = "SELECT id FROM " . XT_TABLE_SHARE_COMMENT . " WHERE share_id = '$share_id' AND user_id = '$user_id' AND content = '$content' LIMIT 1";

    if ($wpdb->get_var($dupe)) {
        do_action('xt_comment_duplicate_trigger', $commentdata);
        if (defined('DOING_AJAX'))
            die('重复评论啦!');

        wp_die('重复评论啦!');
    }

    do_action('xt_check_comment_flood', $ip, $create_date);

    if (isset($user_id) && $user_id) {
        $userdata = get_userdata($user_id);
        $user = new WP_User($user_id);
        $share_author = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . XT_TABLE_SHARE . " WHERE ID = %d LIMIT 1", $share_id));
    }

    if (isset($userdata) && ($user_id == $share_author || $user->has_cap('moderate_comments'))) {
        // The author and the admins get respect.
        $approved = 1;
    } else {
        // Everyone else's comments will be checked.
        if (xt_check_comment($user_name, $content, $ip))
            $approved = 1;
        else
            $approved = 0;
        if (wp_blacklist_check($user_name, '', '', $content, $ip, ''))
            $approved = 'spam';
    }

    $approved = apply_filters('xt_pre_status', $approved, $commentdata);
    return $approved;
}

function xt_check_comment_flood_db($ip, $date) {
    global $wpdb;
    if (current_user_can('manage_options'))
        return; // don't throttle admins
    $hour_ago = gmdate('Y-m-d H:i:s', time() - 3600);
    if ($lasttime = $wpdb->get_var($wpdb->prepare("SELECT `create_date` FROM `" . XT_TABLE_SHARE_COMMENT . "` WHERE `create_date` >= %s ORDER BY `create_date` DESC LIMIT 1", $hour_ago, $ip))) {
        $time_lastcomment = mysql2date('U', $lasttime, false);
        $time_newcomment = mysql2date('U', $date, false);
        $flood_die = apply_filters('xt_comment_flood_filter', false, $time_lastcomment, $time_newcomment);
        if ($flood_die) {
            do_action('xt_comment_flood_trigger', $time_lastcomment, $time_newcomment);

            if (defined('DOING_AJAX'))
                die('评论过快,请稍后再评论');

            wp_die('评论过快,请稍后再评论', '', array(
                'response' => 403
            ));
        }
    }
}

function xt_comment_delete($ids) {
    global $wpdb;
    $ids = explode(',', $ids);
    if (!empty($ids)) {
        foreach ($ids as $id) {
            xt_delete_comment(intval($id));
        }
    }
}

function xt_delete_comment($id, $force_delete = false) {
    global $wpdb;
    if (!$comment = xt_get_comment($id))
        return false;

    do_action('xt_delete_comment', $id);

    if (!$wpdb->delete(XT_TABLE_SHARE_COMMENT, array(
                'id' => $id
            )))
        return false;
    do_action('xt_deleted_comment', $id);

    $share_id = $comment->share_id;
    if ($share_id && $comment->status == 1)
        xt_update_comment_count($share_id);

    return true;
}

function xt_insert_comment($commentdata) {
    global $wpdb;
    extract(stripslashes_deep($commentdata), EXTR_SKIP);

    if (!isset($create_date))
        $create_date = current_time('mysql');
    if (!isset($comment_date_gmt))
        $comment_date_gmt = get_gmt_from_date($create_date);
    if (!isset($status))
        $status = 1;
    if (!isset($user_id))
        $user_id = 0;

    $data = compact('share_id', 'user_name', 'ip', 'create_date', 'create_date_gmt', 'content', 'status', 'type', 'user_id');
    $wpdb->insert(XT_TABLE_SHARE_COMMENT, $data);

    $id = (int) $wpdb->insert_id;

    return $id;
}

function xt_filter_comment($commentdata) {
    $commentdata['user_id'] = apply_filters('xt_pre_user_id', $commentdata['user_id']);
    $commentdata['user_name'] = apply_filters('xt_pre_user_name', $commentdata['user_name']);
    $commentdata['content'] = apply_filters('xt_pre_comment_content', $commentdata['content']);
    $commentdata['ip'] = apply_filters('xt_pre_comment_ip', $commentdata['ip']);
    $commentdata['filtered'] = true;
    return $commentdata;
}

function xt_throttle_comment_flood($block, $time_lastcomment, $time_newcomment) {
    if ($block) // a plugin has already blocked... we'll let that decision stand
        return $block;
    if (($time_newcomment - $time_lastcomment) < 15)
        return true;
    return false;
}

function xt_new_comment($commentdata) {
    $commentdata = apply_filters('xt_preprocess_comment', $commentdata);

    $commentdata['share_id'] = (int) $commentdata['share_id'];
    $commentdata['user_id'] = (int) $commentdata['user_id'];

    $commentdata['ip'] = preg_replace('/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR']);

    $commentdata['create_date'] = current_time('mysql');
    $commentdata['create_date_gmt'] = current_time('mysql', 1);

    $commentdata = xt_filter_comment($commentdata);

    $commentdata['status'] = xt_allow_comment($commentdata);

    $id = xt_insert_comment($commentdata);

    if ($id > 0) {
        if ($commentdata['status'] == 1) {
            xt_update_comment_count($commentdata['share_id']);
            xt_update_share_comments($commentdata['share_id']); //更新share cache_data
        }
    }

    return $id;
}

function xt_update_share_comments($share_id) {
    $_share = get_share($share_id);
    if (!empty($_share)) {
        global $wpdb;
        $result = query_comments(array(
            'share_id' => $share_id,
            'page' => 1,
            'comment_per_page' => 2
                ));
        $comments = isset($result['comments']) ? $result['comments'] : array();
        $cache_data = unserialize($_share->cache_data);
        $_comments = array();
        foreach ($comments as $c) {
            $cc['user_id'] = $c->user_id;
            $cc['nick'] = $c->user_name;
            $cc['pic_url'] = $c->avatar;
            $cc['content'] = $c->content;
            $_comments[] = $cc;
        }
        $cache_data['comment']['comments'] = $_comments;
        $cache_data['comment']['total'] = $result['total'];
        $wpdb->update(XT_TABLE_SHARE, array(
            'cache_data' => serialize($cache_data)
                ), array(
            'id' => $share_id
        ));
    }
}

function xt_set_comment_status($id, $comment_status, $wp_error = false) {
    global $wpdb;

    $status = '0';
    switch ($comment_status) {
        case '0' :
            $status = '0';
            break;
        case '1' :
            $status = '1';
            if (get_option('comments_notify')) {
                $comment = xt_get_comment($id);
                xt_notify_shareauthor($id, $comment->comment_type); //TODO
            }
            break;
        default :
            return false;
    }

    $comment_old = clone xt_get_comment($id);

    if (!$wpdb->update(XT_TABLE_SHARE_COMMENT, array(
                'status' => $status
                    ), array(
                'id' => $id
            ))) {
        if ($wp_error)
            return new WP_Error('db_update_error', '评论状态更新失败', $wpdb->last_error);
        else
            return false;
    }

    $comment = xt_get_comment($id);

    do_action('xt_set_comment_status', $id, $comment_status);

    xt_update_comment_count($comment->share_id);

    return true;
}

function xt_update_comment($commentarr) {
    global $wpdb;

    // First, get all of the original fields
    $comment = xt_get_comment($commentarr['id'], ARRAY_A);

    // Escape data pulled from DB.
    $comment = esc_sql($comment);

    $old_status = $comment['status'];

    // Merge old and new fields with new fields overwriting old ones.
    $commentarr = array_merge($comment, $commentarr);

    $commentarr = xt_filter_comment($commentarr);

    // Now extract the merged array.
    extract(stripslashes_deep($commentarr), EXTR_SKIP);

    $content = apply_filters('xt_comment_save_pre', $content);

    $create_date = get_gmt_from_date($create_date);

    if (!isset($status))
        $status = 1;
    else
    if ('unapproved' == $status)
        $status = 0;
    else
    if ('approved' == $status)
        $status = 1;

    $data = compact('content', 'user_name', 'status', 'create_date');
    $rval = $wpdb->update(XT_TABLE_SHARE_COMMENT, $data, compact('id'));

    xt_update_comment_count($share_id);
    do_action('xt_edit_comment', $id);
    $comment = xt_get_comment($id);
    return $rval;
}

function xt_defer_comment_counting($defer = null) {
    static $_xt_defer = false;

    if (is_bool($defer)) {
        $_xt_defer = $defer;
        // flush any deferred counts
        if (!$defer)
            xt_update_comment_count(null, true);
    }

    return $_xt_defer;
}

function xt_update_comment_count($share_id, $do_deferred = false) {
    static $_xt_deferred = array();

    if ($do_deferred) {
        $_xt_deferred = array_unique($_xt_deferred);
        foreach ($_xt_deferred as $i => $_share_id) {
            xt_update_comment_count_now($_share_id);
            unset($_xt_deferred[$i]);/** @todo Move this outside of the foreach and reset $_xt_deferred to an array instead */
        }
    }

    if (xt_defer_comment_counting()) {
        $_xt_deferred[] = $share_id;
        return true;
    } elseif ($share_id) {
        return xt_update_comment_count_now($share_id);
    }
}

function xt_update_comment_count_now($share_id) {
    global $wpdb;
    $share_id = (int) $share_id;
    if (!$share_id)
        return false;
    if (!$share = get_share($share_id))
        return false;

    $old = (int) $share->comment_count;
    $new = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . XT_TABLE_SHARE_COMMENT . " WHERE share_id = %d AND status = 1", $share_id));
    $wpdb->update(XT_TABLE_SHARE, array(
        'comment_count' => $new
            ), array(
        'id' => $share_id
    ));

    do_action('xt_update_comment_count', $share_id, $new, $old);
    do_action('xt_edit_post', $share_id, $share);

    return true;
}

function xt_setup_commentdata($comment) {

    do_action_ref_array('xt_the_comment', array(
        & $comment
    ));

    return true;
}

//Define GLOBAL query
global $xt_the_comment_query;
$xt_the_comment_query = new XT_Comment_Query();
$xt_comment_query = & $xt_the_comment_query;