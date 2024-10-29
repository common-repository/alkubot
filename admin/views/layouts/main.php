<div class="alkubot-main-container">
  <h1><?php _e('Alkubot beállítása', 'Alkubot') ?></h1>

  <div class="alkubot-box">
    <form class="ajaxForm">
      <input type="hidden" value="updateToken" name="action">
      <div class="">
        <span><?php _e('Azonosító', 'Alkubot') ?></span>
        <input type="text" class="alkubot-token-input" name="token" value="<?php echo $token; ?>">
      </div>
      <div style="text-align: center">
        <button class="alkubot-btn" type="submit"><?php _e('Mentés', 'Alkubot') ?></button>
      </div>
      <div style="text-align: center; margin-top: 25px;">
        <?php _e('Még nincs fiókod?', 'Alkubot') ?>
        <a href="<?php _e('SIGNUP_LINK', 'Alkubot') ?>" target="_blank">
          <?php _e('Ide kattintva', 'Alkubot') ?></a>
        <?php _e('tudsz regisztrálni.', 'Alkubot') ?>
      </div>
    </form>
  </div>

  <div class="alkubot-box">
    <div class="alkubot-help-title"><?php _e('Problémába ütköztél?', 'Alkubot') ?></div>
    <div class="alkubot-help-text">
      <?php _e('Vedd fel velünk a', 'Alkubot') ?>
      <a href="<?php _e('CONTACT_LINK', 'Alkubot') ?>" target="_blank">
        <?php _e('kapcsolatot', 'Alkubot') ?></a>.
    </div>
  </div>
  <?php
  include_once(ALKUBOT_ADMIN_VIEWS_PATH . '/layouts/loader.php');
  include_once(ALKUBOT_ADMIN_VIEWS_PATH . '/layouts/popup.php');
  ?>
</div>

