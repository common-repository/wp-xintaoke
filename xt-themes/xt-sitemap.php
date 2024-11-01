<?php

global $wp_query;
include XT_PLUGIN_DIR . '/xt-includes/sitemap.function.php';
$xt_action = isset($wp_query->query_vars['xt_action']) ? $wp_query->query_vars['xt_action'] : 'sitemap';
$force = isset($_GET['force']) ? true : false;
switch ($xt_action) {
    case 'sitemapshare':
        echo xt_sitemap_share($force);
        break;
    case 'sitemappost':
        echo xt_sitemap_post($force);
        break;
    case 'sitemapalbum':
        echo xt_sitemap_album($force);
        break;
    case 'sitemapuser':
        echo xt_sitemap_user($force);
        break;
    case 'sitemapother':
        echo xt_sitemap_other($force);
        break;
    default:
        echo xt_sitemap_index();
        break;
}

