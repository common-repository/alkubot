<?php

/**
 * Class Alkubot
 *
 * This class creates the option page and add the web app script
 */
class AlkubotAdmin
{
  /**
   * Alkubot constructor.
   *
   * The main plugin actions registered for WordPress
   */
  public function __construct()
  {
    wp_enqueue_style('alkubotAdminMenu', ALKUBOT_CSS_PATH . '/admin/adminMenu.css', [], time());

    if (isset($_GET['page'])) {
      $page = sanitize_text_field(@$_GET['page']);

      if (strpos($page, 'alkubot') !== false) {
        $notification = @$_GET['notification'];
        if ($notification) {
          update_option(ALKUBOT_OPTION_NOTIFICATION_UNREAD, $notification);
          wp_redirect($_SERVER['HTTP_REFERER']);
          exit;
        }

        $alkubotOptions['page'] = $page;
        add_action('admin_enqueue_scripts', [$this, 'addAdminScripts']);
      }
    }

    add_action('admin_menu', [$this, 'addAdminMenu']);

    add_action('admin_notices', [$this, 'displayNotification']);
    $this->registerAjaxEndpoints();
  }

  /**
   * If there is unread notification message then display it
   */
  public function displayNotification()
  {
    $unread = get_option(ALKUBOT_OPTION_NOTIFICATION_UNREAD, false);

    if ($unread === 'true') {
      wp_enqueue_style('alkubotAdminNotification', ALKUBOT_CSS_PATH . '/admin/notification.css', [], ALKUBOT_PLUGIN_VERSION);
      $title = get_option(ALKUBOT_OPTION_NOTIFICATION_TITLE);
      $message = get_option(ALKUBOT_OPTION_NOTIFICATION_MESSAGE);
      include_once(ALKUBOT_ADMIN_VIEWS_PATH . '/layouts/notification.php');
    }
  }

  /**
   * Register available admin side AJAX endpoints
   */
  private function registerAjaxEndpoints()
  {
    add_action('wp_ajax_updateToken', ['AlkubotAdmin', 'updateToken']);
  }

  public static function updateToken()
  {
    $token = $_REQUEST["token"];

    if ($token) {
      AlkubotDB::saveToken($token);

      $data = [
        "body" => AlkubotDB::getStoreData(),
        "method" => "PATCH"
      ];

      $response = wp_remote_request(ALKUBOT_ACTIVATE_STORE_URL, $data);
      $httpStatus = wp_remote_retrieve_response_code($response);

      if ($httpStatus !== 200) {
        wp_send_json("Error", $httpStatus);
      }
    }
  }

  /**
   * Outputs the Admin Dashboard layout containing the form with all its options
   */
  public function layout()
  {
    $token = AlkubotDB::getToken();

    include_once(ALKUBOT_ADMIN_VIEWS_PATH . '/layouts/main.php');
  }

  /**
   * Adds the Alkubot label to the WordPress Admin Sidebar Menu
   */
  public function addAdminMenu()
  {
    $iconUrl = ALKUBOT_IMAGES_PATH . '/menu-icon.png';

    add_menu_page(
      __('Alkubot', 'Alkubot'),
      __('Alkubot', 'Alkubot'),
      'manage_options',
      'alkubot',
      [$this, 'layout'],
      $iconUrl,
      2
    );
  }

  /**
   * Adds Admin Scripts
   */
  public function addAdminScripts()
  {
    wp_enqueue_style('alkubotAdmin', ALKUBOT_CSS_PATH . '/admin/admin.css', [], ALKUBOT_PLUGIN_VERSION);

    wp_enqueue_script('alkubot-admin-popup', ALKUBOT_JS_PATH . '/adminPopup.js', ['jquery'], ALKUBOT_PLUGIN_VERSION, true);
    wp_enqueue_script('alkubot-admin', ALKUBOT_JS_PATH . '/admin.js', ['jquery'], ALKUBOT_PLUGIN_VERSION, true);

    $globalVariables = [
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'security' => wp_create_nonce('ALKUBOT_SECURITY'),
    ];

    wp_localize_script('alkubot-admin', 'alkubot', $globalVariables);
  }

}
