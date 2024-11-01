<?php

function xt_row_comment($comment, $count) {
    ?>
    <tr id="comment-<?php echo $comment->id; ?>" <?php echo $count % 2 == 0 ? 'class="alternate"' : '' ?>>
        <td scope="row"><span><?php echo $comment->id; ?></span></td>
        <td class="name column-name"><strong><?php echo (xt_comment_text(0, $comment->content)); ?></strong><br>
            <div class="row-actions">
                    <!--<span class="edit"><a href="">编辑</a> | </span>-->
                <span class="inline hide-if-no-js"><a href="#" class="editinline">编辑</a> | </span>
                <span class="delete"><a class="delete-comment" href="javascript:;" data-value="<?php echo $comment->id; ?>">删除</a></span>
            </div>
        </td>
        <td><?php echo $comment->user_name ?></td>
        <td><?php echo $comment->ip ?></td>
        <td><?php echo $comment->create_date ?></td>
    </tr>
    <?php
}

function xt_comments_pagination_links() {
    echo xt_get_comments_pagination_links();
}

function xt_get_comments_pagination_links() {
    global $xt_comment_query;
    return apply_filters('xt_get_comments_pagination_links', isset($xt_comment_query->paginate_links) ? $xt_comment_query->paginate_links : '');
}

function xt_comments_pagination_count() {
    echo xt_get_comments_pagination_count();
}

function xt_get_comments_pagination_count() {
    global $xt_comment_query;
    $pag_page = $xt_comment_query->get('page');
    $pag_num = $xt_comment_query->get('comment_per_page');
    $start_num = intval(($pag_page - 1) * $pag_num) + 1;
    $from_num = $start_num;
    $to_num = ($start_num + ($pag_num - 1) > $xt_comment_query->found_comments) ? $xt_comment_query->found_comments : $start_num + ($pag_num - 1);
    $total = $xt_comment_query->found_comments;

    return apply_filters('xt_get_comments_pagination_count', sprintf('浏览%1$s to %2$s (共 %3$s)', $from_num, $to_num, $total));
}

function xt_get_comment_useravatar($id = 0) {
    $comment = xt_get_comment($id);
    return apply_filters('xt_get_comment_useravatar', $comment->avatar);
}

function xt_comment_useravatar($id = 0) {
    $avatar = xt_get_comment_useravatar($id);
    echo $avatar;
}

function xt_get_comment_username($id = 0) {
    $comment = xt_get_comment($id);
    return apply_filters('xt_comment_username', $comment->user_name);
}

function xt_comment_username($id = 0) {
    $author = xt_get_comment_username($id);
    echo $author;
}

function xt_get_comment_userid($id = 0) {
    $comment = xt_get_comment($id);
    return apply_filters('xt_get_comment_username', $comment->user_id);
}

function xt_comment_userid($id = 0) {
    $author = xt_get_comment_username($id);
    echo $author;
}

function xt_get_comment_ip($id = 0) {
    $comment = xt_get_comment($id);
    return apply_filters('xt_get_comment_ip', $comment->ip);
}

function xt_comment_ip($id = 0) {
    echo xt_get_comment_ip($id);
}

function xt_get_comment_date($d = '', $id = 0) {
    $comment = xt_get_comment($id);
    if ('' == $d)
        $date = mysql2date(get_option('date_format'), $comment->create_date);
    else
        $date = mysql2date($d, $comment->create_date);
    return apply_filters('xt_get_comment_date', $date, $d);
}

function xt_comment_date($d = '', $id = 0) {
    echo xt_get_comment_date($d, $id);
}

function xt_get_comment_excerpt($id = 0, $content = '') {
    if (!empty($content)) {
        return apply_filters('xt_comment_text', wp_trim_words($content, 20));
    }
    $comment = xt_get_comment($id);
    $xt_comment_text = strip_tags($comment->content);
    $blah = explode(' ', $xt_comment_text);
    if (count($blah) > 20) {
        $k = 20;
        $use_dotdotdot = 1;
    } else {
        $k = count($blah);
        $use_dotdotdot = 0;
    }
    $excerpt = '';
    for ($i = 0; $i < $k; $i++) {
        $excerpt .= $blah[$i] . ' ';
    }
    $excerpt .= ($use_dotdotdot) ? '...' : '';
    return apply_filters('xt_comment_text', $excerpt);
}

function xt_comment_excerpt($id = 0, $content = '') {
    echo apply_filters('xt_comment_text', xt_get_comment_excerpt($id, $content));
}

function xt_get_comment_id() {
    global $comment;
    return apply_filters('xt_get_comment_id', $comment->id);
}

function xt_comment_id() {
    echo xt_get_comment_id();
}

function xt_get_comment_text($id = 0, $content = '') {
    $_content = '';
    if ($content != '') {
        $_content = $content;
    } else {
        $comment = xt_get_comment($id);
        $_content = $comment->content;
    }
    return apply_filters('xt_comment_text', $_content);
}

function xt_comment_text($id = 0, $content = '') {
    echo xt_get_comment_text($id, $content);
}

function xt_get_comment_time($d = '', $gmt = false, $translate = true) {
    global $xt_comment;
    $comment_date = $gmt ? $xt_comment->create_date_gmt : $xt_comment->create_date;
    if ('' == $d)
        $date = mysql2date(get_option('time_format'), $comment_date, $translate);
    else
        $date = mysql2date($d, $comment_date, $translate);
    return apply_filters('xt_get_comment_time', $date, $d, $gmt, $translate);
}

function xt_comment_time($d = '') {
    echo xt_get_comment_time($d);
}

function xt_get_comment_time_human($gmt = false) {
    global $xt_comment;
    $comment_date = $gmt ? $xt_comment->create_date_gmt : $xt_comment->create_date;
    return apply_filters('xt_get_comment_time_human', $comment_date, $gmt);
}

function xt_comment_time_human($gmt = false) {
    echo xt_get_comment_time_human($gmt);
}

function xt_comment_template() {
    xt_the_comment();
    ?>
    <li class="media" style="border-bottom: 1px dashed #DDD;padding-bottom:5px;">
        <a class="pull-left X_Nick" data-value="<?php echo xt_get_comment_userid(); ?>" href="<?php xt_the_user_url(xt_get_comment_userid()) ?>" target="_blank"><img src="<?php xt_the_user_pic(xt_get_comment_useravatar()); ?>" style="width:32px;height:32px;"></a>
        <div class="media-body">
            <h5 class="media-heading clearfix" style="margin-bottom:0px;">
                <span class="pull-right"><?php xt_comment_time_human(); ?></span>
                <a  class="X_Nick" data-value="<?php echo xt_get_comment_userid(); ?>" href="<?php xt_the_user_url(xt_get_comment_userid()) ?>" target="_blank"><?php xt_comment_username(); ?></a>
            </h5>
            <div class="media" style="margin-top:0px;">
    <?php xt_comment_text(); ?> <a href="javascript:;" class="xt-share-reply" data-nick="<?php xt_comment_username(); ?>">回复</a>
            </div>
        </div>
    </li>
    <?php
}