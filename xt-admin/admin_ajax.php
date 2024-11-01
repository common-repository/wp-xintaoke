<?php

function xt_admin_ajax_yiqifa_import_cps() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    $result['code'] = 500;
    $result['msg'] = '导入功能暂时屏蔽'; //下载的CPS订单明细缺少yiqifaId
    exit(json_encode($result));
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    $id = $_POST['id'];
    if (empty($id)) {
        $result['code'] = 500;
        $result['msg'] = '未指定要导入的订单文件';
        exit(json_encode($result));
    }
    $path = get_attached_file($id);
    if (empty($path)) {
        $result['code'] = 500;
        $result['msg'] = '指定的订单文件不存在';
        exit(json_encode($result));
    }
    $file = fopen($path, "r");
    $_count = 0;
    $orders = array();
    while (!feof($file)) {
        $row = (fgetcsv($file));
        if ($_count == 0) {
            if (count($row) != 18) {
                $result['code'] = 500;
                $result['msg'] = '指定的订单文件不正确,必须是从亿起发下载的CPS订单明细';
                exit(json_encode($result));
            }
        } else {
            if (!empty($row)) {
//                $order = array();
//                $order['yiqifaId'] = $row[5]; // 亿起发唯一编号
//                $order['actionId'] = $row[0]; // 联盟活动编号
//                $order['sid'] = NULL; // 网营商ID（商城）
//                $order['wid'] = $row[2]; // 网站编号
//                $order['orderTime'] = $row[4]; // 下单时间
//                $order['orderNo'] = $row[5]; // 订单编号
//                $order['commissionType'] = $row[6]; //佣金分类
//                $order['itemId'] = $row[7]; //商品编号
//                $order['itemNums'] = $row[8]; //订单商品件数
//                $order['itemPrice'] = $row[9]; //订单商品价格
//                $order['outerCode'] = $row[10]; //反馈标签（返利标识）
//                $order['orderStatus'] = $row[11]; //订单状态
//                $order['commission'] = $row[12]; //网站主佣金
//                $order['cid'] = $row[13]; //商品分类
//                //14 未知
//                $order['itemTitle'] = $row[15]; //商品标题
//                $order['actionName'] = $row[16]; //商城活动标题
//                foreach ($row as $col) {
//                    $col = xt_iconv($col, 'GBK', 'UTF-8');
//                }
            }
        }
        $_count++;
    }
    fclose($file);
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_yiqifa_import_cps', 'xt_admin_ajax_yiqifa_import_cps');

function xt_admin_ajax_site_verify() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    $verification = isset($_POST['verification']) ? ($_POST['verification']) : '';
    update_option(XT_OPTION_VERIFICATION, $verification);
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_site_verify', 'xt_admin_ajax_site_verify');

function xt_admin_ajax_link_convert() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    $links = $_POST['links'];
    if (empty($links)) {
        $result['code'] = 500;
        $result['msg'] = '未指定要转换的链接';
        exit(json_encode($result));
    }
    $_result = array();
    foreach ($links as $link) {
        if (!empty($link)) {
            $_result[] = xt_jump_url(array(
                'type' => 'url',
                'url' => $link
                    ));
        } else {
            $_result[] = '';
        }
    }
    $result['result'] = $_result;
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_link_convert', 'xt_admin_ajax_link_convert');

