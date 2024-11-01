<?php

/**
 * Plugin Name: 新淘客WordPress插件
 * Plugin URI:	http://plugin.xintaonet.com
 * Description: 支持主流电子商务平台(淘宝，拍拍，其他B2C商城)推广赚取佣金，支持会员购物分享，购买返现，推广返现，分享返现，站长可个性化装修设计所有页面。
 * Version: 1.0.0
 * Author:  fxy060608
 * Author URI: http://plugin.xintaonet.com
 * */
if (function_exists('saeAutoLoader')) {
    define('IS_CLOUD', true);
    define('IS_SAE', true);
    define('IS_BAE', false);
} elseif (isset($_SERVER['HTTP_BAE_ENV_APPID'])) {
    define('IS_CLOUD', true);
    define('IS_BAE', true);
    define('IS_SAE', false);
} else {
    define('IS_SAE', false);
    define('IS_BAE', false);
    define('IS_CLOUD', false);
}

/**
 * WP_Xintaoke
 *
 * Main XT Plugin Class
 *
 * @package wp-xintaoke
 */
class WP_Xintaoke {

    /**
     * Start XT on plugins loaded
     */
    function WP_Xintaoke() {
        add_action('plugins_loaded', array(
            $this,
            'init'
                ), 8);
    }

    /**
     * Takes care of loading up XT
     */
    function init() {
        // Previous to initializing
        do_action('xt_pre_init');

        // Initialize
        $this->start();
        $this->constants();
        $this->includes();
        $this->load();

        // Finished initializing
        do_action('xt_init');
    }

    /**
     * Initialize the basic XT constants
     */
    function start() {
        // Set the core file path
        define('XT_FILE_PATH', dirname(__FILE__));

        // Define the path to the plugin folder
        define('XT_DIR_NAME', basename(XT_FILE_PATH));

        // Define the URL to the plugin folder
        define('XT_FOLDER', dirname(plugin_basename(__FILE__)));
        define('XT_URL', plugins_url('', __FILE__));

        require_once (XT_FILE_PATH . '/xt-languages/lang.php');
        // Place your custom code (actions/filters) in a file called
        // '/plugins/xt-custom.php' and it will be loaded before anything else.
        if (file_exists(WP_PLUGIN_DIR . '/xt-custom.php'))
            require (WP_PLUGIN_DIR . '/xt-custom.php');

        // Finished starting
        do_action('xt_started');
    }

    /**
     * Setup the XT core constants
     */
    function constants() {
        if ((isset($_REQUEST['action']) && in_array($_REQUEST['action'], array(
                    'xt_ajax_api',
                    'xt_ajax_api_app',
                    'xt_ajax_api_version'
                ))) || is_admin()) {
            
        } else {
            require_once (XT_FILE_PATH . '/xt-core/360_safe3.php');
        }
        // Define globals and constants used by wp-xintaoke
        require_once (XT_FILE_PATH . '/xt-core/xt-constants.php');

        // Load the XT core constants
        xt_core_constants();

        // Is WordPress Multisite
        xt_core_is_multisite();

        // Start the xt session
        xt_core_load_session();

        // Which version of XT
        xt_core_constants_version_processing();

        // XT Table names and related constants
        xt_core_constants_table_names();

        // Uploads directory info
        xt_core_constants_uploads();

        // Any additional constants can hook in here
        do_action('xt_constants');
    }

    /**
     * Include the rest of XT's files
     */
    function includes() {
        require_once (XT_FILE_PATH . '/xt-core/xt-functions.php');
        require_once (XT_FILE_PATH . '/xt-core/xt-installer.php');
        require_once (XT_FILE_PATH . '/xt-core/xt-includes.php');

        // Any additional file includes can hook in here
        do_action('xt_includes');
    }

    /**
     * Setup the XT core
     */
    function load() {
        // Before setup
        do_action('xt_pre_load');

        // Legacy action
        do_action('xt_before_init');

        // Setup the core XT globals
        xt_core_setup_globals();

        // Load the thumbnail sizes
        //xt_core_load_thumbnail_sizes();
        // Set page title array for important XT pages
        //xt_core_load_page_titles();
        //register_theme_directory(XT_PLUGIN_DIR . '/xt-theme');
        // XT is fully loaded
        do_action('xt_loaded');
    }

    /**
     * XT Activation Hook
     */
    function install() {
        global $wp_version;
        if ((float) $wp_version < 3.0) {
            deactivate_plugins(plugin_basename(__FILE__)); // Deactivate ourselves
            wp_die('新淘客要求WordPress版本位3.0以上,请升级您的WordPress版本', '版本不兼容', array(
                'back_link' => true
            ));
            return;
        }
        define('XT_FILE_PATH', dirname(__FILE__));
        require_once (XT_FILE_PATH . '/xt-core/xt-installer.php');
        $this->constants();
        xt_install();
    }

    public function deactivate() {
        foreach (wp_get_schedules() as $cron => $schedule) {
            wp_clear_scheduled_hook("xt_{$cron}_cron_task");
        }
    }

}

// Start XT
$XT = new WP_Xintaoke();

// Activation
register_activation_hook(__FILE__, array(
    $XT,
    'install'
));
register_deactivation_hook(__FILE__, array(
    $XT,
    'deactivate'
));

