<?php
define('ALKUBOT_DB_VERSION', '2.0');
define('ALKUBOT_PLUGIN_VERSION', '2.3.3');

define('ALKUBOT_URL', plugin_dir_url(__FILE__));
define('ALKUBOT_PATH', plugin_dir_path(__FILE__));

define('ALKUBOT_CLASS_PATH', ALKUBOT_PATH . 'class');
define('ALKUBOT_ADMIN_VIEWS_PATH', ALKUBOT_PATH . 'admin/views');
define('ALKUBOT_API_CLASS_PATH', ALKUBOT_PATH . 'api');

define('ALKUBOT_CSS_PATH', ALKUBOT_URL . 'css');
define('ALKUBOT_JS_PATH', ALKUBOT_URL . 'js');
define('ALKUBOT_IMAGES_PATH', ALKUBOT_URL . 'images');

if (!getenv('ALKUBOT_REMOTE_SERVER_URL')) {
  define('ALKUBOT_REMOTE_SERVER_URL', 'https://backend.alkubot.com');
  define('ALKUBOT_PLUGIN_URL', 'https://plugins.alkubot.com/woocommerce');
} else {
  define('ALKUBOT_REMOTE_SERVER_URL', getenv('ALKUBOT_REMOTE_SERVER_URL'));
  define('ALKUBOT_PLUGIN_URL', getenv('ALKUBOT_PLUGIN_URL'));
}

define('ALKUBOT_ACTIVATE_STORE_URL', ALKUBOT_REMOTE_SERVER_URL . '/store');
define('ALKUBOT_DEACTIVATE_STORE_URL', ALKUBOT_REMOTE_SERVER_URL . '/store/plugin/deactivate');
define('ALKUBOT_DELETE_STORE_URL', ALKUBOT_REMOTE_SERVER_URL . '/store/plugin/delete');
define('ALKUBOT_UPDATE_STORE_URL', ALKUBOT_REMOTE_SERVER_URL . '/store/plugin/update');
define('ALKUBOT_UPDATE_USED_COUPON', ALKUBOT_REMOTE_SERVER_URL . '/coupon');


define('ALKUBOT_OPTION_DB_VERSION', 'alkubot_db_version');
define('ALKUBOT_OPTION_TOKEN', 'alkubot_token');
define('ALKUBOT_OPTION_NOTIFICATION_TITLE', 'alkubot_notification_title');
define('ALKUBOT_OPTION_NOTIFICATION_MESSAGE', 'alkubot_notification_message');
define('ALKUBOT_OPTION_NOTIFICATION_UNREAD', 'alkubot_notification_unread');


define('ALKUBOT_REST_NAMESPACE', 'alkubot');