function xt_admin_ajax_setting() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    //GLOBAL
    $_option_global = array();
    if (isset($_POST['isFanxian'])) {
        $_option_global['isFanxian'] = $_POST['isFanxian'] ? 1 : 0;
    }
    if (isset($_POST['isS8'])) {
        $_option_global['isS8'] = $_POST['isS8'] ? 1 : 0;
    }
    if (isset($_POST['isTaobaoPopup'])) {
        $_option_global['isTaobaoPopup'] = $_POST['isTaobaoPopup'] ? 1 : 0;
    }
    if (isset($_POST['isForceLogin'])) {
        $_option_global['isForceLogin'] = $_POST['isForceLogin'] ? 1 : 0;
    }
    if (isset($_POST['isDisplayComment'])) {
        $_option_global['isDisplayComment'] = $_POST['isDisplayComment'] ? 1 : 0;
    }
    if (isset($_POST['isScroll'])) {
        $_option_global['isScroll'] = $_POST['isScroll'] ? 1 : 0;
    }
    if (isset($_POST['followLimit'])) {
        $_option_global['followLimit'] = absint($_POST['followLimit']);
    }

    if (isset($_POST['albumDisplay'])) {
        $_option_global['albumDisplay'] = ($_POST['albumDisplay'] == 'big') ? 'big' : 'small';
    }
    if (isset($_POST['userDescription'])) {
        $_option_global['userDescription'] = $_POST['userDescription'] ? 1 : 0;
    }
    if (isset($_POST['bulletin'])) {
        $_option_global['bulletin'] = $_POST['bulletin'];
    }
    if (isset($_POST['bdshare'])) {
        if (is_numeric($_POST['bdshare'])) {
            $_option_global['bdshare'] = absint($_POST['bdshare']);
        }
    }
    if (isset($_POST['codeAnalytics'])) {
        $codeAnalytics = trim($_POST['codeAnalytics']);
        if (!empty($codeAnalytics)) {
            update_option(XT_OPTION_CODE_ANALYTICS, stripslashes($codeAnalytics));
        } else {
            update_option(XT_OPTION_CODE_ANALYTICS, false);
        }
    }
    if (isset($_POST['codeShare'])) {
        $codeShare = trim($_POST['codeShare']);
        if (!empty($codeShare)) {
            update_option(XT_OPTION_CODE_SHARE, stripslashes($codeShare));
        } else {
            update_option(XT_OPTION_CODE_SHARE, false);
        }
    }

    $option_global = get_option(XT_OPTION_GLOBAL);
    update_option(XT_OPTION_GLOBAL, array_merge($option_global, $_option_global));
    //FANXIAN
    $_option_fanxian = array();

    if (isset($_POST['isPendingTixian'])) {
        $_option_fanxian['isPendingTixian'] = $_POST['isPendingTixian'] ? 1 : 0;
    }
    if (isset($_POST['isAutoCash'])) {
        $_option_fanxian['isAutoCash'] = $_POST['isAutoCash'] ? 1 : 0;
    }
    if (isset($_POST['rate_cashback'])) {
        $_option_fanxian['rate_cashback'] = absint($_POST['rate_cashback']);
    }
    //share
    if (isset($_POST['isShare'])) {
        $_option_fanxian['isShare'] = absint($_POST['isShare']);
    }
    if (isset($_POST['rate_share'])) {
        $_option_fanxian['rate_share'] = absint($_POST['rate_share']);
    }
    //ads
    if (isset($_POST['isAd'])) {
        $_option_fanxian['isAd'] = absint($_POST['isAd']);
    }
    if (isset($_POST['rate_ad'])) {
        $_option_fanxian['rate_ad'] = absint($_POST['rate_ad']);
    }
    //tixian
    if (isset($_POST['cashback'])) {
        $_option_fanxian['cashback'] = absint($_POST['cashback']);
    }
    //registe
    if (isset($_POST['registe_cash'])) {
        $_option_fanxian['registe_cash'] = absint($_POST['registe_cash']);
    }
    if (isset($_POST['registe_jifen'])) {
        $_option_fanxian['registe_jifen'] = absint($_POST['registe_jifen']);
    }

    $option_fanxian = get_option(XT_OPTION_FANXIAN);
    update_option(XT_OPTION_FANXIAN, array_merge($option_fanxian, $_option_fanxian));

    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_setting', 'xt_admin_ajax_setting');

function xt_admin_ajax_mail() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    //GLOBAL
    $_option_mail = array();
    if (isset($_POST['mailer']) && $_POST['mailer'] == 'mail') {
        $_option_mail['mailer'] = 'mail';
    } else {
        $_option_mail['mailer'] = 'smtp';
    }
    if (isset($_POST['smtp_host'])) {
        $_option_mail['smtp_host'] = $_POST['smtp_host'];
    }
    if (isset($_POST['smtp_port'])) {
        $_option_mail['smtp_port'] = absint($_POST['smtp_port']);
    }
    if (isset($_POST['smtp_user'])) {
        if (!is_email(trim($_POST['smtp_user']))) {
            $result['code'] = 500;
            $result['msg'] = '邮箱用户名不合法';
            exit(json_encode($result));
        }
        $_option_mail['smtp_user'] = trim($_POST['smtp_user']);
    }
    if (isset($_POST['smtp_pass'])) {
        $_option_mail['smtp_pass'] = trim($_POST['smtp_pass']);
    }
    $option_mail = get_option(XT_OPTION_MAIL);
    if (empty($option_mail)) {
        $option_mail = array();
    }
    update_option(XT_OPTION_MAIL, array_merge($option_mail, $_option_mail));
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_mail', 'xt_admin_ajax_mail');

