<?php

/**
 * Class AlkubotRestProduct
 *
 * This class fetch product related data from DB and return as REST response
 */
class AlkubotRestProduct
{

  public static function countProducts()
  {
    $products = self::queryAllProducts();

    wp_send_json_success(count($products));
  }

  public static function getProduct($request)
  {
    $product = wc_get_product($request["id"]);

    if (!$product) {
      return wp_send_json_error("Product doesn't exists.", 404);
    }

    wp_send_json_success(self::productDetailsDTO($product));
  }

  public static function getProducts()
  {
    $products = self::queryAllProducts();

    wp_send_json_success(self::mapProducts($products));
  }

  private static function queryAllProducts()
  {
    $args = [
      'orderby' => 'name',
      'order' => 'ASC',
      'posts_per_page' => -1
    ];

    return wc_get_products($args);
  }

  private static function mapProducts($products = [])
  {
    $result = [];
    if (!empty($products) && is_array($products)) {
      foreach ($products as $product) {
        $result[] = self::productDTO($product);
      }
    }
    return $result;
  }

  private static function productDTO($product)
  {
    $hasAvailableVariation = AlkubotProductHelper::hasAvailableVariation($product->get_id());

    if ($hasAvailableVariation) {
      $variations = AlkubotProductHelper::getProductVariations($product->get_id());
      $prices = AlkubotProductHelper::getVariableProductPrices($variations);
    } else {
      $prices = AlkubotProductHelper::getProductPrices($product);
    }


    return [
      "id" => $product->get_id(),
      "name" => $product->get_name(),
      "minCurrentPrice" => $prices["regularMin"],
      "maxCurrentPrice" => $prices["regularMax"],
      "minSalePrice" => $prices["saleMin"],
      "maxSalePrice" => $prices["saleMax"],
      "imageUrl" => wp_get_attachment_url($product->get_image_id()),
      "currency" => AlkubotProductHelper::getCurrencySettings()
    ];
  }

  private static function productDetailsDTO($product)
  {
    $hasAvailableVariation = AlkubotProductHelper::hasAvailableVariation($product->get_id());

    if ($hasAvailableVariation) {
      $variations = AlkubotProductHelper::getProductVariations($product->get_id());
      $prices = AlkubotProductHelper::getVariableProductPrices($variations);
    } else {
      $variations = [];
      $prices = AlkubotProductHelper::getProductPrices($product);
    }

    $isAvailable = true;
    if (!$product->is_in_stock() || !$product->is_purchasable()) {
      $isAvailable = false;
    }

    return [
      "id" => $product->get_id(),
      "name" => $product->get_name(),
      "currentPrice" => $prices["regularMax"],
      "salePrice" => $prices["saleMax"],
      "isAvailable" => $isAvailable,
      "hasAvailableVariation" => $hasAvailableVariation,
      "variations" => $variations,
      "categoryIds" => $product->get_category_ids(),
      "imageUrl" => wp_get_attachment_url($product->get_image_id()),
      "currency" => AlkubotProductHelper::getCurrencySettings()
    ];
  }
}

