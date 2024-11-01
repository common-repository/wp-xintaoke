<?php
$user = wp_get_current_user();
$invite_url = '';
if (xt_fanxian_is_ad()) {
	$invite_url = xt_site_url('invite-' . $user->ID);
}
elseif (xt_fanxian_is_share()) {
	$invite_url = xt_site_url('uid-' . $user->ID);
}
if (!empty ($invite_url))
	:
?>
    <div id="X_Tuiguang-Invite" class="well">
        <div>
            <h4>这是您的专用邀请链接，适合通过聊天工具如 QQ , 旺旺 , MSN 发送给好友：</h4>
            <input class="input-xxlarge" value="<?php echo $invite_url ?>" type="text">
        </div>
        <div>
            <h4>这是您的专用邀请代码，适合在支持HTML的网页如 论坛 , 博客 粘贴以下代码：</h4>
            <textarea class="input-xxlarge"><a href="<?php echo $invite_url ?>" target="_blank"><?php esc_html(bloginfo('name')) ?></a></textarea>
        </div>
    </div>
<?php endif; ?>