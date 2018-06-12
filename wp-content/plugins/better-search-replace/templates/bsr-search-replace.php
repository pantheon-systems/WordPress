<?php
/**
 * Displays the main "Search/Replace" tab.
 *
 * @link       https://bettersearchreplace.com
 * @since      1.1
 *
 * @package    Better_Search_Replace
 * @subpackage Better_Search_Replace/templates
 */

// Prevent direct/unauthorized access.
if ( ! defined( 'BSR_PATH' ) ) exit;

?>

<div id="bsr-search-replace-wrap">

	<div class="inside">

		<p><?php _e( 'This tool allows you to search and replace text in your database (supports serialized arrays and objects).', 'better-search-replace' ); ?></p>
		<p><?php _e( 'To get started, use the form below to enter the text to be replaced and select the tables to update.', 'better-search-replace' ); ?></p>
		<p><?php _e( '<strong>WARNING:</strong> Make sure you backup your database before using this plugin!', 'better-search-replace' ); ?></p>

		<table id="bsr-search-replace-form" class="form-table">

			<tr>
				<td><label for="search_for"><strong><?php _e( 'Search for', 'better-search-replace' ); ?></strong></label></td>
				<td><input id="search_for" class="regular-text" type="text" name="search_for" value="<?php BSR_Admin::prefill_value( 'search_for' ); ?>" /></td>
			</tr>

			<tr>
				<td><label for="replace_with"><strong><?php _e( 'Replace with', 'better-search-replace' ); ?></strong></label></td>
				<td><input id="replace_with" class="regular-text" type="text" name="replace_with" value="<?php BSR_Admin::prefill_value( 'replace_with' ); ?>" /></td>
			</tr>

			<tr>
				<td><label for="select_tables"><strong><?php _e( 'Select tables', 'better-search-replace' ); ?></strong></label></td>
				<td>
					<?php BSR_Admin::load_tables(); ?>
					<p class="description"><?php _e( 'Select multiple tables with Ctrl-Click for Windows or Cmd-Click for Mac.', 'better-search-replace' ); ?></p>
				</td>
			</tr>

			<tr>
				<td><label for="case_insensitive"><strong><?php _e( 'Case-Insensitive?', 'better-search-replace' ); ?></strong></label></td>
				<td>
					<input id="case_insensitive" type="checkbox" name="case_insensitive" <?php BSR_Admin::prefill_value( 'case_insensitive', 'checkbox' ); ?> />
					<label for="case_insensitive"><span class="description"><?php _e( 'Searches are case-sensitive by default.', 'better-search-replace' ); ?></span></label>
				</td>
			</tr>

			<tr>
				<td><label for="replace_guids"><strong><?php _e( 'Replace GUIDs<a href="http://codex.wordpress.org/Changing_The_Site_URL#Important_GUID_Note" target="_blank">?</a>', 'better-search-replace' ); ?></strong></label></td>
				<td>
					<input id="replace_guids" type="checkbox" name="replace_guids" <?php BSR_Admin::prefill_value( 'replace_guids', 'checkbox' ); ?> />
					<label for="replace_guids"><span class="description"><?php _e( 'If left unchecked, all database columns titled \'guid\' will be skipped.', 'better-search-replace' ); ?></span></label>
				</td>
			</tr>

			<tr>
				<td><label for="dry_run"><strong><?php _e( 'Run as dry run?', 'better-search-replace' ); ?></strong></label></td>
				<td>
					<input id="dry_run" type="checkbox" name="dry_run" checked />
					<label for="dry_run"><span class="description"><?php _e( 'If checked, no changes will be made to the database, allowing you to check the results beforehand.', 'better-search-replace' ); ?></span></label>
				</td>
			</tr>

		</table>

		<br>

		<div id="bsr-submit-wrap">
			<?php wp_nonce_field( 'process_search_replace', 'bsr_nonce' ); ?>
			<input type="hidden" name="action" value="bsr_process_search_replace" />
			<button id="bsr-submit" type="submit" class="button"><?php _e( 'Run Search/Replace', 'better-search-replace' ); ?></button>
		</div>

	</div><!-- /.inside -->

</div><!-- /#bsr-search-replace-wrap -->
