<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Store_Selector
 * @subpackage Store_Selector/public/partials
 */
?>
<div class="ssm ssm-slide" id="store-selector-modal" role="dialog" aria-modal="true" aria-labelledby="ssm-title" aria-describedby="ssm-description" aria-hidden="true">
  <div class="ssm__overlay" tabindex="-1" data-micromodal-close>
    <div class="ssm__container">
      <header>
        <h2 id="ssm-title"><?php echo $title; ?></h2>
      </header>
      <main>
        <div class="site" data-ss-href="<?php echo $suggested_site_href ?>"><img src="<?php echo $suggested_site_img; ?>" /><span><?php echo $suggested_site_name; ?></span></div>
        <div id="ssm-description"><?php echo $explanation; ?></div>
      </main>
      <footer>
        <button id="ss-cancel" data-micromodal-close><?php echo $cancel_label ?></button>
        <button id="ss-accept"><?php echo $accept_label ?></button>
      </footer>
    </div>
  </div>
</div>
