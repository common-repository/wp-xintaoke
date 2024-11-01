<style type="text/css">
    .widefat tbody th.check-column{padding-bottom:2px;}.inline-edit-row .error{color:red;}
</style>
<?php
$_catalogs = xt_catalogs_share(true);
$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$_result = query_tags(array(
    'tag_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'cid' => $cid,
    's' => $s
        ));
$_tags = $_result['tags'];
?>
<div class="clear" style="margin-top:10px;">
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索标签:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索标签">
    </p>    
</div>

<div id="col-container" class="clear">
    <div id="col-right">
        <div class="tablenav top">
            <!--<div class="alignleft actions">
                <select name="action">
                    <option value="-1" selected="selected">批量操作</option>
                    <option value="delete">删除</option>
                </select>
                <input type="submit" name="" id="post-query-submit" class="button" value="应用">
            </div>-->
            <div class="alignleft actions">
                <select name="cat" id="filter-cat" class="postform">
                    <option value="0" <?php echo $cid == 0 ? 'selected' : '' ?>>全部</option>
                    <option value="-1" <?php echo $cid == -1 ? 'selected' : '' ?>>未分类</option>
                    <?php
                    if (!empty($_catalogs)) {
                        foreach ($_catalogs as $_cat) {
                            $_selected = '';
                            if ($_cat->id == $cid) {
                                $_selected = 'selected';
                            }
                            echo '<option ' . $_selected . ' class="level-0" value="' . $_cat->id . '">' . $_cat->title . '</option>';
                            if (isset($_cat->child) && !empty($_cat->child)) {
                                $childrens = $_cat->child['catalogs'];
                                if (!empty($childrens)) {
                                    foreach ($childrens as $_subCat) {
                                        $_selected = '';
                                        if ($_subCat->id == $cid) {
                                            $_selected = 'selected';
                                        }
                                        echo '<option ' . $_selected . ' class="level-1" value="' . $_subCat->id . '">&nbsp;&nbsp;&nbsp;' . $_subCat->title . '</option>';
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php xt_tags_pagination_count(); ?>
                </span> <span class="pagination-links">
                    <?php xt_tags_pagination_links(); ?>
                </span>
            </div>
            <br class="clear">
        </div>
        <table class="wp-list-table widefat fixed tags" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column" style="width: 100px"><span>编号</span></th>
                    <th class="manage-column" style="width: 100px"><span>名称</span></th>
                    <th class="manage-column"><span>所属分类</span></th>
                    <th class="manage-column" style="width: 100px"><span>排序</span></th>
                    <th class="manage-column" style="width: 100px"><span>分享</span></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th class="manage-column" style="width: 100px"><span>编号</span></th>
                    <th class="manage-column" style="width: 100px"><span>名称</span></th>
                    <th class="manage-column"><span>所属分类</span></th>
                    <th class="manage-column" style="width: 100px"><span>排序</span></th>
                    <th class="manage-column" style="width: 100px"><span>分享</span></th>
                </tr>
            </tfoot>
            <tbody id="the-list" class="list:tag">
                <?php
                $_tag_count = 0;
                if (!empty($_tags)) {
                    $object_ids = array();
                    foreach ($_tags as $tag) {
                        $object_ids[] = $tag->id;
                    }
                    $object_ids = implode(',', $object_ids);
                    $query = "SELECT t.*, tr.id AS tag_id FROM " . XT_TABLE_CATALOG . " AS t INNER JOIN " . XT_TABLE_SHARE_TAG_CATALOG . " AS tr ON t.id = tr.cid WHERE t.type = 'share' AND tr.id IN ($object_ids) ORDER BY t.sort ASC,t.count DESC";
                    global $wpdb;
                    $terms = $wpdb->get_results($query);
                    $_terms = array();
                    foreach ($terms as $term) {
                        $_term = isset($_terms[$term->tag_id]) ? $_terms[$term->tag_id] : array();
                        $_term[] = $term;
                        $_terms[$term->tag_id] = $_term;
                    }
                    foreach ($_terms as $_tag_id => $_term) {
                        xt_update_catalog_terms_cache($_tag_id, $_term);
                    }
                    foreach ($_tags as $tag) {
                        xt_row_tag($tag, $_tag_count, $cid);
                        $_tag_count++;
                    }
                }
                ?>
            </tbody>
        </table>
        <table style="display: none">
            <tbody>
                <tr id="inline-edit" class="inline-edit-row">
                    <td colspan="5" class="colspanchange">
                        <fieldset class="inline-edit-col-left" style="width:40%;">
                            <div class="inline-edit-col">
                                <h4>快速编辑</h4>
                                <label> <span class="title">名称</span> <span class="input-text-wrap"><input type="text" name="title" disabled class="ptitle" value="" /></span></label> 
                                <label> <span class="title">排序</span> <span class="input-text-wrap"><input type="text" name="sort" class="ptitle" value="" />排序数值越小越靠前</span></label>
                                <label style="display: none;"> <span class="title">当前分类</span> <span class="input-text-wrap"><input type="text" name="cid" class="ptitle" value="" /></span></label>
                            </div>
                        </fieldset>
                        <fieldset class="inline-edit-col-center inline-edit-categories" style="width:50%">
                            <div class="inline-edit-col">
                                <span class="title inline-edit-categories-label">所属分类</span>
                                <input type="hidden" name="share_tag_category[]" value="0">
                                <ul class="cat-checklist category-checklist">
                                    <?php xt_catalog_checklist('share_tag', $_catalogs); ?>
                                </ul>
                            </div>
                        </fieldset>
                        <p class="inline-edit-save submit">
                            <a accesskey="c" href="#inline-edit" title="取消" class="cancel button-secondary alignleft">取消</a> <a accesskey="s" href="#inline-edit" title="更新标签" class="save button-primary alignright">更新标签</a>
                            <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" /> <span class="error" style="display: none;"></span> <br class="clear" />
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="col-left">
        <div class="col-wrap">
            <div class="form-wrap">
                <h3><?php xt_admin_help_link('share_tag')?>添加新标签</h3>
                <form id="addtag" method="post" action="" class="validate">
                    <input type="hidden" name="action" value="add-tag">
                    <div class="form-field form-required">
                        <label for="tag-title">名称</label> <input name="title" id="tag-title" type="text" value="" size="40" aria-required="true">
                        <p>这将是它在站点上显示的名字。</p>
                    </div>
                    <div class="form-field">
                        <label for="catalog-parent">分类</label>
                        <select name="tag_catalog" id="tag-catalog" class="postform">
                            <option value="0">无</option>
                            <?php
                            if (!empty($_catalogs)) {
                                foreach ($_catalogs as $_cat) {
                                    echo '<option class="level-0" value="' . $_cat->id . '">' . $_cat->title . '</option>';
                                    if (isset($_cat->child) && !empty($_cat->child)) {
                                        $childrens = $_cat->child['catalogs'];
                                        if (!empty($childrens)) {
                                            foreach ($childrens as $_subCat) {
                                                echo '<option class="level-1" value="' . $_subCat->id . '">&nbsp;&nbsp;&nbsp;' . $_subCat->title . '</option>';
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button" value="添加新标签">
                        <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" />
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        var what = '#tag-';
	
        $('#submit').click(function() {
            addTag();
            return false;
        });
        $('a.delete-tag').live('click',function() {
            if(confirm('您确认要删除该标签!')){
                deleteTag($(this).attr('data-value'));	
            }
            return false;
        });
        $('#filter-cat').change(function(){
            var cid = $(this).val();
            document.location.href = 'http://<?php echo add_query_arg(array('cid' => 'CID', 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('CID',cid);
        });
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1, 'cid' => $cid), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
        $('.editinline').live('click', function() {
            revertRow();
            var id = $(this).parents('tr:first').attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var editRow = $('#inline-edit').clone(true);
            var rowData = $('#inline_' + id);
            $('td', editRow).attr('colspan',
            $('.widefat:first thead th:visible').length);
            if ($(what + id).hasClass('alternate'))
                $(editRow).addClass('alternate');
            $(what + id).hide().after(editRow);

            $(':input[name="title"]', editRow).val($('.title', rowData).text());
            $(':input[name="sort"]', editRow).val($('.sort', rowData).text());
            $(':input[name="cid"]', editRow).val($('.cid', rowData).text());
            var cids = $('.cids', rowData).text().split(',');
            $(':input[name="share_tag_category[]"]', editRow).each(function(){
                var cid = $(this).val();
                if($.inArray(cid, cids)>-1){
                    $(this).attr('checked',true);
                }else{
                    $(this).attr('checked',false);
                }
            });

            $(editRow).attr('id', 'edit-' + id).addClass('inline-editor').show();
            $('a.cancel', $(editRow)).click(function() {
                revertRow();
                return false;
            });
            $('a.save', $(editRow)).click(function() {
                inline_edit($(editRow));
                return false;
            });

            $('.ptitle', editRow).eq(0).focus();
            return false;
        });
        function revertRow() {
            var id = $('#the-list tr.inline-editor').attr('id');
            if (id) {
                $('#the-list .inline-edit-save .waiting').hide();
                $('#' + id).remove();
                id = id.substr(id.lastIndexOf('-') + 1);
                $(what + id).show();
            }
        }
        function deleteTag(ids) {
            if (ids) {
                var setting = {};
                setting.action = 'xt_admin_ajax_tag_delete';
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
        function addTag() {
            var form = $('#addtag');
            if (!validateForm(form))
                return false;
            var setting = {};
            setting.title = $('#tag-title').val();
            setting.catalog = $('#tag-catalog').val();
            setting.sort = 100;
            setting.action = 'xt_admin_ajax_tag_add';
            $('.waiting', form).show();
            $.ajax({
                type : "post",
                dataType : "html",
                url : ajaxurl + '?rand=' + Math.random(),
                data : setting,
                success : function(r) {
                    if (r == '') {
                        top.location.reload();// 刷新
                    } else {
                        alert(r);
                        $('.waiting', form).hide();
                    }
                }
            })
        }
        function inline_edit(editRow) {
            var id = editRow.attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var sort = editRow.find(':input[name="sort"]').val();
            var cid = editRow.find(':input[name="cid"]').val();
            var cids = [];
            $(':input[name="share_tag_category[]"]', editRow).each(function(){
                if($(this).is(':checked')){
                    cids.push($(this).val());
                }
            });
            
            var error = editRow.find('.error');
            var waiting = editRow.find('.waiting');
            if (!sort) {
                error.text('排序不能为空').show();
                return false;
            } else {
                var reg = /^\d+$/;
                if (!reg.test(sort)) {
                    error.text('排序必须为数字').show();
                    return false;
                }
            }
            var setting = {};
            setting.action = 'xt_admin_ajax_tag_update';

            setting.id = id;
            setting.sort = sort;
            setting.cids = cids.join(',');
            setting.cid = cid;
            setting.alternate = editRow.hasClass('alternate') ? 1 : 0;
            waiting.show();
            error.hide();
            $.ajax({
                type : "post",
                dataType : "html",
                url : ajaxurl + '?rand=' + Math.random(),
                data : setting,
                success : function(r) {
                    var row, new_id;
                    waiting.hide();
                    if (r) {
                        if (-1 != r.indexOf('<tr')) {
                            $(what + id).remove();
                            new_id = $(r).attr('id');

                            $('#edit-' + id).before(r).remove();
                            row = new_id ? $('#' + new_id) : $(what + id);
                            row.hide().fadeIn();
                        } else
                            error.text(r).show();
                    } else
                        error.text('未知错误').show();
                }
            })
        }
    });
</script>