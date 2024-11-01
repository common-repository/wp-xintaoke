<?php
if (!function_exists('xt_fanxian_html')) {

    function xt_fanxian_html($fanxian = '', $text = '', $style = '') {
        if (!empty($fanxian)) {
            return '<div class="X_Fanxian"' . (!empty($style) ? (' style="' . $style . '"') : '') . '><b>返</b><i></i><strong>' . $fanxian . '&nbsp;' . $text . '</strong></div>';
        }
        return '';
    }

}
if (!function_exists('xt_not_found')) {

    function xt_not_found($msg, $class = '') {
        global $xt, $wp_query, $XT_LANG;
        $s = '';
        if (($xt->is_shares || $xt->is_albums || $xt->is_paipais || $xt->is_users)) {
            $xt_share_param = $wp_query->query_vars['xt_param'];
            if (isset($xt_share_param['s'])) {
                $s = $xt_share_param['s'];
            } elseif (isset($xt_share_param['keyWord'])) {
                $s = $xt_share_param['keyWord'];
            }
        }
        if (!empty($s)) {
            $s = '"' . $s . '"';
        }
        ?>
        <div id="X_Not-Found" class="row-fluid <?php echo $class ?>">
            <div class="span12">
                <div class="well xt-not-found"><?php echo isset($XT_LANG[$msg]) ? sprintf($XT_LANG[$msg], $s) : $msg; ?></div>            
            </div>
        </div>
        <?php
    }

}
if (!function_exists('xt_api_error')) {

    function xt_api_error($error) {
        ?>
        <div id="X_API-Error" style="width:auto;">
            <div class="well xt-api-error">
                发生错误:<?php echo $error->get_error_code() . ' ' . $error->get_error_message(); ?>
            </div>
        </div>
        <?php
    }

}

function xt_header_script() {
    global $xt;
    $_global = get_option(XT_OPTION_GLOBAL);
    $searchtaobaourl = '';
    if (xt_is_s8()) {
        $searchtaobaourl = xt_jump_url(array('title' => 'SEARCH'));
    } else {
        $searchtaobaourl = xt_get_taobao_search_url(array('keyword' => 'SEARCH'));
    }
    echo '<script type="text/javascript">var XT = ' . json_encode(array(
        'isfanxian' => xt_is_fanxian() ? 1 : 0,
        'siteurl' => home_url(),
        'pluginurl' => XT_PLUGIN_URL,
        'ajaxurl' => admin_url('admin-ajax.php'),
        'loginurl' => site_url('wp-login.php'),
        'inviteurl' => xt_site_url('invite-USERID'),
        'authorizeurl' => xt_platform_authorize_url('[PLATFORM]', '[STATE]', '[MODE]'),
        'registerurl' => site_url('wp-login.php?action=register&redirect_to=[REDIRECT]', 'login'),
        'searchshareurl' => xt_get_shares_search_url(array('s' => 'SEARCH')),
        'searchalbumurl' => xt_get_albums_search_url(array('s' => 'SEARCH')),
        'searchuserurl' => '',
        'searchtaobaourl' => $searchtaobaourl,
        'taobaoitemurl' => xt_site_url('taobao-NUMIID'),
        'searchshopurl' => xt_get_shop_search_url(array('keyword' => 'SEARCH')),
        'searchtaobaoitemurl' => xt_jump_url(array('id' => 'SEARCH')),
        'searchpaipaiurl' => xt_get_paipai_search_url(array('keyWord' => 'SEARCH')),
        'searchbijiaurl' => xt_get_bijia_search_url(array('keyword' => 'SEARCH')),
        'searchtuanurl' => xt_get_tuan_search_url(array('keyword' => 'SEARCH')),
        'userId' => get_current_user_id(),
        'token' => wp_create_nonce('token'),
        'option' => $_global,
        'is_taobaopopup' => xt_is_taobaoPopup(),
        'is_shares' => $xt->is_shares,
        'is_albums' => $xt->is_albums,
        'is_users' => $xt->is_users,
        'is_shops' => $xt->is_shops,
        'is_paipais' => $xt->is_paipais,
        'is_bijias' => $xt->is_bijias,
        'is_tuans' => $xt->is_tuans,
        'is_user' => $xt->is_user,
        'is_album' => $xt->is_album,
        'is_account' => $xt->is_account,
        'rate' => xt_get_rate(),
        'jifenbao' => xt_jifenbao_text(),
        'outercode' => xt_outercode(),
        'fanxianhtml' => xt_fanxian_html('{fx}', '{fxtext}')
    )) . ';</script>';
}

function xt_get_header($name = null) {
    $template = 'xt-header.php';
    if (isset($name))
        $template = "xt-header-{$name}.php";
    xt_load_template($template);
}

function xt_get_footer() {
    $template = 'xt-footer.php';
    if (isset($name))
        $template = "xt-footer-{$name}.php";
    xt_load_template($template);
}

