<?php

/**
 * xt has breadcrumbs function
 * @return boolean - true if we have and use them, false otherwise
 */
function xt_has_breadcrumbs() {
    global $xt_breadcrumbs;
    $xt_breadcrumbs = new xt_breadcrumbs();

    if (($xt_breadcrumbs->breadcrumb_count > 0)) {
        return true;
    } else {
        return false;
    }
}

/**
 * xt have breadcrumbs function
 * @return boolean - true if we have breadcrumbs to loop through
 */
function xt_have_breadcrumbs() {
    global $xt_breadcrumbs;

    return $xt_breadcrumbs->have_breadcrumbs();
}

/**
 * xt the breadcrumbs function
 * @return nothing - iterate through the breadcrumbs
 */
function xt_the_breadcrumb() {
    global $xt_breadcrumbs;

    $xt_breadcrumbs->the_breadcrumb();
}

/**
 * xt breadcrumb name function
 * @return string - the breadcrumb name 
 */
function xt_breadcrumb_name() {
    global $xt_breadcrumbs;

    return $xt_breadcrumbs->breadcrumb['name'];
}

/**
 * xt breadcrumb URL function
 * @return string - the breadcrumb URL
 */
function xt_breadcrumb_url() {
    global $xt_breadcrumbs;

    if ($xt_breadcrumbs->breadcrumb['url'] == '') {
        return false;
    } else {
        return $xt_breadcrumbs->breadcrumb['url'];
    }
}

/**
 * Output breadcrumbs if configured
 * @return None - outputs breadcrumb HTML
 */
function xt_output_breadcrumbs($echo = true, $after = array(), $before = array(), $right = '') {
    global $xt_breadcrumbs, $xt_current_widget;
    if (!xt_has_breadcrumbs()) {
        return;
    }
    $output = '<ul class="breadcrumb clearfix">';
    if (!empty($before)) {
        array_unshift($xt_breadcrumbs->breadcrumbs, $before);
    }
    if (!empty($after)) {
        $xt_breadcrumbs->breadcrumbs[] = $after;
    }
    $xt_breadcrumbs->breadcrumb_count = count($xt_breadcrumbs->breadcrumbs);
    $_index = 0;
    while (xt_have_breadcrumbs()) {
        xt_the_breadcrumb();
        $output.=_xt_output_breadcrumb($_index++);
    }
    if ($xt_current_widget == 'systemais') {
        $right = '';
    }
    $output .=$right;
    $output .= '</ul>';
    if ($echo) {
        echo $output;
    } else {
        return $output;
    }
}

function _xt_output_breadcrumb($index) {
    global $xt_breadcrumbs;
    $name = xt_breadcrumb_name();
    $url = xt_breadcrumb_url();
    $divider = '<span class="divider">></span>';
    if ($xt_breadcrumbs->breadcrumb_count <= ($index + 1)) {
        $divider = '';
    }
    if (!empty($url)) {
        return '<li><a href="' . $url . '">' . $name . '</a>' . $divider . '</li>';
    }
    return '<li class="active">' . $name . $divider . '</li>';
}

/**
 * xt_breadcrumbs class.
 * 
 */
class xt_breadcrumbs {

    var $breadcrumbs;
    var $breadcrumb_count = 0;
    var $current_breadcrumb = -1;
    var $breadcrumb;

