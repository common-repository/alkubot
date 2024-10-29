<?php

/**
 * Class AlkubotRestStore
 */
class AlkubotRestStore
{
  public static function getStoreDetails()
  {
    $data = AlkubotDB::getStoreData();

    wp_send_json_success($data);
  }

  public static function getCurrency()
  {
    $currency = AlkubotProductHelper::getCurrencySettings();

    wp_send_json_success($currency);
  }

  public static function getPluginState()
  {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    $data = [
      'isActive' => is_plugin_active('alkubot/alkubot.php'),
      'version' => ALKUBOT_PLUGIN_VERSION
    ];

    wp_send_json_success($data);
  }

  public static function getCouponUsage($request)
  {
    $result = [];

    if (isset($request['codes'])) {
      $codes = $request['codes'];

      foreach ($codes as $code) {
        $coupon = new WC_Coupon($code);

        $data = [
          "code" => $coupon->get_code(),
          "amount" => $coupon->get_amount(),
          "usage_count" => $coupon->get_usage_count(),
          "used_by" => $coupon->get_used_by(),
          "date_created" => json_encode($coupon->get_date_created()),
        ];

        $result[$code] = $data;
      }
    }

    wp_send_json_success($result);
  }
}

