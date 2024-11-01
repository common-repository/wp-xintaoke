<?php
global $xt_user_follow;
$_user = wp_get_current_user();
if ($_user->exists()) {
    if (empty($xt_user_follow)) {
        $xt_user_follow = get_user_meta($_user->ID, XT_USER_FOLLOW, true);
    }
    if (empty($xt_user_follow)) {
        $xt_user_follow = array(
            $_user->ID
        );
    }
}
?>
<div class="xt-userinfo">
    <div class="clearfix" style="width:100%;margin-bottom:5px;">
        <div class="xt-user-avatar xt-l">
            <a href="<?php xt_the_user_url(); ?>" target="_blank"><img
                    src="<?php xt_the_user_pic(); ?>"></a>
        </div>
        <div class="xt-l">
            <div class="xt-user-nickname">
                <a target="_blank" href="<?php xt_the_user_url(); ?>"><?php xt_the_user_title(); ?></a>
            </div>
            <?php if (!xt_is_self(xt_get_the_user_id())): ?>
                <div class="xt-user-follow clearfix">
                    <?php if (!empty($xt_user_follow) && in_array((int) xt_get_the_user_id(), $xt_user_follow)): ?>
                        <span class="xt-unfollow" data-userid="<?php xt_the_user_id(); ?>">取消关注</span> <span class="xt-sendmsg" name="<?php xt_the_user_title(); ?>">发私信</span>
                    <?php else: ?>
                        <span class="xt-follow" data-userid="<?php xt_the_user_id(); ?>">+ 加关注</span> <span class="xt-sendmsg" name="<?php xt_the_user_title(); ?>">发私信</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="xt-user-status">
        <div>
            <div class="xt-user-status-li"><a rel="nofollow" href="<?php xt_the_user_url(); ?>#follow" data-hash="#follow"><span class="xt-user-status-label">关注</span><span
                        class="xt-user-nums"><?php  (xt_the_user_followcount()) ?></span></a></div>
            <div class="xt-user-status-li"><a rel="nofollow" href="<?php xt_the_user_url(); ?>#fans" data-hash="#fans"><span class="xt-user-status-label">粉丝</span><span
                        class="xt-user-nums"><?php  (xt_the_user_fanscount()) ?></span></a></div>		
            <div class="xt-user-status-li"><a rel="nofollow" href="<?php xt_the_user_url(); ?>#share" data-hash="#share"><span class="xt-user-status-label">宝贝</span><span
                        class="xt-user-nums"><?php echo (xt_get_the_user_sharecount() + xt_get_the_user_fav_sharecount()) ?></span></a></div>
            <div class="xt-user-status-li xt-last" style="width:48px;"><a rel="nofollow" href="<?php xt_the_user_url(); ?>#like" data-hash="#like"><span class="xt-user-status-label">喜欢</span><span
                        class="xt-user-nums"><?php echo (xt_get_the_user_fav_sharecount() + xt_get_the_user_fav_albumcount()) ?></span></a></div>
        </div>		
    </div>
    <?php $_description = xt_get_the_user_description();
    if (!empty($_description)): ?>
        <div class="xt-user-description clearfix">
            <img src="<?php echo XT_CORE_IMAGES_URL ?>/dotleft.gif"><?php echo wp_trim_words(xt_get_the_user_description(), 35); ?>
            <img src="<?php echo XT_CORE_IMAGES_URL ?>/dotright.gif"></div>
<?php endif; ?>
    <div class="clearfix"></div>

</div>