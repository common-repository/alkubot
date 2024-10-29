<?php

/**
 * Class AlkubotActivation
 */
class AlkubotActivation
{
  /**
   * This method runs when plugins are updated
   * Sends information to Alkubot server, if the plugin was updated.
   */
  public static function onUpdate($upgrader, $options)
  {
    if ($options['action'] == 'update' && $options['type'] == 'plugin') {
      foreach ($options['plugins'] as $each_plugin) {
        if (strpos($each_plugin, 'alkubot') !== false) {
          update_option(ALKUBOT_OPTION_NOTIFICATION_UNREAD, 'false');
          $data = [
            "body" => AlkubotDB::getStoreData(),
            "method" => "PATCH"
          ];

          wp_remote_request(ALKUBOT_UPDATE_STORE_URL, $data);
        }
      }
    }
  }

  /**
   * This method runs when Alkubot plugin is activated.
   * Sends activation request to Alkubot server.
   */
  public static function onActivate()
  {
    AlkubotDB::init();

    if (!empty(AlkubotDB::getToken())) {
      $data = [
        "body" => AlkubotDB::getStoreData(),
        "method" => "PATCH"
      ];

      wp_remote_request(ALKUBOT_UPDATE_STORE_URL, $data);
    }
  }

  /**
   * This method runs when Alkubot plugin is deactivated
   * Sends deactivation request to Alkubot server.
   */
  public static function onDeactivate()
  {
    $data = [
      "body" => AlkubotDB::getStoreData(),
      "method" => "PATCH"
    ];

    wp_remote_request(ALKUBOT_DEACTIVATE_STORE_URL, $data);
  }

  /**
   * This method runs when Alkubot plugin is deleted
   * Sends delete request to Alkubot server. And cleanup DB.
   */
  public static function onUninstall()
  {
    $data = [
      "body" => AlkubotDB::getStoreData(),
      "method" => "PATCH"
    ];

    wp_remote_request(ALKUBOT_DELETE_STORE_URL, $data);
  }
}
