<?php

if (isset($_GET['platform']) && !empty($_GET['platform'])) {
    $name = strtolower($_GET['platform']);
    $state = isset($_GET['state']) && !empty($_GET['state']) ? $_GET['state'] : home_url();
    $mode = isset($_GET['mode']) && !empty($_GET['mode']) ? $_GET['mode'] : '';
    if (in_array($name, array(
                'qq',
                'weibo',
                'taobao'
            ))) {
        if (isset($_GET['code']) && !empty($_GET['code'])) { //授权成功
            //$mode = '';
            $P_PRE = '';
            $P_KEY = '';
            $P_TOKEN = '';
            $P = '';
            $result = array();
            switch ($name) {
                case 'weibo' :
                    $P_PRE = XT_WEIBO_PRE;
                    $P_KEY = XT_WEIBO_KEY;
                    $P_TOKEN = XT_WEIBO_TOKEN;
                    $P = XT_WEIBO;
                    $result = xt_platform_weibo_token();
                    break;
                case 'taobao' :
                    $P_PRE = XT_TAOBAO_PRE;
                    $P_KEY = XT_TAOBAO_KEY;
                    $P_TOKEN = XT_TAOBAO_TOKEN;
                    $P = XT_TAOBAO;
                    $result = xt_platform_taobao_token();
                    break;
                case 'qq' :
                    $P_PRE = XT_QQ_PRE;
                    $P_KEY = XT_QQ_KEY;
                    $P_TOKEN = XT_QQ_TOKEN;
                    $P = XT_QQ;
                    $result = xt_platform_qq_token();
                    break;
            }
            if (!empty($result) && isset($result['id'])) {
                if ($mode == 'admin') {
                    $option_platform = get_option(XT_OPTION_PLATFORM);
                    $app = $option_platform[$name];
                    $app['uid'] = $result['id'];
                    $app['name'] = $result['display_name'];
                    $app['token'] = $result['token'];
                    $option_platform[$name] = $app;
                    update_option(XT_OPTION_PLATFORM, $option_platform);
                    xt_platform_redirect($mode, urldecode($state));
                } else {
                    $_oldUsers = get_users(array(
                        'meta_key' => $P_KEY,
                        'meta_value' => $result['id']
                            ));
                    if (is_user_logged_in()) { // 同步绑定
                        $user_id = get_current_user_id();
                        if (!empty($_oldUsers)) {
                            foreach ($_oldUsers as $_old) {
                                if ($_old->ID != $user_id) {
                                    wp_die($result['display_name'] . '已被用户[' . $_old->user_login . ']绑定');
                                }
                            }
                        }
                        update_user_meta($user_id, $P_KEY, $result['id']);
                        update_user_meta($user_id, $P_TOKEN, $result['token']);
                        update_user_meta($user_id, $P, $result['user']);
                        if (!get_user_meta($user_id, XT_USER_GENDER)) {
                            update_user_meta($user_id, XT_USER_GENDER, $result['sex']);
                        }
                        if (!get_user_meta($user_id, XT_USER_AVATAR)) {
                            update_user_meta($user_id, XT_USER_AVATAR, $result['avatar']);
                        }

                        xt_platform_redirect($mode, urldecode($state));
                    } else {
                        if (!empty($_oldUsers)) { //已存在,直接登录
                            $_old = $_oldUsers[0];
                            wp_set_auth_cookie($_old->ID, true, false);
                            wp_set_current_user($_old->ID);
                            do_action('wp_login', $_old->user_login);
                            xt_platform_redirect($mode, urldecode($state));
                        } else {
                            if (!get_option('users_can_register')) {
                                wp_die('站点暂时关闭注册通道!');
                            }
                            //注册
                            $user_pass = wp_generate_password(12, false);
                            $user_login = $P_PRE . '_' . $result['id'];
                            if ($P_PRE == XT_QQ_PRE) {
                                global $wpdb;
                                $maxId = $wpdb->get_var("SELECT ID FROM $wpdb->users ORDER BY ID DESC LIMIT 1");
                                $user_login = $P_PRE . '_' . (20000 + absint($maxId));
                            }
                            if (username_exists($user_login)) {
                                $user_login = $user_login . '_' . rand(1, 10000);
                            }
                            $userdata = array(
                                'user_login' => $user_login,
                                'user_pass' => $user_pass,
                                'user_nicename' => $result['display_name'],
                                'nickname' => $result['display_name'],
                                'first_name' => $result['display_name'],
                                'display_name' => $result['display_name']
                            );
                            $user_id = wp_insert_user($userdata);
                            if (!is_numeric($user_id)) {
                                $errors = $user_id->errors;
                                if ($errors['existing_user_login']) {
                                    wp_die("该用户名 {$user_login} 已被注册。 ");
                                }
                                wp_die(sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
                            }
                            if (!$user_id) {
                                wp_die(sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
                            }
                            update_user_option($user_id, 'default_password_nag', true, true); //Set up the Password change nag.
                            update_user_meta($user_id, $P_KEY, $result['id']);
                            update_user_meta($user_id, $P_TOKEN, $result['token']);
                            update_user_meta($user_id, $P, $result['user']);
                            update_user_meta($user_id, XT_USER_GENDER, $result['sex']);
                            update_user_meta($user_id, XT_USER_AVATAR, $result['avatar']);

                            wp_set_auth_cookie($user_id, true, false);
                            wp_set_current_user($user_id);
                            do_action('wp_login', $user_login);
                            xt_platform_redirect($mode, urldecode($state));
                        }
                    }
                }
            }
            wp_die(sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_option('admin_email')));
        } else {
            switch ($name) {
                case 'weibo' :
                    xt_platform_weibo_authorize($mode, $state);
                    break;
                case 'taobao' :
                    xt_platform_taobao_authorize($mode, $state);
                    break;
                case 'qq' :
                    xt_platform_qq_authorize($mode, $state);
                    break;
            }
        }
    }
}

function xt_platform_redirect($mode, $state) {
    if (!empty($state)) {
        if (!strpos($state, 'http://', 0) === 0)
            $state = 'http://' . $state;
    } else {
        $state = home_url();
    }
    if ($mode == 'popup') {
        exit('<script>window.top.location.href = "' . $state . '"</script>');
    } elseif ($mode == 'admin') {
        exit('<html><head><script type="text/javascript">window.opener && window.opener.' . $state . '();</script></head><body>success!</body></html>');
    } else {
        wp_safe_redirect($state);
    }
    exit();
}

function xt_platform_authorize_redirect($mode, $state) {
    if (!empty($state)) {
        if (!strpos($state, 'http://', 0) === 0)
            $state = 'http://' . $state;
    } else {
        $state = home_url();
    }
    header('Location:' . $state);
    exit();
}

function xt_platform_weibo_authorize($mode, $state) {
    $app = xt_get_app_weibo();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret']) || !$app['isValid']) {
        wp_die('暂不支持新浪微博登录!');
    }
    $params = array();
    $params['client_id'] = $app['appKey'];
    $params['redirect_uri'] = xt_platform_weibo_authorize_url('', $mode);
    $params['response_type'] = 'code';
    $params['state'] = $state;
    $params['display'] = NULL;
    xt_platform_authorize_redirect($mode, XT_WEIBO_AUTHORIZE_URL . "?" . http_build_query($params));
}

