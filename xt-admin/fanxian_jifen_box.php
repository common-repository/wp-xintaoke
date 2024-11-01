<?php
$id = isset ($_GET['id']) ? (int) $_GET['id'] : '';
if (empty ($id)) {
	exit ('未指定兑换记录');
}
global $wpdb;
$order = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_USER_JIFEN_ORDER . ' WHERE id=' . (int) $id);
if (empty ($order)) {
	exit ('指定的兑换记录不存在');
}
$item = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_USER_JIFEN_ITEM . ' WHERE id=' . $order->item_id);
if (empty ($item)) {
	exit ('要兑换的商品不存在');
}
$user = new WP_User($order->user_id);
if(!$user->exists()){
	exit('兑换的会员不存在');
}
?>
<div id="X_Fanxian-Jifen-Item-Box">
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th style="width:70px;">商品标题</th>
			<td><?php echo $item->title;?></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">兑换<?php echo xt_jifenbao_text();?></th>
			<td><?php echo $item->jifen?></td>
		</tr>
		<tr valign="top">
			<th style="width:70px;">会员</th>
			<td><?php echo $user->user_login?></td>
		</tr>
		<?php
		$qq_field = XT_USER_QQ;
		$qq = $user-> $qq_field;
		$mobile_field = XT_USER_MOBILE;
		$mobile = $user-> $mobile_field;
		?>
		<?php if(!empty($user->email)):?>
		<tr valign="top">
			<th style="width:70px;">邮箱</th>
			<td><?php echo $user->email?></td>
		</tr>
		<?php endif;?>
		<?php if(!empty($qq)):?>
		<tr valign="top">
			<th style="width:70px;">Q&nbsp;Q</th>
			<td><?php echo $qq?></td>
		</tr>
		<?php endif;?>
		<?php if(!empty($mobile)):?>
		<tr valign="top">
			<th style="width:70px;">手机</th>
			<td><?php echo $mobile?></td>
		</tr>
		<?php endif;?>
		<tr valign="top">
			<th style="width:70px;">说明</th>
			<td>
				<textarea rows="3" cols="10" id="X_Jifen-Order-Content" class="large-text code"></textarea>
				<small>建议:虚拟充值类的,请将卡密填写在说明里,也可以使用邮箱,QQ联系到该会员告知卡密或联系该会员为他自动充值</small>
			</td>
		</tr>
	</tbody>
</table>
<p class="submit" style="text-align:center;">
<input id="X_Fanxian-Jifen-Order-Submit" data-id="<?php echo $id;?>" type="button" class="button-primary" value="确定">
<span><img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-feedback"></span>
<a class="button" href="javascript:;" id="X_Fanxian-Jifen-Order-Cancel">取消</a>
</p>
</div>