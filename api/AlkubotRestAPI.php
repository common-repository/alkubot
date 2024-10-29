<?php

/**
 * Class AlkubotRestAPI
 */
class AlkubotRestAPI
{
  public static function loadRoutes()
  {
    add_action('rest_api_init', function () {
      self::registerProductRoutes();
      self::registerCategoryRoutes();
      self::registerStoreRoutes();
      self::registerNotificationRoutes();
    });
  }

  private static function registerNotificationRoutes()
  {
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'notification', [
      'methods' => 'POST',
      'callback' => ['AlkubotRestNotification', 'updateNotification'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
  }

  private static function registerStoreRoutes()
  {
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'store', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestStore', 'getStoreDetails'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'store/pluginState', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestStore', 'getPluginState'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'store/couponUsage', [
      'methods' => 'POST',
      'callback' => ['AlkubotRestStore', 'getCouponUsage'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'store/coupon', [
      'methods' => 'POST',
      'callback' => ['AlkubotRestCoupon', 'createCoupon'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'store/currency', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestStore', 'getCurrency'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
  }

  private static function registerProductRoutes()
  {
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'product', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestProduct', 'getProducts'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'product/(?P<id>[\d]+)', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestProduct', 'getProduct'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'product/count', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestProduct', 'countProducts'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
  }

  private static function registerCategoryRoutes()
  {
    register_rest_route(ALKUBOT_REST_NAMESPACE, 'category', [
      'methods' => 'GET',
      'callback' => ['AlkubotRestCategory', 'getCategories'],
      'permission_callback' => ['AlkubotRestAPI', 'isValidToken']
    ]);
  }

  public static function isValidToken($request)
  {
    $token = $request->get_param("platformToken");

    if ($token) {
      $tokenValue = AlkubotDB::getToken();

      if ($token === $tokenValue) {
        return true;
      }
    }

    return false;
  }
}
