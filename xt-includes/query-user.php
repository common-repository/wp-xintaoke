<?php


function xt_user_byguid($guid) {
    global $wpdb;
    $user_id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='" . XT_USER_GUID . "' AND meta_value=%s", $guid));
    if (!empty($user_id)) {
        return $user_id;
    }
    return 0;
}

function xt_user_guid($user_id = 0) {
    if ($user_id == 0) {
        $user_id = get_current_user_id();
    }
    if ($user_id == 0) {
        return false;
    }
    $guid = get_user_meta($user_id, XT_USER_GUID, true);
    if (empty($guid)) {
        $guid = xt_generate_guid();
        _xt_user_guid($guid);
        update_user_meta($user_id, XT_USER_GUID, $guid);
    }
    return $guid;
}

function _xt_user_guid(&$guid) {
    global $wpdb;
    $_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key='" . XT_USER_GUID . "' AND meta_value=%s", $guid));
    if ($_count == 0) {
        return $guid;
    } else {
        $guid = xt_generate_guid();
        _xt_user_guid($guid);
    }
}

function xt_generate_guid() {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < 4; $i++) {
        $password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
    }

    // random_password filter was previously in random_password function which was deprecated
    return apply_filters('random_password', $password);
}

function xt_cron_user_account() {
    global $wpdb;
    $ids = $wpdb->get_col('SELECT ID FROM ' . $wpdb->users . ' WHERE user_login not like \'xt_%\'');
    foreach ($ids as $id) {
        xt_update_user_account_counts($id);
    }
}

function xt_update_user_account_counts($user_id, $cash = true, $jifen = true) {
    if ($cash) {
        xt_update_user_count($user_id, XT_USER_COUNT_CASH, xt_user_total_fanxian($user_id));
    }
    if ($jifen) {
        xt_update_user_count($user_id, XT_USER_COUNT_JIFEN, xt_user_total_jifen($user_id));
    }
}

function xt_update_user_account_cost_counts($user_id, $cash = true, $jifen = true) {
    if ($cash) {
        $tixians = xt_total_tixian($user_id);
        xt_update_user_count($user_id, XT_USER_COUNT_CASH_COST, $tixians[0] + $tixians[1]);
    }
    if ($jifen) {
        $jifenOrder = xt_user_total_jifen_order($user_id);
        xt_update_user_count($user_id, XT_USER_COUNT_JIFEN_COST, $jifenOrder[0] + $jifenOrder[1]);
    }
}

function xt_user_counts($counts) {
    if (!empty($counts)) {
        return array_merge(xt_default_counts(), unserialize($counts));
    }
    return xt_default_counts();
}

