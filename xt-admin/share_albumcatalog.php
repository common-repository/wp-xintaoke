<style type="text/css">
    .widefat tbody th.check-column{padding-bottom:2px;}.inline-edit-row .error{color:red;}
</style>
<?php
$_catalogs = xt_catalogs_album(true);
?>
<div id="col-container" style="padding-top: 20px;">
    <div id="col-right">
        <table class="wp-list-table widefat fixed catalogs" cellspacing="0">
            <thead>
                <tr>
                    <th class="manage-column" style="width:50px"><span>编号</span></th>
                    <th class="manage-column"><span>名称</span></th>
                    <th class="manage-column" style="width:100px"><span>类型</span></th>
                    <th class="manage-column" style="width:100px"><span>排序</span></th>
                    <th class="manage-column" style="width:100px"><span>专辑</span></th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th class="manage-column" style="width:50px"><span>编号</span></th>
                    <th class="manage-column"><span>名称</span></th>
                    <th class="manage-column" style="width:100px"><span>类型</span></th>
                    <th class="manage-column" style="width:100px"><span>排序</span></th>
                    <th class="manage-column" style="width:100px"><span>专辑</span></th>
                </tr>
            </tfoot>
            <tbody id="the-list" class="list:catalog">
                <?php
                $_cat_count = 0;
                foreach ($_catalogs as $cat) {
                    $subs = isset($cat->child) ? $cat->child['catalogs'] : array();
                    xt_row_catalog($cat, $_cat_count);
                    foreach ($subs as $sub) {
                        $_cat_count++;
                        xt_row_catalog($sub, $_cat_count);
                    };
                    $_cat_count++;
                }
                ?>
            </tbody>
        </table>
        <table style="display: none">
            <tbody>
                <tr id="inline-edit" class="inline-edit-row">
                    <td colspan="5" class="colspanchange">
                        <fieldset>
                            <div class="inline-edit-col">
                                <h4>快速编辑</h4>
                                <label> <span class="title">名称</span> <span class="input-text-wrap"><input type="text" name="title" class="ptitle" value="" /></span></label>
                                <label> <span class="title">图片</span> <span class="input-text-wrap"><input type="text" name="pic" class="ptitle" value="" /></span></label>
                                <label> <span class="title">排序</span> <span class="input-text-wrap"><input type="text" name="sort" class="ptitle" value="" />排序数值越小越靠前</span></label>
                            </div>
                        </fieldset>
                        <p class="inline-edit-save submit">
                            <a accesskey="c" href="#inline-edit" title="取消" class="cancel button-secondary alignleft">取消</a> 
                            <a accesskey="s" href="#inline-edit" title="更新分类" class="save button-primary alignright">更新分类</a>
                            <img class="waiting" style="display: none;" src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" alt="" />
                            <span class="error" style="display: none;"></span>
                            <br class="clear" />
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <div id="col-left">
        <div class="col-wrap">
            <div class="form-wrap">
                <h3><?php xt_admin_help_link('share_albumcatalog')?>添加新分类</h3>
                <form id="addcatalog" method="post" action="" class="validate">
                    <input type="hidden" name="action" value="add-catalog">
                    <div class="form-field form-required">
                        <label for="catalog-title">名称</label> <input name="title" id="catalog-title" type="text" value="" size="40" aria-required="true">
                        <p>这将是它在站点上显示的名字。</p>
                    </div>
                    <div class="form-field">
                        <label for="catalog-pic">图片</label> <input name="pic" id="catalog-pic" type="text" value="" size="40">
                        <p>该分类的代表图片,部分模块需要显示该图片,大小160X160。</p>
                    </div>
                    <div class="form-field">
                        <label for="catalog-isfront">系统分类</label><input name="is_front" id="catalog-isfront" type="checkbox">
                        <p>系统分类:不会显示在搜索列表的分类,该分类的通常为站长自己单独推广使用</p>
                    </div>
                    <div class="form-field">
                        <label for="catalog-parent">父级</label> <select name="parent" id="catalog-parent" class="postform">
                            <option value="0">无</option>
                            <?php
                            foreach ($_catalogs as $cat) {
                                echo '<option value="' . $cat->id . '" data-isfront="' . $cat->is_front . '">' . $cat->title . '</option>';
                            }
                            ?>
                        </select>
                        <p>目前不提供无限分级。举例:“女装”为一级分类，下面可以有叫做“连衣裙”和“短外套”的子分类。</p>
                    </div>
                    <div class="form-field" style="display:none">
                        <label for="catalog-keywords">关键词(seo)</label>
                        <textarea name="keywords" id="catalog-keywords" rows="3" cols="40"></textarea>
                        <p>该分类页面的关键词,seo优化</p>
                    </div>
                    <div class="form-field" style="display:none">
                        <label for="catalog-description">描述(seo)</label>
                        <textarea name="description" id="catalog-description" rows="3" cols="40"></textarea>
                        <p>该分类页面的描述,seo优化</p>
                    </div>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button" value="添加新分类">
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
        var what = '#catalog-';
        $('#catalog-isfront').change(function() {
            if ($(this).is(':checked')) {
                $('#catalog-parent').val(0).parents('.form-field:first').hide();
            } else {
                $('#catalog-parent').parents('.form-field:first').show();
            }
        });
        $('#catalog-parent').change(function(){
            var cid = $('#catalog-parent').val();
            if(cid>0){
                $('#catalog-isfront').attr('checked',false);
            }
        });	
        $('#submit').click(function() {
            addCatalog();
            return false;
        });
        $('a.delete-catalog').live('click',function() {
            if(confirm('您确认要删除该分类?删除父级分类时,子分类同时被删除,分类下的分享并不会被删除!')){
                deleteCatalog($(this).attr('data-value'));	
            }
            return false;
        });
        $('.editinline').live('click', function() {
            revertRow();
            id = $(this).parents('tr:first').attr('id');
            id = id.substr(id.lastIndexOf('-') + 1);
            var editRow = $('#inline-edit').clone(true);
            var rowData = $('#inline_' + id);
            $('td', editRow).attr('colspan',
            $('.widefat:first thead th:visible').length);
            if ($(what + id).hasClass('alternate'))
                $(editRow).addClass('alternate');
            $(what + id).hide().after(editRow);

            $(':input[name="title"]', editRow).val($('.title', rowData).text());
            $(':input[name="pic"]', editRow).val($('.pic', rowData).text());
            $(':input[name="sort"]', editRow).val($('.sort', rowData).text());

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
        function deleteCatalog(ids) {
            if (ids) {
                var setting = {};
                setting.action = 'xt_admin_ajax_catalog_delete';
                setting.ids = ids;
                setting.type = 'album';
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
        function addCatalog() {
            var form = $('#addcatalog');
            if (!validateForm(form))
                return false;
            var setting = {};
            setting.title = $('#catalog-title').val();
            setting.pic = $('#catalog-pic').val();
            setting.parent = $('#catalog-parent').val();
            setting.is_front = $('#catalog-isfront').is(':checked') ? 0 : 1;
            if(!setting.is_front){
                setting.parent=0;	
            }
            setting.keywords = $('#catalog-keywords').val();
            setting.description = $('#catalog-description').val();
            setting.sort = 100;
            setting.type = 'album';
            setting.action = 'xt_admin_ajax_catalog_add';
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
            var title = editRow.find(':input[name="title"]').val();
            var pic = editRow.find(':input[name="pic"]').val();
            var sort = editRow.find(':input[name="sort"]').val();
            var error = editRow.find('.error');
            var waiting = editRow.find('.waiting');
            if (!title) {
                error.text('标题不能为空').show();
                return false;
            }
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
            setting.action = 'xt_admin_ajax_catalog_update';

            setting.id = id;
            setting.type = 'album';
            setting.title = title;
            setting.pic = pic;
            setting.sort = sort;
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