    /**
     * xt__breadcrumbs function.
     * 
     * @access public
     * @return void
     */
    function xt_breadcrumbs() {
        global $xt, $wp_query;
        $this->breadcrumbs = array();
        if ($xt->is_taobaos || $xt->is_coupons) {
            $this->breadcrumbs[] = array(
                'name' => '所有分类',
                'url' => xt_site_url($xt->is_coupons ? 'coupon' : 'taobao')
            );
            $_param = $wp_query->query_vars['xt_param'];
            if (!empty($_param['cid'])) {
                $xt_taobao_itemcat = xt_taobao_item_cat(absint($_param['cid']));
                if (!empty($xt_taobao_itemcat)) {
                    $this->breadcrumbs[] = array(
                        'name' => htmlentities($xt_taobao_itemcat['name'], ENT_QUOTES, 'UTF-8'),
                        'url' => !empty($_param['keyword']) ? ($xt->is_coupons ? xt_get_coupon_search_url(array('cid' => $xt_taobao_itemcat['cid'])) : xt_get_taobao_search_url(array('cid' => $xt_taobao_itemcat['cid']))) : ''
                    );
                }
            }
            if (!empty($_param['keyword'])) {
                $_s = htmlentities(trim($_param['keyword']), ENT_QUOTES, 'UTF-8');
                $this->breadcrumbs[] = array(
                    'name' => $_s,
                    'url' => ''
                );
            }
        } elseif ($xt->is_shops) {
            $_param = $wp_query->query_vars['xt_param'];
            if (!empty($_param['keyword'])) {
                $this->breadcrumbs[] = array(
                    'name' => '所有分类',
                    'url' => xt_get_shop_search_url(array('keyword' => $_param['keyword']))
                );
            }
            if (!empty($_param['cid'])) {
                $xt_taobao_shopcat = xt_taobao_shopcat(absint($_param['cid']));
                if (!empty($xt_taobao_shopcat)) {
                    $this->breadcrumbs[] = array(
                        'name' => htmlentities($xt_taobao_shopcat['name'], ENT_QUOTES, 'UTF-8'),
                        'url' => !empty($_param['keyword']) ? (xt_get_shop_search_url(array('cid' => $xt_taobao_shopcat['cid']))) : ''
                    );
                }
            }
            if (!empty($_param['keyword'])) {
                $_s = htmlentities(trim($_param['keyword']), ENT_QUOTES, 'UTF-8');
                $this->breadcrumbs[] = array(
                    'name' => $_s,
                    'url' => ''
                );
            }
        } elseif ($xt->is_paipais) {
            $this->breadcrumbs[] = array(
                'name' => '所有分类',
                'url' => xt_site_url('paipai')
            );
            $_param = $wp_query->query_vars['xt_param'];
            if (!empty($_param['classId'])) {
                $xt_paipai_itemcat = xt_paipai_item_cat(absint($_param['classId']));
                if (!empty($xt_paipai_itemcat)) {
                    $this->breadcrumbs[] = array(
                        'name' => htmlentities($xt_paipai_itemcat['name'], ENT_QUOTES, 'UTF-8'),
                        'url' => !empty($_param['keyWord']) ? xt_get_paipai_search_url(array('classId' => $xt_paipai_itemcat['cid'])) : ''
                    );
                }
            }
            if (!empty($_param['keyWord'])) {
                $_s = htmlentities(trim($_param['keyWord']), ENT_QUOTES, 'UTF-8');
                $this->breadcrumbs[] = array(
                    'name' => $_s,
                    'url' => ''
                );
            }
        } elseif ($xt->is_bijias) {
            $this->breadcrumbs[] = array(
                'name' => '所有分类',
                'url' => xt_site_url('bijia')
            );
            $_param = $wp_query->query_vars['xt_param'];
            if (!empty($_param['category']) && $_param['category'] != -1) {
                $xt_bijia_itemcat = xt_bijia_item_cat(absint($_param['category']));
                if (!empty($xt_bijia_itemcat)) {
                    $this->breadcrumbs[] = array(
                        'name' => htmlentities($xt_bijia_itemcat['catName'], ENT_QUOTES, 'UTF-8'),
                        'url' => !empty($_param['keyword']) ? xt_get_bijia_search_url(array('category' => $xt_bijia_itemcat['catId'])) : ''
                    );
                }
            }
            if (!empty($_param['keyword'])) {
                $_s = htmlentities(trim($_param['keyword']), ENT_QUOTES, 'UTF-8');
                $this->breadcrumbs[] = array(
                    'name' => $_s,
                    'url' => ''
                );
            }
        } elseif ($xt->is_temais) {
            $_param = $wp_query->query_vars['xt_param'];
            if (!empty($_param['cat']) && $_param['cat'] != -1) {
                $xt_temai_itemcat = xt_temai_item_cat(absint($_param['cat']));
                if (!empty($xt_temai_itemcat)) {
                    if (!empty($xt_temai_itemcat['parent_cid'])) {
                        $this->breadcrumbs[] = array(
                            'name' => htmlentities($xt_temai_itemcat['parent_name'], ENT_QUOTES, 'UTF-8'),
                            'url' => xt_get_temai_search_url(array('cat' => $xt_temai_itemcat['parent_cid']))
                        );
                    }
                    $this->breadcrumbs[] = array(
                        'name' => htmlentities($xt_temai_itemcat['name'], ENT_QUOTES, 'UTF-8'),
                        'url' => ''
                    );
                }
            }
        }
        $this->breadcrumbs = apply_filters('xt_breadcrumbs', $this->breadcrumbs);
        $this->breadcrumb_count = count($this->breadcrumbs);
    }

    /**
     * next_breadcrumbs function.
     * 
     * @access public
     * @return void
     */
    function next_breadcrumbs() {
        $this->current_breadcrumb++;
        $this->breadcrumb = $this->breadcrumbs[$this->current_breadcrumb];
        return $this->breadcrumb;
    }

    /**
     * the_breadcrumb function.
     * 
     * @access public
     * @return void
     */
    function the_breadcrumb() {
        $this->breadcrumb = $this->next_breadcrumbs();
    }

    /**
     * have_breadcrumbs function.
     * 
     * @access public
     * @return void
     */
    function have_breadcrumbs() {
        if ($this->current_breadcrumb + 1 < $this->breadcrumb_count) {
            return true;
        } else
        if ($this->current_breadcrumb + 1 == $this->breadcrumb_count && $this->breadcrumb_count > 0) {
            $this->rewind_breadcrumbs();
        }
        return false;
    }

    /**
     * rewind_breadcrumbs function.
     * 
     * @access public
     * @return void
     */
    function rewind_breadcrumbs() {
        $this->current_breadcrumb = -1;
        if ($this->breadcrumb_count > 0) {
            $this->breadcrumb = $this->breadcrumbs[0];
        }
    }

}