if (!function_exists('wp_new_user_notification'))
    :

    /**
     * Notify the blog admin of a new user, normally via email.
     *
     * @since 2.0
     *
     * @param int $user_id User ID
     * @param string $plaintext_pass Optional. The user's plaintext password
     */
    function wp_new_user_notification($user_id, $plaintext_pass = '', $flag = '') {
        if (func_num_args() > 1 && $flag !== 1)
            return;
        //	$user = new WP_User($user_id);
        //
	//	$user_login = stripslashes($user->user_login);
        //	$user_email = stripslashes($user->user_email);
        //
	//	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
        //	// we want to reverse this for the plain text arena of emails.
        //	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        //
	//	$message = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
        //	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        //	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
        //
	//	@ wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);
        //
	//	if (empty ($plaintext_pass))
        //		return;
        //
	//	$message = sprintf(__('Username: %s'), $user_login) . "\r\n";
        //	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
        //	$message .= wp_login_url() . "\r\n";
        //
	//	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
    }

endif;
if (IS_BAE) {

    if (!function_exists('wp_mail')) :

        function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
            require_once ABSPATH . WPINC . '/Bcms.class.php';
            $mail = get_option(XT_OPTION_MAIL);
            $sitename = strtolower($_SERVER['SERVER_NAME']);
            if (substr($sitename, 0, 4) == 'www.') {
                $sitename = substr($sitename, 4);
            }
            $from_email = 'no-reply@' . $sitename;
            if (!empty($mail) && !empty($mail['smtp_user']) && validate_email($mail['smtp_user'], false)) {
                $from_email = $mail['smtp_user'];
            }
            $bcms = new Bcms ();
            $ret = $bcms->mail(BCMS_QUEUE, $message, array($to), array(Bcms::FROM => $from_email, Bcms::MAIL_SUBJECT => $subject));
            if (false === $ret) {
                return false;
            } else {
                return true;
            }
        }

    endif;
}

if (!function_exists('phpmailer_init_smtp')) :

    function phpmailer_init_smtp($phpmailer) {
        $mail = get_option(XT_OPTION_MAIL);
        $mailer = $mail['mailer'];
        $smtp_host = $mail['smtp_host'];
        $smtp_port = $mail['smtp_port'];
        $smtp_user = $mail['smtp_user'];
        $smtp_pass = $mail['smtp_pass'];
        // Check that mailer is not blank, and if mailer=smtp, host is not blank
        if (!$mailer || ($mailer == 'smtp' && !$smtp_host )) {
            return;
        }

        // Set the mailer type as per config above, this overrides the already called isMail method
        $phpmailer->Mailer = $mailer;

        // Set the Sender (return-path) if required
        $phpmailer->Sender = $phpmailer->From;

        // Set the SMTPSecure value, if set to none, leave this blank
        $phpmailer->SMTPSecure = '';

        // If we're sending via SMTP, set the host
        if ($mailer == "smtp") {

            // Set the SMTPSecure value, if set to none, leave this blank
            $phpmailer->SMTPSecure = '';

            // Set the other options
            $phpmailer->Host = $smtp_host;
            $phpmailer->Port = $smtp_port;

            // If we're using smtp auth, set the username & password
            $phpmailer->SMTPAuth = TRUE;
            $phpmailer->Username = $smtp_user;
            $phpmailer->Password = $smtp_pass;
        }
    }

// End of phpmailer_init_smtp() function definition
endif;
if (!function_exists('validate_email')) :

    function validate_email($email, $check_domain = true) {
        if (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' .
                        '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' .
                        '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email)) {
            if ($check_domain && function_exists('checkdnsrr')) {
                list (, $domain) = explode('@', $email);

                if (checkdnsrr($domain . '.', 'MX') || checkdnsrr($domain . '.', 'A')) {
                    return true;
                }
                return false;
            }
            return true;
        }
        return false;
    }

// End of validate_email() function definition
endif;
if (!function_exists('wp_mail_smtp_mail_from')) :

    function wp_mail_smtp_mail_from($orig) {

        // This is copied from pluggable.php lines 348-354 as at revision 10150
        // http://trac.wordpress.org/browser/branches/2.7/wp-includes/pluggable.php#L348
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }

        $default_from = 'wordpress@' . $sitename;
        // End of copied code
        // If the from email is not the default, return it unchanged
        if ($orig != $default_from) {
            return $orig;
        }
        $mail = get_option(XT_OPTION_MAIL);
        if (!empty($mail) && !empty($mail['smtp_user']) && validate_email($mail['smtp_user'], false)) {
            return $mail['smtp_user'];
        }

        // If in doubt, return the original value
        return $orig;
    }

// End of wp_mail_smtp_mail_from() function definition
endif;
if (!function_exists('wp_mail_smtp_mail_from_name')) :

    function wp_mail_smtp_mail_from_name($orig) {

        // Only filter if the from name is the default
        if ($orig == 'WordPress') {
            return get_bloginfo('name');
        }

        // If in doubt, return the original value
        return $orig;
    }

// End of wp_mail_smtp_mail_from_name() function definition
endif;
add_action('phpmailer_init', 'phpmailer_init_smtp');
add_filter('wp_mail_from', 'wp_mail_smtp_mail_from');
add_filter('wp_mail_from_name', 'wp_mail_smtp_mail_from_name');
//Multiple emails
if (version_compare($GLOBALS['wp_version'], '3.3', '<') && !function_exists('get_user_by_email')) {

    function get_user_by_email($email) {
        global $xt_during_user_creation;
        if ($xt_during_user_creation)
            return false;
        return get_user_by('email', $email);
    }

}
if (version_compare($GLOBALS['wp_version'], '3.2.99', '>') && !function_exists('get_user_by')) {

    function get_user_by($field, $value) {

        global $xt_during_user_creation;
        if ('email' == $field && $xt_during_user_creation)
            return false;

        $userdata = WP_User::get_data_by($field, $value);

        if (!$userdata)
            return false;

        $user = new WP_User;
        $user->init($userdata);

        return $user;
    }

}
?>