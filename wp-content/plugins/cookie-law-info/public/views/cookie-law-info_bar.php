<?php 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if($notify_html==""){ return; } //if filter is applied
echo $notify_html;
?>
<div class="cli-modal-backdrop cli-fade cli-settings-overlay"></div>
<div class="cli-modal-backdrop cli-fade cli-popupbar-overlay"></div>
<script type="text/javascript">
  /* <![CDATA[ */
  cli_cookiebar_settings='<?php echo Cookie_Law_Info::get_json_settings(); ?>';
  /* ]]> */
</script>