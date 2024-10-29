<?php

/**
 * Class AlkubotProductHelper
 */
class AlkubotProductHelper
{
  /**
   * Transforms the variation attribute from human readable to WC conventional name
   * @param $product
   * @param $attributeName
   * @return string
   */
  public static function getWoocommerceAttributeFromLabel($product, $attributeName)
  {
    $attributeName = strtolower($attributeName);

    $prefix = "attribute_";

    $isNotCustomAttribute = $product->get_attribute('pa_' . $attributeName);
    if ($isNotCustomAttribute) {
      $prefix .= "pa_";
    }

    return $prefix . $attributeName;
  }

  /**
   * Returns actual and sale price of the simple product
   * @param $product
   * @return array
   */
  public static function getProductPrices($product)
  {
    return [
      "saleMin" => $product->get_sale_price(),
      "saleMax" => $product->get_sale_price(),
      "regularMin" => $product->get_regular_price(),
      "regularMax" => $product->get_regular_price(),
    ];
  }

  /**
   * Returns actual and sale price of the variational product
   * @param $variations
   * @return array
   */
  public static function getVariableProductPrices($variations)
  {
    $minSalePrice = PHP_INT_MAX;
    $minRegularPrice = PHP_INT_MAX;
    $maxSalePrice = 0;
    $maxRegularPrice = 0;
    foreach ($variations as $variation) {
      $variationSalePrice = (int)$variation["salePrice"];
      $variationRegularPrice = (int)$variation["currentPrice"];

      if ($variationRegularPrice > $maxRegularPrice) {
        $maxRegularPrice = $variationRegularPrice;
      }
      if ($variationSalePrice > $maxSalePrice) {
        $maxSalePrice = $variationSalePrice;
      }
      if ($variationRegularPrice > 0 && $variationRegularPrice < $minRegularPrice) {
        $minRegularPrice = $variationRegularPrice;
      }
      if ($variationSalePrice > 0 && $variationSalePrice < $minSalePrice) {
        $minSalePrice = $variationSalePrice;
      }
    }
    return [
      "saleMin" => $minSalePrice !== PHP_INT_MAX ? $minSalePrice : 0,
      "saleMax" => $maxSalePrice,
      "regularMin" => $minRegularPrice !== PHP_INT_MAX ? $minRegularPrice : 0,
      "regularMax" => $maxRegularPrice,
    ];
  }

  /**
   * Collect and return the available attributes for given product
   * @param int $productId
   * @return array
   */
  public static function getProductVariations(int $productId)
  {
    $product = wc_get_product($productId);
    $attributes = [];

    if ($product && $product->is_type('variable')) {
      $attributes = self::extractVariations($product);
    }

    return $attributes;
  }

  /**
   * Extract and process available variations and attributes for the given product
   * @param WC_Product $product
   * @return array
   */
  private static function extractVariations($product)
  {
    $attributes = [];
    $productVariations = $product->get_available_variations();

    if ($productVariations) {
      foreach ($productVariations as $i => $variation) {
        if ($variation['attributes']) {
          foreach ($variation['attributes'] as $oldName => $option) {
            $newName = wc_attribute_label(self::getAttributeFromSlug($oldName), $product);
            $options = self::getVariationOption($product, $oldName, $option);
            $variation['attributes'][$newName] = $options;
            unset($variation['attributes'][$oldName]);
          }
          $productVariation = new WC_Product_Variation($variation['variation_id']);

          $attributes[$i]["variation"] = $variation['attributes'];
          $attributes[$i]["isAvailable"] = true;
          $attributes[$i]["salePrice"] = $productVariation->get_sale_price();
          $attributes[$i]["currentPrice"] = $productVariation->get_regular_price();
          if (!$variation['is_in_stock'] || !$variation['is_purchasable']) {
            $attributes[$i]["isAvailable"] = false;
          }
        }
      }
    }

    return self::checkForAttributeCombinations($attributes);
  }