function xt_admin_ajax_mail_test() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        $result['result'] = array(
            'phpmailer' => '您无权操作此功能',
            'smtpdebug' => ''
        );
        exit(json_encode($result));
    }
    $to = isset($_POST['to']) ? $_POST['to'] : '';
    if (empty($to)) {
        $result['code'] = 500;
        $result['msg'] = '收件人不能为空';
        $result['result'] = array(
            'phpmailer' => '收件人不能为空',
            'smtpdebug' => ''
        );
        exit(json_encode($result));
    }
    if (!is_email($to)) {
        $result['code'] = 500;
        $result['msg'] = '收件人邮箱地址不合法';
        $result['result'] = array(
            'phpmailer' => '收件人邮箱地址不合法',
            'smtpdebug' => ''
        );
        exit(json_encode($result));
    }

    $option_mail = get_option(XT_OPTION_MAIL);
    if (empty($option_mail)) {
        $result['code'] = 500;
        $result['msg'] = '您尚未配置邮件服务器';
        $result['result'] = array(
            'phpmailer' => '您尚未配置邮件服务器',
            'smtpdebug' => ''
        );
        exit(json_encode($result));
    }
    $sitetitle = get_bloginfo('name');
    $subject = $sitetitle . '-邮件测试: ' . $to;
    $message = '这是一封测试邮件';
    global $phpmailer;
    if (!is_object($phpmailer) || !is_a($phpmailer, 'PHPMailer')) {
        require_once ABSPATH . WPINC . '/class-phpmailer.php';
        require_once ABSPATH . WPINC . '/class-smtp.php';
        $phpmailer = new PHPMailer();
    }
    $phpmailer->SMTPDebug = 2;
    ob_start();
    wp_mail($to, $subject, $message);
    $smtp_debug = ob_get_clean();
    if (!empty($phpmailer->ErrorInfo)) {
        $result['code'] = 500;
        $result['msg'] = '发送失败';
        $result['result'] = array(
            'phpmailer' => $phpmailer->ErrorInfo,
            'smtpdebug' => $smtp_debug
        );
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_mail_test', 'xt_admin_ajax_mail_test');

function xt_admin_ajax_platform() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
        exit(json_encode($result));
    }
    if (!isset($_POST['platform']) || empty($_POST['platform']) || !in_array($_POST['platform'], xt_platforms())) {
        $result['code'] = 500;
        $result['msg'] = '未指定平台';
    }
    $platform = $_POST['platform'];
    $appKey = '';
    $appSecret = '';
    $tkpid = '';
    $s8pid = '';
    $token = '';
    $userId = '';
    $uid = '';
    $account = '';
    $sid = '';
    $wid = '';
    $syncSecret = '';
    //校验
    switch ($platform) {
        case 'xt' :
            update_option(XT_OPTION_INSTALLED, 1);
            break;
        case 'taobao' :
            $tkpid = trim($_POST['tkpid']);
            $s8pid = trim($_POST['s8pid']);
            break;
        case 'paipai' :
            $uid = trim($_POST['uid']);
            $token = trim($_POST['token']);
            $userId = trim($_POST['userId']);
            break;
        case 'yiqifa' :
            if (!isset($_POST['account']) || empty($_POST['account'])) {
                $result['code'] = 500;
                $result['msg'] = '未填写亿起发账号!';
            }
            if (!isset($_POST['sid']) || empty($_POST['sid'])) {
                $result['code'] = 500;
                $result['msg'] = '未填写亿起发网站主ID';
            }
            if (!isset($_POST['wid']) || empty($_POST['wid'])) {
                $result['code'] = 500;
                $result['msg'] = '未填写亿起发网站ID';
            }
            $account = trim($_POST['account']);
            $sid = trim($_POST['sid']);
            $wid = trim($_POST['wid']);
            $syncSecret = trim($_POST['syncSecret']);
            break;
        case 'weibo' :
            break;
        case 'qq' :
            break;
    }
    $appKey = trim($_POST['appKey']);
    $appSecret = trim($_POST['appSecret']);

    if ($result['code'] == 0) {
        $option_platform = get_option(XT_OPTION_PLATFORM);
        $app = $option_platform[$platform];

        if ($platform == 'paipai') {
            $app['uid'] = $uid;
            $app['token'] = $token;
            $app['userId'] = $userId;
        } elseif ($platform == 'yiqifa') {
            $app['account'] = $account;
            $app['sid'] = $sid;
            $app['wid'] = $wid;
            $app['syncSecret'] = $syncSecret;
        } elseif ($platform == 'taobao') {
            $app['tkpid'] = $tkpid;
            $app['s8pid'] = $s8pid;
        } elseif ($app['appKey'] != $appKey) {
            $app['uid'] = '';
            $app['name'] = '';
            $app['token'] = array();
        }
        $app['appKey'] = $appKey;
        $app['appSecret'] = $appSecret;
        $option_platform[$platform] = $app;
        update_option(XT_OPTION_PLATFORM, $option_platform);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_platform', 'xt_admin_ajax_platform');

function xt_admin_ajax_catalog_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        exit('您无权操作此功能');
    }
    if (!isset($_POST['type']) || empty($_POST['type']) || !in_array($_POST['type'], array(
                'share',
                'album'
            ))) {
        exit('未指定分类类型');
    }
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        exit('未指定分类');
    }
    $id = absint($_POST['id']);
    if ($result['code'] == 0) {
        global $wpdb;
        $title = strip_tags(isset($_POST['title']) ? $_POST['title'] : '');
        $pic = isset($_POST['pic']) ? $_POST['pic'] : '';
        $sort = isset($_POST['sort']) ? absint($_POST['sort']) : '';
        $_cat = xt_get_catalog($id);
        if (empty($_cat)) {
            exit('要修改的分类不存在');
        }
        $data = array();
        if (!empty($title)) {
            if ($_cat->title != $title) {
                if (xt_catalog_exit(0, $title, $_cat->parent, $_cat->type)) {
                    exit('分类名称重复');
                }
            }
            $data['title'] = $title;
        }
        if (!empty($sort)) {
            $data['sort'] = $sort;
        }
        $data['pic'] = $pic;
        if (!empty($data)) {
            $wpdb->update(XT_TABLE_CATALOG, $data, array(
                'id' => $id
            ));
            if ($_cat->type == 'share') {
                xt_catalogs_share(true); //FORCE                
            } elseif ($_cat->type == 'album') {
                xt_catalogs_album(true); //FORCE                                
            }
        }
        $cat = xt_get_catalog($id);
        exit(xt_row_catalog($cat, isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1));
    }
    exit('未知错误');
}

