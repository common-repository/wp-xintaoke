<?php 
$type = isset($_POST['type'])?$_POST['type']:'jifen';
if(!in_array($type,array('jifen','order','item'))){
	$type='jifen';
}
if($type=='jifen'):
$_result = query_jifens(array('user_id'=>get_current_user_id(),'page'=>absint($_POST['page']),'jifen_per_page'=>10));

$_jifens=$_result['jifens'];
$_total = $_result['total'];
?>
<table class="table table-striped table-hover table-bordered">
	<colgroup>
		<col style="width:150px;"/>
		<col style="width:80px;"/>
        <col style="width:150px;"/>
        <col/>
	</colgroup>
	<thead>
		<tr>
			<th>时间</th>
			<th><?php echo xt_jifenbao_text();?></th>
			<th>类型</th>
			<th>备注</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if($_total>0){
				foreach($_jifens as $_f){
					echo "<tr><td class=\"xt-td-center\">$_f->create_time</td><td class=\"xt-td-center\">$_f->jifen</td><td class=\"xt-td-center\">$_f->action_name</td><td class=\"xt-td-center\">$_f->content</td></tr>";	
				}
			}
		?>
		
	</tbody>
</table>
<?php if($_total==0){?>
<div class="well xt-noresult">
	<div>暂无<?php echo xt_jifenbao_text();?>记录</div>
</div>
<?php }else{
	echo '<div id="X_Pagination-Bottom" class="clearfix">';
	xt_jifens_paging_text();
	echo '<div class="pagination xt-pagination-links xt-account-pagination-links">';
	xt_jifens_pagination_links();
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
elseif($type=='order'):
$_result = query_jifenOrders(array('user_id'=>get_current_user_id(),'page'=>absint($_POST['page']),'jifenOrder_per_page'=>10));

$_jifenOrders=$_result['jifenOrders'];
$_total = $_result['total'];
?>
<table class="table table-striped table-hover table-bordered">
	<colgroup>
		<col style="width:80px;"/>
        <col style="width:200px;"/>
        <col style="width:60px;"/>
        <col style="width:80px;"/>
        <col style="width:150px;"/>
        <col/>
	</colgroup>
	<thead>
		<tr>
			<th>状态</th>
			<th>商品</th>
			<th>数量</th>
			<th><?php echo xt_jifenbao_text();?></th>
			<th>时间</th>
			<th>备注</th>
		</tr>
	</thead>
	<tbody>
		<?php
			if($_total>0){
				foreach($_jifenOrders as $_f){
					$_status = $_f->status==0?'处理中':($_f->status==1?'已完成':'拒绝');
					echo "<tr><td class=\"xt-td-center\">$_status</td><td class=\"xt-td-center\">$_f->title</td><td class=\"xt-td-center\">$_f->num</td><td class=\"xt-td-center\">$_f->jifen</td><td class=\"xt-td-center\">$_f->create_time</td><td class=\"xt-td-center\">$_f->content</td></tr>";	
				}
			}
		?>
		
	</tbody>
</table>
<?php if($_total==0){?>
<div class="well xt-noresult">
	<div>暂无兑换记录</div>
</div>
<?php }else{
	echo '<div id="X_Pagination-Bottom" class="clearfix">';
	xt_jifenOrders_paging_text();
	echo '<div class="pagination xt-pagination-links xt-account-pagination-links">';
	xt_jifenOrders_pagination_links();
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
elseif($type=='item'):
$_result = query_jifenItems(array('page'=>absint($_POST['page']),'jifenItem_per_page'=>4));
$_jifenItems=$_result['jifenItems'];
$_total = $_result['total'];
echo '<div class="clearfix" style="margin-bottom:15px;margin-left:15px;">';
if($_total>0){
	foreach($_jifenItems as $_f){
$_title = esc_html($_f->title);
?>
<div class="xt-account-jifen-item">
	<div class="jifen-item-pic">
		<a><img alt="<?php echo $_title?>" src="<?php echo $_f->pic?>"></a>
	</div>
	<h3>
		<a><?php echo $_title?></a>
	</h3>
	<dl>
		<a href="javascript:;" class="xt-jifen-item-exchange" data-jifen="<?php echo $_f->jifen?>" data-id="<?php echo $_f->id?>" data-stock="<?php echo ($_f->stock-$_f->buy_count);?>"><dt><p><span><?php echo $_f->jifen?>分</span></p></dt></a>
		<dd>
			<p>库存剩余<b><?php echo ($_f->stock-$_f->buy_count);?></b></p>
			<p>已兑数量<b><?php echo $_f->buy_count?></b></p>
			<p>每人限兑<b><?php echo $_f->user_count?></b></p>
		</dd>
	</dl>
</div>
<?php 			
	}
}
echo '</div>';
if($_total==0){?>
<div class="xt-tips-nothing">
	<div>暂无兑换记录</div>
</div>
<?php }else{
	echo '<div id="X_Pagination-Bottom" class="clearfix">';
	xt_jifenItems_paging_text();
	echo '<div class="xt-pagination-links xt-account-pagination-links">';
	xt_jifenItems_pagination_links();
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
endif;
?>				