function xt_platform_taobao_authorize($mode, $state) {
    $app = xt_get_app_taobao();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret']) || !$app['isValid']) {
        wp_die('暂不支持淘宝账号登录!');
    }
    $params = array();
    $params['client_id'] = $app['appKey'];
    $params['redirect_uri'] = xt_platform_taobao_authorize_url('', $mode);
    $params['response_type'] = 'code';
    $params['scope'] = NULL;
    $params['state'] = $state;
    $params['view'] = NULL;
    xt_platform_authorize_redirect($mode, XT_TAOBAO_AUTHORIZE_URL . "?" . http_build_query($params));
}

function xt_platform_qq_authorize($mode, $state) {
    $app = xt_get_app_qq();
    if (empty($app) || empty($app['appKey']) || empty($app['appSecret']) || !$app['isValid']) {
        wp_die('暂不支持QQ登录!');
    }
    $params = array();
    $params['client_id'] = $app['appKey'];
    $params['redirect_uri'] = xt_platform_qq_authorize_url('', $mode);
    $params['response_type'] = 'code';
    $params['scope'] = NULL;
    $params['state'] = $state;
    $params['display'] = NULL;
    xt_platform_authorize_redirect($mode, XT_QQ_AUTHORIZE_URL . "?" . http_build_query($params));
}

