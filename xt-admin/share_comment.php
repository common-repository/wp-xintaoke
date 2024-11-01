<?php
$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$_result = query_comments(array(
    'is_user' => 0,
    'comment_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    's' => $s
        ));
$_comments = $_result['comments'];
?>
<div class="clear" style="margin-top:10px;">
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索评论:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索评论">
    </p>    
</div>
<div class="tablenav top">
    <div class="alignleft actions"></div>
    <div class="tablenav-pages">
        <span class="displaying-num">
            <?php
            xt_comments_pagination_count();
            ?>
        </span> 
        <span class="pagination-links">
            <?php
            xt_comments_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 100px"><?php xt_admin_help_link('share_comment')?><span>编号</span></th>
            <th class="manage-column"><span>内容</span></th>
            <th class="manage-column" style="width: 200px"><span>会员</span></th>
            <th class="manage-column" style="width: 150px"><span>IP</span></th>
            <th class="manage-column" style="width: 150px"><span>时间</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 100px"><span>编号</span></th>
            <th class="manage-column"><span>内容</span></th>
            <th class="manage-column" style="width: 200px"><span>会员</span></th>
            <th class="manage-column" style="width: 150px"><span>IP</span></th>
            <th class="manage-column" style="width: 150px"><span>时间</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_comment_count = 0;
        foreach ($_comments as $comment) {
            xt_row_comment($comment, $_comment_count);
            $_comment_count++;
        }
        ?>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
        
        $('a.delete-comment').live('click',function() {
            if(confirm('您确认要删除该评论?')){
                deleteComment($(this).attr('data-value'));	
            }
            return false;
        });
        function deleteComment(ids){
            if (ids) {
                var setting = {};
                setting.action = 'xt_admin_ajax_comment_delete';
                setting.ids = ids;
                $.ajax({
                    type : "post",
                    dataType : "json",
                    url : ajaxurl + '?rand=' + Math.random(),
                    data : setting,
                    success : function(r) {
                        if (r.code > 0) {
                            alert(r.msg);
                        } else {
                            top.location.reload();// 刷新
                        }
                    }
                })
            }
        }
    });	
</script>