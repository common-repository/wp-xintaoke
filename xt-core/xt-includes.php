<?php

if (defined('XT_LOAD_DEPRECATED'))
    require_once( XT_FILE_PATH . '/xt-core/xt-deprecated.php' );

// Start including the rest of the plugin here
require_once( XT_FILE_PATH . '/xt-includes/widgets.php' );
require_once( XT_FILE_PATH . '/xt-includes/default-page-widgets.php' );
require_once( XT_FILE_PATH . '/xt-includes/default-system-widgets.php' );
require_once( XT_FILE_PATH . '/xt-includes/default-widgets.php' );
require_once( XT_FILE_PATH . '/xt-includes/share.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-catalog.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-tag.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-user.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-share.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-favorite.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-comment.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-jifen.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-album.php' );
require_once( XT_FILE_PATH . '/xt-includes/query-fanxian.php' );
require_once( XT_FILE_PATH . '/xt-includes/general-template.php' );
require_once( XT_FILE_PATH . '/xt-includes/template-share.php' );
require_once( XT_FILE_PATH . '/xt-includes/template-comment.php' );
require_once( XT_FILE_PATH . '/xt-includes/template-album.php' );
require_once( XT_FILE_PATH . '/xt-includes/breadcrumbs.class.php' );
require_once( XT_FILE_PATH . '/xt-includes/ajax.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/report.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/display.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/theme.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/taobao.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/paipai.function.php' );
require_once( XT_FILE_PATH . '/xt-includes/yiqifa2.function.php' );


// Admin
if (is_admin()) {
    require_once( XT_FILE_PATH . '/xt-admin/admin.php' );
    require_once( XT_FILE_PATH . '/xt-admin/admin_ajax.php' );
    require_once( XT_FILE_PATH . '/xt-admin/admin_help.php' );
}
require_once( XT_FILE_PATH . '/xt-core/api.php' );
?>