add_action('wp_ajax_xt_admin_ajax_catalog_update', 'xt_admin_ajax_catalog_update');

function xt_admin_ajax_catalog_add() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        exit('您无权操作此功能');
    }
    if (!isset($_POST['type']) || empty($_POST['type']) || !in_array($_POST['type'], array(
                'share',
                'album'
            ))) {
        exit('未指定分类类型');
    }
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        exit('未指定分类标题');
    }
    if ($result['code'] == 0) {
        global $wpdb;
        $type = $_POST['type'];
        $title = $_POST['title'];
        $pic = $_POST['pic'];
        $sort = isset($_POST['sort']) ? absint($_POST['sort']) : '';
        $parent = isset($_POST['parent']) ? absint($_POST['parent']) : 0;
        $is_front = isset($_POST['is_front']) ? absint($_POST['is_front']) : 1;
        $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $data = array();
        $data['title'] = $title;
        $data['pic'] = $pic;
        $data['sort'] = $sort;
        $data['parent'] = $parent;
        $data['keywords'] = '';
        $data['description'] = '';
        if ($is_front) { //前台分类
            $data['is_front'] = 1;
        } else { //系统分类,不提供多级
            $data['parent'] = 0;
            $data['is_front'] = 0;
        }
        $data['type'] = $type;
        if (!empty($data)) {
            if (xt_catalog_exit(0, $title, $parent, $type)) {
                exit('分类名称重复');
            }
            if ($id = xt_new_catalog($data) > 0) {
                if ($type == 'share') {
                    xt_catalogs_share(true); //FORCE                    
                } elseif ($type == 'album') {
                    xt_catalogs_album(true); //FORCE                    
                }

                exit();
            }
        }
    }
    exit('未知错误');
}

add_action('wp_ajax_xt_admin_ajax_catalog_add', 'xt_admin_ajax_catalog_add');

