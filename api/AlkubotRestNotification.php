<?php

class AlkubotRestNotification
{
  public static function updateNotification($request)
  {
    $title = $request['title'];
    $message = $request['message'];

    update_option(ALKUBOT_OPTION_NOTIFICATION_TITLE, $title);
    update_option(ALKUBOT_OPTION_NOTIFICATION_MESSAGE, $message);
    update_option(ALKUBOT_OPTION_NOTIFICATION_UNREAD, 'true');

    wp_send_json_success();
  }

}

