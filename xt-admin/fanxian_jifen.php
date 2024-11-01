
<?php
wp_enqueue_script('thickbox');
wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
$type = (isset ($_GET['type']) ? ($_GET['type']) : 'order');
?>
<ul class="subsubsub">
	<li><a href="http://<?php echo add_query_arg(array('type'=>'order','paged'=>1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?>"<?php echo $type=='order'?' class="current"':''?>>兑换记录</a> |</li>
	<li><a href="http://<?php echo add_query_arg(array('type'=>'item','paged'=>1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?>"<?php echo $type=='item'?' class="current"':''?>>商品管理</a> |</li>
	<li><a href="http://<?php echo add_query_arg(array('type'=>'jifen','paged'=>1), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])?>"<?php echo $type=='jifen'?' class="current"':''?>><?php echo xt_jifenbao_text();?>记录</a></li>
</ul>
<?php


if ($type == 'jifen')
	: $_result = query_jifens(array (
		'jifen_per_page' => 50,
		'page' => isset ($_GET['paged']) ? intval($_GET['paged']) : 1
	));
$_jifens = $_result['jifens'];
?>

<div class="tablenav top">
	<div class="alignleft actions">
		
	</div>
	<div class="tablenav-pages">
		<span class="displaying-num">
			<?php


xt_jifens_paging_text();
?>


		</span> <span class="pagination-links">
			<?php


xt_jifens_pagination_links();
?>


		</span>
	</div>
	<br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
	<thead>
		<tr>
			<th class="manage-column" style="width: 150px"><span>用户名</span></th>
			<th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 150px"><span>时间</span></th>
			<th class="manage-column" style="width: 200px"><span>类型</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th class="manage-column" style="width: 150px"><span>用户名</span></th>
			<th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 150px"><span>时间</span></th>
			<th class="manage-column" style="width: 200px"><span>类型</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</tfoot>
	<tbody id="the-list" class="list:tag">
		<?php


$_jifen_count = 0;
foreach ($_jifens as $jifen) {
	xt_row_jifen($jifen, $_jifen_count);
	$_jifen_count++;
}
?>
	</tbody>
</table>
<?php


elseif ($type == 'order') : $_result = query_jifenOrders(array (
	'jifenOrder_per_page' => 50,
	'page' => isset ($_GET['paged']) ? intval($_GET['paged']) : 1
));
$_jifenOrders = $_result['jifenOrders'];
?>

<div class="tablenav top">
	<div class="alignleft actions">
		
	</div>
	<div class="tablenav-pages">
		<span class="displaying-num">
			<?php


xt_jifenOrders_paging_text();
?>


		</span> <span class="pagination-links">
			<?php


xt_jifenOrders_pagination_links();
?>


		</span>
	</div>
	<br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
	<thead>
		<tr>
			<th class="manage-column" style="width: 100px"><span>状态</span></th>
			<th class="manage-column" style="width: 150px"><span>用户名</span></th>
			<th class="manage-column" style="width: 300px"><span>商品</span></th>
			<th class="manage-column" style="width: 60px"><span>数量</span></th>
			<th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 120px"><span>时间</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th class="manage-column" style="width: 100px"><span>状态</span></th>
			<th class="manage-column" style="width: 150px"><span>用户名</span></th>
			<th class="manage-column" style="width: 300px"><span>商品</span></th>
			<th class="manage-column" style="width: 60px"><span>数量</span></th>
			<th class="manage-column" style="width: 80px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 120px"><span>时间</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</tfoot>
	<tbody id="the-list" class="list:tag">
		<?php


$_jifenOrder_count = 0;
foreach ($_jifenOrders as $jifenOrder) {
	xt_row_jifenOrder($jifenOrder, $_jifenOrder_count);
	$_jifenOrder_count++;
}
?>
	</tbody>
</table>
<?php


elseif ($type == 'item') : $_result = query_jifenItems(array (
	'jifenItem_per_page' => 50,
	'page' => isset ($_GET['paged']) ? intval($_GET['paged']) : 1
));
$_jifenItems = $_result['jifenItems'];
?>

<div class="tablenav top">
	<div class="alignleft actions">
		<input id="X_Fanxian-Jifen-Item" type="button" class="button-primary" value="添加商品">
	</div>
	<div class="tablenav-pages">
		<span class="displaying-num">
			<?php


xt_jifenItems_paging_text();
?>


		</span> <span class="pagination-links">
			<?php


xt_jifenItems_pagination_links();
?>


		</span>
	</div>
	<br class="clear">
