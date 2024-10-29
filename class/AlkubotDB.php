<?php

/**
 * A class that helps to create/update tables
 */
class AlkubotDB
{
  /**
   * Run all DB related SQLs
   */
  public static function init()
  {
    self::dbVersion();
  }

  /**
   * Add current DB version
   */
  private static function dbVersion()
  {
    add_option(ALKUBOT_OPTION_DB_VERSION, ALKUBOT_DB_VERSION, '', false);
  }

  /**
   * Store alkubot token as WP option
   */
  public static function saveToken(string $token = '')
  {
    update_option(ALKUBOT_OPTION_TOKEN, $token);
  }

  /**
   * Get alkubot token from WP options
   */
  public static function getToken()
  {
    return get_option(ALKUBOT_OPTION_TOKEN, '');
  }

  public static function getStoreData()
  {
    $args = [
      'orderby' => 'name',
      'order' => 'ASC',
      'posts_per_page' => -1
    ];

    return [
      "url" => get_option('siteurl'),
      "name" => get_option('blogname'),
      "version" => ALKUBOT_PLUGIN_VERSION,
      "platformToken" => AlkubotDB::getToken()
    ];
  }
}
