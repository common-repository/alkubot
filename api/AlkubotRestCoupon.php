<?php

/**
 * Class AlkubotRestCoupon
 *
 * This class handle coupon related things
 */
class AlkubotRestCoupon
{

  public static function createCoupon($request)
  {
    $code = $request["coupon"];
    $productId = $request["productId"];
    $percentageAmount = $request["percentageAmount"];
    $dateExpires = $request["dateExpires"];

    $couponCreationInfo = AlkubotCouponHelper::create($code, $productId, $percentageAmount, $dateExpires);

    if ($couponCreationInfo["success"]) {
      return wp_send_json_success();
    }

    wp_send_json_error($couponCreationInfo["message"], 400);
  }

}

