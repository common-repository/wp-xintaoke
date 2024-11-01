<?php

function xt_auto_update() {
//    global $wpdb;
////    include (XT_FILE_PATH . '/xt-updates/updating_tasks.php');
//    xt_create_or_update_tables();
//    update_option(XT_OPTION_VERSION_DB, XT_DB_VERSION);
//    update_option(XT_OPTION_VERSION, XT_VERSION);
}

function xt_install() {
    //	global $wpdb, $user_level, $wp_rewrite, $wp_version, $xt_page_titles;
    // run the create or update code here.
    // All code to add new database tables and columns must be above here
    $xt_version = get_option(XT_OPTION_VERSION_DB, 0);
    if ($xt_version === XT_DB_VERSION) {
        return true;
    }
    //TODO Upgrade
    //
    //INSTALLING
    xt_create_or_update_tables();
    xt_create_tables_data();
    if ($xt_version === false) {
        add_option(XT_OPTION_VERSION_DB, XT_DB_VERSION, '', 'yes');
    } else {
        update_option(XT_OPTION_VERSION_DB, XT_DB_VERSION);
    }
    update_option(XT_OPTION_VERSION, XT_VERSION);
    if ('' == get_option(XT_OPTION_GLOBAL)) {
        global $wp_rewrite;
        $index = '';
        if ($wp_rewrite->using_index_permalinks()) {
            $index = '/' . $wp_rewrite->index;
        }
        update_option(XT_OPTION_GLOBAL, array(
            'index' => $index,
            'base' => 'share',
            'daogou' => 'daogou',
            'help' => 'help',
            'isMenu' => 0,
            'isHelp' => 0,
            'bdshare' => 0,
            'isFanxian' => 0,
            'isForceLogin' => 0,
            'isS8' => 0,
            'isTaobaoPopup' => 1,
            'isDisplayComment' => 0,
            'followLimit' => 2000,
            'albumDisplay' => 'small',
            'userDescription' => '关注我，让我每天给你推荐好看的东东吧！',
            'loading' => '/wp-content/plugins/wp-xintaoke/xt-themes/images/loader.gif',
            'bulletin' => '',
            'prices' => array(
                'low' => array(
                    'start' => 0,
                    'end' => 100
                ),
                'medium' => array(
                    'start' => 100,
                    'end' => 200
                ),
                'high' => array(
                    'start' => 200,
                    'end' => 500
                ),
                'higher' => array(
                    'start' => 500,
                    'end' => 0
                )
            )
        ));
    }
    if ('' == get_option(XT_OPTION_MAIL)) {
        update_option(XT_OPTION_MAIL, array(
            'mail_from' => '',
            'mail_from_name' => '',
            'mailer' => 'smtp',
            'mail_set_return_path' => 'false',
            'smtp_host' => 'localhost',
            'smtp_port' => '25',
            'smtp_ssl' => 'none',
            'smtp_auth' => false,
            'smtp_user' => '',
            'smtp_pass' => ''
        ));
    }
    if ('' == get_option(XT_OPTION_FANXIAN)) {
        update_option(XT_OPTION_FANXIAN, array(
            'isMulti' => 0,
            'isAutoCash' => 0,
            'isPendingTixian' => 0,
            'rate_cashback' => 50,
            'isAd' => 0,
            'rate_ad' => 10,
            'isShare' => 0,
            'rate_share' => 0,
            'cashback' => 5,
            'registe_cash' => 1,
            'registe_jifen' => 0
        ));
    }
    if ('' == get_option(XT_OPTION_PLATFORM)) {
        update_option(XT_OPTION_PLATFORM, array(
            'xt' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'uid' => '',
                'name' => '',
                'token' => array()
            ),
            'taobao' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'tkpid' => '',
                's8pid' => '',
                'uid' => '',
                'name' => '',
                'token' => array()
            ),
            'paipai' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'uid' => '',
                'name' => '',
                'userId' => '',
                'token' => array()
            ),
            'yiqifa' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'account' => '',
                'sid' => '',
                'wid' => '',
                'syncSecret' => '',
                'uid' => '',
                'name' => '',
                'token' => array()
            ),
            'weibo' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'uid' => '',
                'name' => '',
                'token' => array()
            ),
            'qq' => array(
                'isValid' => 1,
                'isLogin' => 0,
                'appKey' => '',
                'appSecret' => '',
                'uid' => '',
                'name' => '',
                'token' => array()
            )
        ));
    }
}

