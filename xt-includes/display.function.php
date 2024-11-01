<?php

/**
 * xt_obtain_the_title function, for replaacing the page title with the category or product
 * @return string - the new page title
 */
function xt_obtain_the_title($sep) {
    global $xt, $wpdb, $wp_query, $xt_meta, $share, $xt_catalog, $xt_user, $xt_album, $xt_taobao_item;
    if (!$xt->is_xintao)
        return NULL;
    $_site = get_bloginfo('name');
    if ($xt->is_index) {
        $title = $_site;
        $site_description = get_bloginfo('description', 'display');
        if ($site_description) {
            $title = "$_site $sep $site_description";
        }

        $_metas = get_option(XT_OPTION_PAGE_PRE . 'home');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array('{sitetitle}', '{description}');
            $_to = array($_site, $site_description);
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
        }
        return $title;
    } elseif ($xt->is_page) {
        if (isset($wp_query->post) && isset($wp_query->post->ID)) {
            $title = $wp_query->post->post_title;
            $_metas = get_option(XT_OPTION_PAGE_PRE . $wp_query->post->ID);
            $_from = array('{title}', '{sitetitle}');
            $_to = array($title, $_site);
            $_meta_title = $title;
            if (!empty($_metas) && isset($_metas['seos'])) {
                $_meta = $_metas['seos'];
                $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
                $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
                $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');

                $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
                $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            }
            $_title = str_replace($_from, $_to, $_meta_title);
            return ( empty($_title) ? $title : $_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_shares) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'shares');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_price = $_param['price'];
            $prices = xt_prices();
            switch ($_price) {
                case 'low' :
                    $_price = '(价位:0-' . $prices['low']['end'] . '元)';
                    break;
                case 'medium' :
                    $_price = '(价位:' . $prices['medium']['start'] . '-' . $prices['medium']['end'] . '元)';
                    break;
                case 'high' :
                    $_price = '(价位:' . $prices['high']['start'] . '-' . $prices['high']['end'] . '元)';
                    break;
                case 'higher' :
                    $_price = '(价位:' . $prices['higher']['start'] . '元以上)';
                    break;
                default :
                    $_price = '';
            }
            $_from = array(
                '{page}',
                '{sortOrder}',
                '{cat}',
                '{s}',
                '{price}',
                '{sitetitle}'
            );
            $_sortOrder = array(
                'newest' => '最新',
                'popular' => '潮流',
                'hot' => '最热'
            );
            $_to = array(
                $_param['page'] > 1 ? '第' . $_param['page'] . '页 ' : '',
                isset($_sortOrder[$_param['sortOrder']]) ? $_sortOrder[$_param['sortOrder']] : '',
                (!empty($xt_catalog) ? $xt_catalog->title : ''),
                $_param['s'],
                $_price,
                $_site
            );
            if (empty($xt_catalog)) {
                $_meta_keywords = preg_replace('/(\{sortOrder\}){0,1}\{cat\}(,|\s|，)?/i', '', $_meta_keywords);
            }
            if (empty($_param['s'])) {
                $_meta_keywords = preg_replace('/(\{sortOrder\}){0,1}\{s\}(,|\s|，)?/i', '', $_meta_keywords);
            }
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_share) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'share');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array(
                '{title}',
                '{price}',
                '{sitetitle}',
                '{tags}',
                '{user}',
                '{share}'
            );
            $_to = array(
                $share->title,
                $share->cache_data['item']['price'],
                $_site,
                $share->tags,
                $share->user_name,
                $share->content
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_user) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'user');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array(
                '{user}',
                '{sitetitle}',
                '{description}'
            );
            if (empty($xt_user->display_name))
                $xt_user->display_name = $xt_user->user_login;
            $user_name = $wpdb->escape($xt_user->display_name);
            $_description = xt_html(xt_get_the_user_description());
            $_to = array(
                $user_name,
                $_site,
                $_description
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    }elseif ($xt->is_albums) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'albums');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];

            $_from = array(
                '{page}',
                '{sortOrder}',
                '{s}',
                '{sitetitle}'
            );
            $_sortOrder = array(
                'newest' => '最新',
                'popular' => '潮流',
                'hot' => '最热'
            );
            $_to = array(
                $_param['page'] > 1 ? '第' . $_param['page'] . '页 ' : '',
                isset($_sortOrder[$_param['sortOrder']]) ? $_sortOrder[$_param['sortOrder']] : '',
                $_param['s'],
                $_site
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_album) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'album');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array(
                '{user}',
                '{sitetitle}',
                '{albumtitle}',
                '{albumcontent}'
            );
            if (empty($xt_user->display_name))
                $xt_user->display_name = $xt_user->user_login;
            $user_name = $wpdb->escape($xt_user->display_name);
            $_to = array(
                $user_name,
                $_site,
                $xt_album->title,
                $xt_album->content
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    }
    elseif ($xt->is_daogous) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'daogous');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_cat = '最新';
            $_from = array(
                '{cat}',
                '{s}',
                '{sitetitle}'
            );
            if (!empty($_param['cid'])) {
                $xt_daogou_itemcat = xt_daogou_item_cat($_param['cid']);
                if (!empty($xt_daogou_itemcat)) {
                    $_cat = $xt_daogou_itemcat->name;
                }
            }
            $_to = array(
                $_cat,
                $_param['s'],
                $_site
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_helps) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'helps');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_cat = '常见问题';
            $_from = array(
                '{cat}',
                '{s}',
                '{sitetitle}'
            );
            if (!empty($_param['cid'])) {
                $xt_help_itemcat = xt_help_item_cat($_param['cid']);
                if (!empty($xt_help_itemcat)) {
                    $_cat = $xt_help_itemcat->name;
                }
            }
            $_to = array(
                $_cat,
                $_param['s'],
                $_site
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);

            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_daogou || $xt->is_help) {
        $__page = 'daogou';
        if ($xt->is_help) {
            $__page = 'help';
        }
        $_metas = get_option(XT_OPTION_PAGE_PRE . $__page);
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array(
                '{sitetitle}',
                '{blogtitle}',
                '{blogtags}',
                '{blogexcerpt}'
            );
            $_postexcerpt = '';
            $_post = get_queried_object();
            if ($_post->post_excerpt) {
                $_postexcerpt = $_post->post_excerpt;
            } else {
                $_postexcerpt = wp_trim_words(strip_tags($_post->post_content), 180);
            }
            $_posttags = array();
            $_tags = wp_get_post_tags($_post->ID);
            if (!empty($_tags)) {
                foreach ($_tags as $_tag) {
                    $_posttags[] = $_tag->name;
                }
            }
            $_posttags = implode(' ', $_posttags);
            $_to = array(
                $_site,
                $_post->post_title,
                $_posttags,
                $_postexcerpt
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_account) {
        if (empty($xt_user->display_name))
            $xt_user->display_name = $xt_user->user_login;
        $user_name = $wpdb->escape($xt_user->display_name);
        return $user_name . $sep . '管理中心' . " " . $sep . " " . $_site;
    } elseif ($xt->is_taobaos) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'taobaos');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyword'];
            $_start_price = $_param['start_price'];
            $_end_price = $_param['end_price'];
            $_sort = $_param['sort'];
            $_mall = $_param['mall_item'];
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_taobao = xt_sort_taobao();
                if (isset($xt_sort_taobao[$_sort])) {
                    $_sort = $xt_sort_taobao[$_sort]['seo'];
                } else {
                    $_sort = '人气';
                }
            }
            if (empty($_sort)) {
                $_sort = '人气';
            }
            if (!empty($_param['cid'])) {
                $xt_taobao_itemcat = xt_taobao_item_cat(absint($_param['cid']));
                if (!empty($xt_taobao_itemcat)) {
                    $_cat = $xt_taobao_itemcat['name'];
                }
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{s}',
                '{start_price}',
                '{end_price}',
                '{sort}',
                '{mall}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_cat,
                $_s,
                $_start_price ? '最低价格:' . $_start_price : '',
                $_end_price ? '最高价格:' . $_end_price : '',
                $_sort,
                $_mall ? '天猫' : ''
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_taobao) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'taobao');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_from = array(
                '{sitetitle}',
                '{itemtitle}',
                '{itemprice}',
            );
            $_to = array(
                $_site,
                $xt_taobao_item->title,
                $xt_taobao_item->price,
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_shops) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'shops');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyword'];
            $_start_credit = $_param['start_credit'];
            $_end_credit = $_param['end_credit'];
            $_mall = $_param['only_mall'];
            $_cat = '';

            if (!empty($_param['cid'])) {
                $xt_taobao_shopcat = xt_taobao_shopcat(absint($_param['cid']));
                if (!empty($xt_taobao_shopcat)) {
                    $_cat = $xt_taobao_shopcat['name'];
                }
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{s}',
                '{start_credit}',
                '{end_credit}',
                '{mall}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_cat,
                $_s,
                $_start_credit ? '最低信用:' . $_start_credit : '',
                $_end_credit ? '最高信用:' . $_end_credit : '',
                $_mall ? '天猫' : ''
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_paipais) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'paipais');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyWord'];
            $_start_price = $_param['begPrice'];
            $_end_price = $_param['endPrice'];
            $_sort = $_param['orderStyle'];
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_paipai = xt_sort_paipai();
                if (isset($xt_sort_paipai[$_sort])) {
                    $_sort = $xt_sort_paipai[$_sort]['seo'];
                } else {
                    $_sort = '';
                }
            }
            if (!empty($_param['classId'])) {
                $xt_paipai_itemcat = xt_paipai_item_cat(absint($_param['classId']));
                if (!empty($xt_paipai_itemcat)) {
                    $_cat = $xt_paipai_itemcat['name'];
                }
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{s}',
                '{start_price}',
                '{end_price}',
                '{sort}'
            );
            $_to = array(
                $_param['pageIndex'] > 1 ? '第' . $_param['pageIndex'] . '页 ' : '',
                $_site,
                $_cat,
                $_s,
                $_start_price > 0 ? ('最低价格:' . $_start_price) : '',
                $_end_price > 0 ? ('最高价格:' . $_end_price) : '',
                $_sort
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_bijias) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'bijias');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyword'];
            $_start_price = $_param['minprice'];
            $_end_price = $_param['maxprice'];
            $_sort = $_param['orderby'];
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_bijia = xt_sort_bijia();
                if (isset($xt_sort_bijia[$_sort])) {
                    $_sort = $xt_sort_bijia[$_sort]['seo'];
                } else {
                    $_sort = '';
                }
            }
            if (!empty($_param['catid']) && $_param['catid'] != -1) {
                $xt_bijia_itemcat = xt_bijia_item_cat($_param['catid']);
                if (!empty($xt_bijia_itemcat)) {
                    $_cat = $xt_bijia_itemcat['name'];
                }
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{s}',
                '{start_price}',
                '{end_price}',
                '{sort}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_cat,
                $_s,
                $_start_price > 0 ? ('最低价格:' . $_start_price) : '',
                $_end_price > 0 ? ('最高价格:' . $_end_price) : '',
                $_sort
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_tuans) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'tuans');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyword'];
            $_price = $_param['price'];
            $_sort = $_param['orderby'];
            $_city = '';
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_tuan = xt_sort_tuan();
                if (isset($xt_sort_tuan[$_sort])) {
                    $_sort = $xt_sort_tuan[$_sort]['seo'];
                } else {
                    $_sort = '';
                }
            }
            if (!empty($_param['catid']) && $_param['catid'] != -1) {
                $xt_tuan_itemcat = xt_tuan_item_cat($_param['catid']);
                if (!empty($xt_tuan_itemcat)) {
                    $_cat = $xt_tuan_itemcat['name'];
                }
            }
            if (!empty($_param['city_id'])) {
                $xt_yiqifa_tuan_cities = xt_yiqifa_tuan_city();
                if (isset($xt_yiqifa_tuan_cities[$_param['city_id']])) {
                    $_city = $xt_yiqifa_tuan_cities[$_param['city_id']]['name_cn'];
                }
            }
            switch ($_price) {
                case 'low' :
                    $_price = '50元以下';
                    break;
                case 'medium' :
                    $_price = '50-100元';
                    break;
                case 'high' :
                    $_price = '100元以上';
                    break;
                default :
                    $_price = '';
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{city}',
                '{cat}',
                '{s}',
                '{price}',
                '{sort}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_city,
                $_cat,
                $_s,
                $_price,
                $_sort
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_temais) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'temais');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_sort = $_param['sort'];
            $_city = '';
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_temai = xt_sort_temai();
                if (isset($xt_sort_temai[$_sort])) {
                    $_sort = $xt_sort_temai[$_sort]['seo'];
                } else {
                    $_sort = '';
                }
            }
            if (!empty($_param['cat'])) {
                $xt_temai_itemcat = xt_temai_item_cat($_param['cat']);
                if (!empty($xt_temai_itemcat)) {
                    $_cat = $xt_temai_itemcat['name'];
                }
            }

            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{sort}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_cat,
                $_sort
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_coupons) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'coupons');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_param = $wp_query->query_vars['xt_param'];
            $_s = $_param['keyword'];
            $_sort = $_param['sort'];
            $_mall = $_param['shop_type'];
            $_cat = '';
            if (!empty($_sort)) {
                $xt_sort_taobao = xt_sort_taobao();
                if (isset($xt_sort_taobao[$_sort])) {
                    $_sort = $xt_sort_taobao[$_sort]['seo'];
                } else {
                    $_sort = '人气';
                }
            }
            if (empty($_sort)) {
                $_sort = '人气';
            }
            if (!empty($_param['cid'])) {
                $xt_taobao_itemcat = xt_taobao_item_cat(absint($_param['cid']));
                if (!empty($xt_taobao_itemcat)) {
                    $_cat = $xt_taobao_itemcat['name'];
                }
            }
            $_from = array(
                '{page}',
                '{sitetitle}',
                '{cat}',
                '{s}',
                '{sort}',
                '{mall}'
            );
            $_to = array(
                $_param['page_no'] > 1 ? '第' . $_param['page_no'] . '页 ' : '',
                $_site,
                $_cat,
                $_s,
                $_sort,
                $_mall ? '天猫' : ''
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_invite) {
        $_metas = get_option(XT_OPTION_PAGE_PRE . 'invite');
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            if (empty($xt_user->display_name))
                $xt_user->display_name = $xt_user->user_login;
            $user_name = $wpdb->escape($xt_user->display_name);
            $jifen = intval(xt_fanxian_registe_jifen());
            if ($jifen > 100) {
                $jifen = intval($jifen / 100);
            } else {
                $jifen = 0;
            }
            $cash = intval(xt_fanxian_registe_cash());
            $amount = $cash + $jifen;
            $_from = array(
                '{sitetitle}',
                '{user}',
                '{cash}',
                '{jifenbao}',
                '{amount}'
            );
            $_to = array(
                $_site,
                $user_name,
                $cash,
                $jifen,
                $amount
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    } elseif ($xt->is_brands || $xt->is_stars || $xt->is_activities || $xt->is_taoquan || $xt->is_malls || $xt->is_error404) {
        $__page = 'stars';
        if ($xt->is_brands) {
            $__page = 'brands';
        } elseif ($xt->is_activities) {
            $__page = 'activities';
        } elseif ($xt->is_taoquan) {
            $__page = 'taoquan';
        } elseif ($xt->is_malls) {
            $__page = 'malls';
        } elseif ($xt->is_error404) {
            $__page = 'error404';
        }
        $_metas = get_option(XT_OPTION_PAGE_PRE . ($__page));
        if (!empty($_metas) && isset($_metas['seos'])) {
            $_meta = $_metas['seos'];
            $_meta_title = htmlentities(stripslashes($_meta['title']), ENT_QUOTES, 'UTF-8');
            $_meta_keywords = htmlentities(stripslashes($_meta['keywords']), ENT_QUOTES, 'UTF-8');
            $_meta_description = htmlentities(stripslashes($_meta['description']), ENT_QUOTES, 'UTF-8');
            $_from = array(
                '{sitetitle}'
            );
            $_to = array(
                $_site
            );
            $xt_meta['keywords'] = str_replace($_from, $_to, $_meta_keywords);
            $xt_meta['description'] = str_replace($_from, $_to, $_meta_description);
            return str_replace($_from, $_to, $_meta_title) . " " . $sep . " " . $_site;
        }
    }
    return null;
}

function xt_title($input, $sep) {
    $output = xt_obtain_the_title($sep);
    if ($output != null) {
        return $output;
    }
    return $input;
}

add_filter('wp_title', 'xt_title', 100, 2);

function xt_meta() {
    global $xt, $xt_meta;
    if ($xt->is_xintao && !empty($xt_meta)) {
        if (!empty($xt_meta['keywords'])) {
            ?>
            <meta name="keywords" content="<?php echo esc_html($xt_meta['keywords']); ?>" />
            <?php
        }
        if (!empty($xt_meta['description'])) {
            ?>
            <meta name="description" content="<?php echo esc_html($xt_meta['description']); ?>" />
            <?php
        }
    }
}
?>