function xt_admin_ajax_catalog_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分类';
    }
    $ids = trim($_POST['ids']);
    $type = trim($_POST['type']);
    if ($result['code'] == 0) {
        xt_catalog_delete($ids);
        if ($type == 'share') {
            xt_catalogs_share(true); //FORCE
        } elseif ($type == 'album') {
            xt_catalogs_album(true); //FORCE
        }
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_catalog_delete', 'xt_admin_ajax_catalog_delete');

function xt_admin_ajax_tag_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定标签';
    }
    $ids = trim($_POST['ids']);
    if ($result['code'] == 0) {
        xt_tag_delete($ids);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_tag_delete', 'xt_admin_ajax_tag_delete');

function xt_admin_ajax_tag_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定标签';
    }
    if (!isset($_POST['sort']) || empty($_POST['sort'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定标签排序';
    }
    $id = intval(trim($_POST['id']));
    $sort = intval($_POST['sort']);
    $cids = isset($_POST['cids']) ? $_POST['cids'] : '';
    $currentCid = intval($_POST['cid']);
    if ($result['code'] == 0) {
        global $wpdb;
        if ($currentCid > 0) {
            echo 'update:[' . $wpdb->update(XT_TABLE_SHARE_TAG_CATALOG, array('sort' => $sort), array('id' => $id, 'cid' => $currentCid)) . ']';
        } else {
            $wpdb->update(XT_TABLE_SHARE_TAG, array(
                'sort' => $sort
                    ), array(
                'id' => $id
            ));
        }
        $query = "SELECT t.*, tr.id AS tag_id FROM " . XT_TABLE_CATALOG . " AS t INNER JOIN " . XT_TABLE_SHARE_TAG_CATALOG . " AS tr ON t.id = tr.cid WHERE t.type = 'share' AND tr.id =" . ($id) . " ORDER BY t.sort ASC,t.count DESC";
        $term = $wpdb->get_results($query);
        $old = array();
        if (!empty($term)) {
            foreach ($term as $_term) {
                $old[] = $_term->id;
            }
        }
        $cids = array_map('intval', explode(',', $cids));
        if (empty($cids)) {
            $cids = array();
        }
        $deleteIds = (array_diff($old, $cids)); //delete
        $addIds = (array_diff($cids, $old)); //add
        if (!empty($deleteIds)) {
            foreach ($deleteIds as $del) {
                xt_delete_tag_catalog($del, $id);
            }
        }
        if (!empty($addIds)) {
            foreach ($addIds as $add) {
                xt_new_tag_catalog(array('cid' => $add, 'id' => $id));
            }
        }
        $tag = xt_get_tag($id);
        if (!empty($addIds)) {
            global $wpdb;
            foreach ($addIds as $add) {//refresh count
                $count = xt_get_sharecount_bytagandcid($tag->title, $add);
                $wpdb->query("UPDATE " . XT_TABLE_SHARE_TAG_CATALOG . " SET count=$count WHERE id=" . absint($id) . " AND cid=" . absint($add));
            }
        }

        $term = $wpdb->get_results($query); //reload
        xt_update_catalog_terms_cache(($id), $term);
        //sort and count
        if ($currentCid > 0) {
            $catalog = xt_get_catalog($currentCid);
            if (!empty($catalog)) {
                global $wpdb;
                $sql = "SELECT " . XT_TABLE_SHARE_TAG_CATALOG . ".cid," . XT_TABLE_SHARE_TAG_CATALOG . ".sort," . XT_TABLE_SHARE_TAG . ".id," . XT_TABLE_SHARE_TAG . ".title," . XT_TABLE_SHARE_TAG . ".is_hot," . XT_TABLE_SHARE_TAG_CATALOG . ".count," . XT_TABLE_SHARE_TAG . ".nums FROM " . XT_TABLE_SHARE_TAG_CATALOG . " INNER JOIN " . XT_TABLE_SHARE_TAG . " ON " . XT_TABLE_SHARE_TAG . ".id=" . XT_TABLE_SHARE_TAG_CATALOG . ".id WHERE " . XT_TABLE_SHARE_TAG_CATALOG . ".id=$id AND " . XT_TABLE_SHARE_TAG_CATALOG . ".cid=$currentCid";
                if (isset($catalog->children) && !empty($catalog->children)) {
                    $sql = "SELECT " . XT_TABLE_SHARE_TAG_CATALOG . ".cid," . XT_TABLE_SHARE_TAG_CATALOG . ".sort AS sort,min(" . XT_TABLE_SHARE_TAG_CATALOG . ".sort) AS childSort," . XT_TABLE_SHARE_TAG . ".id," . XT_TABLE_SHARE_TAG . ".title," . XT_TABLE_SHARE_TAG . ".is_hot,max(" . XT_TABLE_SHARE_TAG_CATALOG . ".count) AS count," . XT_TABLE_SHARE_TAG . ".nums FROM " . XT_TABLE_SHARE_TAG_CATALOG . " INNER JOIN " . XT_TABLE_SHARE_TAG . " ON " . XT_TABLE_SHARE_TAG . ".id=" . XT_TABLE_SHARE_TAG_CATALOG . ".id WHERE " . XT_TABLE_SHARE_TAG_CATALOG . ".id=$id AND " . XT_TABLE_SHARE_TAG_CATALOG . ".cid in(" . $wpdb->escape($catalog->children) . ",$currentCid) GROUP BY " . XT_TABLE_SHARE_TAG . ".id ORDER BY sort ASC,childSort ASC," . XT_TABLE_SHARE_TAG_CATALOG . ".count DESC";
                }
                $tag = $wpdb->get_row($sql);
            }
        }
        exit(xt_row_tag($tag, (isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1), $currentCid));
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_tag_update', 'xt_admin_ajax_tag_update');

function xt_admin_ajax_tag_add() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        exit('您无权操作此功能');
    }
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        exit('未指定分类标题');
    }
    if ($result['code'] == 0) {
        $title = $_POST['title'];
        $cid = $_POST['catalog'];
        $sort = isset($_POST['sort']) ? absint($_POST['sort']) : 100;
        $data = array();
        $data['title'] = $title;
        $data['sort'] = $sort;
        $data['is_hot'] = 0;
        if (!empty($data)) {
            $tag = xt_get_tag(0, $title);
            $tagSort = 100;
            $id = 0;
            if (empty($tag)) {
                $id = xt_new_tag($data);
                $tagSort = xt_tag_default_sort($title);
            } else {
                $id = $tag->id;
                $tagSort = $tag->sort;
            }
            if ($id > 0 && $cid > 0 && xt_catalog_exit($cid)) {
                if (!(xt_tag_catalog_exit($id, $cid))) {
                    xt_new_tag_catalog(array('id' => $id, 'cid' => $cid, 'sort' => $tagSort));
                }
            }
        }
        exit();
    }
    exit('未知错误');
}

add_action('wp_ajax_xt_admin_ajax_tag_add', 'xt_admin_ajax_tag_add');

