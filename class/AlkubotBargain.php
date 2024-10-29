<?php

/**
 * Class Bargain
 *
 * This class creates the option page and add the web app script
 */
class AlkubotBargain
{

  private $token = '';

  /**
   * Bargain constructor.
   * @param $token
   */
  public function __construct($token)
  {
    $this->token = $token;
  }

  public static function successfulBargain()
  {
    check_ajax_referer(ALKUBOT_SECURITY, 'security', true);
    $coupon = @$_POST['coupon'];

    if ($coupon) {
      $code = $coupon['code'];
      $productId = (int)$coupon['productId'];
      $chosenAttributes = $coupon['variation'];

      try {
        AlkubotProduct::addProduct($productId, $chosenAttributes);
      } catch (Exception $e) {
        die(var_dump(e));
      }
      AlkubotCouponHelper::applyCode($code);

      $cartFragments = self::getCartFragment();

      wp_send_json_success($cartFragments);
    }

    wp_send_json_error();
  }

  /**
   * If update cart fragment is enabled, then return the cart content
   * @return array $cartFragments
   */
  public static function getCartFragment()
  {
    $updateCartFragment = get_option(ALKUBOT_OPTION_UPDATE_CART_FRAGMENT, ALKUBOT_DEFAULT_UPDATE_CART_FRAGMENT);
    $cartFragments = [];
    if ($updateCartFragment) {
      ob_start();
      woocommerce_mini_cart();
      $mini_cart = ob_get_clean();
      $cartFragments = [
        'cartFragments' => apply_filters(
          'woocommerce_add_to_cart_fragments',
          ['div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>']
        )
      ];
    }

    return $cartFragments;
  }
}
