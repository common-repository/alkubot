<?php

/**
 * Class Coupon
 *
 * This class handle coupon related things
 */
class AlkubotCouponHelper
{

  /**
   * Create a new coupon for given product
   * @param string $code
   * @param int $productId
   * @param float $percentageAmount
   * @param $dateExpires
   * @return array
   */
  public static function create(string $code, int $productId, float $percentageAmount, $dateExpires)
  {
    $product = wc_get_product($productId);

    if (!$product) {
      return ["success" => false, "message" => "Product doesn't exists."];
    }

    $coupon = wc_get_coupon_id_by_code($code);

    if ($coupon) {
      return ["success" => false, "message" => "Coupon code already exists."];
    }

    if (!$coupon) {
      $coupon = [
        'post_title' => $code,
        'post_content' => '',
        'post_status' => 'publish',
        'post_author' => 1,
        'post_type' => 'shop_coupon'
      ];

      $newCouponId = wp_insert_post($coupon);

      update_post_meta($newCouponId, 'discount_type', 'percent_product');
      update_post_meta($newCouponId, 'coupon_amount', $percentageAmount);
      update_post_meta($newCouponId, 'individual_use', 'no');
      update_post_meta($newCouponId, 'product_ids', $productId);
      update_post_meta($newCouponId, 'exclude_product_ids', '');
      update_post_meta($newCouponId, 'usage_limit', '1');
      update_post_meta($newCouponId, 'date_expires', $dateExpires);
      update_post_meta($newCouponId, 'apply_before_tax', 'yes');
      update_post_meta($newCouponId, 'free_shipping', 'no');
      update_post_meta($newCouponId, 'expiry_date', true);
      update_post_meta($newCouponId, 'is_alkubot_coupon', true);
    }

    return ["success" => true];
  }

  /**
   * Apply the given coupon code
   * @param string $code
   * @return boolean
   */
  public static function applyCode(string $code)
  {
    self::enableCoupons();
    if (WC()->cart->apply_coupon($code)) {
      return false;
    }

    return true;
  }

  /**
   * Check if Alkubot coupon has been already applied to a product.
   * Remove if it has.
   * @param string $couponCode
   */
  public static function checkAppliedCoupons($couponCode)
  {
    $couponDetails = new WC_Coupon($couponCode);
    $productIds = $couponDetails->get_product_ids();
    $appliedCoupons = self::getAppliedAlkubotCouponsForProductsWithIds($productIds);
    if (count($appliedCoupons) > 1) {
      WC()->cart->remove_coupon($couponCode);
      wc_add_notice(translate('Csak egy Alkubotos kuponkód használható fel ehhez a termékhez!', 'Alkubot'), 'error');
    }
  }

  /**
   * Check if there is any product in the cart with alkubot discount already
   * @param array $productIds
   * @return array $appliedCoupons
   */
  private static function getAppliedAlkubotCouponsForProductsWithIds($productIds)
  {
    $appliedCoupons = [];
    $coupons = WC()->cart->get_coupons();

    if ($coupons) {
      foreach ($coupons as $coupon) {
        $couponMeta = get_post_meta($coupon->get_id());
        if (@$couponMeta['is_alkubot_coupon']) {
          if (array_intersect($couponMeta['product_ids'], $productIds)) {
            $appliedCoupons[] = $coupon->get_code();
          }
        }
      }
    }

    return $appliedCoupons;
  }

  /**
   * Check if is enabled WC coupons. If not, then enable it.
   */
  private static function enableCoupons()
  {
    $isCouponEnabled = get_option('woocommerce_enable_coupons');
    $isSequentialCouponEnabled = get_option('woocommerce_calc_discounts_sequentially');

    if ($isCouponEnabled != 'yes') {
      update_option('woocommerce_enable_coupons', 'yes');
    }
    if ($isSequentialCouponEnabled != 'yes') {
      update_option('woocommerce_calc_discounts_sequentially', 'yes');
    }
  }

  /**
   * Check if Alkubot coupon is used after success order
   * Modify Alkubot Bargain record (usedCoupon=>true)
   * @param int $orderId
   */
  public static function checkCouponsAfterOrder($orderId)
  {
    $order = new WC_Order($orderId);

    $coupons = $order->get_items('coupon');

    if ($coupons) {
      foreach ($coupons as $coupon) {
        $couponCode = $coupon->get_code();

        self::updateUsedCouponRemote($couponCode);
      }
    }
  }

  /**
   * Send the used coupon to remote alkubot server
   * @param string $code
   */
  private static function updateUsedCouponRemote(string $code)
  {
    $args = [
      "method" => "PATCH",
      "body" => [
        "platformToken" => AlkubotDB::getToken(),
        "couponCode" => $code,
      ]
    ];

    wp_remote_request(ALKUBOT_UPDATE_USED_COUPON, $args);
  }
}

?>
