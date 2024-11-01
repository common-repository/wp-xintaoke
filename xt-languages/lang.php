<?php

global $XT_LANG;
$XT_LANG = array();
$XT_LANG['fanxian_jifenbao'] = '集分宝';
$XT_LANG['fanxian_cash_back'] = ',返%s元';
$XT_LANG['fanxian_cash_back_jifenbao'] = ',返%s' . $XT_LANG['fanxian_jifenbao'];
$XT_LANG['user_not_found'] = '没有找到符合%s的会员';
$XT_LANG['user_follow_not_found'] = '没有关注任何人';
$XT_LANG['user_fans_not_found'] = '还没有粉丝';
$XT_LANG['album_not_found'] = '没有找到符合%s的专辑';
$XT_LANG['album_favorite_not_found'] = '没有喜欢任何专辑';
$XT_LANG['album_share_not_found'] = '还没有创建专辑';
$XT_LANG['share_not_found'] = '没有找到符合%s的分享';
$XT_LANG['share_home_not_found_myself'] = '您关注的会员还没有分享';
$XT_LANG['share_home_not_found_other'] = 'TA关注的会员还没有分享';
$XT_LANG['share_favorite_not_found'] = '没有喜欢任何分享';
$XT_LANG['share_share_not_found'] = '还没有分享';
$XT_LANG['share_album_not_found'] = '还没有添加分享';
$XT_LANG['item_not_found'] = '没有找到符合%s的宝贝';
$XT_LANG['item_taobao_not_found'] = '没有找到符合%s的淘宝宝贝';
$XT_LANG['item_paipai_not_found'] = '没有找到符合%s的拍拍宝贝';
$XT_LANG['item_coupon_not_found'] = '没有找到符合%s的淘宝折扣宝贝';
$XT_LANG['item_temai_not_found'] = '没有找到符合%s的淘宝特卖宝贝';
$XT_LANG['item_bijia_not_found'] = '没有在全网找到符合%s的宝贝';
$XT_LANG['item_tuan_not_found'] = '没有找到符合%s的团购';

function xt_jifenbao_text() {
    global $XT_LANG;
    return $XT_LANG['fanxian_jifenbao'];
}