</div>
<table class="wp-list-table widefat fixed tags" cellspacing="0">
	<thead>
		<tr>
			<th class="manage-column" style="width: 200px"><span>标题</span></th>
			<th class="manage-column" style="width: 200px"><span>图片</span></th>
			<th class="manage-column" style="width: 60px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 60px"><span>库存</span></th>
			<th class="manage-column" style="width: 60px"><span>每人限兑</span></th>
			<th class="manage-column" style="width: 60px"><span>已兑换</span></th>
			<th class="manage-column" style="width: 60px"><span>排序</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<th class="manage-column" style="width: 200px"><span>标题</span></th>
			<th class="manage-column" style="width: 200px"><span>图片</span></th>
			<th class="manage-column" style="width: 60px"><span><?php echo xt_jifenbao_text();?></span></th>
			<th class="manage-column" style="width: 60px"><span>库存</span></th>
			<th class="manage-column" style="width: 60px"><span>每人限兑</span></th>
			<th class="manage-column" style="width: 60px"><span>已兑换</span></th>
			<th class="manage-column" style="width: 60px"><span>排序</span></th>
			<th class="manage-column"><span>备注</span></th>
		</tr>
	</tfoot>
	<tbody id="the-list" class="list:tag">
		<?php


$_jifenItem_count = 0;
foreach ($_jifenItems as $jifenItem) {
	xt_row_jifenItem($jifenItem, $_jifenItem_count);
	$_jifenItem_count++;
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
						<label> <span class="title">商品标题</span> <span class="input-text-wrap"><input type="text" name="title" class="ptitle" value="" /></span></label>
						<label> <span class="title">图片地址</span> <span class="input-text-wrap"><input type="text" name="pic" class="ptitle" value="" /></span></label>
						<label> <span class="title">排序</span> <span class="input-text-wrap"><input type="text" style="width:80px;" name="sort" class="ptitle" value="" /></span></label>
						<label> <span class="title">库存</span> <span class="input-text-wrap"><input type="text" style="width:80px;" name="stock" class="ptitle" value="" /></span></label>
						<label> <span class="title">兑换<?php echo xt_jifenbao_text();?></span> <span class="input-text-wrap"><input type="text" style="width:80px;" name="jifen" class="ptitle" value="" /></span></label>
						<label> <span class="title">每人限兑</span> <span class="input-text-wrap"><input type="text" style="width:80px;" name="userCount" class="ptitle" value="" /></span></label>
						<label> <span class="title">说明</span> <span class="input-text-wrap"><input type="text" name="content" class="ptitle" value="" /></span></label> 
					</div>
				</fieldset>
				<p class="inline-edit-save submit">
					<a accesskey="c" href="#inline-edit" title="取消" class="cancel button-secondary alignleft">取消</a> 
					<a accesskey="s" href="#inline-edit" title="更新商品" class="save button-primary alignright">更新商品</a>
					<img class="waiting" style="display: none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
					<span class="error" style="display: none;"></span>
					<br class="clear" />
				</p>
			</td>
		</tr>
	</tbody>
</table>
<div id="X_Fanxian-Jifen-Item-Box" style="display:none;">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th style="width:70px;">商品标题</th>
			<td><input type="text" style="width:250px;" id="X_Jifen-Item-Title" value=""></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">图片地址</th>
			<td><input type="text" style="width:250px;" id="X_Jifen-Item-Pic" value=""></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">排序</th>
			<td><input type="text" style="width:50px;" id="X_Jifen-Item-Sort" value="100"></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">库存</th>
			<td><input type="text" style="width:50px;" id="X_Jifen-Item-Stock" value="1"></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">兑换<?php echo xt_jifenbao_text();?></th>
			<td><input type="text" style="width:50px;" id="X_Jifen-Item-Jifen" value="100"></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">每人限兑</th>
			<td><input type="text" style="width:50px;" id="X_Jifen-Item-UserCount" value="1"></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">说明</th>
			<td><textarea rows="3" cols="10" id="X_Jifen-Item-Content" class="large-text code"></textarea></td>
		</tr>
	</tbody>
</table>
<p class="submit" style="text-align:center;">
<input id="X_Fanxian-Jifen-Item-Submit" type="button" class="button-primary" value="确定">
<span><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback"></span>
<a class="button" href="javascript:;" id="X_Fanxian-Jifen-Item-Cancel">取消</a>
</p>
</div>
<?php


endif;
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	var ajaxurl = '<?php echo admin_url("admin-ajax.php");?>';
	$('.order-status').click(function(){
		tb_show('处理<?php echo xt_jifenbao_text();?>兑换',ajaxurl
							+ '?action=xt_admin_ajax_jifen_order_box&id=' + $(this).attr('data-id')
							+ '&mode=popup&keepThis=true&height=400&width=460');
	});
	$('#X_Fanxian-Jifen-Order-Cancel').live('click',function(){
		tb_remove();
		return false;
	});
	$('#X_Fanxian-Jifen-Order-Submit').live('click',function(){
		$.ajax({
				type : "post",
				dataType : "json",
				url : ajaxurl + '?rand=' + Math.random(),
				data : {
					action : 'xt_admin_ajax_jifen_order_update',
					id : $(this).attr('data-id'),
					content:$('#X_Jifen-Order-Content').val()
				},
				success : function(response) {
					if (response.code > 0) {
						alert(response.msg);
					} else {
						alert('操作成功');
						tb_remove();
						document.location.href = document.location.href;
					}
				}
			})
		return false;
	});
	$('#X_Fanxian-Jifen-Item').click(function(){
		tb_show('添加<?php echo xt_jifenbao_text();?>可兑换商品','#TB_inline?height=420&width=420&inlineId=X_Fanxian-Jifen-Item-Box');
		return false;
	});
	$('#X_Fanxian-Jifen-Item-Cancel').click(function(){
		tb_remove();
		return false;
	});
	$('#X_Fanxian-Jifen-Item-Submit').click(function(){
		var title = $('#X_Jifen-Item-Title').val();
		if(!title){
			alert('商品标题不能为空');
			return false;
		}
		var pic = $('#X_Jifen-Item-Pic').val();
		if(!pic){
			alert('商品图片地址不能为空');
			return false;
		}
		var sort = $('#X_Jifen-Item-Sort').val();
		if(!sort){
			alert('商品排序不能为空');
			return false;
		}
		var stock = $('#X_Jifen-Item-Stock').val();
		if(!stock){
			alert('商品库存不能为空');
			return false;
		}
		var jifen = $('#X_Jifen-Item-Jifen').val();
		if(!jifen){
			alert('商品兑换<?php echo xt_jifenbao_text();?>不能为空');
			return false;
		}
		var userCount = $('#X_Jifen-Item-UserCount').val();
		if(!userCount){
			alert('每人限兑不能为空');
			return false;
		}
		$.ajax({
				type : "post",
				dataType : "json",
				url : ajaxurl + '?rand=' + Math.random(),
				data : {
					action : 'xt_admin_ajax_jifen_item_save',
					title : title,
					pic : pic,
					sort : sort,
					stock:stock,
					jifen:jifen,
					userCount:userCount,
					content:$('#X_Jifen-Item-Content').val()
				},
				success : function(response) {
					if (response.code > 0) {
						alert(response.msg);
					} else {
						alert('操作成功');
						tb_remove();
						document.location.href = document.location.href;
					}
				}
			})
	});
	var what = '#jifenItem-';
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
		$(':input[name="stock"]', editRow).val($('.stock', rowData).text());
		$(':input[name="jifen"]', editRow).val($('.jifen', rowData).text());
		$(':input[name="userCount"]', editRow).val($('.userCount', rowData).text());
		$(':input[name="content"]', editRow).val($('.content', rowData).text());

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
	function inline_edit(editRow) {
		var id = editRow.attr('id');
		id = id.substr(id.lastIndexOf('-') + 1);
		var title = editRow.find(':input[name="title"]').val();
		var pic = editRow.find(':input[name="pic"]').val();
		var stock = editRow.find(':input[name="stock"]').val();
		var userCount = editRow.find(':input[name="userCount"]').val();
		var jifen = editRow.find(':input[name="jifen"]').val();
		var content = editRow.find(':input[name="content"]').val();
		var sort = editRow.find(':input[name="sort"]').val();
		var error = editRow.find('.error');
		var waiting = editRow.find('.waiting');
		if (!title) {
			error.text('商品标题不能为空').show();
			return false;
		}
		if (!pic) {
			error.text('图片地址不能为空').show();
			return false;
		}
		if (!stock) {
			error.text('库存不能为空').show();
			return false;
		}
		if (!userCount) {
			error.text('每人限兑不能为空').show();
			return false;
		}
		if (!jifen) {
			error.text('兑换<?php echo xt_jifenbao_text();?>不能为空').show();
			return false;
		}
		if (!sort) {
			error.text('排序不能为空').show();
			return false;
		}
		
		var setting = {};
		setting.action = 'xt_admin_ajax_jifen_item_update';

		setting.id = id;
		setting.title = title;
		setting.pic = pic;
		setting.stock = stock;
		setting.userCount = userCount;
		setting.jifen = jifen;
		setting.content = content;
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