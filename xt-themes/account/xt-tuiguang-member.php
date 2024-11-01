<?php
$user = wp_get_current_user();
if (!$user->exists()) {
    exit('您尚未登录');
}

$_result = query_users(array(
    'parent' => serialize(array(
        'id' => (string) $user->ID,
        'name' => $user->user_login
    )),
    'parent_id' => $user->ID,
    'page' => absint($_POST['page']),
    'user_per_page' => 15
        ));
$_users = $_result['users'];
$_total = $_result['total'];
?>
<table class="table table-striped table-hover table-bordered">
    <colgroup>
        <col style="width:200px;"/>
        <col/>
    </colgroup>
    <thead>
        <tr>
            <th>用户名</th>
            <th>注册时间</th>
        </tr>
    </thead>	
    <tbody>	
        <?php
        if ($_total > 0) {
            foreach ($_users as $_u) {
                $_user_registered = gmdate('Y-m-d H:i:s', (mysql2date('G', $_u->user_registered) + (get_option('gmt_offset') * 3600)));
                echo "<tr><td class=\"xt-td-center\">$_u->user_login</td><td class=\"xt-td-center\">$_user_registered</td></tr>";
            }
        }
        ?>

    </tbody>
</table>
<?php if ($_total == 0) { ?>
    <div class="well xt-noresult">
        <div>暂无推广记录 | 暂无符合查询条件的推广信息</div>
    </div>
    <?php
} else {
    echo '<div id="X_Pagination-Bottom" class="clearfix">';
    xt_users_paging_text();
    echo '<div class="pagination xt-pagination-links xt-account-pagination-links">';
    xt_users_pagination_links();
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
?>				