  /**
   * @param $data
   * @return array
   */
  private static function checkForAttributeCombinations($data)
  {
    $result = [];
    if ($data) {
      foreach ($data as $index => $mergedVariations) {
        $variations = $mergedVariations['variation'];
        $isAvailable = $mergedVariations['isAvailable'];
        $salePrice = $mergedVariations['salePrice'];
        $currentPrice = $mergedVariations['currentPrice'];

        $generatedVariations = self::generateCombinations([$variations]);
        if ($generatedVariations) {
          foreach ($generatedVariations as $variations) {
            $result[] = [
              'variation' => $variations,
              'isAvailable' => $isAvailable,
              'salePrice' => $salePrice,
              'currentPrice' => $currentPrice
            ];
          }
        }
      }
    }
    return $result;
  }

  /**
   * @param $variationsArray
   * @return array
   */
  private static function generateCombinations($variationsArray)
  {
    $wasArray = false;
    if ($variationsArray) {
      foreach ($variationsArray as $index => $variations) {
        if ($variations) {
          foreach ($variations as $attribute => $options) {
            if (is_array($options)) {
              $wasArray = true;
              $simpleVariations = $variations;
              unset($simpleVariations[$attribute]);
              foreach ($options as $option) {
                $variationsArray[] = array_merge($simpleVariations, [$attribute => $option]);
              }
              unset($variationsArray[$index]);
            }
            if ($wasArray) {
              return self::generateCombinations($variationsArray);
            }
          }
        }
      }
    }

    return $variationsArray;
  }

  /**
   * Returns the attribute option
   * @param WC_Product $product
   * @param string $attributeName
   * @param string $attributeOption
   * @return array|string
   */
  private static function getVariationOption(WC_Product $product, $attributeName = '', $attributeOption = '')
  {
    if ($attributeOption === "") {
      $newName = self::getAttributeFromSlug($attributeName);
      $options = $product->get_attribute($newName);
      if (self::isCustomAttribute($attributeName)) {
        $options = explode("|", $options);
      } else {
        $options = explode(",", $options);
      }
      $options = array_map('trim', $options);

      return $options;
    } else {
      return $attributeOption;
    }
  }

  /**
   * Split attribute slug by '_' and return the attribute real name
   * @param string $attribute
   * @return string
   */
  private static function getAttributeFromSlug($attribute)
  {
    $splittedValue = explode('_', $attribute);

    return array_pop($splittedValue);
  }

  /**
   * Check if given attribute is custom or not
   * @param $attribute
   * @return bool
   */
  private static function isCustomAttribute($attribute)
  {
    $splittedValue = explode('_', $attribute);

    if (count($splittedValue) < 3 && $splittedValue[1] !== 'pa') {
      return true;
    }
    return false;
  }

  /**
   * Check if product has any variation that is available for sale
   * @param int $productId
   * @return bool
   */
  public static function hasAvailableVariation(int $productId = 0)
  {
    $product = wc_get_product($productId);

    if ($product->is_type('variable')) {
      $numberOfAvailableVariations = 0;
      $variations = $product->get_available_variations();
      foreach ($variations as $key => $value) {
        if ($value['is_in_stock'] && $value['is_purchasable']) {
          $numberOfAvailableVariations++;
        }
      }

      return $numberOfAvailableVariations > 0;
    }

    return false;
  }

  /**
   * Select WC currency settings and return them
   * @return array $currencySettings
   */
  public static function getCurrencySettings()
  {
    $currencySettings = [
      "name" => get_woocommerce_currency(),
      "symbol" => get_woocommerce_currency_symbol(),
      "position" => get_option('woocommerce_currency_pos'),
      "decimalNumbers" => get_option('woocommerce_price_num_decimals'),
      "decimalSep" => get_option('woocommerce_price_decimal_sep'),
      "thousandSep" => get_option('woocommerce_price_thousand_sep'),
    ];

    return $currencySettings;
  }
}