function xt_create_tables_data($debug = false) {
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $data = include_once 'xt-installer-data.php';
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $user_name = $user->user_login;
    $date_time = current_time('mysql');
    $gmt_date_time = current_time('mysql', 1);
    $sql = array();
    if (!empty($data)) {
        foreach ($data as $s) {
            $sql[] = str_replace(array('USER_ID', 'USER_NAME', 'DATE_TIME', 'GMT_TIME'), array($user_id, $user_name, $date_time, $gmt_date_time), $s);
        }
    }
    dbDelta($sql);
}

/**
 * xt_create_or_update_tables count function,
 * * @return boolean true on success, false on failure
 */
function xt_create_or_update_tables($debug = false) {
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $base_prefix = $wpdb->base_prefix;
    $sql = array();
    $sql[] = "CREATE TABLE `{$base_prefix}xt_album` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_name` varchar(255) NOT NULL,
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `title` varchar(80) NOT NULL DEFAULT '0',
                `content` varchar(500) DEFAULT NULL,
                `cover` varchar(255) DEFAULT '',
                `pic_url` varchar(5000) DEFAULT NULL,
                `share_count` int(11) NOT NULL DEFAULT '0',
                `fav_count` int(11) NOT NULL DEFAULT '0',
                `comment_count` int(11) NOT NULL DEFAULT '0',
                `create_date` datetime DEFAULT NULL,
                `create_date_gmt` datetime DEFAULT '0000-00-00 00:00:00',
                `update_date` datetime DEFAULT NULL,
                `update_date_gmt` datetime DEFAULT NULL,
                `cache_data` text,
                `sort` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
              ) ENGINE = MyISAM AUTO_INCREMENT = " . rand(10000, 30000) . " DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_album_catalog` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `cid` bigint(11) NOT NULL,
                `create_date_gmt` datetime DEFAULT NULL,
                PRIMARY KEY (`id`,`cid`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_catalog` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(100) NOT NULL,
                `pic` varchar(500) DEFAULT NULL,
                `sort` int(11) NOT NULL DEFAULT '100',
                `parent` int(11) NOT NULL DEFAULT '0',
                `is_front` tinyint(1) NOT NULL DEFAULT '1',
                `count` int(11) NOT NULL DEFAULT '0',
                `children` varchar(5000) DEFAULT NULL,
                `keywords` varchar(1000) DEFAULT NULL,
                `description` longtext,
                `type` varchar(10) NOT NULL DEFAULT 'share' COMMENT 'share,album',
                PRIMARY KEY (`id`),
                UNIQUE KEY `title` (`title`,`parent`,`type`) USING BTREE
              ) ENGINE = MyISAM AUTO_INCREMENT = 200 DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_catalog_itemcat` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `cid` bigint(20) NOT NULL,
                `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `type` varchar(10) NOT NULL COMMENT 'taobao,paipai',
                PRIMARY KEY (`id`,`cid`,`type`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_fanxian` (
                `platform` varchar(20) NOT NULL COMMENT 'taobao,paipai,yiqifa',
                `trade_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `type` varchar(10) NOT NULL COMMENT 'BUY,ADS',
                `user_id` bigint(20) NOT NULL,
                `user_name` varchar(255) NOT NULL,
                `buy_user_id` bigint(20) NOT NULL DEFAULT '0',
                `buy_user_name` varchar(255) DEFAULT NULL,
                `share_user_id` bigint(20) NOT NULL DEFAULT '0',
                `share_user_name` varchar(255) DEFAULT NULL,
                `ads_user_id` bigint(20) NOT NULL,
                `ads_user_name` varchar(255) DEFAULT NULL,
                `commission` varchar(255) NOT NULL,
                `fanxian` varchar(20) NOT NULL,
                `jifen` varchar(20) NOT NULL,
                `status` int(11) NOT NULL DEFAULT '0',
                `create_time` datetime DEFAULT NULL,
                `order_time` datetime NOT NULL,
                `content` varchar(500) DEFAULT NULL,
                `from_type` varchar(20) NOT NULL,
                PRIMARY KEY (`trade_id`,`type`,`user_id`,`platform`),
                KEY `term_taxonomy_id` (`type`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_favorite` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'share:1,album:2,topic:3,group:4,brand:5',
                `create_date` datetime DEFAULT NULL,
                PRIMARY KEY (`id`,`user_id`,`type`),
                KEY `term_taxonomy_id` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_paipai_itemcat` (
                `cid` bigint(20) unsigned NOT NULL DEFAULT '0',
                `parent_cid` bigint(20) unsigned NOT NULL DEFAULT '0',
                `name` varchar(255) NOT NULL,
                `is_parent` tinyint(1) NOT NULL DEFAULT '0',
                `is_class` tinyint(1) NOT NULL,
                `navprop` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`cid`),
                KEY `parent_cid` (`parent_cid`) USING BTREE
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_paipai_report` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `dealId` bigint(20) unsigned NOT NULL DEFAULT '0',
                `discount` int(11) DEFAULT NULL,
                `careAmount` bigint(20) DEFAULT NULL,
                `brokeragePrice` bigint(20) DEFAULT NULL,
                `realCost` bigint(20) DEFAULT NULL,
                `bargainState` tinyint(2) DEFAULT NULL COMMENT '0:正常,1:佣金冻结',
                `chargeTime` datetime DEFAULT NULL,
                `commNum` int(11) DEFAULT NULL,
                `commId` varchar(50) DEFAULT NULL,
                `commName` varchar(255) DEFAULT NULL,
                `classId` bigint(20) DEFAULT NULL,
                `className` varchar(255) DEFAULT NULL,
                `shopId` bigint(20) DEFAULT NULL,
                `shopName` varchar(255) DEFAULT NULL,
                `outInfo` varchar(50) NOT NULL DEFAULT '0',
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `user_name` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `dealId` (`dealId`),
                KEY `user_id` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(500) NOT NULL DEFAULT '',
                `share_key` varchar(255) DEFAULT NULL,
                `cid` int(11) DEFAULT NULL,
                `price` decimal(10,2) DEFAULT NULL,
                `pic_url` varchar(500) DEFAULT NULL,
                `guid` varchar(10) NOT NULL,
                `user_id` bigint(20) NOT NULL,
                `user_name` varchar(255) DEFAULT '',
                `tags` varchar(255) DEFAULT NULL,
                `content` varchar(500) DEFAULT NULL,
                `share_count` int(11) NOT NULL DEFAULT '0',
                `fav_count` int(11) unsigned NOT NULL DEFAULT '0',
                `comment_count` int(11) unsigned NOT NULL DEFAULT '0',
                `create_date` datetime DEFAULT '0000-00-00 00:00:00',
                `create_date_gmt` datetime DEFAULT NULL,
                `update_date` datetime DEFAULT '0000-00-00 00:00:00',
                `update_date_gmt` datetime DEFAULT NULL,
                `status` tinyint(1) NOT NULL DEFAULT '1',
                `cache_data` text,
                `sort` smallint(4) DEFAULT '100',
                `from_type` varchar(10) NOT NULL DEFAULT 'taobao',
                `data_type` tinyint(2) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `key_user` (`share_key`,`user_id`),
                KEY `uid` (`user_id`),
                KEY `status` (`status`),
                KEY `sort` (`sort`),
                KEY `fav_count` (`fav_count`) USING BTREE
              )  ENGINE = MyISAM AUTO_INCREMENT = " . rand(10000, 30000) . " DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `wp_xt_share_cron` (
                `share_key` varchar(255) NOT NULL DEFAULT '',
                `album_id` bigint(20) NOT NULL,
                `user_id` bigint(20) NOT NULL,
                `user_name` varchar(255) NOT NULL DEFAULT '',
                `create_date` datetime NOT NULL,
                `cache_data` longtext NOT NULL,
                PRIMARY KEY (`share_key`)
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";


    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_album` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `album_id` bigint(11) NOT NULL,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `user_name` varchar(255) NOT NULL,
                `create_date` datetime DEFAULT NULL,
                `create_date_gmt` datetime DEFAULT NULL,
                PRIMARY KEY (`id`,`user_id`,`album_id`),
                KEY `term_taxonomy_id` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_catalog` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `cid` bigint(11) NOT NULL,
                `create_date_gmt` datetime DEFAULT NULL,
                PRIMARY KEY (`id`,`cid`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_comment` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `share_id` int(11) NOT NULL,
                `user_id` int(11) NOT NULL,
                `user_name` varchar(255) NOT NULL,
                `content` varchar(500) NOT NULL,
                `type` tinyint(1) NOT NULL DEFAULT '1',
                `create_date` datetime DEFAULT '0000-00-00 00:00:00',
                `create_date_gmt` datetime DEFAULT NULL,
                `status` tinyint(4) DEFAULT '1',
                `ip` varchar(100) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `share_id` (`share_id`),
                KEY `uid` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_match` (
                `share_id` int(11) NOT NULL,
                `content_match` text NOT NULL,
                PRIMARY KEY (`share_id`),
                FULLTEXT KEY `content_match` (`content_match`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_tag` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(100) NOT NULL,
                `sort` smallint(5) NOT NULL DEFAULT '100',
                `is_hot` tinyint(1) NOT NULL DEFAULT '0',
                `count` int(11) NOT NULL DEFAULT '1',
                `nums` int(11) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `title` (`title`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_share_tag_catalog` (
                `id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `cid` bigint(11) NOT NULL,
                `sort` smallint(5) NOT NULL DEFAULT '100',
                `count` bigint(20) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`,`cid`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_taobao_itemcat` (
                `cid` bigint(20) unsigned NOT NULL DEFAULT '0',
                `parent_cid` bigint(20) unsigned NOT NULL DEFAULT '0',
                `name` varchar(255) NOT NULL,
                `is_parent` tinyint(1) NOT NULL DEFAULT '0',
                `status` varchar(20) NOT NULL,
                `sort_order` bigint(20) NOT NULL DEFAULT '0',
                PRIMARY KEY (`cid`),
                KEY `parent_cid` (`parent_cid`) USING BTREE
              ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_taobao_report` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `trade_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `trade_parent_id` bigint(20) unsigned NOT NULL DEFAULT '0',
                `real_pay_fee` varchar(20) DEFAULT NULL,
                `commission_rate` varchar(20) DEFAULT NULL,
                `commission` varchar(20) DEFAULT NULL,
                `app_key` varchar(50) DEFAULT NULL,
                `outer_code` varchar(50) DEFAULT NULL,
                `pay_time` datetime DEFAULT NULL,
                `pay_price` varchar(50) DEFAULT NULL,
                `num_iid` bigint(20) DEFAULT NULL,
                `item_title` varchar(255) DEFAULT NULL,
                `item_num` int(11) DEFAULT NULL,
                `category_id` bigint(20) DEFAULT NULL,
                `category_name` varchar(255) DEFAULT NULL,
                `shop_title` varchar(500) DEFAULT NULL,
                `seller_nick` varchar(255) DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT '0',
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `user_name` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `trade_id` (`trade_id`,`trade_parent_id`) USING BTREE,
                KEY `user_id` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_tixian` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `cash` varchar(20) NOT NULL DEFAULT '0',
                `freeze` varchar(20) DEFAULT '0',
                `jifen` varchar(20) DEFAULT '0',
                `freeze_jifen` varchar(20) DEFAULT '0',
                `status` int(11) NOT NULL DEFAULT '0' COMMENT '0:待支付,1:已支付',
                `account` varchar(255) DEFAULT NULL,
                `account_name` varchar(255) DEFAULT NULL,
                `create_time` datetime NOT NULL,
                `update_time` datetime NOT NULL,
                `content` varchar(500) DEFAULT NULL,
                `opertor` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_user_follow` (
                `user_id` bigint(20) NOT NULL,
                `f_user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '1:关注，2:喜欢，3:评论，4:提到，5:信件',
                `create_time` datetime NOT NULL,
                PRIMARY KEY (`user_id`,`f_user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_user_jifen_item` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) NOT NULL DEFAULT '0',
                `pic` varchar(255) NOT NULL,
                `jifen` int(11) NOT NULL DEFAULT '0',
                `stock` int(11) NOT NULL DEFAULT '1',
                `buy_count` int(11) NOT NULL DEFAULT '0',
                `user_count` int(11) NOT NULL DEFAULT '1',
                `sort` int(11) NOT NULL DEFAULT '100',
                `is_valid` tinyint(1) NOT NULL DEFAULT '1',
                `create_time` datetime NOT NULL,
                `content` varchar(500) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_user_jifen_log` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `user_name` varchar(255) NOT NULL,
                `jifen` int(11) NOT NULL DEFAULT '0',
                `create_time` datetime NOT NULL,
                `content` varchar(500) DEFAULT NULL,
                `action_id` varchar(255) NOT NULL,
                `action_name` varchar(50) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `actionId` (`user_id`,`action_id`,`action_name`) USING BTREE
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_user_jifen_order` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `item_id` bigint(20) NOT NULL DEFAULT '0',
                `num` int(11) NOT NULL,
                `jifen` bigint(20) NOT NULL DEFAULT '0',
                `status` int(11) NOT NULL DEFAULT '0' COMMENT '0:待审核,1:已完成,2:拒绝',
                `create_time` datetime NOT NULL,
                `content` varchar(500) DEFAULT NULL,
                `user_id` bigint(20) NOT NULL,
                `user_name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_user_notice` (
                `user_id` bigint(20) NOT NULL,
                `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1:关注，2:喜欢，3:评论，4:提到，5:信件',
                `num` int(11) NOT NULL DEFAULT '0',
                `create_time` datetime NOT NULL,
                PRIMARY KEY (`user_id`,`type`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    $sql[] = "CREATE TABLE `{$base_prefix}xt_yiqifa_report` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `actionId` varchar(255) DEFAULT NULL,
                `actionName` varchar(255) DEFAULT NULL,
                `cid` varchar(255) DEFAULT NULL,
                `cname` varchar(255) DEFAULT NULL,
                `commission` varchar(255) DEFAULT NULL,
                `commissionType` varchar(255) DEFAULT NULL,
                `itemId` varchar(255) DEFAULT NULL,
                `itemNums` varchar(255) DEFAULT NULL,
                `itemPrice` varchar(255) DEFAULT NULL,
                `itemTitle` varchar(255) DEFAULT NULL,
                `nick` varchar(255) DEFAULT NULL,
                `orderNo` varchar(255) DEFAULT NULL,
                `orderStatus` varchar(255) DEFAULT NULL,
                `orderTime` varchar(255) DEFAULT NULL,
                `outerCode` varchar(255) DEFAULT NULL,
                `sid` varchar(255) DEFAULT NULL,
                `wid` varchar(255) DEFAULT NULL,
                `yiqifaId` varchar(255) NOT NULL,
                `user_id` bigint(20) NOT NULL DEFAULT '0',
                `user_name` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `yiqifaId` (`yiqifaId`),
                KEY `user_id` (`user_id`)
              ) ENGINE = MyISAM DEFAULT CHARSET = utf8;";

    dbDelta($sql);
}

?>
