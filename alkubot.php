<?php
/**
 * Plugin Name: Alkubot
 * Plugin URI: https://www.alkubot.com/pricing
 * Description: The negotiator chatbot that sells your product to hesitant visitors.
 * Author: Alkubot KFT
 * Author URI: https://www.alkubot.com/
 * Version: 3.0.0
 * Developer: tyukesz
 * Text Domain: Alkubot
 *
 * Copyright: © 2019-2021 Alkubot.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

if (!session_id()) session_start();
registerAlkubotHooks();

function loadAlkubot()
{
  $activePlugins = apply_filters('active_plugins', get_option('active_plugins'));
  if (in_array('woocommerce/woocommerce.php', $activePlugins)) {
    load_plugin_textdomain('Alkubot', false, basename(dirname(__FILE__)) . '/languages');

    if (is_admin()) {
      if (@!isset($_POST['frontendAjax'])) {
        require_once(ALKUBOT_CLASS_PATH . '/AlkubotAdmin.php');
        new AlkubotAdmin();
        return;
      }
    }

    if (strpos($_SERVER["REQUEST_URI"], "/wp-json") !== false) {
      includeAlkubotRestAPI();

      AlkubotRestAPI::loadRoutes();
    } else {
      includeAlkubotFrontend();

      new AlkubotFrontend();
    }
  }
}

function includeAlkubotFrontend()
{
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotBargain.php');
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotProduct.php');
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotFrontend.php');
}

function includeAlkubotRestAPI()
{
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestAPI.php');
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestProduct.php');
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestCategory.php');
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestCoupon.php');
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestStore.php');
  require_once(ALKUBOT_API_CLASS_PATH . '/AlkubotRestNotification.php');
}

function registerAlkubotHooks()
{
  loadAlkubotDotenv();

  require_once('config.php');

  require_once(ALKUBOT_CLASS_PATH . '/AlkubotDB.php');
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotCouponHelper.php');
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotActivation.php');
  require_once(ALKUBOT_CLASS_PATH . '/AlkubotProductHelper.php');

  register_activation_hook(__FILE__, ['AlkubotActivation', 'onActivate']);
  register_deactivation_hook(__FILE__, ['AlkubotActivation', 'onDeactivate']);
  register_uninstall_hook(__FILE__, ['AlkubotActivation', 'onUninstall']);

  add_action('upgrader_process_complete', ['AlkubotActivation', 'onUpdate'], 10, 2);

  add_action('wp_loaded', 'loadAlkubot');
}

function loadAlkubotDotenv()
{
  $envFilePath = __DIR__ . DIRECTORY_SEPARATOR . '.env';

  if (file_exists($envFilePath)) {
    $envFile = file_get_contents($envFilePath);
    $envVariables = explode(PHP_EOL, $envFile);

    if (count($envVariables) > 0) {
      foreach ($envVariables as $envVariable) {
        if ($envVariable) {
          putenv($envVariable);
        }
      }
    }
  }
}
