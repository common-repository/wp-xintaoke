<?php
global $xt_user, $xt_user_counts, $xt_user_follow;
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
if (isset($_POST['userId']) && absint($_POST['userId'])) {
    $xt_user = new WP_User(absint($_POST['userId']));
    if ($xt_user->exists()) {
        $xt_user_counts = xt_default_counts();
        $_xt_user_counts = get_user_meta($xt_user->ID, XT_USER_COUNT, true);
        if (!empty($_xt_user_counts)) {
            $xt_user_counts = array_merge($xt_user_counts, $_xt_user_counts);
        }
        $url = xt_get_the_user_url();
        ?>
        <div class="clearfix">
            <div class="clearfix">
                <a href="<?php echo $url; ?>" class="xt-avatar pull-left" target="_blank">
                    <img src="<?php xt_the_user_pic() ?>" alt="<?php xt_the_user_title() ?>">
                </a>
                <div class="pull-left">
                    <p><a href="<?php echo $url; ?>" class="xt-username" target="_blank"><?php xt_the_user_title() ?></a></p>
                    <p class="xt-last">
                        <a href="<?php echo $url; ?>#fans" target="_blank"><span><?php (xt_the_user_fanscount()) ?></span></a>粉丝
                        <a style="margin-left:10px;" href="<?php echo $url; ?>#share" target="_blank"><span><?php xt_the_user_sharecount() ?></span></a>分享
                        <a style="margin-left:10px;" href="<?php echo $url; ?>#like" target="_blank"><span><?php xt_the_user_fav_sharecount() ?></span></a>喜欢
                    </p>
                </div>     
            </div>
            <div class="xt-userdesc clearfix"><?php echo wp_trim_words(xt_get_the_user_description(), 20); ?></div>
            <?php if (!xt_is_self(xt_get_the_user_id())): ?>
                <div class="xt-toolbar clearfix">

                    <div class="xt-user-follow clearfix">
                        <?php if (!empty($xt_user_follow) && in_array((int) xt_get_the_user_id(), $xt_user_follow)): ?>
                            <span class="xt-unfollow btn btn-small" data-userid="<?php xt_the_user_id(); ?>">取消关注</span>
                        <?php else: ?>
                            <span class="xt-follow btn btn-small" data-userid="<?php xt_the_user_id(); ?>">+ 加关注</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    } else {
        exit('<h4>抱歉,该用户不存在!</h4>');
    }
} else {
    exit('<h4>未指定用户</h4>');
}
