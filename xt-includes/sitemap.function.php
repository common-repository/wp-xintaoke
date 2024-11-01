<?php

function xt_sitemap_index($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_INDEX : XT_OPTION_SITEMAP_INDEX;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_index($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_index($type);
        }
    }
    return $value;
}

function xt_sitemap_index_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_INDEX_TIMESTAMP : XT_OPTION_SITEMAP_INDEX_TIMESTAMP;
    return get_option($option);
}

/**
 * share,post,shares,album,user,page,system
 */
function xt_sitemap_build_index($type = '') {
    $value = _xt_sitemap_build_index($type);
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_INDEX : XT_OPTION_SITEMAP_INDEX;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_INDEX_TIMESTAMP : XT_OPTION_SITEMAP_INDEX_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_index($type = '') {
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version = "1.0" encoding = "UTF-8"?><sitemapindex>';
        $indexs = array('sitemapshare', 'sitemappost', 'sitemapalbum', 'sitemapuser', 'sitemapother');
        $xml_indexs = '';
        $timestamp = current_time('timestamp', 1);
        foreach ($indexs as $index) {
            $xml_indexs.='<sitemap>';
            $xml_indexs.='<loc>' . xt_site_url($index) . '</loc>';
            $xml_indexs.='<lastmod>' . date('Y-m-d', $timestamp) . '</lastmod>';
            $xml_indexs.='</sitemap>';
        }
        $xml_end = '</sitemapindex>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}

//SHARE
function xt_sitemap_share($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_SHARE : XT_OPTION_SITEMAP_SHARE;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_share($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_share($type);
        }
    }

    return $value;
}

function xt_sitemap_share_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_SHARE_TIMESTAMP : XT_OPTION_SITEMAP_SHARE_TIMESTAMP;
    return get_option($option);
}

/**
 * share,shares,album,user,page,system
 */
function xt_sitemap_build_share($type = '') {
    $value = _xt_sitemap_build_share();
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_SHARE : XT_OPTION_SITEMAP_SHARE;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_SHARE_TIMESTAMP : XT_OPTION_SITEMAP_SHARE_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_share($type = '') {
    global $wpdb;
    $shares = $wpdb->get_results('SELECT id,create_date FROM ' . XT_TABLE_SHARE . ' ORDER BY id DESC LIMIT ' . XT_SITEMAP_SHARE_LIMIT);
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $xml_indexs = '';
        if (!empty($shares)) {
            foreach ($shares as $share) {
                $xml_indexs.='<url>';
                $xml_indexs.='<loc>' . get_the_share_url($share->id) . '</loc>';
                $xml_indexs.='<lastmod>' . date('Y-m-d', strtotime($share->create_date)) . '</lastmod>';
                $xml_indexs.='<changefreq>' . XT_SITEMAP_SHARE_CHANGEFREQ . '</changefreq>';
                $xml_indexs.='<priority>' . XT_SITEMAP_SHARE_PRIORITY . '</priority>';
                $xml_indexs.='</url>';
            }
        }
        $xml_end = '</urlset>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}

//POST
function xt_sitemap_post($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_POST : XT_OPTION_SITEMAP_POST;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_post($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_post($type);
        }
    }
    return $value;
}

function xt_sitemap_post_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_POST_TIMESTAMP : XT_OPTION_SITEMAP_POST_TIMESTAMP;
    return get_option($option);
}

function xt_sitemap_build_post($type = '') {
    $value = _xt_sitemap_build_post();
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_POST : XT_OPTION_SITEMAP_POST;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_POST_TIMESTAMP : XT_OPTION_SITEMAP_POST_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_post($type = '') {
    global $wpdb;
    $sql = "select ID,post_modified,post_date,post_type FROM $wpdb->posts
	        WHERE post_password = ''
			AND (post_type != 'revision' AND post_type != 'attachment' AND post_type != 'nav_menu_item')
			AND post_status = 'publish'
			ORDER BY post_modified_gmt DESC
			LIMIT " . XT_SITEMAP_POST_LIMIT;
    $posts = $wpdb->get_results($sql);
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $xml_indexs = '';
        if (!empty($posts)) {
            foreach ($posts as $post) {
                if ($post->post_modified == '0000-00-00 00:00:00') {
                    $post_date = $post->post_date;
                } else {
                    $post_date = $post->post_modified;
                }
                $lastmod = date("Y-m-d", strtotime($post_date));
                $xml_indexs.='<url>';
                $xml_indexs.='<loc>' . get_permalink($post->ID) . '</loc>';
                $xml_indexs.='<lastmod>' . $lastmod . '</lastmod>';
                $xml_indexs.='<changefreq>' . XT_SITEMAP_POST_CHANGEFREQ . '</changefreq>';
                $xml_indexs.='<priority>' . XT_SITEMAP_POST_PRIORITY . '</priority>';
                $xml_indexs.='</url>';
            }
        }
        $xml_end = '</urlset>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}

//ALBUM
function xt_sitemap_album($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_ALBUM : XT_OPTION_SITEMAP_ALBUM;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_album($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_album($type);
        }
    }

    return $value;
}

function xt_sitemap_album_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_ALBUM_TIMESTAMP : XT_OPTION_SITEMAP_ALBUM_TIMESTAMP;
    return get_option($option);
}

