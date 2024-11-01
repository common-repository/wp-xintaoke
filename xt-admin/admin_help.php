<?php

global $XT_HELPS, $XT_HELP_URL;
$XT_HELP_URL = 'http://plugin.xintaonet.com/help/?id=';
$XT_HELPS = array();
$XT_HELPS['sys_setting_base'] = '132#X_Help-Base';
$XT_HELPS['sys_setting_member'] = '132#X_Help-Member';
$XT_HELPS['sys_setting_fanxian'] = '132#X_Help-Fanxian';
$XT_HELPS['sys_platform_xintao'] = '133#X_Help-Xintao';
$XT_HELPS['sys_platform_taobao'] = '133#X_Help-Taobao';
$XT_HELPS['sys_platform_paipai'] = '133#X_Help-Paipai';
$XT_HELPS['sys_platform_yiqifa'] = '133#X_Help-Yiqifa';
$XT_HELPS['sys_platform_weibo'] = '133#X_Help-Weibo';
$XT_HELPS['sys_platform_qq'] = '133#X_Help-Qq';
$XT_HELPS['sys_mail'] = '161';
$XT_HELPS['share_catalog'] = '135';
$XT_HELPS['share_albumcatalog'] = '159';
$XT_HELPS['share_tag'] = '136';
$XT_HELPS['share_share'] = '137';
$XT_HELPS['share_album'] = '138';
$XT_HELPS['share_comment'] = '139';
$XT_HELPS['fanxian_taobao'] = '140';
$XT_HELPS['fanxian_paipai'] = '141';
$XT_HELPS['fanxian_yiqifa'] = '142';
$XT_HELPS['fanxian_cash'] = '143';
$XT_HELPS['fanxian_buy'] = '144';
$XT_HELPS['fanxian_ads'] = '145';
$XT_HELPS['member_member'] = '146';
$XT_HELPS['member_role'] = '147';

function xt_admin_help_link($help, $echo = true) {
    $link = '<a class="xt-help-link" href="' . xt_admin_help_url($help) . '" target="_blank"></a>';
    if ($echo) {
        echo $link;
    } else {
        return $link;
    }
}

function xt_admin_help_url($help) {
    global $XT_HELPS, $XT_HELP_URL;
    if (isset($XT_HELPS[$help])) {
        return $XT_HELP_URL . $XT_HELPS[$help];
    }
    return 'javascript:;';
}