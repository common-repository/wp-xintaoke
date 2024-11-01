<?php

function xt_row_album($album, $count) {
    $terms = xt_get_catalog_terms_cache($album->id, 'album');
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
    <tr id="album-<?php echo $album->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td scope="row"><span><?php echo $album->id; ?></span></td>
        <td class="name column-name"><strong><a class="row-title" href="<?php the_album_url($album->id) ?>" target="_blank"><?php echo ($album->title); ?></a></strong><br>
            <div class="row-actions">
                    <!--<span class="edit"><a href="">编辑</a> | </span>-->
                <span class="inline hide-if-no-js"><a href="#" class="editinline">编辑</a> | </span>
                <span class="delete"><a class="delete-album" href="javascript:;" data-value="<?php echo $album->id; ?>">删除</a></span>
            </div>
            <div class="hidden" id="inline_<?php echo $album->id; ?>">
                <div class="title"><?php echo $album->title; ?></div>
                <div class="cids"><?php echo implode(',', $cids); ?></div>
            </div>
        </td>
        <td><?php echo implode('&nbsp;,&nbsp;', $as); ?></td>
        <td><?php echo $album->user_name ?></td>
        <td><?php echo $album->fav_count ?></td>
        <td><?php echo $album->share_count ?></td>
    </tr>
    <?php
}

function the_album_id() {
    echo get_the_album_id();
}

function get_the_album_id() {
    global $xt_album;
    return $xt_album->id;
}

function the_album_url($album_id = 0) {
    echo get_the_album_url($album_id);
}

function get_the_album_url($album_id = 0) {
    if ($album_id == 0) {
        global $xt_album;
        $album_id = $xt_album->id;
    }
    return apply_filters('the_album_url', xt_site_url('aid-' . $album_id));
}

function get_the_album_picurls_big($_album = 0) {
    if (!$_album) {
        global $xt_album;
        $_album = $xt_album;
    }
    return apply_filters('the_album_picurls_big', convert_album_picurls_big(explode(",", $_album->pic_url, 5)));
}

function get_the_album_picurls_small($_album = 0) {
    if (!$_album) {
        global $xt_album;
        $_album = $xt_album;
    }
    return apply_filters('the_album_picurls_small', convert_album_picurls_small(explode(",", $_album->pic_url, 9)));
}

function convert_album_picurls_big($pic_urls = array()) {
    $result = array();
    if (count($pic_urls) > 0) {
        $count = 0;
        foreach ($pic_urls as $_p) {
            $__p = '';
            $size = 80;
            if (count($result) == 0) {
                $size = 200;
            }
            $__p = xt_pic_url($_p, $size);
            if (!empty($__p)) {
                $result[] = $__p;
            }
            $count++;
        }
    }
    $_count = count($result);
    if ($_count < 5) {
        for ($i = 0; $i < (5 - $_count); $i++) {
            $result[] = '';
        }
    }

    return $result;
}

function convert_album_picurls_small($pic_urls = array()) {
    $result = array();
    if (count($pic_urls) > 0) {
        $count = 0;
        foreach ($pic_urls as $_p) {
            $__p = '';
            $size = 80;
            $__p = xt_pic_url($_p, $size);
            if (!empty($__p)) {
                $result[] = $__p;
            }
            $count++;
        }
    }
    $_count = count($result);
    if ($_count < 9) {
        for ($i = 0; $i < (9 - $_count); $i++) {
            $result[] = '';
        }
    }
    return $result;
}

function the_album_title($before = '', $after = '', $echo = true) {
    $title = get_the_album_title();

    if (strlen($title) == 0)
        return;

    $title = $before . $title . $after;

    if ($echo)
        echo $title;
    else
        return $title;
}

function get_the_album_title($id = 0) {
    global $xt_album;

    return apply_filters('the_album_title', $xt_album->title);
}

function the_album_userid() {
    echo get_the_album_userid();
}

function get_the_album_userid() {
    global $xt_album;
    return $xt_album->user_id;
}

function the_album_username() {
    echo get_the_album_username();
}

function get_the_album_username() {
    global $xt_album;
    return apply_filters('the_album_username', $xt_album->user_name);
}

function the_album_content() {
    echo get_the_album_content();
}

function get_the_album_content() {
    global $xt_album;
    return apply_filters('the_album_content', $xt_album->content);
}

function the_album_sharecount() {
    echo get_the_album_sharecount();
}

function get_the_album_sharecount() {
    global $xt_album;
    return $xt_album->share_count;
}