function xt_sitemap_build_album($type = '') {
    $value = _xt_sitemap_build_album();
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_ALBUM : XT_OPTION_SITEMAP_ALBUM;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_ALBUM_TIMESTAMP : XT_OPTION_SITEMAP_ALBUM_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_album($type = '') {
    global $wpdb;
    $albums = $wpdb->get_results('SELECT id,create_date FROM ' . XT_TABLE_ALBUM . ' ORDER BY id DESC LIMIT ' . XT_SITEMAP_ALBUM_LIMIT);
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $xml_indexs = '';
        if (!empty($albums)) {
            foreach ($albums as $album) {
                $xml_indexs.='<url>';
                $xml_indexs.='<loc>' . get_the_album_url($album->id) . '</loc>';
                $xml_indexs.='<lastmod>' . date('Y-m-d', strtotime($album->create_date)) . '</lastmod>';
                $xml_indexs.='<changefreq>' . XT_SITEMAP_ALBUM_CHANGEFREQ . '</changefreq>';
                $xml_indexs.='<priority>' . XT_SITEMAP_ALBUM_PRIORITY . '</priority>';
                $xml_indexs.='</url>';
            }
        }
        $xml_end = '</urlset>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}

//USER
function xt_sitemap_user($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_USER : XT_OPTION_SITEMAP_USER;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_user($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_user($type);
        }
    }
    return $value;
}

function xt_sitemap_user_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_USER_TIMESTAMP : XT_OPTION_SITEMAP_USER_TIMESTAMP;
    return get_option($option);
}

function xt_sitemap_build_user($type = '') {
    $value = _xt_sitemap_build_user();
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_USER : XT_OPTION_SITEMAP_USER;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_USER_TIMESTAMP : XT_OPTION_SITEMAP_USER_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_user($type = '') {
    global $wpdb;
    $users = $wpdb->get_results('SELECT ID,user_registered FROM ' . $wpdb->users . ' ORDER BY id DESC LIMIT ' . XT_SITEMAP_USER_LIMIT);
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $xml_indexs = '';
        if (!empty($users)) {
            foreach ($users as $user) {
                $xml_indexs.='<url>';
                $xml_indexs.='<loc>' . xt_get_the_user_url($user->ID) . '</loc>';
                $xml_indexs.='<lastmod>' . date('Y-m-d', strtotime($user->user_registered)) . '</lastmod>';
                $xml_indexs.='<changefreq>' . XT_SITEMAP_USER_CHANGEFREQ . '</changefreq>';
                $xml_indexs.='<priority>' . XT_SITEMAP_USER_PRIORITY . '</priority>';
                $xml_indexs.='</url>';
            }
        }
        $xml_end = '</urlset>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}

//OTHER(shares,system)
function xt_sitemap_other($force = false, $type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_OTHER : XT_OPTION_SITEMAP_OTHER;
    $value = '';
    if ($force) {
        $value = xt_sitemap_build_other($type);
    } else {
        $value = get_option($option);
        if (empty($value)) {
            $value = xt_sitemap_build_other($type);
        }
    }

    return $value;
}

function xt_sitemap_other_timestamp($type = '') {
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_OTHER_TIMESTAMP : XT_OPTION_SITEMAP_OTHER_TIMESTAMP;
    return get_option($option);
}

function xt_sitemap_build_other($type = '') {
    $value = _xt_sitemap_build_other();
    $timestamp = current_time('timestamp', 1);
    $option = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_OTHER : XT_OPTION_SITEMAP_OTHER;
    $option_timestamp = $type == 'baidu' ? XT_OPTION_SITEMAP_BAIDU_OTHER_TIMESTAMP : XT_OPTION_SITEMAP_OTHER_TIMESTAMP;
    if (!add_option($option, $value, '', 'no')) {
        update_option($option, $value);
    }
    if (!add_option($option_timestamp, $timestamp, '', 'no')) {
        update_option($option_timestamp, $timestamp);
    }
    return $value;
}

function _xt_sitemap_build_other($type = '') {
    global $wpdb;
    $cats = $wpdb->get_col('SELECT id FROM ' . XT_TABLE_CATALOG . ' WHERE type=\'share\' ORDER BY id DESC');
    if ($type == 'baidu') {
        
    } else {
        $xml_begin = '<?xml version="1.0" encoding="utf-8"?><urlset>';
        $xml_indexs = '';
        $pages = xt_design_syspages();
        $timestamp = current_time('timestamp', 1);
        if (!empty($pages)) {
            foreach ($pages as $page) {
                if (!empty($page['preview'])) {
                    $xml_indexs.='<url>';
                    $xml_indexs.='<loc>' . $page['preview'] . '</loc>';
                    $xml_indexs.='<lastmod>' . date('Y-m-d', $timestamp) . '</lastmod>';
                    $xml_indexs.='<changefreq>' . XT_SITEMAP_OTHER_CHANGEFREQ . '</changefreq>';
                    $xml_indexs.='<priority>' . XT_SITEMAP_OTHER_PRIORITY . '</priority>';
                    $xml_indexs.='</url>';
                }
            }
        }
        if (!empty($cats)) {
            foreach ($cats as $cat) {
                $xml_indexs.='<url>';
                $xml_indexs.='<loc>' . xt_get_shares_search_url(array('cid' => $cat)) . '</loc>';
                $xml_indexs.='<lastmod>' . date('Y-m-d', $timestamp) . '</lastmod>';
                $xml_indexs.='<changefreq>' . XT_SITEMAP_OTHER_CHANGEFREQ . '</changefreq>';
                $xml_indexs.='<priority>' . XT_SITEMAP_OTHER_PRIORITY . '</priority>';
                $xml_indexs.='</url>';
            }
        }
        $xml_end = '</urlset>';
        return $xml_begin . $xml_indexs . $xml_end;
    }
}