function xt_widget_template_grid($args) {
    global $wp_query;

    $default = array(
        'breadcrumbs' => 0,
        'pager_top' => '',
        'pager_bottom' => '',
        'total' => 0,
        'size' => '',
        'count' => '',
        'title' => '',
        'isHd' => true,
        'type' => '',
        'items' => array(),
        'urlType' => 'direct',
        'display' => array(
            'title',
            'price',
            'volume',
            'seller',
            'discount'
        ),
        'params' => array()
    );
    $args = array_merge($default, $args);
    extract($args);
    $rate = xt_get_rate();
    $span = 'span3';
    switch ($size) {
        case 'big' :
            $span = 'span3';
            break;
        case 'normal' :
            $span = 'span2-4';
            break;
        case 'small' :
            $span = 'span2';
            break;
    }
    global $xt_current_widget;
    if ($xt_current_widget == 'systaobaos') {
        $breadcrumbs = 1;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['page_no'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_taobao_search_url(array_merge($params, array('page_no' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_taobao_search_url(array_merge($params, array('page_no' => $page + 1)));
        }
        $base = xt_get_taobao_search_url(array_merge($params, array('page_no' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total);
    } elseif ($xt_current_widget == 'syspaipais') {
        $breadcrumbs = 1;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['pageIndex'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_paipai_search_url(array_merge($params, array('pageIndex' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_paipai_search_url(array_merge($params, array('pageIndex' => $page + 1)));
        }
        $base = xt_get_paipai_search_url(array_merge($params, array('pageIndex' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total);
    } elseif ($xt_current_widget == 'sysbijias') {
        $breadcrumbs = 1;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['page_no'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_bijia_search_url(array_merge($params, array('page_no' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_bijia_search_url(array_merge($params, array('page_no' => $page + 1)));
        }
        $base = xt_get_bijia_search_url(array_merge($params, array('page_no' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total);
    } elseif ($xt_current_widget == 'systuans') {
        $breadcrumbs = 0;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['page_no'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_tuan_search_url(array_merge($params, array('page_no' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_tuan_search_url(array_merge($params, array('page_no' => $page + 1)));
        }
        $base = xt_get_tuan_search_url(array_merge($params, array('page_no' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total);
    } elseif ($xt_current_widget == 'systemais') {
        $breadcrumbs = 1;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['page_no'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_temai_search_url(array_merge($params, array('page_no' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_temai_search_url(array_merge($params, array('page_no' => $page + 1)));
        }
        $base = xt_get_temai_search_url(array_merge($params, array('page_no' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total, 100);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total, 100);
    } elseif ($xt_current_widget == 'syscoupons') {
        $breadcrumbs = 1;
        $params = $wp_query->query_vars['xt_param'];
        $page = $params['page_no'];
        $prev_url = $next_url = '';
        if ($page > 1) {
            $prev_url = xt_get_coupon_search_url(array_merge($params, array('page_no' => $page - 1)));
        }
        if ($page < $total) {
            $next_url = xt_get_coupon_search_url(array_merge($params, array('page_no' => $page + 1)));
        }
        $base = xt_get_coupon_search_url(array_merge($params, array('page_no' => '%#%')));
        $pager_top = xt_search_pager_top($prev_url, $next_url, $page, $count, $total);
        $pager_bottom = xt_search_pager_bottom($base, $page, $count, $total);
    }
    ?>
    <div class="shop-display">
        <div class="hd">
            <?php
            if ($breadcrumbs):
                xt_output_breadcrumbs(true, array('name' => $total . '件宝贝', 'url' => ''), false, $pager_top);
            else:
                if ($xt_current_widget != 'systuans') {
                    ?>
                    <h4 class="xt-bd-l" <?php echo!empty($title) ? '' : 'style="display:none"' ?>><span><?php echo $title ?></span></h4>
                    <?php
                }
            endif;
            if ($xt_current_widget == 'syspaipais') {
                _xt_widget_template_grid_item_paipai_filter($params);
            } elseif ($xt_current_widget == 'sysbijias') {
                _xt_widget_template_grid_item_bijia_filter($params);
            } elseif ($xt_current_widget == 'systuans') {
                _xt_widget_template_grid_item_tuan_filter($params, $pager_top);
            } elseif ($xt_current_widget == 'systemais') {
                _xt_widget_template_grid_item_temai_filter($params, $pager_top);
            }
            ?>
        </div>
        <div class="bd">
            <?php
            if (!empty($items)) {
                echo '<ul class="thumbnails thumbnails-' . $span . ' clearfix">';
                $_count = 0;
                if (empty($count)) {
                    $count = count($items);
                }
                $fanxianText = '元';
                $platform = '';
                $isJifenbao = false;
                if ($type == 'taobao' || $type == 'coupon' || $type == 'temai') {
                    $platform = 'taobao';
                } elseif ($type == 'paipai') {
                    $platform = 'paipai';
                }
                if ($platform) {
                    if (xt_fanxian_is_jifenbao($platform)) {
                        $isJifenbao = true;
                        $fanxianText = xt_jifenbao_text();
                    }
                }

                foreach ($items as $item) {
                    if ($_count >= $count) {
                        break;
                    }
                    switch ($type) {
                        case 'taobao' :
                            xt_widget_template_grid_item_taobao($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType);
                            break;
                        case 'paipai' :
                            xt_widget_template_grid_item_paipai($span, $rate, $item, $display, $isJifenbao, $fanxianText);
                            break;
                        case 'share' :
                            xt_widget_template_grid_item_share($span, $rate, $item, $display);
                            break;
                        case 'coupon' :
                            xt_widget_template_grid_item_coupon($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType);
                            break;
                        case 'temai' :
                            xt_widget_template_grid_item_temai($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType);
                            break;
                        case 'bijia':
                            xt_widget_template_grid_item_bijia($span, $rate, $item, $display, $urlType);
                            break;
                        case 'tuan':
                            xt_widget_template_grid_item_tuan($span, $rate, $item, $display, $urlType);
                            break;
                    }
                    $_count++;
                }
                echo '</ul>';
                if (!empty($pager_bottom)) {
                    echo '<div id="X_Pagination-Bottom" class="clearfix">';
                    echo '<div class="pagination pagination-large xt-pagination-links">';
                    echo $pager_bottom;
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                $msg = 'item_not_found';
                switch ($type) {
                    case 'taobao' :
                        $msg = 'item_taobao_not_found';
                        break;
                    case 'paipai' :
                        $msg = 'item_paipai_not_found';
                        break;
                    case 'share' :
                        $msg = 'share_not_found';
                        break;
                    case 'coupon' :
                        $msg = 'item_coupon_not_found';
                        break;
                    case 'temai' :
                        $msg = 'item_temai_not_found';
                        break;
                    case 'bijia':
                        $msg = 'item_bijia_not_found';
                        break;
                    case 'tuan':
                        $msg = 'item_tuan_not_found';
                        break;
                }
                echo xt_not_found($msg);
            }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>			
    <?php
}

function _xt_widget_template_grid_item_paipai_filter($params = array()) {
    $_params = array_merge(array('keyWord' => '', 'begPrice' => '', 'endPrice' => '', 'orderStyle' => 11), $params);
    $orderStyle = intval($_params['orderStyle']);
    if (!in_array($orderStyle, array(6, 7, 9, 11, 21))) {
        $orderStyle = 11;
    }
    $_url = xt_get_paipai_search_url(array_merge($params, array('keyWord' => 'keyWord', 'begPrice' => 'begPrice', 'endPrice' => 'endPrice', 'orderStyle' => 'orderStyle')));
    ?>
    <div class="xt-filter-box xt-paipai-filter form-inline">
        关键词：<input type="text" id="X_Paipai-KeyWord" class="input-small" placeholder="请输入关键词" value="<?php echo htmlentities($_params['keyWord'], ENT_QUOTES, "UTF-8") ?>">
        价格：<input type="number" id="X_Paipai-BegPrice" class="span1" value="<?php echo $_params['begPrice'] > 0 ? $_params['begPrice'] : '' ?>">
        &nbsp;至&nbsp;<input type="number" id="X_Paipai-EndPrice" class="span1" value="<?php echo $_params['endPrice'] ? $_params['endPrice'] : '' ?>">
        <select id="X_Paipai-OrderStyle">
            <option value="6"<?php selected($orderStyle, 6) ?>>价格从低到高</option>
            <option value="7"<?php selected($orderStyle, 7) ?>>价格从高到低</option>
            <option value="9"<?php selected($orderStyle, 9) ?>>信用从高到低</option>
            <option value="11"<?php selected($orderStyle, 11) ?>>浏览量从高到低</option>
            <option value="21"<?php selected($orderStyle, 21) ?>>收藏量从高到低</option>
        </select>
        <button id="X_Paipai-Filter-Search-Btn" type="button" class="btn btn-small btn-primary" data-url="<?php echo ($_url) ?>" data-loading-text="搜索中...">搜索</button>
    </div>
    <?php
}

function _xt_widget_template_grid_item_bijia_filter($params = array()) {
    $_params = array_merge(array('keyword' => '', 'minprice' => '', 'maxprice' => '', 'orderby' => 1), $params);
    $orderby = intval($_params['orderby']);
    if (!in_array($orderby, array(1, 2, 3))) {
        $orderby = 3;
    }
    $_url = xt_get_bijia_search_url(array_merge($params, array('keyword' => 'keyword', 'minprice' => 'minprice', 'maxprice' => 'maxprice', 'orderby' => 'orderby')));
    ?>
    <div class="xt-filter-box xt-bijia-filter form-inline">
        关键词：<input type="text" id="X_Bijia-KeyWord" class="input-small" placeholder="请输入关键词" value="<?php echo htmlentities($_params['keyword'], ENT_QUOTES, "UTF-8") ?>">
        价格：<input type="number" id="X_Bijia-BegPrice" class="span1" value="<?php echo $_params['minprice'] > 0 ? $_params['minprice'] : '' ?>">
        &nbsp;至&nbsp;<input type="number" id="X_Bijia-EndPrice" class="span1" value="<?php echo $_params['maxprice'] > 0 ? $_params['maxprice'] : '' ?>">
        <select id="X_Bijia-OrderStyle">
            <option value="3"<?php selected($orderby, 3) ?>>按相关程度排序</option>
            <option value="1"<?php selected($orderby, 1) ?>>按价格从低到高</option>
            <option value="2"<?php selected($orderby, 2) ?>>按价格从高到低</option>
        </select>
        <button id="X_Bijia-Filter-Search-Btn" type="button" class="btn btn-small btn-primary" data-url="<?php echo ($_url) ?>" data-loading-text="搜索中...">搜索</button>
    </div>
    <?php
}

function _xt_widget_template_grid_item_tuan_filter($params = array(), $pager_top = '') {
    $_params = array_merge(array('keyword' => '', 'price' => '', 'orderby' => 'desc,bought', 'catid' => ''), $params);
    $h3 = $_params['keyword'];
    $filterSortOrder = $_params['orderby'];
    $filterPrice = $_params['price'];
    $filterCity = $_params['city_id'];
    $firstCategory = $filterCategory = $_params['catid'] > 0 ? $_params['catid'] : '';
    $cities = xt_yiqifa_tuan_city();
    $hotCities = array();
    foreach ($cities as $city) {
        if ($city['is_hot']) {
            $hotCities[] = $city;
        }
    }
    ?>
    <div class="well well-small xt-tuan-websites">
        <ul class="nav nav-pills">
            <?php
            $websites = xt_yiqifa_tuan_website();
            $_site_count = 0;
            foreach ($websites as $site) {
                echo '<li><a rel="nofollow" target="_blank" href="' . $site['url'] . '">' . $site['name'] . '</a></li>';
                $_site_count++;
                if ($_site_count == 19) {
                    echo '<li><a href="#" id="X_Tuan-WebSites-More-Btn">更多</a></li>';
                    echo '</ul><ul class="nav nav-pills xt-tuan-websites-more hide" id="X_Tuan-WebSites-More">';
                }
            }
            ?>
        </ul>
    </div>
    <div class="navbar" style="margin-top:-10px;margin-bottom:0px">
        <div class="navbar-inner">
            <ul class="nav">
                <li class="dropdown">
                    <a href="#" class="brand dropdown-toggle" data-toggle="dropdown"><?php echo isset($cities[$filterCity]) ? $cities[$filterCity]['name_cn'] : '全国' ?> <b class="caret"></b></a>
                    <div class="dropdown-menu">
                        <h5>热门城市</h5>
                        <ul class="inline clearfix">
                            <?php
                            $_city_url = xt_get_tuan_search_url(array('city_id' => 'CITY'));
                            foreach ($hotCities as $city) {
                                echo '<li><a href="' . str_replace('CITY', $city['id'], $_city_url) . '">' . $city['name_cn'] . '</a></li>';
                            }
                            ?>
                        </ul>
                        <h5>团购城市</h5>
                        <div class="xt-letter" id="X_City-Letter">
                            <a href="#" class="label-default">A</a>
                            <a href="#">B</a><a href="#">C</a><a href="#">D</a><a href="#">E</a><a href="#">F</a>
                            <a href="#">G</a><a href="#">H</a><a href="#">J</a><a href="#">K</a><a href="#">L</a>
                            <a href="#">M</a><a href="#">N</a><a href="#">P</a><a href="#">Q</a><a href="#">R</a>
                            <a href="#">S</a><a href="#">T</a><a href="#">W</a><a href="#">X</a><a href="#">Y</a>
                            <a href="#">Z</a>
                        </div>
                        <ul class="inline clearfix" id="X_City-All">
                            <?php
                            foreach ($cities as $city) {
                                echo '<li data-letter="' . $city['head'] . '"><a href="' . str_replace('CITY', $city['id'], $_city_url) . '">' . $city['name_cn'] . '</a></li>';
                            }
                            ?>
                        </ul>
                    </div>
                </li>
                <?php
                $cats = xt_yiqifa_tuan_category();
                $rootCats = array();
                $secondCats = array();
                $isSecond = false;
                //find rootCats And $firstCategory
                foreach ($cats as $cat) {
                    if (empty($cat['parent'])) {
                        $rootCats[] = $cat;
                    } else {
                        if ($filterCategory == $cat['id']) {
                            $firstCategory = $cat['parent'];
                            $isSecond = true;
                        }
                    }
                }
                foreach ($cats as $cat) {
                    if ($firstCategory == $cat['id'] && !empty($cat['cats'])) {
                        $secondCats = $cat['cats'];
                        break;
                    }
                }
                foreach ($rootCats as $cat) {
                    $_url = xt_get_tuan_search_url(array('catid' => $cat['id'], 'city_id' => $filterCity));
                    echo '<li ' . ($firstCategory == $cat['id'] ? 'class="active"' : '') . '><a href="' . $_url . '">' . $cat['name'] . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="row-fluid clearfix" style="margin-bottom:10px;background-color: #f1f1f1;padding:10px;width:auto;">
        <?php if (!empty($secondCats)): ?>
            <dl class="dl-horizontal" style="margin:0px;">
                <dt style="width:58px;">分类：</dt>
                <dd style="margin-left:58px;">
                    <ul class="inline" style="margin:0px;">
                        <li><a rel="nofollow" href="<?php echo xt_get_tuan_search_url(array('catid' => $firstCategory)) ?>" class="<?php echo $isSecond ? 'text-gray' : 'text-default'; ?>">全部</a></li>
                        <?php
                        foreach ($secondCats as $cat) {
                            $_url = xt_get_tuan_search_url(array('catid' => $cat['id'], 'city_id' => $filterCity));
                            echo '<li><a href="' . $_url . '" class="' . ($filterCategory == $cat['id'] ? 'text-default' : 'text-gray') . '">' . $cat['name'] . '</a></li>';
                        }
                        ?>
                    </ul>
                </dd>
            </dl>
        <?php endif; ?>
        <h3 class="pull-left text-default" style="margin:0px;"><?php echo $h3 ?></h3>&nbsp;&nbsp;
        <div class="pull-left" style="padding:8px 0px 3px 20px;">
            <?php if (!empty($_params['keyword'])) { ?>
                排序：
                <div class="btn-group">
                    <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'desc,bought' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => 'desc,bought', 'price' => $_params['price'], 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>">默认</a>
                    <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'asc,curPrice' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => 'asc,curPrice', 'price' => $_params['price'], 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>">最便宜</a>
                    <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'desc,curPrice' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => 'desc,curPrice', 'price' => $_params['price'], 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>">最贵</a>
                    <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'asc,rebate' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => 'asc,rebate', 'price' => $_params['price'], 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>">最优惠</a>
                </div>
                &nbsp;
                &nbsp;
                &nbsp;
            <?php } ?>
            价格：
            <div class="btn-group">
                <a rel="nofollow" class="btn btn-small <?php echo $filterPrice == '' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => $_params['orderby'], 'price' => '', 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>" data-value="">全部</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterPrice == 'low' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => $_params['orderby'], 'price' => 'low', 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>" data-value="low">50元</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterPrice == 'medium' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => $_params['orderby'], 'price' => 'medium', 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>" data-value="medium">100元</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterPrice == 'high' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_tuan_search_url(array('keyword' => $_params['keyword'], 'orderby' => $_params['orderby'], 'price' => 'high', 'catid' => $filterCategory, 'city_id' => $filterCity)); ?>" data-value="high">更高</a>
            </div>
        </div>
        <div class="pull-right"  style="padding:8px 0px 3px 20px;">
            <?php echo $pager_top; ?>            
        </div>
    </div>
    <?php
}

function _xt_widget_template_grid_item_temai_filter_1($params = array(), $pager_top = '') {
    global $xt_temai_itemcat;
    $_params = array_merge(array('sort' => 's', 'cat' => XT_TAOBAO_CAT_TEMAI), $params);
    $filterSortOrder = $_params['sort'];
    $firstCategory = $filterCategory = $_params['cat'] > 0 ? $_params['cat'] : '';
    $secondCategories = array();
    if ($xt_temai_itemcat) {
        $secondCategories = $xt_temai_itemcat['cats'];
    }
    ?>
    <div class="xt-temai-nav">
        <a class="xt-temai-lady" href="<?php echo xt_get_temai_search_url(array('cat' => 50101052)) ?>">时尚女装</a>
        <a class="xt-temai-underwear" href="<?php echo xt_get_temai_search_url(array('cat' => 50101073)) ?>">舒适内衣</a>
        <a class="xt-temai-bag" href="<?php echo xt_get_temai_search_url(array('cat' => 50101030)) ?>">包包配饰</a>
        <a class="xt-temai-shoe" href="<?php echo xt_get_temai_search_url(array('cat' => 50101012)) ?>">男鞋女鞋</a>
        <a class="xt-temai-man" href="<?php echo xt_get_temai_search_url(array('cat' => 50101035)) ?>">品质男装</a>
        <a class="xt-temai-baby" href="<?php echo xt_get_temai_search_url(array('cat' => 50101134)) ?>">母婴儿童</a>
        <a class="xt-temai-home" href="<?php echo xt_get_temai_search_url(array('cat' => 50101115)) ?>">日用百货</a>
        <a class="xt-temai-food" href="<?php echo xt_get_temai_search_url(array('cat' => 50101011)) ?>">美食特产</a>
        <a class="xt-temai-digit" href="<?php echo xt_get_temai_search_url(array('cat' => 50100986)) ?>">数码家电</a>
        <a class="xt-temai-beauty" href="<?php echo xt_get_temai_search_url(array('cat' => 50101089)) ?>">美容护肤</a>
        <a class="xt-temai-outdoor" href="<?php echo xt_get_temai_search_url(array('cat' => 50101115)) ?>">车品户外</a>
        <a class="xt-temai-live" href="<?php echo xt_get_temai_search_url(array('cat' => 50101133)) ?>">吃喝玩乐</a>        
    </div>
    </div>
    </div>
    <div class="row-fluid clearfix" style="margin:0px 10px 0px 10px;background-color: #f1f1f1;padding:10px;width:auto;">
        <?php if (!empty($secondCategories)): ?>        
            <dl class="dl-horizontal" style="margin:0px;">
                <dt style="width:58px;">分类：</dt>
                <dd style="margin-left:58px;">                
                    <ul class="inline" style="margin:0px;">
                        <?php
                        foreach ($secondCategories as $cat) {
                            $_url = xt_get_temai_search_url(array('cat' => $cat['cid']));
                            echo '<li><a href="' . $_url . '" class="' . ($filterCategory == $cat['cid'] ? 'text-default' : 'text-gray') . '">' . $cat['name'] . '</a></li>';
                        }
                        ?>
                    </ul>
                </dd>
            </dl>
        <?php endif; ?>
        <div class="pull-left" style="padding:8px 0px 3px 20px;">
            排序：
            <div class="btn-group">
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 's' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 's', 'cat' => $filterCategory)); ?>">默认</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'p' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'p', 'cat' => $filterCategory)); ?>">最便宜</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'pd' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'pd', 'cat' => $filterCategory)); ?>">最贵</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'd' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'd', 'cat' => $filterCategory)); ?>">月销量最高</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'pt' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'pt', 'cat' => $filterCategory)); ?>">最新发布</a>                
            </div>
        </div>
        <div class="pull-right"  style="padding:8px 0px 3px 20px;">
            <?php echo $pager_top; ?>            
        </div>
    </div>
    <?php
}

function _xt_widget_template_grid_item_temai_filter($params = array(), $pager_top = '') {
    global $xt_temai_itemcat;
    $_params = array_merge(array('sort' => 's', 'cat' => XT_TAOBAO_CAT_TEMAI), $params);
    $filterSortOrder = $_params['sort'];
    $firstCategory = $filterCategory = $_params['cat'] > 0 ? $_params['cat'] : '';
    $secondCategories = array();
    $thirdCategories = array();
    if ($xt_temai_itemcat) {
        $parentCid = $xt_temai_itemcat['parent_cid'];
        $parent = $second = false;
        if (empty($parentCid)) {
            $secondCategories = $thirdCategories = array();
        } else {
            if ($parentCid != XT_TAOBAO_CAT_TEMAI) {
                $parent = $second = xt_temai_item_cat($parentCid, true);
                if ($parent['parent_cid'] != XT_TAOBAO_CAT_TEMAI) {
                    $parent = xt_temai_item_cat($parent['parent_cid'], true);
                }
                $firstCategory = $parent['cid'];
            }
            $secondCategories = $xt_temai_itemcat['cats'];
            if (!$secondCategories) {
                if ($second) {
                    $secondCategories = $second['cats'];
                }
            }
        }
    }
    ?>
    <div class="navbar" style="margin:0px 10px 0px 10px;">
        <div class="navbar-inner">
            <ul class="nav">
                <li <?php echo $firstCategory == 50101034 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50101034)) ?>">服饰</a></li>
                <li <?php echo $firstCategory == 50101011 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50101011)) ?>">鞋包</a></li>
                <li <?php echo $firstCategory == 50100986 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50100986)) ?>">时尚</a></li>
                <li <?php echo $firstCategory == 50101089 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50101089)) ?>">运动</a></li>
                <li <?php echo $firstCategory == 50101115 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50101115)) ?>">居家</a></li>
                <li <?php echo $firstCategory == 50101133 ? 'class="active"' : '' ?>><a href="<?php echo xt_get_temai_search_url(array('cat' => 50101133)) ?>">其他</a></li>                
            </ul>
        </div>
    </div>
    <div class="row-fluid clearfix" style="margin:0px 10px 0px 10px;background-color: #f1f1f1;padding:10px;width:auto;">
        <?php if (!empty($secondCategories)): ?>        
            <dl class="dl-horizontal" style="margin:0px;">
                <dt style="width:58px;">分类：</dt>
                <dd style="margin-left:58px;">                
                    <ul class="inline" style="margin:0px;">
                        <?php
                        foreach ($secondCategories as $cat) {
                            $_url = xt_get_temai_search_url(array('cat' => $cat['cid']));
                            echo '<li><a href="' . $_url . '" class="' . ($filterCategory == $cat['cid'] ? 'text-default' : 'text-gray') . '">' . $cat['name'] . '</a></li>';
                        }
                        ?>
                    </ul>
                </dd>
            </dl>
        <?php endif; ?>
        <div class="pull-left" style="padding:8px 0px 3px 20px;">
            排序：
            <div class="btn-group">
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 's' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 's', 'cat' => $filterCategory)); ?>">默认</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'p' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'p', 'cat' => $filterCategory)); ?>">最便宜</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'pd' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'pd', 'cat' => $filterCategory)); ?>">最贵</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'd' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'd', 'cat' => $filterCategory)); ?>">月销量最高</a>
                <a rel="nofollow" class="btn btn-small <?php echo $filterSortOrder == 'pt' ? 'btn-primary' : '' ?>" href="<?php echo xt_get_temai_search_url(array('sort' => 'pt', 'cat' => $filterCategory)); ?>">最新发布</a>                
            </div>
        </div>
        <div class="pull-right"  style="padding:8px 0px 3px 20px;">
            <?php echo $pager_top; ?>            
        </div>
    </div>
    <?php
}

function xt_widget_template_grid_item_share($span, $rate, $item, $display) {
    $_url = get_the_share_url($item->id);
    $_title = esc_html($item->title);
    $_pic = get_the_share_picurl(200, $item);
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"> <?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a target="_blank" href="<?php echo $_url ?>" class="text-gray"><?php echo $_title ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left">
                        <span>￥ </span><strong><?php echo $item->price ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display)): ?>
                    <div class="span6 pull-left">
                        <a href="<?php xt_the_user_url($item->user_id) ?>" target="_blank"><?php echo $item->user_name ?></a>
                    </div>
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right muted">
                        <small>喜欢<em><?php echo $item->fav_count ?></em></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </li>	
    <?php
}

function xt_widget_template_grid_item_paipai($span, $rate, $item, $display, $isJifenbao, $fanxianText) {
    $_title = esc_html($item->title);
    $_pic = (str_replace('.300x300.jpg', '.200x200.jpg', $item->bigUri));
    $_price = round($item->price / 100, 2);
    $fx = round(($rate / 100) * round($item->crValue / 100, 2), 2);
    $_url = xt_jump_url(array(
        'type' => 'paipai',
        'id' => $item->commId,
        'title' => $item->title,
        'url' => ($item->tagUrl),
        'fx' => $fx
            ));
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" rel="nofollow" target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"><?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a rel="nofollow" target="_blank" href="<?php echo $_url ?>" class="text-gray"><?php echo $item->title ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left">
                        <span>￥ </span><strong><?php echo $_price ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display)): ?>
                    <div class="span6 pull-left">
                        <span class="text-default"><?php echo $item->nickName ?></span>
                    </div>
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right muted">
                        <small>已售出<em><?php echo $item->saleNum ?></em></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
    </li>
    <?php
}

function xt_widget_template_grid_item_taobao($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType = 'jump') {
    $_title = esc_html(str_replace(array(
                '<span class=H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = ($item->pic_url . '_220x220.jpg');
    $fx = round(($rate / 100) * $item->commission, 2);
    $_url = '';
    $_rel = 'rel="nofollow"';
    $_data = '';
    if ($urlType == 'direct') {
        $_data = 'data-itemid="' . $item->num_iid . '"';
        $_url = "javascript:;";
    } elseif ($urlType == 'detail') {
        $_url = xt_taobao_detail($item->num_iid);
        $_rel = '';
    } else {
        $_url = xt_jump_url(array(
            'id' => $item->num_iid,
            'title' => $_title,
            'url' => ($item->click_url),
            'fx' => $fx
                ));
    }
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" <?php echo $_rel; ?> <?php echo $_data; ?> target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"><?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a <?php echo $_rel; ?> target="_blank" href="<?php echo $_url ?>" class="text-gray"><?php echo $item->title ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left">
                        <span>￥ </span><strong><?php echo $item->price ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display)): ?>
                    <div class="span6 pull-left">
                        <a rel="nofollow" href="<?php
            echo xt_jump_url(array('url' => $item->shop_click_url, 'title' => $item->nick));
                    ?>" target="_blank"><?php echo $item->nick ?></a>
                    </div>
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right muted">
                        <small>最近成交<em><?php echo $item->volume ?></em></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
    </li>
    <?php
}

function xt_widget_template_grid_item_coupon($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType = 'jump') {
    $_title = esc_html(str_replace(array(
                '<span class = H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = ($item->pic_url . '_220x220.jpg');
    $fx = round(($rate / 100) * ($item->coupon_price * $item->commission_rate / 10000), 2);
    $_url = '';
    $_data = '';
    $_rel = 'rel="nofollow"';
    if ($urlType == 'direct') {
        $_data = 'data-itemid="' . $item->num_iid . '"';
        $_url = "javascript:;";
    } elseif ($urlType == 'detail') {
        $_url = xt_taobao_detail($item->num_iid);
        $_rel = '';
    } else {
        $_url = xt_jump_url(array(
            'type' => 'coupon',
            'id' => $item->num_iid,
            'title' => $_title,
            'url' => ($item->click_url),
            'fx' => $fx
                ));
    }
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" <?php echo $_rel; ?> <?php echo $_data; ?>  target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"><?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a <?php echo $_rel; ?> target="_blank" href="<?php echo $_url ?>" class="text-gray"><?php echo $item->title ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left muted">
                        原价:<em><?php echo $item->price ?></em>
                    </div>
                    <div class="span6 pull-right">
                        折扣价:<span>￥</span><strong><?php echo $item->coupon_price ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display)): ?>
                    <div class="span6 pull-left">
                        <a rel="nofollow" href="<?php
            echo xt_jump_url(array('url' => $item->shop_click_url, 'title' => $item->nick));
            ;
                    ?>" target="_blank"><?php echo $item->nick ?></a>
                    </div>	
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right muted">
                        <small>最近成交<em><?php echo $item->volume ?></em></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
    </li>
    <?php
}

function xt_widget_template_grid_item_temai($span, $rate, $item, $display, $isJifenbao, $fanxianText, $urlType = 'jump') {
    $_title = esc_html(str_replace(array(
                '<span class = H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = (str_replace('_b.jpg', '', $item->pic_url) . '_220x220.jpg');
    $fx = round(($rate / 100) * ($item->commission_rate / 100 * $item->promotion_price), 2);
    $_url = '';
    $_rel = 'rel="nofollow"';
    $_data = '';
    $num_iids = explode('_track_', $item->track_iid);
    if ($urlType == 'direct') {
        $_data = 'data-itemid="' . $num_iids[0] . '"';
        $_url = "javascript:;";
    } elseif ($urlType == 'detail') {
        $_url = xt_taobao_detail($num_iids[0]);
        $_rel = '';
    } else {
        $_url = xt_jump_url(array(
            'type' => 'temai',
            'id' => $item->track_iid,
            'title' => $_title,
            'url' => ($item->detail_url),
            'fx' => $fx
                ));
    }
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" <?php echo $_rel; ?> <?php echo $_data; ?>  target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"><?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a <?php echo $_rel; ?> target="_blank" href="<?php echo $_url ?>" class="text-gray"><?php echo $item->title ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left muted">
                        原价:<em><?php echo $item->price ?></em>
                    </div>
                    <div class="span6 pull-right">
                        特价:<span>￥</span><strong><?php echo $item->promotion_price ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display)): ?>
                    <div class="span6 pull-left">
                        <span class="text-default"><?php echo $item->brand_name ?></span>
                    </div>	
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right muted">
                        <small>最近成交<em><?php echo $item->volume ?></em></small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
    </li>
    <?php
}

function xt_widget_template_grid_item_bijia($span, $rate, $item, $display, $urlType = 'direct') {
    $_title = esc_html($item['p_name']);
    $_pic = $item['pic_url'];
    parse_str($item['p_o_url'], $params);
    $_url = isset($params['t']) ? urldecode($params['t']) : $item['p_o_url'];
    if ($urlType == 'jump') {
        $_url = xt_jump_url(array('type' => 'tuan', 'url' => $_url, 'title' => $item['web_name']));
    }
    $_topUrl = '';
    if (!empty($item['web_name']) && !empty($item['web_id'])) {
        $_topUrl = xt_jump_url(array(
            'type' => 'mall',
            'id' => $item['web_id'],
            'title' => $item['web_name']
                ));
    }
    ?>
    <li class="<?php echo $span; ?>">
        <a class="thumbnail" rel="nofollow" target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>"><?php xt_write_pic(base64_encode($_pic), $_title) ?></a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a rel="nofollow" target="_blank" href="<?php echo $_url; ?>" class="text-gray"><?php echo $item['p_name'] ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left">
                        <span>￥ </span><strong><?php echo $item['cur_price']; ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <?php if (in_array('seller', $display) && !empty($_topUrl)): ?>
                    <div class="span6 pull-right">
                        <a rel="nofollow" href="<?php echo $_topUrl ?>" target="_blank"><?php echo $item['web_name'] ?></a>
                    </div>	
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_item_tuan($span, $rate, $item, $display, $urlType = 'direct') {
    $_title = esc_html($item['title']);
    $_pic = $item['pic_url'];
    parse_str($item['pdt_o_url'], $params);
    $_url = isset($params['t']) ? urldecode($params['t']) : $item['pdt_o_url'];
    if ($urlType == 'jump') {
        $_url = xt_jump_url(array('type' => 'tuan', 'url' => $_url, 'title' => $item['city_name']));
    }
    ?>
    <li class="<?php echo $span; ?>">
        <a  class="thumbnail" rel="nofollow" target="_blank" href="<?php echo $_url ?>" title="<?php echo $_title ?>">
            <?php xt_write_pic(base64_encode($_pic), $_title) ?>
        </a>
        <div class="caption">
            <?php if (in_array('title', $display)): ?>
                <div class="desc">
                    <a rel="nofollow" target="_blank" href="<?php echo $_url; ?>" class="text-gray"><?php echo wp_trim_words($item['title'], 50) ?></a>
                </div>
            <?php endif; ?>
            <div class="price clearfix row-fluid">
                <?php if (in_array('price', $display)): ?>
                    <div class="span6 pull-left">
                        <span>￥</span><strong><?php echo $item['cur_price'] ?></strong>
                        <?php if (!empty($item['discount'])): ?>&nbsp;&nbsp;(<span class="text-default"><?php echo $item['discount'] ?>折</span>)<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if (in_array('volume', $display)): ?>
                    <div class="span6 pull-right">
                        <?php echo $item['bought'] ?>人购买
                    </div>
                <?php endif; ?>
            </div>
            <div class="seller clearfix row-fluid">
                <div class="xt-tuan-time muted" data-time="<?php echo (strtotime($item['end_time']) - current_time('timestamp')) ?>"></div>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall($args) {
    extract($args);
    ?>
    <div class="hd">
        <h4 class="xt-bd-l" <?php echo!empty($title) ? '' : 'style="display:none"' ?>><span><?php echo $title ?></span></h4>
    </div>
    <div class="bd">
        <ul class="media-list">
            <?php
            if (!empty($items)) {
                $rate = xt_get_rate();
                $_count = 0;
                if (empty($count)) {
                    $count = count($items);
                }
                $fanxianText = '元';
                $platform = '';
                $isJifenbao = false;
                if ($type == 'taobao' || $type == 'coupon' || $type == 'temai') {
                    $platform = 'taobao';
                } elseif ($type == 'paipai') {
                    $platform = 'paipai';
                }
                if ($platform) {
                    if (xt_fanxian_is_jifenbao($platform)) {
                        $isJifenbao = true;
                        $fanxianText = xt_jifenbao_text();
                    }
                }

                foreach ($items as $item) {
                    if ($_count >= $count) {
                        break;
                    }
                    switch ($type) {
                        case 'taobao' :
                            xt_widget_template_grid_sidesmall_item_taobao($_count, $rate, $item, $isJifenbao, $fanxianText);
                            break;
                        case 'paipai' :
                            xt_widget_template_grid_sidesmall_item_paipai($_count, $rate, $item, $isJifenbao, $fanxianText);
                            break;
                        case 'share' :
                            xt_widget_template_grid_sidesmall_item_share($_count, $rate, $item);
                            break;
                        case 'coupon' :
                            xt_widget_template_grid_sidesmall_item_coupon($_count, $rate, $item, $isJifenbao, $fanxianText);
                            break;
                        case 'temai' :
                            xt_widget_template_grid_sidesmall_item_temai($_count, $rate, $item, $isJifenbao, $fanxianText);
                            break;
                        case 'bijia':
                            xt_widget_template_grid_sidesmall_item_bijia($_count, $rate, $item);
                            break;
                        case 'tuan':
                            xt_widget_template_grid_sidesmall_item_tuan($_count, $rate, $item);
                            break;
                    }
                    $_count++;
                }
                ?>

                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}

function xt_widget_template_grid_sidesmall_item_taobao($_count, $rate, $item, $isJifenbao, $fanxianText) {
    $_title = esc_html(str_replace(array(
                '<span class=H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = ($item->pic_url . '_100x100.jpg');
    $fx = round(($rate / 100) * $item->commission, 2);
    $_url = xt_jump_url(array(
        'id' => $item->num_iid,
        'title' => $_title,
        'url' => ($item->click_url),
        'fx' => $fx
            ));
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>销量：<?php echo $item->volume ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item->price ?></strong></p>
                <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_paipai($_count, $rate, $item, $isJifenbao, $fanxianText) {
    $_title = esc_html($item->title);
    $_pic = (str_replace('.300x300.jpg', '.80x80.jpg', $item->bigUri));
    $_price = round($item->price / 100, 2);
    $fx = round(($rate / 100) * round($item->crValue / 100, 2), 2);
    $_url = xt_jump_url(array(
        'type' => 'paipai',
        'id' => $item->commId,
        'title' => $item->title,
        'url' => ($item->tagUrl),
        'fx' => $fx
            ));
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>销量：<?php echo $item->saleNum ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $_price ?></strong></p>
                <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_share($_count, $rate, $item) {
    $_url = get_the_share_url($item->id);
    $_title = esc_html($item->title);
    $_pic = get_the_share_picurl(80, $item);
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>喜欢：<?php echo $item->fav_count ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item->price ?></strong></p>
                <p><span>来自：</span><a class="X_Nick" data-value="<?php echo $item->user_id; ?>" href="<?php xt_the_user_url($item->user_id) ?>" target="_blank"><?php echo $item->user_name ?></a></p>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_coupon($_count, $rate, $item, $isJifenbao, $fanxianText) {
    $_title = esc_html(str_replace(array(
                '<span class = H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = ($item->pic_url . '_100x100.jpg');
    $fx = round(($rate / 100) * ($item->coupon_price * $item->commission_rate / 10000), 2);
    $_url = xt_jump_url(array(
        'type' => 'coupon',
        'id' => $item->num_iid,
        'title' => $_title,
        'url' => ($item->click_url),
        'fx' => $fx
            ));
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>销量：<?php echo $item->volume ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item->coupon_price ?></strong></p>
                <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_temai($_count, $rate, $item, $isJifenbao, $fanxianText) {
    $_title = esc_html(str_replace(array(
                '<span class = H>',
                '</span>'
                    ), array(
                '',
                ''
                    ), $item->title));
    $_pic = (str_replace('_b.jpg', '', $item->pic_url) . '_100x100.jpg');
    $fx = round(($rate / 100) * ($item->commission_rate / 100 * $item->promotion_price), 2);
    $_url = xt_jump_url(array(
        'type' => 'temai',
        'id' => $item->track_iid,
        'title' => $_title,
        'url' => ($item->detail_url),
        'fx' => $fx
            ));
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>销量：<?php echo $item->volume ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item->promotion_price ?></strong></p>
                <?php echo xt_fanxian_html($isJifenbao ? $fx * 100 : $fx, $fanxianText) ?>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_bijia($_count, $rate, $item) {
    $_title = esc_html($item['p_name']);
    $_pic = $item['pic_url'];
    parse_str($item['p_o_url'], $params);
    $_url = isset($params['t']) ? urldecode($params['t']) : $item['p_o_url'];
    $_topUrl = '';
    if (!empty($item['web_name']) && !empty($item['web_id'])) {
        $_topUrl = xt_jump_url(array(
            'type' => 'mall',
            'id' => $item['web_id'],
            'title' => $item['web_name']
                ));
    }
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item['cur_price'] ?></strong></p>
                <p><span>来自：</span><a rel="nofollow" href="<?php echo $_topUrl ?>" target="_blank"><?php echo $item['web_name'] ?></a></p>
            </div>
        </div>
    </li>
    <?php
}

function xt_widget_template_grid_sidesmall_item_tuan($_count, $rate, $item) {
    $_title = esc_html($item['title']);
    $_pic = $item['pic_url'];
    parse_str($item['pdt_o_url'], $params);
    $_url = isset($params['t']) ? urldecode($params['t']) : $item['pdt_o_url'];
    ?>
    <li class="media"<?php echo ($_count == 0 ? ' style="padding-top:0px;"' : ''); ?>>
        <a rel="nofollow" class="pull-left" href="<?php echo $_url ?>" target="_blank">
            <?php xt_write_pic(base64_encode($_pic), $_title, 0, 0, 'media-object') ?><span><i>销量：<?php echo $item['bought'] ?></i></span>
        </a>
        <div class="media-body">
            <h4 class="media-heading"><a rel="nofollow" class="text-gray" href="<?php echo $_url ?>" target="_blank"><?php echo $_title ?></a></h4>
            <div class="media">
                <p><span>￥</span><strong><?php echo $item['cur_price'] ?></strong></p>
            </div>
        </div>
    </li>
    <?php
}

function xt_select_catalogs($id, $name, $class, $selected = '', $isBlank = true, $isChild = false) {
    $roots = xt_catalogs_share();
    echo '<select id = "' . $id . '" name = "' . $name . '" class = "' . $class . '">';
    if ($isBlank) {
        echo '<option value = "0">无</option>';
    }
    foreach ($roots as $cat) {
        echo '<option value = "' . $cat->id . '" data-issub = "0" data-isfront = "' . $cat->is_front . '" ' . selected($cat->id, $selected, false) . '>' . $cat->title . '</option>';
        if ($isChild) {
            if (isset($cat->child) && !empty($cat->child) && isset($cat->child['catalogs'])) {
                $childs = $cat->child['catalogs'];
                foreach ($childs as $sub) {
                    echo '<option value = "' . $sub->id . '" data-issub = "1" ' . selected($sub->id, $selected, false) . '>&nbsp;
                        &nbsp;
                        &nbsp;
                        ' . $sub->title . '</option>';
                }
            }
        }
    }
    echo '</select>';
}

function xt_row_page($page, $count) {
    ?>
    <tr id="page-<?php echo $page->ID; ?>" <?php echo $count % 2 == 1 ? 'class = "alternate"' : '' ?>>
        <td><span><?php echo $page->post_title; ?></span></td>
        <td><span>自定义</span></td>
        <td><span><a href="<?php echo get_permalink($page->ID); ?>" target="_blank"><?php echo get_permalink($page->ID); ?></a></span></td>
        <td><span><a href="<?php echo add_query_arg(array('xt-action' => 'page', 'xt-page' => $page->ID)) ?>">设计</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo admin_url('/post.php ? post = ' . $page->ID . '&action = edit'); ?>">编辑</a></span></td>
    </tr>
    <?php
}

function xt_row_role($key, $role, $count) {
    global $wp_roles;
    $_title = isset($wp_roles->role_names[$role->name]) ? translate_user_role($wp_roles->role_names[$role->name]) : '未知';
    $_rate = xt_get_role_rate(array(
        $key
            ));
    $_adrate = xt_get_role_adrate(array(
        $key
            ));
    $_sharerate = xt_get_role_sharerate(array(
        $key
            ));
    $_ismulti = xt_is_role_multicashback(array(
        $key
            ));
    $_isSys = in_array($key, xt_roles());
    ?>
    <tr id="role-<?php echo $key; ?>" <?php echo $count % 2 == 0 ? 'class = "alternate"' : '' ?>>
        <td><span><?php echo $_isSys ? '内置' : '自定义'; ?></span></td>
        <td><span><?php echo $key; ?></span></td>
        <td>
            <span><?php echo $_title; ?></span>
            <?php if (!$_isSys) { ?>
                <br>
                <div class="row-actions">
                        <!--<span class="edit"><a href="">编辑</a> | </span>-->
                    <span class="inline hide-if-no-js"><a href="#" class="editinline">快速编辑</a> | </span>
                    <span class="delete"><a class="delete-role" href="javascript:;" data-value="<?php echo $key; ?>">删除</a></span>
                </div>
                <div class="hidden" id="inline_<?php echo $key; ?>">
                    <div class="title"><?php echo $_title; ?></div>
                    <div class="rate"><?php echo $_rate; ?></div>
                    <div class="sharerate"><?php echo $_sharerate; ?></div>
                    <div class="adrate"><?php echo $_adrate; ?></div>
                </div>
            <?php } ?>
        </td>
        <td><?php echo $_rate; ?>%</td>
        <td><?php echo $_sharerate ?>%</td>
        <td><?php echo $_adrate ?>%</td>
    </tr>
    <?php
}

function xt_search_form($form) {
    global $xt;
    if ($xt->is_xintao) {
        $style = '<style type = "text/css">.xt-searchtype {
                        text-align :left;
                                background-color: white;
                                border: 1px solid #CCC;
                                cursor: pointer;
                                overflow: hidden;
                                position: absolute;
                                left: -1px;
                                top:31px;
                                padding: 1px;
                                width: 100%;
                                list-style: none;
                                z-index: 450;
                                display:none;
                                }
                                .xt-searchtype-bg {
                                background: #F2F0F1;
                                }
                                .xt-searchtype-bg li {
                                padding: 5px 10px;
                                }
                                .xt-searchinput-words {
                                color: #F69;
                                }</style>';
        return $style . $form . '<ul class = "xt-searchtype" data-value = "share"> <li class = "xt-searchtype-bg" data-type = "share">搜<samp>"</samp><span class="xt-searchinput-words"></span><samp>"</samp>相关宝贝</li> <li data-type = "user">搜<samp>"</samp><span class="xt-searchinput-words"></span><samp>"</samp>相关用户</li> <li data-type = "album">搜<samp>"</samp><span class="xt-searchinput-words"></span><samp>"</samp>相关专辑</li> </ul>';
    }
    return $form;
}

/**
 * Display userinfo.
 *
 * @param boolean $echo Default to echo and not return the userinfo.
 */
function xt_get_user_info() {
    xt_load_template('xt-widget_userinfo.php');
}

function xt_the_taobao_go($num_iid) {
    echo xt_get_the_taobao_go($num_iid);
}

function xt_get_the_taobao_go($num_iid) {
    return apply_filters('xt_the_taobao_go', 'javascript:;
                                ');
}

function xt_the_user_id() {
    echo xt_get_the_user_id();
}

function xt_get_the_user_id() {
    global $xt_user;
    return (int) $xt_user->ID;
}

function xt_the_user_url($user_id = 0) {
    echo xt_get_the_user_url($user_id);
}

function xt_get_the_user_url($user_id = 0) {
    if (empty($user_id)) {
        global $xt_user;
        $user_id = $xt_user->ID;
    }
    $_url = 'javascript:;
                                ';
    if ($user_id) {
        $_url = xt_site_url('uid-' . $user_id);
    }

    return apply_filters('xt_the_user_url', $_url);
}

function xt_the_user_pic($user_pic = '', $user_id = 0, $size = 50) {
    echo xt_get_the_user_pic($user_pic, $user_id, $size);
}

function xt_get_the_user_pic($user_pic = '', $user_id = 0, $size = 50) {
    if (empty($user_pic)) {
        if ($user_id != 0) {
            $user_pic = get_user_meta($user_id, XT_USER_AVATAR);
            if (!empty($user_pic) && is_array($user_pic)) {
                $user_pic = $user_pic[0];
            }
        } else {
            global $xt_user;
            if (!empty($xt_user))
                $user_pic = isset($xt_user->avatar) ? $xt_user->avatar : get_user_meta($xt_user->ID, XT_USER_AVATAR, true);
        }
    }
    if (empty($user_pic)) {
        $user_pic = XT_USER_AVATAR_DEFAULT;
    }
    if (!empty($user_pic)) {
        $rs = preg_match("/^(http:\/\/|https:\/\/)/", $user_pic, $match);
        if (intval($rs) == 0) {
            $user_pic = get_home_url() . $user_pic;
        }
    }
    return apply_filters('xt_the_user_pic', $user_pic, $size);
}

function xt_the_user_title($user_nick = '') {
    echo xt_get_the_user_title($user_nick);
}

function xt_get_the_user_title($user_nick = '') {
    if (empty($user_nick)) {
        global $xt_user;
        $user_nick = $xt_user->display_name;
    }
    return apply_filters('xt_the_user_title', $user_nick);
}

function xt_the_user_description() {
    echo xt_get_the_user_description();
}

function xt_get_the_user_description() {
    global $xt_user;
    $description = '';
    if (is_a($xt_user, 'WP_User')) {
        $description = $xt_user->description;
        if (empty($description)) {
            $description = xt_user_default_description();
        }
    }
    return apply_filters('xt_the_user_description', $description);
}

function xt_the_user_followcount() {
    echo xt_get_the_user_followcount();
}

function xt_get_the_user_followcount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_followcount', $xt_user_counts[XT_USER_COUNT_FOLLOW]);
}

function xt_the_user_fanscount() {
    echo xt_get_the_user_fanscount();
}

function xt_get_the_user_fanscount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_fanscount', $xt_user_counts[XT_USER_COUNT_FANS]);
}

function xt_the_user_fav_sharecount() {
    echo xt_get_the_user_fav_sharecount();
}

function xt_get_the_user_fav_sharecount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_fav_sharecount', $xt_user_counts[XT_USER_COUNT_FAV_SHARE]);
}

function xt_the_user_fav_albumcount() {
    echo xt_get_the_user_fav_albumcount();
}

function xt_get_the_user_fav_albumcount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_fav_albumcount', $xt_user_counts[XT_USER_COUNT_FAV_ALBUM]);
}

function xt_the_user_sharecount() {
    echo xt_get_the_user_sharecount();
}

function xt_get_the_user_sharecount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_sharecount', $xt_user_counts[XT_USER_COUNT_SHARE]);
}

function xt_the_user_albumcount() {
    echo xt_get_the_user_albumcount();
}

function xt_get_the_user_albumcount() {
    global $xt_user_counts;
    return apply_filters('xt_the_user_albumcount', $xt_user_counts[XT_USER_COUNT_ALBUM]);
}

function xt_search_pager_top($prev_url, $next_url, $page, $pagesize, $total = 0, $maxpage = 10) {
    $totalPage = ceil($total / intval($pagesize));
    $totalPage = $totalPage > $maxpage ? $maxpage : $totalPage;
    $pager = $prev = $next = $current = '';
    if ($total > 0 && $page > 0) {
        $current = '<li class="disabled"><span>' . $page . '/' . ($totalPage) . '</span></li>';
        if ($page <= 1) {
            $prev_url = '';
        }
        if ($page >= $totalPage) {
            $next_url = '';
        }
        if (!empty($prev_url)) {
            $prev = '<li><a href="' . $prev_url . '" rel="nofollow"><i class="icon-chevron-left"></i></a></li>';
        } else {
            $prev = '<li class="active"><a href="javascript:;"><i class="icon-chevron-left"></i></a></li>';
        }
        if (!empty($next_url)) {
            $next = '<li><a href="' . $next_url . '" rel="nofollow"><i class="icon-chevron-right"></i></a></li>';
        } else {
            $next = '<li class="active"><a href="javascript:;"><i class="icon-chevron-right"></i></a></li>';
        }
        $container = 'li';
        global $xt_current_widget;
        if ($xt_current_widget == 'systuans' || $xt_current_widget == 'systemais') {
            $container = 'div';
        }
        $pager = '<' . $container . ' class="pull-right xt-pagination-top"><div class="pagination pagination-mini"><ul>' . $current . $prev . $next . '</ul></div></' . $container . '>';
    }

    return $pager;
}

function xt_search_pager_bottom($base, $page, $pagesize, $total = 0, $maxpage = 10) {
    $totalPage = ceil($total / intval($pagesize));
    $totalPage = $totalPage > $maxpage ? $maxpage : $totalPage;
    return paginate_links(array(
                'base' => $base,
                'format' => '',
                'end_size' => 3,
                'total' => $totalPage,
                'current' => $page,
                'prev_text' => '上一页',
                'next_text' => '下一页',
                'mid_size' => 1,
                'type' => 'list'
            ));
}