function xt_row_user($user, $count) {
    global $wp_roles;
    $_roles = unserialize($user->role);
    $_role = array();
    if (!empty($_roles)) {
        foreach ($_roles as $_r => $_v) {
            if ($_v) {
                $_role[] = isset($wp_roles->role_names[$_r]) ? translate_user_role($wp_roles->role_names[$_r]) : '未知';
            }
        }
    }
    $_counts = xt_user_counts($user->counts);
    $_parent = array();
    if (!empty($user->parent)) {
        $_parent = unserialize($user->parent);
    }
    $parent_url = 'http://' . add_query_arg(array('s' => '', 'paged' => 1, 'issys' => -1, 'parent_id' => 'PARENT_ID'), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    ?>
    <tr id="user-<?php echo $user->ID; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td><?php echo $user->ID; ?></td>
        <td><span><?php echo $user->user_login; ?></span></td>
        <td><span><?php echo implode(',', $_role); ?></span></td>
        <td><?php echo!empty($_parent) ? ('<a href="' . str_replace('PARENT_ID', $_parent['id'], $parent_url) . '">' . $_parent['name'] . '</a>') : '' ?></td>
        <td><?php echo $user->user_email ?></td>
        <td>
            <p>现金：<?php echo $_counts[XT_USER_COUNT_CASH]; ?></p>
            <p><?php echo xt_jifenbao_text(); ?>：<?php echo $_counts[XT_USER_COUNT_JIFEN] ?></p>
            <p>分享：<?php echo $_counts[XT_USER_COUNT_SHARE] ?></p>
            <p>专辑：<?php echo $_counts[XT_USER_COUNT_ALBUM] ?></p>
        </td>
        <td><?php echo gmdate('Y-m-d H:i:s', (mysql2date('G', $user->user_registered) + (get_option('gmt_offset') * 3600))) ?></td>
    </tr>
    <?php
}

function xt_total_user() {
    global $wpdb;
    $result = $wpdb->get_row('SELECT COUNT(id) AS total,COUNT(NULLIF(user_login LIKE \'xt_%\',false)) AS system FROM ' . $wpdb->users, ARRAY_A);
    $_result = array(
        'total' => 0,
        'system' => 0
    );
    if (!empty($result)) {
        $_result['total'] = $result['total'] > 0 ? $result['total'] : 0;
        $_result['system'] = $result['system'] > 0 ? $result['system'] : 0;
    }
    return $_result;
}

function xt_users_pagination_links() {
    echo xt_get_users_pagination_links();
}

function xt_get_users_pagination_links() {
    global $xt_user_query;
    return apply_filters('xt_get_users_pagination_links', $xt_user_query->paginate_links);
}

function xt_users_paging_text() {
    echo xt_get_users_paging_text();
}

function xt_get_users_paging_text() {
    global $xt_user_query;
    return apply_filters('xt_users_paging_text', $xt_user_query->paging_text);
}

function get_the_user_container($_params = array(), $isCatalog = false, $isAjax = false, $isScroll = true) {
    $isFollow = false;
    $isFans = false;
    $msg = 'user_not_found';
    if (isset($_params['follow']) && $_params['follow']) {
        $isFollow = true;
        $msg = 'user_follow_not_found';
    }
    if (isset($_params['fans']) && $_params['fans']) {
        $isFans = true;
        $msg = 'user_fans_not_found';
    }
    echo '<div id="X_Wall-Result" class="clearfix">';
    echo '<div id="X_Wall-Container" class="xt-wall-container row" data-scroll="' . ($isScroll ? 'true' : 'false') . '">';
    echo '<ul class="nav nav-pills xt-user-nav-pills"><li ' . ($isFans ? 'class="active"' : '') . '><a id="X_User-Fans-Nav" href="javascript:;">粉丝列表</a></li><li ' . ($isFollow ? 'class="active"' : '') . '><a id="X_User-Follow-Nav" href="javascript:;">关注列表</a></li></ul>';
    echo '<div class="media-list clearfix xt-user-media-list">';
    $_count = 0;
    while (xt_have_users()) {
        xt_the_user();
        get_the_user_template($_count);
        $_count++;
    }
    if ($_count == 0) {
        echo xt_not_found($msg);
    }
    echo '</div>';
    echo '</div>';
    echo '<div id="X_Pagination-Bottom" class="clearfix">';
    echo '<div class="pagination xt-pagination-links">';
    xt_users_pagination_links();
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

function get_the_user_template($count) {
    global $xt_user_follow;
    $_user = wp_get_current_user();
    if ($_user->exists()) {
        if (empty($xt_user_follow)) {
            $xt_user_follow = get_user_meta($_user->ID, XT_USER_FOLLOW, true);
        }
        if (empty($xt_user_follow)) {
            $xt_user_follow = array(
                $_user->ID
            );
        }
    }
    ?>
    <div class="xt-share xt-share-user media<?php echo $count % 3 == 2 ? ' xt-last' : '' ?>">
        <a class="pull-left" target="_blank" href="<?php xt_the_user_url(); ?>">
            <img class="media-object" src="<?php xt_the_user_pic(); ?>">
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a target="_blank" href="<?php xt_the_user_url(); ?>"><?php xt_the_user_title(); ?></a></h4>
            <div class="media">
                <small>
                    <span>关注</span><span><?php echo (xt_the_user_followcount()) ?></span>&nbsp;&nbsp;
                    <span>粉丝</span><span><?php echo (xt_the_user_fanscount()) ?></span>&nbsp;&nbsp;
                    <span>宝贝</span><span><?php echo (xt_get_the_user_sharecount() + xt_get_the_user_fav_sharecount()) ?></span>&nbsp;&nbsp;
                    <span>喜欢</span><span><?php echo (xt_get_the_user_fav_sharecount() + xt_get_the_user_fav_albumcount()) ?></span>
                </small>
                <?php if (!xt_is_self(xt_get_the_user_id())): ?>
                    <div class="xt-user-follow clearfix">
                        <?php if (!empty($xt_user_follow) && in_array((int) xt_get_the_user_id(), $xt_user_follow)): ?>
                            <a class="xt-unfollow" href="javascript:;" data-userid="<?php xt_the_user_id(); ?>">取消关注</a> <a class="xt-sendmsg" href="javascript:;" name="<?php xt_the_user_title(); ?>">发私信</a>
                        <?php else: ?>
                            <a class="xt-follow" href="javascript:;" data-userid="<?php xt_the_user_id(); ?>">+ 加关注</a> <a class="xt-sendmsg" href="javascript:;" name="<?php xt_the_user_title(); ?>">发私信</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function xt_have_users() {
    global $xt_user_query;
    return $xt_user_query->have_users();
}

function xt_in_the_user_loop() {
    global $xt_user_query;
    return $xt_user_query->in_the_loop;
}

function xt_rewind_users() {
    global $xt_user_query;
    return $xt_user_query->rewind_users();
}

function xt_the_user() {
    global $xt_user_query;
    $xt_user_query->the_user();
}

function & query_users($args = '') {
    unset($GLOBALS['xt_user_query']);
    $GLOBALS['xt_user_query'] = new XT_User_Query();
    $result = $GLOBALS['xt_user_query']->query($args);
    return $result;
}

class XT_User_Query {

    var $users;
    var $user_count = 0;
    var $found_users = 0;
    var $current_share = -1;
    var $in_the_loop = false;
    var $user;
    var $paginate_links = '';
    var $paging_text = '';

    function init() {
        unset($this->users);
        $this->user_count = 0;
        $this->found_users = 0;
        $this->current_user = -1;
        $this->in_the_loop = false;

        unset($this->user);
        $this->paginate_links = '';
    }

    function query($query_vars) {
        global $wpdb;
        $this->init();

        $defaults = array(
            's' => '',
            'follow' => 0,
            'fans' => 0,
            'page' => 1,
            'user_per_page' => xt_userperpage(),
            'isadmin' => 0,
            'issys' => 0,
            'parent' => '',
            'parent_id' => 0
        );

        $this->query_vars = wp_parse_args($query_vars, $defaults);
        do_action_ref_array('xt_pre_get_users', array(
            & $this
        ));
        extract($this->query_vars, EXTR_SKIP);

        $page = absint($page);
        $user_per_page = absint($user_per_page);

        $fields = " $wpdb->users.*,xt_avatar.meta_value as avatar,xt_counts.meta_value as counts ";
        $table = " $wpdb->users ";
        $join = " LEFT JOIN $wpdb->usermeta AS xt_avatar ON xt_avatar.user_id = $wpdb->users.ID AND xt_avatar.meta_key='" . XT_USER_AVATAR . "' LEFT JOIN $wpdb->usermeta AS xt_counts ON xt_counts.user_id = $wpdb->users.ID AND xt_counts.meta_key='" . XT_USER_COUNT . "' ";
        $where = "";
        $order = " ORDER BY $wpdb->users.user_registered DESC";
        $limits = "";

        if ($isadmin) {
            $fields = " $wpdb->users.*,xt_role.meta_value as role,xt_counts.meta_value as counts,xt_parent.meta_value as parent ";
            $join = " LEFT JOIN $wpdb->usermeta AS xt_counts ON xt_counts.user_id = $wpdb->users.ID AND xt_counts.meta_key='" . XT_USER_COUNT . "' ";
            $join .= " LEFT JOIN $wpdb->usermeta AS xt_role ON xt_role.user_id = $wpdb->users.ID AND xt_role.meta_key='" . ($wpdb->base_prefix . 'capabilities') . "' ";
            if (!empty($parent)) {
                $parents = maybe_unserialize($parent);
                $join .= $wpdb->prepare(" INNER JOIN $wpdb->usermeta AS xt_parent ON xt_parent.user_id = $wpdb->users.ID AND xt_parent.meta_key = '" . XT_USER_PARENT . "' AND xt_parent.meta_value=%s", $parent);
            } else {
                $join .= " LEFT JOIN $wpdb->usermeta AS xt_parent ON xt_parent.user_id = $wpdb->users.ID AND xt_parent.meta_key='" . XT_USER_PARENT . "' ";
            }
            if ($issys != -1) {
                if ($issys) {
                    $where .= " AND $wpdb->users.user_login like 'xt_%' ";
                } else {
                    $where .= " AND $wpdb->users.user_login not like 'xt_%' ";
                }
            }
        } elseif ($follow > 0) {
            $table = XT_TABLE_USER_FOLLOW . " AS follow ";
            $join = " INNER JOIN $wpdb->users ON follow.user_id=$wpdb->users.ID " . $join;
            $where .= $wpdb->prepare(" AND follow.f_user_id=%d ", $follow);
            $order = " ORDER BY follow.create_time DESC ";
        } elseif ($fans > 0) {
            $table = XT_TABLE_USER_FOLLOW . " AS follow ";
            $join = " INNER JOIN $wpdb->users ON follow.f_user_id=$wpdb->users.ID " . $join;
            $where .= $wpdb->prepare(" AND follow.user_id=%d ", $fans);
            $order = " ORDER BY follow.create_time DESC ";
        } elseif (!empty($parent)) {
            $parents = maybe_unserialize($parent);
            $fields = " $wpdb->users.* ";
            $join = $wpdb->prepare(" INNER JOIN $wpdb->usermeta AS parent ON parent.user_id = $wpdb->users.ID AND parent.meta_key = '" . XT_USER_PARENT . "' AND parent.meta_value=%s", $parent);
        }
        $search = trim(trim($s), '*');
        if (!empty($search)) {
            $search_columns = array();
            if (!empty($search)) {
                if (false !== strpos($search, '@'))
                    $search_columns = array('user_email');
                elseif (is_numeric($search))
                    $search_columns = array('user_login', 'ID');
                else
                    $search_columns = array('user_login', 'user_nicename', 'display_name');
                $where.=$this->get_search_sql($search, $search_columns);
            }
        }
        if ($user_per_page && $page)
            $limits = $wpdb->prepare("LIMIT %d, %d", intval(($page - 1) * $user_per_page), intval($user_per_page));

        $sql = "SELECT $fields FROM {$table} {$join} WHERE 1=1 {$where} {$order}  {$limits}";
        $paged_users = $wpdb->get_results($sql);
        $paged_users = apply_filters_ref_array('xt_the_users', array(
            $paged_users,
            & $this
                ));
        if (!empty($parent)) {
            $total_sql = "SELECT COUNT(*) FROM {$table} {$join} WHERE 1=1 $where";
        } else {
            $total_sql = "SELECT COUNT(*) FROM {$table} WHERE 1=1 $where";
        }

        $total_users = $wpdb->get_var($total_sql);
        //echo $sql.'|'.$total_sql;

        unset($sql, $total_sql);

        $this->found_users = $total_users;
        $this->users = $paged_users;
        $this->user_count = count($paged_users);

        if ($total_users > 1) {
            $total_page = ceil($total_users / $user_per_page);
            $this->paginate_links = paginate_links(array(
                'base' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_member' ? add_query_arg('paged', '%#%', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) : '#%#%',
                'format' => '',
                'end_size' => 3,
                'total' => $total_page,
                'current' => $page,
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1,
                'type' => isset($_GET['page']) && $_GET['page'] == 'xt_menu_member' ? 'plain' : 'list'
                    ));
            $this->paging_text = sprintf('<span class="displaying-num">当前显示 %s&#8211;%s 条，共 %s 条</span>%s', number_format_i18n(($page - 1) * $user_per_page + 1), number_format_i18n(min($page * $user_per_page, $total_users)), number_format_i18n($total_users), $this->paging_text);
        }

        return array(
            'users' => $paged_users,
            'total' => $total_users
        );
    }

    function get_search_sql($string, $cols) {
        $string = esc_sql($string);

        $searches = array();
        $leading_wild = '%';
        $trailing_wild = '%';
        foreach ($cols as $col) {
            if ('ID' == $col)
                $searches[] = "$col = '$string'";
            else
                $searches[] = "$col LIKE '$leading_wild" . like_escape($string) . "$trailing_wild'";
        }

        return ' AND (' . implode(' OR ', $searches) . ')';
    }

    function next_user() {

        $this->current_user++;

        $this->user = $this->users[$this->current_user];
        return $this->user;
    }

    function the_user() {
        global $xt_user;
        $this->in_the_loop = true;

        if ($this->current_user == -1) // loop has just started
            do_action_ref_array('xt_user_loop_start', array(
                & $this
            ));

        $xt_user = $this->next_user();
        xt_setup_userdata($xt_user);
    }

    function have_users() {
        if ($this->current_user + 1 < $this->user_count) {
            return true;
        } elseif ($this->current_user + 1 == $this->user_count && $this->user_count > 0) {
            do_action_ref_array('xt_user_loop_end', array(
                & $this
            ));
            // Do some cleaning up after the loop
            $this->rewind_users();
        }

        $this->in_the_loop = false;
        return false;
    }

    function rewind_users() {
        $this->current_user = -1;
        if ($this->user_count > 0) {
            $this->user = $this->users[0];
        }
    }

}

function xt_setup_userdata($user) {
    global $xt_user_counts;
    $xt_user_counts = xt_default_counts();
    if (isset($user->counts)) {
        $xt_user_counts = array_merge($xt_user_counts, unserialize($user->counts));
    }
    do_action_ref_array('xt_the_share', array(
        & $share
    ));

    return true;
}

function xt_role_delete($roles) {
    $roles = explode(',', $roles);
    if (!empty($roles)) {
        foreach ($roles as $role) {
            //将原角色中的会员移动到subscriber中
            $ids = get_users(array(
                'role' => $role,
                'fields' => 'ID'
                    ));
            foreach ($ids as $id) {
                $id = (int) $id;
                $user = new WP_User($id);
                $user->set_role('subscriber');
            }
            //删除xt角色
            $_roles = get_option(XT_OPTION_ROLE);
            if (!empty($_roles) && isset($_roles[$role])) {
                unset($_roles[$role]);
                update_option(XT_OPTION_ROLE, $_roles);
            }
            //删除角色
            remove_role($role);
        }
    }
}

//Define GLOBAL query
global $xt_the_user_query;
$xt_the_user_query = new XT_User_Query();
$xt_user_query = & $xt_the_user_query;