function xt_admin_ajax_share_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分享';
    }
    $ids = trim($_POST['ids']);
    if ($result['code'] == 0) {
        xt_share_delete($ids);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_share_delete', 'xt_admin_ajax_share_delete');

function xt_admin_ajax_share_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定分享';
    }
    $id = intval(trim($_POST['id']));
    $cids = isset($_POST['cids']) ? $_POST['cids'] : '';
    if ($result['code'] == 0) {
        global $wpdb;

        $query = "SELECT t.*, tr.id AS share_id FROM " . XT_TABLE_CATALOG . " AS t INNER JOIN " . XT_TABLE_SHARE_CATALOG . " AS tr ON t.id = tr.cid WHERE t.type = 'share' AND tr.id =" . ($id) . " ORDER BY t.sort ASC,t.count DESC";
        $term = $wpdb->get_results($query);
        $old = array();
        if (!empty($term)) {
            foreach ($term as $_term) {
                $old[] = $_term->id;
            }
        }
        $cids = array_map('intval', explode(',', $cids));
        if (empty($cids)) {
            $cids = array();
        }
        $deleteIds = (array_diff($old, $cids)); //delete
        $addIds = (array_diff($cids, $old)); //add
        if (!empty($deleteIds)) {
            foreach ($deleteIds as $del) {
                xt_delete_share_catalog($del, $id);
            }
        }
        if (!empty($addIds)) {
            foreach ($addIds as $add) {
                xt_new_share_catalog(array('cid' => $add, 'id' => $id));
            }
        }
        $share = get_share($id);
        $term = $wpdb->get_results($query); //reload
        xt_update_catalog_terms_cache(($id), $term, 'share');
        exit(xt_row_share($share, isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1));
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_share_update', 'xt_admin_ajax_share_update');

function xt_admin_ajax_album_delete() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑';
    }
    $ids = trim($_POST['ids']);
    if ($result['code'] == 0) {
        xt_album_delete($ids);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_album_delete', 'xt_admin_ajax_album_delete');

function xt_admin_ajax_album_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定专辑';
    }
    $id = intval(trim($_POST['id']));
    $cids = isset($_POST['cids']) ? $_POST['cids'] : '';
    if ($result['code'] == 0) {
        global $wpdb;

        $query = "SELECT t.*, tr.id AS album_id FROM " . XT_TABLE_CATALOG . " AS t INNER JOIN " . XT_TABLE_ALBUM_CATALOG . " AS tr ON t.id = tr.cid WHERE t.type = 'album' AND tr.id =" . ($id) . " ORDER BY t.sort ASC,t.count DESC";
        $term = $wpdb->get_results($query);
        $old = array();
        if (!empty($term)) {
            foreach ($term as $_term) {
                $old[] = $_term->id;
            }
        }
        $cids = array_map('intval', explode(',', $cids));
        if (empty($cids)) {
            $cids = array();
        }
        $deleteIds = (array_diff($old, $cids)); //delete
        $addIds = (array_diff($cids, $old)); //add
        if (!empty($deleteIds)) {
            foreach ($deleteIds as $del) {
                xt_delete_album_catalog($del, $id);
            }
        }
        if (!empty($addIds)) {
            foreach ($addIds as $add) {
                xt_new_album_catalog(array('cid' => $add, 'id' => $id));
            }
        }
        $album = xt_get_album($id);
        $term = $wpdb->get_results($query); //reload
        xt_update_catalog_terms_cache(($id), $term, 'album');
        exit(xt_row_album($album, isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1));
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_album_update', 'xt_admin_ajax_album_update');

function xt_admin_ajax_comment_delete() {

    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定评论';
    }
    $ids = trim($_POST['ids']);
    if ($result['code'] == 0) {
        xt_comment_delete($ids);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_comment_delete', 'xt_admin_ajax_comment_delete');

function xt_admin_ajax_role_add() {
    global $wp_roles;
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        exit('您无权操作此功能');
    }
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        exit('未指定角色英文名');
    } else {
        if (!preg_match('/^[a-z]{1,50}$/', $_POST['name'])) {
            exit('角色英文名必须是1-50个小写字母');
        }
        if (isset($wp_roles->role_names[trim($_POST['name'])])) {
            exit('角色英文名已存在');
        }
    }
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        exit('未指定角色中文名');
    }
    if ($result['code'] == 0) {
        global $wpdb;
        $name = trim($_POST['name']);
        $title = trim($_POST['title']);
        $ismulti = isset($_POST['ismulti']) ? (absint($_POST['ismulti']) ? 1 : 0) : 0;
        $rate = isset($_POST['rate']) ? (($_POST['rate'] == '') ? -1 : intval($_POST['rate'])) : '';
        $adrate = isset($_POST['adrate']) ? (($_POST['adrate'] == '') ? -1 : intval($_POST['adrate'])) : '';
        $sharerate = isset($_POST['sharerate']) ? (($_POST['sharerate'] == '') ? -1 : intval($_POST['sharerate'])) : '';
        add_role($name, $title);
        $role = & get_role($name);
        $role->add_cap('read');
        $role->add_cap('level_0');
        $_roles = get_option(XT_OPTION_ROLE);
        if (empty($_roles)) {
            $_roles = array(
                $name => array(
                    'rate' => $rate,
                    'adrate' => $adrate,
                    'sharerate' => $sharerate,
                    'ismulti' => $ismulti
                )
            );
        } else {
            $_roles[$name] = array(
                'rate' => $rate,
                'adrate' => $adrate,
                'sharerate' => $sharerate,
                'ismulti' => $ismulti
            );
        }
        update_option(XT_OPTION_ROLE, $_roles);
        exit();
    }
    exit('未知错误');
}

add_action('wp_ajax_xt_admin_ajax_role_add', 'xt_admin_ajax_role_add');

