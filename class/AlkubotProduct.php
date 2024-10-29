<?php

/**
 * Class AlkubotProduct
 */
class AlkubotProduct
{
  /**
   * @param $product
   * @param $chosenAttributes
   * @return array
   */
  private static function transformVariations($product, $chosenAttributes)
  {
    $result = [];

    if ($chosenAttributes) {
      foreach ($chosenAttributes as $chosenAttribute) {
        $attributeName = AlkubotProductHelper::getWoocommerceAttributeFromLabel($product, $chosenAttribute['attributeName']);

        $attributeValue = $chosenAttribute['attributeValue'];

        $result[$attributeName] = $attributeValue;
      }
    }
    return $result;
  }

  private static function removeAccentedChars($inputString)
  {
    $dictionary = ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
      'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
      'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
      'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
      'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'];

    return strtr($inputString, $dictionary);
  }

  /**
   * Add a product to cart.
   * @param int $productId
   * @param array $chosenAttributes
   * @throws Exception
   */
  public static function addProduct(int $productId, $chosenAttributes = [])
  {
    $product = wc_get_product($productId);
    if (!$product) {
      return;
    }

    $chosenAttributes = self::transformVariations($product, $chosenAttributes);
    $variationId = self::getProductVariationId($product, $chosenAttributes);
    
    if (!$variationId) {
      $variation = self::getProductVariation($productId, $chosenAttributes);
      $variationId = $variation->ID;
    }
    if ($variationId) {
      $productId = $variationId;
    }

    if (self::productIsInCart($productId)) {
      return;
    }

    foreach ($chosenAttributes as $attributeName => $value) {
      $accentlessAttributeName = self::removeAccentedChars($attributeName);
      unset($chosenAttributes[$attributeName]);
      $chosenAttributes[$accentlessAttributeName] = $value;
    }

    WC()->cart->add_to_cart($productId, 1, $variationId, $chosenAttributes);
  }

  /**
   * Checks if product is already in cart
   * @param $productId
   * @return bool
   */
  private static function productIsInCart($productId)
  {
    if (sizeof(WC()->cart->get_cart()) > 0) {
      foreach (WC()->cart->get_cart() as $key => $values) {
        $product = $values['data'];

        if ($product->get_id() === $productId)
          return true;
      }
    }

    return false;
  }

  /**
   * Find the variation ID based chosen attributes
   * @param $product
   * @param $chosenAttributes
   * @return int
   */
  private static function getProductVariationId($product, $chosenAttributes)
  {
    return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation($product, $chosenAttributes);
  }

  /**
   * Search for a variation based chosen attributes
   * @param $productId
   * @param $chosenAttributes
   * @return WP_Post
   */
  public static function getProductVariation($productId, $chosenAttributes)
  {
    $args = [
      'post_type' => 'product_variation',
      'numberposts' => -1,
      'orderby' => 'menu_order',
      'order' => 'asc',
      'post_parent' => $productId
    ];
    $variations = get_posts($args);

    if (!$variations) {
      return null;
    }
    $attributes = array_keys($chosenAttributes);
    $attributes = array_map(function ($value) {
      $splitValues = explode("_", $value);
      return ucfirst(array_pop($splitValues));
    }, $attributes);

    $options = array_values($chosenAttributes);

    foreach ($variations as $arrayIndex => $variation) {
      $availableVariations = explode(',', $variation->post_excerpt);

      foreach ($availableVariations as $currentVariation) {
        list($currentAttribute, $currentOption) = explode(':', $currentVariation);
        $currentAttribute = trim($currentAttribute);
        $currentOption = trim($currentOption);

        if (in_array($currentAttribute, $attributes)) {
          if (!in_array($currentOption, $options)) {
            unset($variations[$arrayIndex]);
          }
        }
      }
    }

    return array_pop($variations);
  }

}
