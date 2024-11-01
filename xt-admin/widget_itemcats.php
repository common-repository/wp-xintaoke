<div id="X_Cat_Selector" title="选择分类" style="position:relative;">
	<a class="btn_left"></a>
	<div class="xt-pre" style="position: relative">
		<div id="X_Cat_Selector_Cats" style="position:relative;left:0;top:0;">
			<ul style="position:relative;left:0;top:0;">
				<?php
$cats = array ();
$type = isset ($_GET['type']) ? $_GET['type'] : '';
switch ($type) {
	case 'taobao' :
		$cats = xt_query_taobao_itemcat();
		break;
	case 'paipai' :
		$cats = xt_query_paipai_itemcat();
		break;
}
foreach ($cats as $c)
	:
?>
				<li cid="<?php echo $c->cid?>" is_parent="<?php echo $c->is_parent?>">
					<?php if($c->is_parent):?><b class="arrow-right"></b><?php endif;?>
					<span><?php echo $c->name?></span>
					<a href="javascript:void(0);" class="btn_add" cid="<?php echo $c->cid?>" cname="<?php echo $c->name?>">添加</a>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
	<div class="selected">
		<ul id="X_Cat_Selector_Selected">
		</ul>
		<div class="operater"><span class="btn_del">删除</span><span class="btn_up">上移</span><span class="btn_down">下移</span></div>
	</div>
	<a class="btn_right"></a>
</div>
<div class="clear" style="padding:15px;"><input type="button" id="X_Cat_Selector_Save" class="button-primary" style="float:right" value="确认"></div>