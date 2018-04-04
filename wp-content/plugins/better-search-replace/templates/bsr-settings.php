<?php
/**
 * Displays the main "Settings" tab.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct/unauthorized access.
if ( ! defined( 'BSR_PATH' ) ) exit;

// Other settings.
$page_size 	= get_option( 'bsr_page_size' ) ? absint( get_option( 'bsr_page_size' ) ) : 20000;

 ?>

<?php settings_fields( 'bsr_settings_fields' ); ?>

<table class="form-table">
	<tbody>

		<tr valign="top">
			<th scope="row" valign="top">
				<?php _e( 'Max Page Size', 'better-search-replace' ); ?>
			</th>
			<td>
				<div id="bsr-page-size-slider" class="bsr-slider"></div>
				<br><span id="bsr-page-size-info"><?php _e( 'Current Setting: ', 'better-search-replace' ); ?></span><span id="bsr-page-size-value"><?php echo $page_size; ?></span>
				<input id="bsr_page_size" type="hidden" name="bsr_page_size" value="<?php echo $page_size; ?>" />
				<p class="description"><?php _e( 'If you\'re noticing timeouts or getting a white screen while running a search replace, try decreasing this value.', 'better-search-replace' ); ?></p>

			</td>
		</tr>

	</tbody>
</table>
<?php submit_button(); ?>
