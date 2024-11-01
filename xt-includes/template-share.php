<?php

function xt_row_share($share, $count) {
    $terms = xt_get_catalog_terms_cache($share->id, 'share');
    $as = array();
    $cids = array();
    if (!empty($terms)) {
        $as = array();
        foreach ($terms as $term) {
            $_url = add_query_arg(array('cid' => $term->id, 'paged' => 1, 's' => ''), $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $as[] = '<a href="http://' . $_url . '">' . $term->title . '</a>';
            $cids[] = $term->id;
        }
    }
    ?>
    <tr id="share-<?php echo $share->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td scope="row"><span><?php echo $share->id; ?></span></td>
        <td class="name column-name"><strong><a class="row-title" href="<?php the_share_url($share->id); ?>" target="_blank"><?php echo ($share->title); ?></a></strong><br>
            <div class="row-actions">
                    <!--<span class="edit"><a href="">编辑</a> | </span>-->
                <span class="inline hide-if-no-js"><a href="#" class="editinline">编辑</a> | </span>
                <span class="delete"><a class="delete-share" href="javascript:;" data-value="<?php echo $share->id; ?>">删除</a></span>
            </div>
            <div class="hidden" id="inline_<?php echo $share->id; ?>">
                <div class="title"><?php echo $share->title; ?></div>
                <div class="cids"><?php echo implode(',', $cids); ?></div>
            </div>
        </td>
        <td><?php echo implode('&nbsp;,&nbsp;', $as); ?></td>
        <td><?php echo $share->price ?></td>
        <td><?php echo $share->user_name ?></td>
        <td><?php echo $share->fav_count ?></td>
        <td><?php echo $share->comment_count; ?></td>
    </tr>
    <?php
}

function the_share_id() {
    echo get_the_share_id();
}

function get_the_share_id() {
    global $share;
    return $share->id;
}

function the_share_go($share_id = 0) {
    echo get_the_share_go($share_id);
}

function get_the_share_go($share_id = 0) {
    if ($share_id == 0) {
        global $share;
        $share_id = $share->id;
    }
    $_url = xt_jump_url(array(
        'type' => get_the_share_fromtype(),
        'id' => get_the_share_key(),
        'share' => get_the_share_guid()
            ));
    return apply_filters('the_share_go', $_url);
}

function the_share_url($share_id = 0) {
    echo get_the_share_url($share_id);
}

function get_the_share_url($share_id = 0) {
    if ($share_id == 0) {
        global $share;
        $share_id = $share->id;
    }
    return apply_filters('the_share_url', xt_site_url('id-' . $share_id));
}

/**
 * Display the picture url of the current item in the Xintaoke Loop.
 *
 */
function the_share_picurl($type = 200, $_share = 0) {
    echo get_the_share_picurl($type, $_share);
}

/**
 * Retrieve the picture url of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_picurl($size = 200, $_share = 0) {
    if (!$_share) {
        global $share;
        $_share = $share;
    }
    return apply_filters('the_share_picurl', xt_pic_url($_share->pic_url, $size, $_share->from_type));
}

/**
 * Display or retrieve the current share title with optional content.
 *
 *
 * @param string $before Optional. Content to prepend to the title.
 * @param string $after Optional. Content to append to the title.
 * @param bool $echo Optional, default to true.Whether to display or return.
 * @return null|string Null on no title. String if $echo parameter is false.
 */
function the_share_title($before = '', $after = '', $echo = true) {
    $title = get_the_share_title();

    if (strlen($title) == 0)
        return;

    $title = $before . $title . $after;

    if ($echo)
        echo $title;
    else
        return $title;
}

/**
 * Retrieve share title.
 *
 *
 * @param int $id Optional. share ID.
 * @return string
 */
function get_the_share_title($id = 0) {
    $share = & get_share($id);

    $title = isset($share->title) ? $share->title : '';
    $id = isset($share->id) ? $share->id : (int) $id;

    return apply_filters('the_share_title', $title, $id);
}

/**
 * Display the share_key of the current item in the Xintaoke Loop.
 *
 */
function the_share_key() {
    echo get_the_share_key();
}

/**
 * Retrieve the share_key of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_key($key = '') {
    if (empty($key)) {
        global $share;
        $key = $share->share_key;
    }

    return apply_filters('the_share_key', str_replace(array(
                        'tb_',
                        'pp_'
                            ), array(
                        '',
                        ''
                            ), $key));
}

function the_share_guid() {
    echo get_the_share_guid();
}

function get_the_share_guid() {
    global $share;
    return $share->guid;
}

/**
 * Display the share_cid of the current item in the Xintaoke Loop.
 *
 */
function the_share_cid() {
    echo get_the_share_cid();
}

/**
 * Retrieve the share_cid of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_cid() {
    global $share;
    return $share->cid;
}

function the_share_fromtype() {
    echo get_the_share_fromtype();
}

function get_the_share_fromtype() {
    global $share;
    return $share->from_type;
}

function the_share_ico() {
    echo get_the_share_ico();
}

function get_the_share_ico() {
    return XT_CORE_IMAGES_URL . '/' . get_the_share_fromtype() . '.gif';
}

/**
 * Display the share_price of the current item in the Xintaoke Loop.
 *
 */
function the_share_price() {
    echo get_the_share_price();
}

/**
 * Retrieve the share_price of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_price($_share = 0) {
    if (!$_share) {
        global $share;
        $_share = $share;
    }
    $_price = $_share->price;
    //	if ($_share->from_type == 'paipai') {
    //		$_price = $_share->price / 100;
    //	}
    return apply_filters('the_share_price', number_format($_price));
}

function the_share_userid() {
    echo get_the_share_userid();
}

function get_the_share_userid() {
    global $share;
    return $share->user_id;
}

function the_share_useravatar() {
    echo get_the_share_useravatar();
}

function get_the_share_useravatar() {
    global $share;
    return $share->user_avatar;
}

/**
 * Display the share_username of the current item in the Xintaoke Loop.
 *
 */
function the_share_username() {
    echo get_the_share_username();
}

/**
 * Retrieve the share_username of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_username() {
    global $share;
    return apply_filters('the_share_username', $share->user_name);
}

/**
 * Display the share_tags of the current item in the Xintaoke Loop.
 *
 */
function the_share_tags() {
    echo get_the_share_tags();
}

/**
 * Retrieve the share_tags of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_tags() {
    global $share;
    return apply_filters('the_share_tags', $share->tags);
}

/**
 * Display the share_content of the current item in the Xintaoke Loop.
 *
 */
function the_share_content() {
    echo get_the_share_content();
}

/**
 * Retrieve the share_content of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_content() {
    global $share;
    return apply_filters('the_share_content', $share->content);
}

/**
 * Display the share_favcount of the current item in the Xintaoke Loop.
 *
 */
function the_share_favcount() {
    echo get_the_share_favcount();
}

/**
 * Retrieve the share_favcount of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_favcount() {
    global $share;
    return $share->fav_count;
}

/**
 * Display the share_commentcount of the current item in the Xintaoke Loop.
 *
 */
function the_share_commentcount() {
    echo get_the_share_commentcount();
}

/**
 * Retrieve the share_commentcount of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_commentcount() {
    global $share;
    return $share->comment_count;
}

/**
 * Display the share_createdate of the current item in the Xintaoke Loop.
 *
 */
function the_share_createdate($format = 'Y-m-d') {
    echo get_the_share_createdate($format);
}

/**
 * Retrieve the share_createdate of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_createdate($format = 'Y-m-d') {
    global $share;
    return apply_filters('the_share_createdate', date($format, strtotime($share->create_date)), $format);
}

/**
 * Display the share_updatedate of the current item in the Xintaoke Loop.
 *
 */
function the_share_updatedate() {
    echo get_the_share_updatedate();
}

/**
 * Retrieve the share_updatedate of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_updatedate() {
    global $share;
    return apply_filters('the_share_updatedate', $share->update_date);
}

/**
 * Retrieve the share time of the current share.
 *
 * @param string $d Optional. The format of the time (defaults to user's config)
 * @param bool $gmt Whether to use the GMT date
 * @param bool $translate Whether to translate the time (for use in feeds)
 * @return string The formatted time
 */
function get_the_share_time_human($gmt = false) {
    global $share;
    $share_date = $gmt ? $share->create_date_gmt : $share->create_date;
    return apply_filters('get_the_share_time_human', $share_date, $gmt);
}

/**
 * Display the share time of the current share.
 *
 * @param string $d Optional. The format of the time (defaults to user's config)
 */
function the_share_time_human($gmt = false) {
    echo get_the_share_time_human($gmt);
}

/**
 * Display the share_status of the current item in the Xintaoke Loop.
 *
 */
function the_share_status() {
    echo get_the_share_status();
}

/**
 * Retrieve the share_status of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_status() {
    global $share;
    return apply_filters('the_share_status', $share->status);
}

/**
 * Retrieve the share_cachedata of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_cachedata() {
    global $share;
    return apply_filters('the_share_cachedata', $share->cache_data);
}

/**
 * Display the share_sort of the current item in the Xintaoke Loop.
 *
 */
function the_share_sort() {
    echo get_the_share_sort();
}

/**
 * Retrieve the share_sort of the current item in the Xintaoke Loop.
 *
 * @uses $share
 *
 * @return int
 */
function get_the_share_sort() {
    global $share;
    return $share->sort;
}

function xt_shares_pagination_links() {
    echo xt_get_shares_pagination_links();
}

function xt_get_shares_pagination_links() {
    global $xt_share_query;
    return apply_filters('xt_get_shares_pagination_links', $xt_share_query->paginate_links);
}

function xt_shares_pagination_count() {
    echo xt_get_shares_pagination_count();
}

function xt_get_shares_pagination_count() {
    global $xt_share_query;
    $pag_page = $xt_share_query->get('page');
    $pag_num = $xt_share_query->get('share_per_page');
    $start_num = intval(($pag_page - 1) * $pag_num) + 1;
    $from_num = $start_num;
    $to_num = ($start_num + ($pag_num - 1) > $xt_share_query->found_shares) ? $xt_share_query->found_shares : $start_num + ($pag_num - 1);
    $total = $xt_share_query->found_shares;

    return apply_filters('xt_get_shares_pagination_count', sprintf('浏览%1$s to %2$s (共 %3$s)', $from_num, $to_num, $total));
}

function get_delete_template($id, $type, $user_id, $class) {
    if ($class == '') {
        return '';
    }
    if (xt_is_self($user_id)) {
        return apply_filters('get_delete_template', '<span class="badge badge-delete ' . $class . '" data-id="' . $id . '" data-type="' . $type . '"><i class="icon-remove icon-white"></i></span>');
    }
    return "";
}

function the_delete_template($id, $type, $user_id, $class) {
    echo get_delete_template($id, $type, $user_id, $class);
}

function get_the_admin_tool_share($share_id = 0) {
    if ($share_id > 0 && current_user_can('publish_pages')) {
        return '<div class="xt-share-tool"><span title="分类" class="label label-catalog-change" data-id="' . $share_id . '">分类</span></div>';
    }
}

function get_the_share_detail_template() {
    global $xt_user_avatar;
    $cacheObj = get_the_share_cachedata();
    $_item = $cacheObj['item'];
    $_title = get_the_share_title();
    $_fxText = '元';
    $isJifenbao = xt_fanxian_is_jifenbao(get_the_share_fromtype());
    if ($isJifenbao) {
        $_fxText = xt_jifenbao_text();
    }
    ?>
    <script type="text/javascript">
        if(typeof(XT)!='undefined'){XT.share_id=<?php the_share_id(); ?>;}
    </script>
    <div class="row-fluid" style="background-color:white;">
        <div class="span12 media well well-small" style="margin-left:0px;margin-bottom:0px;">
            <a style="display:block;width:40px;" class="pull-left X_Nick" data-value="<?php echo get_the_share_userid(); ?>" href="<?php xt_the_user_url(get_the_share_userid()) ?>" target="_blank" rel="nofollow"><img src="<?php xt_the_user_pic($xt_user_avatar) ?>" style="width:40px;height:40px;"></a>
            <div class="media-body">
                <h5 class="media-heading"><a class="xt-red X_Nick" data-value="<?php echo get_the_share_userid(); ?>" href="<?php xt_the_user_url(get_the_share_userid()) ?>" target="_blank"><?php the_share_username(); ?></a>&nbsp;&nbsp;&nbsp;分享了该宝贝<span class="pull-right"><?php the_share_time_human(); ?></span></h5>
                <div class="media" style="margin-top:0px;"><?php the_share_content() ?></div>
            </div>
        </div>
        <div class="span12 media" style="margin-left:0px;padding:5px;">
            <div class="pull-left">
                <a rel="nofollow" href="<?php (the_share_go()); ?>" target="_blank">
                    <img src="<?php the_share_picurl(450) ?>" alt="<?php echo $_title; ?>"/>
                    <?php echo get_the_admin_tool_share(get_the_share_id()); ?>
                </a>
            </div>
            <div class="pull-left" style="width:250px">
                <h1 style="font-size:16px;line-height:22px;margin-top: 0px;"><?php echo '<img src="' . get_the_share_ico() . '"/>' ?><a rel="nofollow" title="<?php echo $_title ?>" target="_blank" href="<?php (the_share_go()); ?>"><?php echo $_title ?></a></h1>
                <a id="X_Share-Detail-Buy" data-from="<?php echo get_the_share_fromtype(); ?>" data-type="<?php echo $isJifenbao ? 'jifenbao' : 'cash' ?>" data-id="<?php echo get_the_share_key() ?>" href="<?php (the_share_go()); ?>" target="_blank" rel="nofollow" class="btn btn-primary" style="padding:4px 20px;font-size:16px;font-weight:bold;">￥<?php the_share_price(); ?>&nbsp;去购买</a>
                <?php if (xt_fanxian_is_sharebuy()) { ?>
                    <div class="clearfix"><?php echo xt_fanxian_html('{fx}', $_fxText, 'margin-top:10px;'); ?></div>
                <?php } ?>
                <div class="clearfix" style="margin-top:10px;padding-bottom: 10px;border-bottom: 1px solid #EAEAEA;">
                    <a class="badge badge-fav" title="喜欢" data-id="<?php the_share_id(); ?>" data-type="1" data-uid="<?php the_share_userid() ?>"><?php the_share_favcount(); ?>&nbsp;&nbsp;<i class="icon-heart icon-white"></i></a>
                    <!--<a href="javascript:;" class="xt-album-add" data-id="<?php //the_share_id()                                              ?>" data-pic="<?php //the_share_picurl(160)                                              ?>">加入专辑</a>-->
                </div>
                <div class="well well-small clearfix" style="margin-top:15px;">
                    <?php
                    $_tags = explode(" ", get_the_share_tags());
                    foreach ($_tags as $_tag) {
                        echo "<a href=\"" . xt_get_shares_search_url(array(
                            's' => $_tag
                        )) . "\" target=\"_blank\" style=\"float: left;margin-left: 8px;\">$_tag</a>";
                    }
                    ?>
                </div>
                <?php
                $bdshare = xt_bdshare();
                if ($bdshare > 0)
                    :
                    ?>
                    <!-- Baidu Button BEGIN -->
                    <div id="bdshare" class="bdshare_b" style="line-height: 12px;"><img src="http://share.baidu.com/static/images/type-button-1.jpg" />
                        <a class="shareCount"></a>
                    </div>
                    <script type="text/javascript">
                        document.getElementById("bdshell_js").src = "http://share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
                    </script>
                    <!-- Baidu Button END -->
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function get_the_share_container($_params = array(), $isCatalog = false, $isAjax = false, $isScroll = true) {
    echo '<div id="X_Wall-Result" class="clearfix">';
    echo '<div id="X_Wall-Container" class="xt-wall-container row" data-scroll="' . ($isScroll ? 'true' : 'false') . '">';

    if ($isCatalog) {
        xt_load_template('xt-widget_catalog');
    }
    $_user_id = 0;
    $_delClass = '';
    $msg = 'share_not_found';
    if (isset($_params['isHome']) && $_params['isHome']) {
        global $xt_pageuser_follows;
        if (!empty($xt_pageuser_follows)) {
            $msg = 'share_home_not_found_other';
            if (!empty($xt_user) && $xt_user->exists()) {
                $current_user = wp_get_current_user();
                if ($current_user->exists()) {
                    if ($current_user->ID == $xt_user->ID) {
                        $msg = 'share_home_not_found_myself';
                    }
                }
            }
        } else {
            $_params['nopage'] = 1;
        }
    } elseif (isset($_params['album_id']) && $_params['album_id']) {
        //xt_load_template('xt-widget_albums');
        $_user_id = $_params['user_id'];
        $_delClass = 'xt-delete-album-share';
        $msg = 'share_album_not_found';
    } elseif (isset($_params['isFavorite']) && $_params['isFavorite']) {
        $_user_id = $_params['user_id'];
        $_delClass = 'xt-delete-favorite';
        $msg = 'share_favorite_not_found';
    } elseif (isset($_params['isShare']) && $_params['isShare']) {
        $msg = 'share_share_not_found';
    }
    $_count = 0;
    echo '<div class="span12">';
    while (xt_have_shares()) {
        xt_the_share();
        get_the_share_template($_user_id, $_delClass);
        $_count++;
    }
    echo '</div>';
    if ($_count == 0) {
        echo xt_not_found($msg, 'xt-share-not-found');
    }
    echo '</div>';

    if ($isScroll) {
        echo "<div id=\"X_Page-Nav\" style=\"display: none; \"><a id=\"X_Page-Next-Link\" rel=\"nofollow\" href=\"" . admin_url('admin-ajax.php') . "?action=xt_ajax_search_shares&s_index=" . $_params['page'] . "&" . http_build_query($_params) . "\"></a></div>";
    }
    if (isset($_params['nopage']) && $_params['nopage']) {
        //no page
    } else {
        echo '<div id="X_Pagination-Bottom" class="clearfix">';
        echo '<div class="pagination xt-pagination-links">';
        xt_shares_pagination_links();
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}

function get_the_share_template($user_id = 0, $delClass = '') {
    global $xt_album;
    $cacheObj = get_the_share_cachedata();
    $_item = isset($cacheObj['item']) ? $cacheObj['item'] : array();
    $_comment = isset($cacheObj['comment']) ? $cacheObj['comment'] : array();
    $_title = get_the_share_title();
    $_url = get_the_share_url();
    $_nick = xt_get_the_user_title(get_the_share_username());
    ?>
    <div class="span3 xt-share">
        <div class="thumbnail">
            <a href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link">
                <img src="<?php the_share_picurl() ?>" title="<?php echo $_title; ?>" alt="<?php echo $_title; ?>" />
                <div class="xt-img-price">¥<?php the_share_price(); ?></div>
                <?php echo get_the_admin_tool_share(get_the_share_id()); ?>
                <?php
                if ($user_id) {
                    the_delete_template(get_the_share_id(), ($delClass == 'xt-delete-album-share' ? $xt_album->id : 1), $user_id, $delClass);
                }
                ?>
                <span class="label label-album-add" data-id="<?php the_share_id() ?>" data-pic="<?php the_share_picurl(160) ?>">加入专辑</span>	
            </a>
            <div class="caption">
                <h5><?php the_share_content(); ?></h5>
                <div class="clearfix">
                    <a class="badge badge-fav" title="喜欢" data-id="<?php the_share_id(); ?>" data-type="1" data-uid="<?php the_share_userid() ?>"><?php the_share_favcount(); ?><i class="icon-heart icon-white"></i></a>
                    <a class="badge badge-comment" title="评论" href="javascript:;" data-id="<?php the_share_id(); ?>"><?php the_share_commentcount() ?><i class="icon-comment icon-white"></i></a>
                </div>
            </div>
            <ul class="media-list clearfix">
                <li class="media">
                    <a class="pull-left X_Nick" data-value="<?php echo get_the_share_userid() ?>" target="_blank" href="<?php xt_the_user_url(get_the_share_userid()); ?>"><img src="<?php xt_the_user_pic(get_the_share_useravatar()); ?>" alt="<?php echo $_nick ?>"></a>
                    <div class="media-body"><p class="muted"><a class="X_Nick" data-value="<?php echo get_the_share_userid() ?>" target="_blank" href="<?php xt_the_user_url(get_the_share_userid()); ?>" class="xt-rep-nick"><?php echo $_nick; ?>        </a>分享了:<?php echo wp_trim_words(get_the_share_title(), 15); ?>    </p></div>
                </li>
                <?php
                if (xt_is_displaycomment() && !empty($_comment) && isset($_comment['comments']) && !empty($_comment['comments'])) {
                    $_comments = $_comment['comments'];
                    foreach ($_comments as $c) {
                        ?>
                        <li class="media">
                            <a class="pull-left X_Nick" data-value="<?php echo $c['user_id'] ?>" target="_blank" href="<?php xt_the_user_url($c['user_id']); ?>"><img src="<?php xt_the_user_pic($c['pic_url']); ?>" alt="<?php xt_the_user_title($c['nick']) ?>"></a>
                            <div class="media-body"><p class="muted"><a class="X_Nick" data-value="<?php echo $c['user_id'] ?>" target="_blank" href="<?php xt_the_user_url($c['user_id']); ?>" class="xt-rep-nick"><?php xt_the_user_title($c['nick']); ?>        </a>：<?php xt_comment_excerpt(0, $c['content']); ?>    </p></div>
                        </li>
                        <?php
                    }
                    if (isset($_comment['total']) && $_comment['total'] > 2) {
                        echo '<li class="xt-rep-more"><a href="' . $_url . '" target="_blank" rel="nofollow"> 查看全部' . $_comment['total'] . '条评论...</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
    </div>
    <?php
}

function get_the_taobao_item_template($item) {
    ?>
    <div class="xt-share xt-share-taobao">
        <div class="xt-inner xt-share-inner clearfix">
            <!-- Item Img -->
            <div class="xt-share-image">
                <div class="xt-img-intro-left">
                    <a href="javascript:;" data-click="<?php echo base64_encode($item->click_url); ?>" title="<?php echo $item->title; ?>" target="_blank" class="xt-share-link"><img
                            class="xt-lazyload" src="<?php echo $item->pic_url ?>_210x1000.jpg" title="<?php echo $item->title; ?>" alt="<?php echo $item->title; ?>" />
                        <span class="xt-img-price">¥<?php echo $item->price; ?></span>
                    </a>

                </div>
            </div>
            <!-- //Item Img -->
            <div class="xt-share-main clearfix">
                <!-- Item header -->
                <div class="xt-share-header clearfix">
                    <h2><a href="javascript:;" data-click="<?php echo base64_encode($item->click_url); ?>" title="<?php echo $item->title; ?>" class="xt-share-link"><?php echo $item->title; ?></a></h2>
                    <div class="xt-fav">

                        <span class="xt-creply-n">(<a><?php echo $item->volume ?></a>)</span>
                        <a class="xt-creply">已售</a>
                    </div>
                </div>
                <!-- //Item header -->
                <!-- Item content -->
                <div class="xt-share-content">

                </div>
                <!-- //Item content -->
                <!-- Item footer -->
                <div class="xt-share-footer clearfix">

                </div>
                <!-- //Item footer -->
            </div>
            <div class="xt-share-separator"></div>
        </div>
    </div>	
    <?php
}
?>