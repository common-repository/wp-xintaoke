<?php
$_catalogs = xt_catalogs_album(true);
$cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
$s = isset($_GET['s']) ? urldecode($_GET['s']) : '';
$_result = query_albums(array(
    'album_per_page' => 50,
    'page' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
    'cid' => $cid,
    's' => $s
        ));
$_albums = $_result['albums'];
?>
<div class="clear" style="margin-top:10px;">
    <p class="search-box">
        <label class="screen-reader-text" for="filter-search-input">搜索专辑:</label>
        <input type="search" id="filter-search-input" name="s" value="<?php echo $s; ?>">
        <input type="button" name="" id="filter-search-submit" class="button" value="搜索专辑">
    </p>    
</div>
<div class="tablenav top">
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
            <?php
            xt_albums_pagination_count();
            ?>
        </span> <span class="pagination-links">
            <?php
            xt_albums_pagination_links();
            ?>
        </span>
    </div>
    <br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
    <thead>
        <tr>
            <th class="manage-column" style="width: 100px"><?php xt_admin_help_link('share_album')?><span>编号</span></th>
            <th class="manage-column"><span>名称</span></th>
            <th class="manage-column" style="width: 200px"><span>分类</span></th>
            <th class="manage-column" style="width: 200px"><span>会员</span></th>
            <th class="manage-column" style="width: 50px"><span>喜欢</span></th>
            <th class="manage-column" style="width: 50px"><span>宝贝</span></th>
        </tr>
    </thead>

    <tfoot>
        <tr>
            <th class="manage-column" style="width: 100px"><span>编号</span></th>
            <th class="manage-column"><span>名称</span></th>
            <th class="manage-column" style="width: 200px"><span>分类</span></th>
            <th class="manage-column" style="width: 100px"><span>会员</span></th>
            <th class="manage-column" style="width: 50px"><span>喜欢</span></th>
            <th class="manage-column" style="width: 50px"><span>宝贝</span></th>
        </tr>
    </tfoot>
    <tbody id="the-list" class="list:tag">
        <?php
        $_album_count = 0;
        if (!empty($_albums)) {
            $object_ids = array();
            foreach ($_albums as $album) {
                $object_ids[] = $album->id;
            }
            $object_ids = implode(',', $object_ids);
            $query = "SELECT t.*, tr.id AS album_id FROM " . XT_TABLE_CATALOG . " AS t INNER JOIN " . XT_TABLE_ALBUM_CATALOG . " AS tr ON t.id = tr.cid WHERE t.type = 'album' AND tr.id IN ($object_ids) ORDER BY t.sort ASC,t.count DESC";
            global $wpdb;
            $terms = $wpdb->get_results($query);
            $_terms = array();
            foreach ($terms as $term) {
                $_term = isset($_terms[$term->album_id]) ? $_terms[$term->album_id] : array();
                $_term[] = $term;
                $_terms[$term->album_id] = $_term;
            }
            foreach ($_terms as $_album_id => $_term) {
                xt_update_catalog_terms_cache($_album_id, $_term, 'album');
            }
            foreach ($_albums as $album) {
                xt_row_album($album, $_album_count);
                $_album_count++;
            }
        }
        ?>
    </tbody>
</table>
<table style="display: none">
    <tbody>
        <tr id="inline-edit" class="inline-edit-row">
            <td colspan="7" class="colspanchange">
                <fieldset class="inline-edit-col-left" style="width:40%;">
                    <div class="inline-edit-col">
                        <h4>快速编辑</h4>
                        <label> <span class="title">名称</span> <span class="input-text-wrap"><input type="text" name="title" disabled class="ptitle" value="" /></span></label>
                    </div>
                </fieldset>
                <fieldset class="inline-edit-col-center inline-edit-categories" style="width:50%">
                    <div class="inline-edit-col">
                        <span class="title inline-edit-categories-label">所属分类</span>
                        <input type="hidden" name="album_category[]" value="0">
                        <ul class="cat-checklist category-checklist">
                            <?php xt_catalog_checklist('album', $_catalogs); ?>
                        </ul>
                    </div>
                </fieldset>
                <p class="inline-edit-save submit">
                    <a accesskey="c" href="#inline-edit" title="取消" class="cancel button-secondary alignleft">取消</a> <a accesskey="s" href="#inline-edit" title="更新专辑" class="save button-primary alignright">更新专辑</a>
                    <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" /> <span class="error" style="display: none;"></span> <br class="clear" />
                </p>
            </td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var ajaxurl = '<?php echo admin_url("admin-ajax.php"); ?>';
        var what = '#album-';
        $('#filter-search-submit').click(function(){
            var s = $('#filter-search-input').val();
            document.location.href = 'http://<?php echo add_query_arg(array('s' => 'SEARCH', 'paged' => 1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('SEARCH',encodeURIComponent(s));
        });
        $('#filter-cat').change(function(){
            var cid = $(this).val();
            document.location.href = 'http://<?php echo add_query_arg(array('cid' => 'CID', 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>'.replace('CID',cid);
        });
        $('a.delete-album').live('click',function() {
            if(confirm('您确认要删除该专辑?')){
                deleteAlbum($(this).attr('data-value'));	
            }
            return false;
        });
        function deleteAlbum(ids){
            if (ids) {
                var setting = {};
                setting.action = 'xt_admin_ajax_album_delete';
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
            var cids = $('.cids', rowData).text().split(',');
            $(':input[name="album_category[]"]', editRow).each(function(){
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
        function inline_edit(editRow) {
            var id = editRow.attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var cids = [];
            $(':input[name="album_category[]"]', editRow).each(function(){
                if($(this).is(':checked')){
                    cids.push($(this).val());
                }
            });
            
            var error = editRow.find('.error');
            var waiting = editRow.find('.waiting');
            var setting = {};
            setting.action = 'xt_admin_ajax_album_update';

            setting.id = id;
            setting.cids = cids.join(',');
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