<?php
$issys = (isset($_GET['issys']) ? ($_GET['issys']) : 0);
$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$parent_id = (isset($_GET['parent_id']) ? absint($_GET['parent_id']) : 0);
$parent = '';
if ($parent_id > 0) {
    global $wpdb;
    $parent_userlogin = $wpdb->get_var('SELECT user_login FROM ' . $wpdb->users . ' WHERE ID=' . ($parent_id));
    if (!empty($parent_userlogin)) {
        $parent = serialize(array(
            'id' => (string) $parent_id,
            'name' => $parent_userlogin
                ));
    }
}
$_result = query_users(array(
    'user_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'isadmin' => 1,
    'issys' => $issys,
    's' => $s,
    'parent' => $parent
        ));
$_users = $_result['users'];
?>
<div class="clear" style="margin-top:10px;">
    <ul class="subsubsub">
        <li><a href="http://<?php echo add_query_arg(array('parent_id' => '', 'issys' => -1, 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $issys != -1 ? '' : ' class="current"' ?>>全部会员</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('parent_id' => '', 'issys' => 0, 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $issys ? '' : ' class="current"' ?>>注册会员</a> |</li>
        <li><a href="http://<?php echo add_query_arg(array('parent_id' => '', 'issys' => 1, 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"<?php echo $issys != -1 && $issys ? ' class="current"' : '' ?>>系统会员</a></li>
    </ul>
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索会员:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索会员">
    </p>    
</div>

<div class="tablenav top">
    <div class="tablenav-pages" style="float:left;">
        <span class="displaying-num">
            <?php
            xt_users_paging_text();
            ?>
        </span> <span class="pagination-links">
            <?php
            xt_users_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 50px"><?php xt_admin_help_link('member_member')?><span>编号</span></th>
            <th class="manage-column"><span>会员账号</span></th>
            <th class="manage-column" style="width: 150px"><span>角色</span></th>
            <th class="manage-column" style="width: 150px"><span>推荐人</span></th>
            <th class="manage-column" style="width: 150px"><span>邮箱</span></th>
            <th class="manage-column" style="width: 150px"><span>账户</span></th>
            <th class="manage-column" style="width: 120px"><span>注册时间</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 50px"><span>编号</span></th>
            <th class="manage-column"><span>会员账号</span></th>
            <th class="manage-column" style="width: 150px"><span>角色</span></th>
            <th class="manage-column" style="width: 150px"><span>推荐人</span></th>
            <th class="manage-column" style="width: 150px"><span>邮箱</span></th>
            <th class="manage-column" style="width: 150px"><span>账户</span></th>
            <th class="manage-column" style="width: 120px"><span>注册时间</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_user_count = 0;
        foreach ($_users as $user) {
            xt_row_user($user, $_user_count);
            $_user_count++;
        }
        ?>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            if(!s){
                alert('请输入会员ID或账号或昵称或邮箱');
                return false;
            }
            document.location.href = 'http://<?php echo add_query_arg(array('parent_id' => '', 's' => 'SEARCH', 'paged' => 1, 'issys' => -1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
    });	
</script>