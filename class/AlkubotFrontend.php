<?php

/**
 * This class creates the option page and add the web app script
 */
class AlkubotFrontend
{
  /**
   * Alkubot constructor.
   * The main plugin actions registered for WordPress
   */
  public function __construct()
  {
    add_action('woocommerce_thankyou', ['AlkubotCouponHelper', 'checkCouponsAfterOrder'], 1, 1);
    add_action('woocommerce_applied_coupon', ['AlkubotCouponHelper', 'checkAppliedCoupons'], 1, 1);

    add_action('wp_ajax_successfulBargain', ['AlkubotBargain', 'successfulBargain']);
    add_action('wp_ajax_nopriv_successfulBargain', ['AlkubotBargain', 'successfulBargain']);

    add_action('wp_enqueue_scripts', [$this, 'addFrontendScripts']);
  }

  private function getUserId()
  {
    $userId = get_current_user_id();

    if (!$userId) {
      if (isset($_SESSION['alkubot-user-id'])) {
        $userId = $_SESSION['alkubot-user-id'];
      } else {
        $userId = time();
      }
    }

    $_SESSION['alkubot-user-id'] = $userId;

    return $userId;
  }

  private function getCurrentProductData()
  {
    if (!is_product()) return [];

    $product = wc_get_product();

    return [
      "id" => $product->get_id(),
    ];
  }


  /**
   * Adds Frontend Scripts
   */
  public function addFrontendScripts()
  {
    wp_enqueue_script('alkubot-frontend', ALKUBOT_PLUGIN_URL . "/index.js", [], ALKUBOT_PLUGIN_VERSION, true);

    $globalVariables = [
      'ajaxUrl' => admin_url('admin-ajax.php'),
      'security' => wp_create_nonce("ALKUBOT_SECURITY"),
      'userId' => $this->getUserId(),
      'shopData' => [
        'shop' => get_option('siteurl'),
        'locale' => strpos(strtolower(get_locale()), 'hu') !== false ? 'hu' : 'en',
      ],
      'isProductPage' => is_product(),
      'cartUrl' => wc_get_cart_url(),
      'productData' => $this->getCurrentProductData(),
    ];
    wp_localize_script('alkubot-frontend', 'alkubot', $globalVariables);
  }
}