function the_album_favcount() {
    echo get_the_album_favcount();
}

function get_the_album_favcount() {
    global $xt_album;
    return $xt_album->fav_count;
}

function the_album_commentcount() {
    echo get_the_album_commentcount();
}

function get_the_album_commentcount() {
    global $xt_album;
    return $xt_album->comment_count;
}

function the_album_createdate($format = 'Y-m-d') {
    echo get_the_album_createdate($format);
}

function get_the_album_createdate($format = 'Y-m-d') {
    global $xt_album;
    return apply_filters('the_album_createdate', date($format, strtotime($xt_album->create_date)), $format);
}

function the_album_updatedate() {
    echo get_the_album_updatedate();
}

function get_the_album_updatedate() {
    global $xt_album;
    return apply_filters('the_album_updatedate', $xt_album->update_date);
}

function get_the_album_time_human($gmt = false) {
    global $xt_album;
    $xt_album_date = $gmt ? $xt_album->create_date_gmt : $xt_album->create_date;
    return apply_filters('get_the_album_time_human', $xt_album_date, $gmt);
}

function the_album_time_human($gmt = false) {
    echo get_the_album_time_human($gmt);
}

function get_the_album_cachedata() {
    global $xt_album;
    return apply_filters('the_album_cachedata', $xt_album->cache_data);
}

function the_album_sort() {
    echo get_the_album_sort();
}

function get_the_album_sort() {
    global $xt_album;
    return $xt_album->sort;
}

function xt_albums_pagination_links() {
    echo xt_get_albums_pagination_links();
}

function xt_get_albums_pagination_links() {
    global $xt_album_query;
    return apply_filters('xt_get_albums_pagination_links', $xt_album_query->paginate_links);
}

function xt_albums_pagination_count() {
    echo xt_get_albums_pagination_count();
}

function xt_get_albums_pagination_count() {
    global $xt_album_query;
    $pag_page = $xt_album_query->get('page');
    $pag_num = $xt_album_query->get('album_per_page');
    $start_num = intval(($pag_page - 1) * $pag_num) + 1;
    $from_num = $start_num;
    $to_num = ($start_num + ($pag_num - 1) > $xt_album_query->found_albums) ? $xt_album_query->found_albums : $start_num + ($pag_num - 1);
    $total = $xt_album_query->found_albums;

    return apply_filters('xt_get_albums_pagination_count', sprintf('浏览%1$s to %2$s (共 %3$s)', $from_num, $to_num, $total));
}

