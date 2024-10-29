<div class="notice is-dismissible">
    <div id="alkubot-notification-container">
        <div class="alkubot-notification-title">
          <?php echo $title; ?>
        </div>
        <div class="alkubot-notification-message">
          <?php echo $message; ?>
        </div>
        <div class="alkubot-notification-buttons">
            <a href="<?php echo admin_url('plugins.php'); ?>">
                <button class="alkubot-btn alkubot-notification-btn"><?php _e('Frissítem', 'Alkubot'); ?></button>
            </a>
            <a href="<?php echo admin_url('?page=alkubot&notification=false'); ?>">
                <button class="alkubot-btn alkubot-notification-dismiss-btn"><?php _e('Értesítés elrejtése', 'Alkubot'); ?></button>
            </a>
        </div>
    </div>
</div>
