<?php

/**
 * Class AlkubotRestCategory
 *
 * This class fetch category related data from DB and return as REST response
 */
class AlkubotRestCategory
{
  public static function getCategories()
  {
    $args = array(
      'taxonomy' => "product_cat",
      'orderby' => "name",
      'show_count' => 1,
      'hide_empty' => 0,
      'posts_per_page' => -1
    );
    $categories = get_categories($args);
    wp_send_json_success(self::mapCategories($categories));
  }

  private static function mapCategories($categories = [])
  {
    $result = [];
    if (!empty($categories) && is_array($categories)) {
      foreach ($categories as $category) {
        $result[] = self::categoryDataModel($category);
      }
    }
    return $result;
  }

  private static function categoryDataModel($category)
  {
    return [
      "id" => $category->term_id,
      "name" => $category->name,
      "numberOfProducts" => $category->count,
      "imageUrl" => wp_get_attachment_url(get_term_meta($category->term_id, 'thumbnail_id', true)),
    ];
  }
}