function xt_get_album_popup($user_id, $id, $pic, $title, $albums = array()) {
    ?>
    <div id="X_Album-Popup" class="row-fluid" style="position:absolute;width:520px;">
        <h4 style="margin-top:0px;"><?php echo $title ?></h4>
        <div class="span12" style="margin-left:0px;">	
            <div style="float: left;width: 160px;height: 160px;overflow: hidden;">
                <img width="160" src="<?php echo $pic ?>">
            </div>
            <div  style="float: left;padding-left: 10px;width: 350px;height:230px;position: relative;">
                <ul class="nav nav-pills" style="margin-bottom:10px;">
                    <li class="dropdown active" style="width:348px">
                        <a class="dropdown-toggle" id="X_Album-Popup-Album-Selected" data-toggle="dropdown"><b class="caret" style="float:right;"></b></a>
                        <ul class="dropdown-menu" id="X_Album-Popup-Albums" style="width:348px;max-height: 300px;overflow-x: hidden;overflow-y: auto;z-index:1100">
                            <?php
                            query_albums(array(
                                'user_id' => $user_id,
                                'no_found_rows' => 1
                            ));
                            global $xt_album;
                            $_count = 0;
                            while (xt_have_albums()) {
                                xt_the_album();
                                echo "<li><a data-value=\"$xt_album->id\" href=\"javascript:;\">$xt_album->title</a></li>";
                                $_count++;
                            }
                            if ($_count == 0) {
                                global $wpdb;
                                $user = wp_get_current_user();
                                if (empty($user->display_name))
                                    $user->display_name = $user->user_login;
                                $user_name = $wpdb->escape($user->display_name);
                                echo '<li><a data-value="0" href=\"javascript:;\">' . $user_name . '的分享</a></li>';
                            }
                            ?>
                            <li class="divider"></li>
                            <div id="X_Album-Create-Msg" class="alert alert-block alert-error fade in hide" style="margin:0 auto;padding:2px;width:310px;"></div>
                            <div class="input-append clearfix" style="padding: 3px 15px;">
                                <input id="X_Album-Create-Input" maxlength="20" type="text" value="" style="width:250px;" placeholder="创建一个专辑">
                                <input class="btn btn-primary" id="X_Album-Create-Btn" data-loading-text="创建中..." value="创建" type="button">
                            </div>
                        </ul>
                    </li>
                </ul>	
                <textarea id="X_Album-Share-Content" class="input-xlarge" style="height:68px;width:330px;" placeholder="写点什么,评论一下"></textarea>
                <input value="确定" data-loading-text="分享中..." class="btn btn-primary" id="X_Album-Submit-Btn" data-id="<?php echo $id; ?>" type="button">
            </div>
        </div>
    </div>	
    <script type="text/javascript">
        jQuery(function($) {
            var li = $('#X_Album-Popup-Albums li:first');
            var a = li.find('a');
            $('#X_Album-Popup-Album-Selected').attr('data-value',a.attr('data-value')).html(a.text()+'<b class="caret" style="float:right;"></b>').dropdown();
            $('#X_Album-Create-Input').click(function(e){
                e.stopPropagation();
            });
            li.remove();
            $('#X_Album-Popup-Albums li a').click(function(){
                $('#X_Album-Popup-Album-Selected').attr('data-value',$(this).attr('data-value')).html($(this).text()+'<b class="caret" style="float:right;"></b>');	
            });
            // 创建专辑
            $('#X_Album-Create-Btn').click(function(e) {
                e.stopPropagation();
                if (!XT.userId) {
                    XT_openLogin($(this).attr('data-url'));
                    return true;
                }
                var msg = $('#X_Album-Create-Msg');
                msg.html('').hide();
                var title = $.trim($('#X_Album-Create-Input').val());
                if (!title) {
                    msg.html('请输入专辑名称!').show();
                    $('#X_Album-Create-Input').focus();
                    return true;
                }
                var reg_title = /[\$|&|#|\|"| |]/.test(title);
                if (reg_title) {
                    msg.html('专辑名称含有非法字符!').show();
                    $('#X_Album-Create-Input').focus();
                    return false;
                }
                if(XTTOOL.getMsgLength(title,30) < 0){
                    msg.html('专辑名称不能超过30字!').show();
                    $('#X_Album-Create-Input').focus();
                    return false;
                }
                msg.html('正在创建...').show();
                $('#X_Album-Create-Btn').addClass('disabled');
                XT.albumAdd(title, '', function(response) {
                    if (typeof(response) == 'string') {
                        msg.html(response).show();
                        $('#X_Album-Create-Input').focus();
                        $('#X_Album-Create-Btn').removeClass('disabled');
                        return false;
                    }
                    if (response.code > 0) {
                        if (response.code == 2000) {
                            msg.show().html("哎呀，这个名称已经有人使用了，请换个名称吧!");
                        } else {
                            msg.show().html(response.msg);
                        }
                        $('#X_Album-Create-Btn').removeClass('disabled');
                    } else {
                        if (response.result == 0) {
                            msg.show().html("创建专辑失败");
                            $('#X_Album-Create-Btn').removeClass('disabled');
                            return false;
                        }
                        $('#X_Album-Create-Input').val('');
                        $('#X_Album-Popup-Albums').prepend('<li><a data-value="'+response.result+'" href="javascript:;">'+title+'</a></li>');
                        $('#X_Album-Popup-Album-Selected').attr('data-value',response.result).html(title+'<b class="caret" style="float:right;"></b>').click();
                        msg.hide().html("");
                        $('#X_Album-Create-Btn').removeClass('disabled');
                    }
                }, function(request, error, status) {
                    msg.show().html("创建专辑失败");
                    $('#X_Album-Create-Input').focus();
                    $('#X_Album-Create-Btn').removeClass('disabled');
                });
            });
        });

    </script>
    <?php
}

function get_the_album_container($_params = array(), $isCatalog = false, $isAjax = false, $isScroll = true) {
    if ($isAjax) {
        echo '<div id="X_Wall-Result" class="clearfix">';
    }
    echo '<div id="X_Wall-Container" class="xt-wall-container row" data-scroll="' . ($isScroll ? 'true' : 'false') . '">';

    $_user_id = 0;
    $_delClass = '';
    $msg = 'album_not_found';
    if (isset($_params['isFavorite']) && $_params['isFavorite']) {
        $_user_id = $_params['user_id'];
        $_delClass = 'xt-delete-favorite';
        $msg = 'album_favorite_not_found';
    } elseif (isset($_params['isShare']) && $_params['isShare']) {
        $msg = 'album_share_not_found';
    }

    $_count = 0;
    $isBig = xt_albumdisplay() == 'big' ? true : false;
    while (xt_have_albums()) {
        xt_the_album();
        if ($isBig) {
            get_the_album_template_big($_user_id, $_delClass);
        } else {
            get_the_album_template_small($_user_id, $_delClass);
        }

        $_count++;
    }
    if ($_count == 0) {
        echo xt_not_found($msg, 'xt-album-not-found');
    }
    echo '</div>';

    if ($isScroll) {
        echo "<div id=\"X_Page-Nav\" style=\"display: none; \"><a id=\"X_Page-Next-Link\" rel=\"nofollow\" href=\"" . admin_url('admin-ajax.php') . "?action=xt_ajax_search_albums&s_index=" . $_params['page'] . "&" . http_build_query($_params) . "\"></a></div>";
    }
    echo '<div id="X_Pagination-Bottom" class="clearfix">';
    echo '<div class="pagination xt-pagination-links">';
    xt_albums_pagination_links();
    echo '</div>';
    echo '</div>';
    if ($isAjax) {
        echo '</div>';
    }
}

function get_the_album_template_big($user_id = 0, $delClass = '') {
    $_title = get_the_album_title();
    $_url = get_the_album_url();
    ?>

    <div class="span3 xt-share xt-share-album xt-share-album-big">
        <div class="thumbnail">

            <ul class="unstyled clearfix">
                <?php $_picurls = get_the_album_picurls_big(); ?>
                <li class="xt-big"><a href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link"><?php echo!empty($_picurls[0]) ? '<img src="' . $_picurls[0] . '"/>' : '<span></span>'; ?></a></li>
                <li class="xt-small xt-first"><a rel="nofollow" href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link"><?php echo!empty($_picurls[1]) ? '<img src="' . $_picurls[1] . '"/>' : ''; ?></a></li>
                <li class="xt-small"><a rel="nofollow" href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link"><?php echo!empty($_picurls[2]) ? '<img src="' . $_picurls[2] . '"/>' : ''; ?></a></li>
                <li class="xt-small"><a rel="nofollow" href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link"><?php echo!empty($_picurls[3]) ? '<img src="' . $_picurls[3] . '"/>' : ''; ?></a></li>
                <li class="xt-small"><a rel="nofollow" href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-share-link"><?php echo!empty($_picurls[4]) ? '<img src="' . $_picurls[4] . '"/>' : ''; ?></a></li>
                <b class="xt-mask"></b>
                <?php
                if ($user_id) {
                    the_delete_template(get_the_album_id(), 2, $user_id, $delClass);
                }
                ?>
            </ul>
            <div class="caption clearfix"><h5><a href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank"><?php echo wp_trim_words($_title, 8); ?></a><span class="pull-right muted">共<?php the_album_sharecount() ?>个分享</span></h5></div>
        </div>
    </div>
    <?php
}

function get_the_album_template_small($user_id = 0, $delClass = '', $isUser = false, $count = 9) {
    $_title = get_the_album_title();
    $_url = get_the_album_url();
    ?>

    <div class="span3 xt-share xt-share-album xt-share-album-small">
        <div class="thumbnail">
            <div class="caption clearfix"><h5><a class="text-gray pull-left" href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank"><?php echo wp_trim_words($_title, 8); ?></a><span class="pull-right muted">共<?php the_album_sharecount() ?>个分享</span></h5></div>
            <?php $_picurls = get_the_album_picurls_small(); ?>
            <a href="<?php echo $_url; ?>" title="<?php echo $_title; ?>" target="_blank" class="xt-album-link clearfix">
                <?php
                $_count = 0;
                foreach ($_picurls as $_pic) {
                    if (!empty($_pic)) {
                        echo '<span class="xt-small"><img src="' . $_pic . '"/></span>';
                    } else {
                        echo '<span class="xt-small"></span>';
                    }
                    $_count++;
                    if ($_count == $count) {
                        break;
                    }
                }
                ?>
            </a>
            <?php
            if ($user_id) {
                the_delete_template(get_the_album_id(), 2, $user_id, $delClass);
            }
            ?>
            <?php if ($isUser): ?>
                <p class="muted">来自：<a class="X_Nick" data-value="<?php echo get_the_album_userid(); ?>" href="<?php xt_the_user_url(get_the_album_userid()); ?>" target="_blank"><?php the_album_username(); ?></a></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}