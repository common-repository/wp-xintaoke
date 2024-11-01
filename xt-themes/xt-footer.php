<div id="footer"></div>
<?php
$app = xt_get_app_taobao();
if (isset($app['tkpid']) && !empty($app['tkpid'])):
    ?>
    <script type="text/javascript">
        (function(win,doc){
            var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
            if (!win.alimamatk_show) {
                s.charset = "gbk";
                s.async = true;
                s.src = "http://a.alimama.cn/tkapi.js";
                h.insertBefore(s, h.firstChild);
            }
            var o = {
                pid: "<?php echo $app['tkpid']; ?>",
                appkey: "<?php echo $app['appKey'] ?>",
                unid: "<?php echo xt_outercode() ?>"
            }
            win.alimamatk_onload = win.alimamatk_onload || [];
            win.alimamatk_onload.push(o);
        })(window,document);
    </script>
    <?php
endif;
global $xt;
if (!$xt->is_paipais) {
    $app = xt_get_app_yiqifa();
    if (isset($app['wid']) && !empty($app['wid']) && is_numeric($app['wid'])):
        ?>
        <script type='text/javascript'>
            var _jjl = new Date().toDateString().replace(/\s/g, '') + new Date().toTimeString().replace(/:\d{2}:\d{2}\sUTC[+]\d{4}$/g, '');
            document.write(unescape("%3Cscript src='http://p.yiqifa.com/js/juejinlian.js' type='text/javascript'%3E%3C/script%3E"));
            document.write(unescape("%3Cscript src='http://p.yiqifa.com/jj?_jjl.js' type='text/javascript'%3E%3C/script%3E"));
            document.write(unescape("%3Cscript src='http://p.yiqifa.com/js/md.js' type='text/javascript'%3E%3C/script%3E"));
        </script> 
        <script type='text/javascript'>
            try{ 
                var siteId = <?php echo $app['wid'] ?>;
                document.write(unescape("%3Cscript src='http://p.yiqifa.com/jj?sid=" + siteId + "&_jjl.js' type='text/javascript'%3E%3C/script%3E"));
                var jjl = JueJinLian._init(); 
                jjl._addWid(siteId);
                jjl._addE('<?php echo xt_outercode() ?>');
                jjl._addScope(1);
                jjl._run(); 
            }catch(e){} 
        </script>
        <?php
    endif;
}
?>

<!--[if IE 6]>
        <script type="text/javascript" src="<?php echo XT_THEME_URL; ?>/bootstrap-ie.js"></script>
        <script src="//letskillie6.googlecode.com/svn/trunk/2/zh_CN.js"></script>
<![endif]-->
<script type="text/javascript" src="<?php echo XT_THEME_URL; ?>/respond.min.js"></script>
<div style="display:none;">
    <?php
    $codeAnalytics = xt_code_analytics();
    if (!empty($codeAnalytics)) {
        echo $codeAnalytics;
    }
    $codeShare = xt_code_share();
    if (!empty($codeShare)) {
        echo $codeShare;
        if (strpos($codeShare, 'baidu.com') !== false) {
            echo '<style>.bdshare_t{display:block;}</style>';
        } elseif (strpos($codeShare, 'jiathis.com') !== false) {
            echo '<style>.jiathis_style{display:block;}</style>';
        }
    }
    ?>
</div>
</body>
</html>	