function xt_admin_ajax_role_delete() {
    global $wp_roles;
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        $result['code'] = 500;
        $result['msg'] = '您无权操作此功能';
    }

    if (!isset($_POST['roles']) || empty($_POST['roles'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定角色';
    }
    $roles = trim($_POST['roles']);
    if ($result['code'] == 0) {
        xt_role_delete($roles);
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_role_delete', 'xt_admin_ajax_role_delete');

function xt_admin_ajax_role_update() {
    global $wp_roles;
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!current_user_can('manage_options')) {
        exit('您无权操作此功能');
    }
    if (!isset($_POST['name']) || empty($_POST['name'])) {
        exit('未指定角色英文名');
    }
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        exit('未指定角色中文名');
    }
    if ($result['code'] == 0) {
        global $wpdb;
        $name = trim($_POST['name']);
        $title = trim($_POST['title']);
        $ismulti = isset($_POST['ismulti']) ? (absint($_POST['ismulti']) ? 1 : 0) : 0;
        $rate = isset($_POST['rate']) ? (($_POST['rate'] == '') ? -1 : intval($_POST['rate'])) : '';
        $adrate = isset($_POST['adrate']) ? (($_POST['adrate'] == '') ? -1 : intval($_POST['adrate'])) : '';
        $sharerate = isset($_POST['sharerate']) ? (($_POST['sharerate'] == '') ? -1 : intval($_POST['sharerate'])) : '';
        $_roles = get_option(XT_OPTION_ROLE);
        if (empty($_roles)) {
            $_roles = array(
                $name => array(
                    'rate' => $rate,
                    'adrate' => $adrate,
                    'sharerate' => $sharerate,
                    'ismulti' => $ismulti
                )
            );
        } else {
            $_roles[$name] = array(
                'rate' => $rate,
                'adrate' => $adrate,
                'sharerate' => $sharerate,
                'ismulti' => $ismulti
            );
        }
        update_option(XT_OPTION_ROLE, $_roles);
        remove_role($name); //通过先删除再增加来修改角色标题
        add_role($name, $title);
        $role = & get_role($name);
        $role->add_cap('read');
        $role->add_cap('level_0');
        exit(xt_row_role($name, $role, isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1));
    }
    exit('未知错误');
}

add_action('wp_ajax_xt_admin_ajax_role_update', 'xt_admin_ajax_role_update');

function xt_admin_ajax_tixian_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定提现记录';
        exit(json_encode($result));
    }
    global $wpdb;
    $id = (int) $_POST['id'];
    $freeze = isset($_POST['freeze']) ? (float) $_POST['freeze'] : 0;
    $freeze_jifen = isset($_POST['freeze_jifen']) ? intval($_POST['freeze_jifen']) : 0;
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $user = wp_get_current_user();
    $opertor = $wpdb->escape($user->user_login);
    $update_time = current_time('mysql');
    $tixianRow = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_TIXIAN . ' WHERE id=' . intval($id));
    if (!empty($tixianRow)) {
        $wpdb->update(XT_TABLE_TIXIAN, array(
            'status' => 1,
            'freeze' => $freeze,
            'freeze_jifen' => $freeze_jifen,
            'content' => $wpdb->escape($content),
            'update_time' => current_time('mysql'),
            'opertor' => $wpdb->escape($user->user_login)
                ), array(
            'id' => $id
        ));
        //update cash cast
        xt_update_user_account_cost_counts($tixianRow->user_id);
    } else {
        $result['code'] = 500;
        $result['msg'] = '指定的提现记录不存在';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_tixian_update', 'xt_admin_ajax_tixian_update');

function xt_admin_ajax_report_taobao() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['start']) || empty($_POST['start'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定开始时间';
        exit(json_encode($result));
    }
    if (!isset($_POST['end']) || empty($_POST['end'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定结束时间';
        exit(json_encode($result));
    }
    $start = date('Ymd', strtotime($_POST['start']));
    $end = date('Ymd', strtotime($_POST['end']) + DAY_IN_SECONDS);
    global $xt_report_total, $xt_report_insert;
    $xt_report_total = 0;
    $xt_report_insert = 0;
    xt_report_taobao($start, $end);
    $result['result'] = array(
        'total' => $xt_report_total,
        'insert' => $xt_report_insert
    );
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_report_taobao', 'xt_admin_ajax_report_taobao');

function xt_admin_ajax_report_paipai() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['start']) || empty($_POST['start'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定开始时间';
        exit(json_encode($result));
    }
    if (!isset($_POST['end']) || empty($_POST['end'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定结束时间';
        exit(json_encode($result));
    }
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end = date('Y-m-d H:i:s', strtotime($_POST['end']));
    global $xt_report_total, $xt_report_insert;
    $xt_report_total = 0;
    $xt_report_insert = 0;
    xt_report_paipai($start, $end);
    $result['result'] = array(
        'total' => $xt_report_total,
        'insert' => $xt_report_insert
    );
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_report_paipai', 'xt_admin_ajax_report_paipai');

function xt_admin_ajax_report_yiqifa() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['start']) || empty($_POST['start'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定开始时间';
        exit(json_encode($result));
    }
    if (!isset($_POST['end']) || empty($_POST['end'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定结束时间';
        exit(json_encode($result));
    }
    $start = $_POST['start'];
    $end = $_POST['end'];
    global $xt_report_total, $xt_report_insert;
    $xt_report_total = 0;
    $xt_report_insert = 0;
    xt_report_yiqifa($start, $end);
    $result['result'] = array(
        'total' => $xt_report_total,
        'insert' => $xt_report_insert
    );
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_report_yiqifa', 'xt_admin_ajax_report_yiqifa');

function xt_admin_ajax_jifen_item_save() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品标题';
        exit(json_encode($result));
    }
    if (!isset($_POST['pic']) || empty($_POST['pic'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品图标地址';
        exit(json_encode($result));
    }
    if (!isset($_POST['sort']) || empty($_POST['sort'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品排序';
        exit(json_encode($result));
    }
    if (!isset($_POST['stock']) || empty($_POST['stock'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品库存';
        exit(json_encode($result));
    }
    if (!isset($_POST['jifen']) || empty($_POST['jifen'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品兑换' . xt_jifenbao_text();
        exit(json_encode($result));
    }
    if (!isset($_POST['userCount']) || empty($_POST['userCount'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定商品每人限兑数量';
        exit(json_encode($result));
    }
    global $wpdb;
    $title = $_POST['title'];
    $pic = $_POST['pic'];
    $sort = $_POST['sort'];
    $stock = $_POST['stock'];
    $jifen = $_POST['jifen'];
    $user_count = $_POST['userCount'];
    $wpdb->insert(XT_TABLE_USER_JIFEN_ITEM, array(
        'title' => $title,
        'pic' => $pic,
        'sort' => (int) $sort,
        'stock' => (int) $stock,
        'jifen' => (int) $jifen,
        'user_count' => $user_count,
        'create_time' => current_time('mysql'),
        'content' => isset($_POST['content']) ? $wpdb->escape($_POST['content']) : ''
    ));
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_jifen_item_save', 'xt_admin_ajax_jifen_item_save');

function xt_admin_ajax_jifen_item_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        exit('未指定商品');
    }
    if (!isset($_POST['title']) || empty($_POST['title'])) {
        exit('未指定商品标题');
    }
    if (!isset($_POST['pic']) || empty($_POST['pic'])) {
        exit('未指定商品图标地址');
    }
    if (!isset($_POST['sort']) || empty($_POST['sort'])) {
        exit('未指定商品排序');
    }
    if (!isset($_POST['stock']) || empty($_POST['stock'])) {
        exit('未指定商品库存');
    }
    if (!isset($_POST['jifen']) || empty($_POST['jifen'])) {
        exit('未指定商品兑换' . xt_jifenbao_text());
    }
    if (!isset($_POST['userCount']) || empty($_POST['userCount'])) {
        exit('未指定商品每人限兑数量');
    }
    global $wpdb;
    $id = (int) $_POST['id'];
    $title = $_POST['title'];
    $pic = $_POST['pic'];
    $sort = $_POST['sort'];
    $stock = $_POST['stock'];
    $jifen = $_POST['jifen'];
    $user_count = $_POST['userCount'];
    $wpdb->update(XT_TABLE_USER_JIFEN_ITEM, array(
        'title' => $title,
        'pic' => $pic,
        'sort' => (int) $sort,
        'stock' => (int) $stock,
        'jifen' => (int) $jifen,
        'user_count' => $user_count,
        'content' => isset($_POST['content']) ? $wpdb->escape($_POST['content']) : ''
            ), array(
        'id' => $id
    ));
    $item = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_USER_JIFEN_ITEM . ' WHERE id=' . $wpdb->escape($id));
    exit(xt_row_jifenItem($item, isset($_POST['alternate']) && $_POST['alternate'] ? 0 : 1));
}

add_action('wp_ajax_xt_admin_ajax_jifen_item_update', 'xt_admin_ajax_jifen_item_update');

function xt_admin_ajax_jifen_order_box() {
    exit(require_once (XT_PLUGIN_DIR . '/xt-admin/fanxian_jifen_box.php'));
}

add_action('wp_ajax_xt_admin_ajax_jifen_order_box', 'xt_admin_ajax_jifen_order_box');

function xt_admin_ajax_jifen_order_update() {
    $result = array(
        'code' => 0,
        'msg' => '',
        'result' => array()
    );
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $result['code'] = 500;
        $result['msg'] = '未指定兑换记录';
        exit(json_encode($result));
    }
    global $wpdb;
    $id = (int) $_POST['id'];

    $orderRow = $wpdb->get_row('SELECT * FROM ' . XT_TABLE_USER_JIFEN_ORDER . ' WHERE id=' . intval($id));
    if (!empty($orderRow)) {
        $wpdb->update(XT_TABLE_USER_JIFEN_ORDER, array(
            'status' => 1,
            'content' => isset($_POST['content']) ? $wpdb->escape($_POST['content']) : ''
                ), array(
            'id' => $id
        ));
        //update jifen cast
        xt_update_user_account_cost_counts($orderRow->user_id);
    } else {
        $result['code'] = 500;
        $result['msg'] = '指定的兑换记录不存在';
    }
    exit(json_encode($result));
}

add_action('wp_ajax_xt_admin_ajax_jifen_order_update', 'xt_admin_ajax_jifen_order_update');