function xt_platform_qq_token() {
    $app = xt_get_app_qq();
    include_once XT_PLUGIN_DIR . ('/xt-core/sdks/taobao/TopClient.php');
    //请求参数
    $postfields = array(
        'grant_type' => 'authorization_code',
        'client_id' => $app['appKey'],
        'client_secret' => $app['appSecret'],
        'code' => $_GET['code'],
        'state' => $_GET['state'],
        'redirect_uri' => xt_platform_qq_authorize_url()
    );

    $client = new TopClient;
    try {
        $response = $client->curl(XT_QQ_TOKEN_URL, $postfields);
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }
    if (strpos($response, "callback") !== false) {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        $msg = json_decode($response);
        if (isset($msg->error)) {
            wp_die($response);
        }
    }

    $token = array();
    parse_str($response, $token);
    $token['expires_in_date'] = date('Y-m-d H:i:s', (current_time('timestamp') + ($token['expires_in'])));
    $access_token = $token['access_token'];

    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $access_token;
    try {
        $response = $client->curl($graph_url);
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }
    if (strpos($response, "callback") !== false) {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
    }
    $response = json_decode($response, true); //openid
    if (isset($response['error'])) {
        wp_die($response['error_description']);
    }
    $openid = $response['openid'];
    $get_user_info = "https://graph.qq.com/user/get_user_info?" . "access_token=" . $access_token . "&oauth_consumer_key=" . $app["appKey"] . "&openid=" . $openid . "&format=json";
    try {
        $response = $client->curl($get_user_info);
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }
    $user = json_decode($response, true);
    if ($user['ret'] > 0) {
        wp_die($user['msg']);
    }
    return array(
        'id' => $openid,
        'display_name' => $user['nickname'],
        'token' => $token,
        'sex' => $user['gender'] == 'm' ? '男' : '女',
        'avatar' => $user['figureurl'],
        'user' => $user
    );
}

function xt_platform_taobao_token() {
    $app = xt_get_app_taobao();
    include_once XT_PLUGIN_DIR . ('/xt-core/sdks/taobao/TopClient.php');
    //请求参数
    $postfields = array(
        'grant_type' => 'authorization_code',
        'client_id' => $app['appKey'],
        'client_secret' => $app['appSecret'],
        'code' => $_GET['code'],
        'redirect_uri' => xt_platform_taobao_authorize_url()
    );

    $client = new TopClient;
    try {
        $token = json_decode($client->curl(XT_TAOBAO_TOKEN_URL, $postfields), true);
        $token['expires_in_date'] = date('Y-m-d H:i:s', (current_time('timestamp') + ($token['expires_in'])));
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }
    $access_token = $token['access_token'];
    include_once XT_PLUGIN_DIR . ('/xt-core/sdks/taobao/RequestCheckUtil.php');
    include_once XT_PLUGIN_DIR . ('/xt-core/sdks/taobao/request/UserBuyerGetRequest.php');
    $client->format = 'json';
    $client->appkey = $app['appKey'];
    $client->secretKey = $app['appSecret'];
    $req = new UserBuyerGetRequest;
    $req->setFields("nick,sex,buyer_credit,avatar,has_shop,vip_info");
    try {
        $resp = (array) $client->execute($req, $access_token);
    } catch (Exception $e) {
        wp_die($e->getMessage());
    }
    if (isset($resp['code'])) {
        wp_die($resp['msg']);
    }
    $user = (array) $resp['user'];
    return array(
        'id' => $user['nick'],
        'display_name' => $user['nick'],
        'token' => $token,
        'sex' => $user['sex'] == 'm' ? '男' : '女',
        'avatar' => $user['avatar'],
        'user' => $user
    );
}

function xt_platform_weibo_token() {
    $weibo = xt_get_app_weibo();
    include_once XT_PLUGIN_DIR . ('/xt-core/sdks/weibo/saetv2.ex.class.php');
    $oAuth = new SaeTOAuthV2($weibo['appKey'], $weibo['appSecret']);
    $keys = array();
    $token = array();
    $keys['code'] = $_GET['code'];
    $keys['redirect_uri'] = xt_platform_weibo_authorize_url();
    try {
        $token = $oAuth->getAccessToken('code', $keys);
        $token['expires_in_date'] = date('Y-m-d H:i:s', (current_time('timestamp') + ($token['expires_in'])));
    } catch (OAuthException $e) {
        wp_die('新浪授权出现错误');
    }
    $client = new SaeTClientV2($weibo['appKey'], $weibo['appSecret'], $token['access_token']);
    $uid_get = $client->get_uid();
    $uid = $uid_get['uid'];
    $user_message = $client->show_user_by_id($uid); //根据ID获取用户等基本信息
    if (isset($user_message['id'])) {
        return array(
            'id' => $user_message['id'],
            'display_name' => $user_message['screen_name'] ? $user_message['screen_name'] : $user_message['name'],
            'token' => $token,
            'sex' => $user_message['gender'] == 'm' ? '男' : '女',
            'avatar' => $user_message['profile_image_url'],
            'user' => $user_message
        );
    } else {
        if ($user_message['error_code'] == '21321') {
            wp_die('站点正在申请新浪微博绑定,请稍候使用新浪微博登陆');
        }
        wp_die($user_message);
    }
}

?>