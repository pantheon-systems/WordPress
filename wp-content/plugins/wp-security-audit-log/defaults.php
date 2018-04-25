<?php
/**
 * Alerts file.
 *
 * Alerts are defined in this file.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If not included correctly...
if ( ! class_exists( 'WpSecurityAuditLog' ) ) {
	exit();
}

// Define custom / new PHP constants.
defined( 'E_CRITICAL' ) || define( 'E_CRITICAL', 'E_CRITICAL' );
defined( 'E_DEBUG' ) || define( 'E_DEBUG', 'E_DEBUG' );
defined( 'E_RECOVERABLE_ERROR' ) || define( 'E_RECOVERABLE_ERROR', 'E_RECOVERABLE_ERROR' );
defined( 'E_DEPRECATED' ) || define( 'E_DEPRECATED', 'E_DEPRECATED' );
defined( 'E_USER_DEPRECATED' ) || define( 'E_USER_DEPRECATED', 'E_USER_DEPRECATED' );

/**
 * Load Custom Alerts from uploads/wp-security-audit-log/custom-alerts.php if exists
 *
 * @param WpSecurityAuditLog $wsal - Instance of main plugin.
 */
function load_include_custom_file( $wsal ) {
	$upload_dir = wp_upload_dir();
	$uploads_dir_path = trailingslashit( $upload_dir['basedir'] ) . 'wp-security-audit-log';
	// Check directory.
	if ( is_dir( $uploads_dir_path ) && is_readable( $uploads_dir_path ) ) {
		$file = $uploads_dir_path . DIRECTORY_SEPARATOR . 'custom-alerts.php';
		if ( file_exists( $file ) ) {
			require_once( $file );
			if ( is_array( $custom_alerts ) ) {
				$wsal->alerts->RegisterGroup( $custom_alerts );
			}
		}
	}
}

/**
 * Define Default Alerts.
 *
 * Define default alerts for the plugin.
 *
 * @param WpSecurityAuditLog $wsal - Instance of main plugin.
 */
function wsaldefaults_wsal_init( WpSecurityAuditLog $wsal ) {
	$wsal->constants->UseConstants(
		array(
			// Default PHP constants.
			array(
				'name' => 'E_ERROR',
				'description' => __( 'Fatal run-time error.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_WARNING',
				'description' => __( 'Run-time warning (non-fatal error).', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_PARSE',
				'description' => __( 'Compile-time parse error.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_NOTICE',
				'description' => __( 'Run-time notice.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_CORE_ERROR',
				'description' => __( 'Fatal error that occurred during startup.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_CORE_WARNING',
				'description' => __( 'Warnings that occurred during startup.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_COMPILE_ERROR',
				'description' => __( 'Fatal compile-time error.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_COMPILE_WARNING',
				'description' => __( 'Compile-time warning.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_USER_ERROR',
				'description' => __( 'User-generated error message.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_USER_WARNING',
				'description' => __( 'User-generated warning message.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_USER_NOTICE',
				'description' => __( 'User-generated notice message.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_STRICT',
				'description' => __( 'Non-standard/optimal code warning.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_RECOVERABLE_ERROR',
				'description' => __( 'Catchable fatal error.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_DEPRECATED',
				'description' => __( 'Run-time deprecation notices.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_USER_DEPRECATED',
				'description' => __( 'Run-time user deprecation notices.', 'wp-security-audit-log' ),
			),
			// Custom constants.
			array(
				'name' => 'E_CRITICAL',
				'description' => __( 'Critical, high-impact messages.', 'wp-security-audit-log' ),
			),
			array(
				'name' => 'E_DEBUG',
				'description' => __( 'Debug informational messages.', 'wp-security-audit-log' ),
			),
		)
	);
	// Create list of default alerts.
	$wsal->alerts->RegisterGroup(
		array(
			/**
			 * Section: Content & Comments
			 */
			__( 'Content & Comments', 'wp-security-audit-log' ) => array(
				/**
				 * Alerts: Content
				 */
				__( 'Content', 'wp-security-audit-log' ) => array(
					array( 2000, E_NOTICE, __( 'User created a new post and saved it as draft', 'wp-security-audit-log' ), __( 'Created a new %PostType% titled %PostTitle% and saved it as draft. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2001, E_NOTICE, __( 'User published a post', 'wp-security-audit-log' ), __( 'Published a %PostType% titled %PostTitle%. URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2002, E_NOTICE, __( 'User modified a post', 'wp-security-audit-log' ), __( 'Modified the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2008, E_WARNING, __( 'User permanently deleted a post from the trash', 'wp-security-audit-log' ), __( 'Permanently deleted the %PostType% titled %PostTitle%. URL was %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2012, E_WARNING, __( 'User moved a post to the trash', 'wp-security-audit-log' ), __( 'Moved the %PostStatus% %PostType% titled %PostTitle% to trash. URL is %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2014, E_CRITICAL, __( 'User restored a post from trash', 'wp-security-audit-log' ), __( 'The %PostStatus% %PostType% titled %PostTitle% has been restored from trash. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2016, E_NOTICE, __( 'User changed post category', 'wp-security-audit-log' ), __( 'Changed the category of the %PostStatus% %PostType% titled %PostTitle% from %OldCategories% to %NewCategories%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2017, E_NOTICE, __( 'User changed post URL', 'wp-security-audit-log' ), __( 'Changed the URL of the %PostStatus% %PostType% titled %PostTitle% from %OldUrl% to %NewUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2019, E_NOTICE, __( 'User changed post author', 'wp-security-audit-log' ), __( 'Changed the author of the %PostStatus% %PostType% titled %PostTitle% from %OldAuthor% to %NewAuthor%. URL is: %PostUrl%.  %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2021, E_NOTICE, __( 'User changed post status', 'wp-security-audit-log' ), __( 'Changed the status of the %PostType% titled %PostTitle% from %OldStatus% to %NewStatus%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2023, E_NOTICE, __( 'User created new category', 'wp-security-audit-log' ), __( 'Created a new category called %CategoryName%. Category slug is %Slug%. %CategoryLink%.', 'wp-security-audit-log' ) ),
					array( 2024, E_WARNING, __( 'User deleted category', 'wp-security-audit-log' ), __( 'Deleted the %CategoryName% category. Category slug was %Slug%. %CategoryLink%.', 'wp-security-audit-log' ) ),
					array( 2025, E_WARNING, __( 'User changed the visibility of a post', 'wp-security-audit-log' ), __( 'Changed the visibility of the %PostStatus% %PostType% titled %PostTitle% from %OldVisibility% to %NewVisibility%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2027, E_NOTICE, __( 'User changed the date of a post', 'wp-security-audit-log' ), __( 'Changed the date of the %PostStatus% %PostType% titled %PostTitle% from %OldDate% to %NewDate%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2047, E_NOTICE, __( 'User changed the parent of a page', 'wp-security-audit-log' ), __( 'Changed the parent of the %PostStatus% %PostType% titled %PostTitle% from %OldParentName% to %NewParentName%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2048, E_CRITICAL, __( 'User changed the template of a page', 'wp-security-audit-log' ), __( 'Changed the template of the %PostStatus% %PostType% titled %PostTitle% from %OldTemplate% to %NewTemplate%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2049, E_NOTICE, __( 'User set a post as sticky', 'wp-security-audit-log' ), __( 'Set the post %PostTitle% as Sticky. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2050, E_NOTICE, __( 'User removed post from sticky', 'wp-security-audit-log' ), __( 'Removed the post %PostTitle% from Sticky. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2052, E_NOTICE, __( 'Changed the parent of a category.', 'wp-security-audit-log' ), __( 'Changed the parent of the category %CategoryName% from %OldParent% to %NewParent%. %CategoryLink%.', 'wp-security-audit-log' ) ),
					array( 2053, E_CRITICAL, __( 'User created a custom field for a post', 'wp-security-audit-log' ), __( 'Created a new custom field called %MetaKey% with value %MetaValue% in the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. %EditorLinkPost%.<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2054, E_CRITICAL, __( 'User updated a custom field value for a post', 'wp-security-audit-log' ), __( 'Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. %EditorLinkPost%.<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2055, E_CRITICAL, __( 'User deleted a custom field from a post', 'wp-security-audit-log' ), __( 'Deleted the custom field %MetaKey% with value %MetaValue% from %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2062, E_CRITICAL, __( 'User updated a custom field name for a post', 'wp-security-audit-log' ), __( 'Changed the custom field\'s name from %MetaKeyOld% to %MetaKeyNew% in the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. %EditorLinkPost%.<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2065, E_WARNING, __( 'User modified the content of a post.', 'wp-security-audit-log' ), __( 'Modified the content of the %PostStatus% %PostType% titled %PostTitle%. Post URL is %PostUrl%. %RevisionLink% %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2073, E_NOTICE, __( 'User submitted a post for review', 'wp-security-audit-log' ), __( 'Submitted the %PostType% titled %PostTitle% for review. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2074, E_NOTICE, __( 'User scheduled a post', 'wp-security-audit-log' ), __( 'Scheduled the %PostType% titled %PostTitle% to be published on %PublishingDate%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2086, E_NOTICE, __( 'User changed title of a post', 'wp-security-audit-log' ), __( 'Changed the title of the %PostStatus% %PostType% from %OldTitle% to %NewTitle%. URL is: %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2100, E_NOTICE, __( 'User opened a post in the editor', 'wp-security-audit-log' ), __( 'Opened the %PostStatus% %PostType% titled %PostTitle% in the editor. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2101, E_NOTICE, __( 'User viewed a post', 'wp-security-audit-log' ), __( 'Viewed the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2106, E_NOTICE, __( 'A plugin modified a post', 'wp-security-audit-log' ), __( 'Plugin modified the %PostStatus% %PostType% titled %PostTitle% of type %PostType%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2111, E_NOTICE, __( 'User disabled Comments/Trackbacks and Pingbacks in a post.', 'wp-security-audit-log' ), __( 'Disabled %Type% on the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2112, E_NOTICE, __( 'User enabled Comments/Trackbacks and Pingbacks in a post.', 'wp-security-audit-log' ), __( 'Enabled %Type% on the %PostStatus% %PostType% titled %PostTitle%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2119, E_NOTICE, __( 'User added post tag', 'wp-security-audit-log' ), __( 'Added the tag %tag% to the %PostStatus% post titled %PostTitle%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2120, E_NOTICE, __( 'User removed post tag', 'wp-security-audit-log' ), __( 'Removed the tag %tag% from the %PostStatus% post titled %PostTitle%. URL is: %PostUrl%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2121, E_NOTICE, __( 'User created new tag', 'wp-security-audit-log' ), __( 'Added a new tag called %TagName%. View the tag: %TagLink%.', 'wp-security-audit-log' ) ),
					array( 2122, E_NOTICE, __( 'User deleted tag', 'wp-security-audit-log' ), __( 'Deleted the tag %TagName%.', 'wp-security-audit-log' ) ),
					array( 2123, E_NOTICE, __( 'User renamed tag', 'wp-security-audit-log' ), __( 'Renamed a tag from %old_name% to %new_name%. View the tag: %TagLink%.', 'wp-security-audit-log' ) ),
					array( 2124, E_NOTICE, __( 'User changed tag slug', 'wp-security-audit-log' ), __( 'Changed the slug of tag %tag% from %old_slug% to %new_slug%. View the tag: %TagLink%.', 'wp-security-audit-log' ) ),
					array( 2125, E_NOTICE, __( 'User changed tag description', 'wp-security-audit-log' ), __( 'Changed the description of the tag %tag% from %old_desc% to %new_desc%. View the tag: %TagLink%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Comments
				 */
				__( 'Comments', 'wp-security-audit-log' ) => array(
					array( 2090, E_NOTICE, __( 'User approved a comment', 'wp-security-audit-log' ), __( 'Approved the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log' ) ),
					array( 2091, E_NOTICE, __( 'User unapproved a comment', 'wp-security-audit-log' ), __( 'Unapproved the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log' ) ),
					array( 2092, E_NOTICE, __( 'User replied to a comment', 'wp-security-audit-log' ), __( 'Replied to the comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log' ) ),
					array( 2093, E_NOTICE, __( 'User edited a comment', 'wp-security-audit-log' ), __( 'Edited a comment posted in response to the post %PostTitle% by %Author% on %CommentLink%.', 'wp-security-audit-log' ) ),
					array( 2094, E_NOTICE, __( 'User marked a comment as Spam', 'wp-security-audit-log' ), __( 'Marked the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% as Spam.', 'wp-security-audit-log' ) ),
					array( 2095, E_NOTICE, __( 'User marked a comment as Not Spam', 'wp-security-audit-log' ), __( 'Marked the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% as Not Spam.', 'wp-security-audit-log' ) ),
					array( 2096, E_NOTICE, __( 'User moved a comment to trash', 'wp-security-audit-log' ), __( 'Moved the comment posted in response to the post %PostTitle% by %Author% on %Date% to trash.', 'wp-security-audit-log' ) ),
					array( 2097, E_NOTICE, __( 'User restored a comment from the trash', 'wp-security-audit-log' ), __( 'Restored the comment posted in response to the post %PostTitle% by %Author% on %CommentLink% from the trash.', 'wp-security-audit-log' ) ),
					array( 2098, E_NOTICE, __( 'User permanently deleted a comment', 'wp-security-audit-log' ), __( 'Permanently deleted the comment posted in response to the post %PostTitle% by %Author% on %Date%.', 'wp-security-audit-log' ) ),
					array( 2099, E_NOTICE, __( 'User posted a comment', 'wp-security-audit-log' ), __( '%CommentMsg% on %CommentLink%.', 'wp-security-audit-log' ) ),
					array( 2126, E_NOTICE, __( 'Visitor posted a comment', 'wp-security-audit-log' ), __( '%CommentMsg% on %CommentLink%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Custom Post Types
				 *
				 * IMPORTANT: These alerts should not be removed from here
				 * for backwards compatibilty.
				 *
				 * @deprecated 3.1.0
				 */
				__( 'Custom Post Types', 'wp-security-audit-log' ) => array(
					array( 2003, E_NOTICE, __( 'User modified a draft blog post', 'wp-security-audit-log' ), __( 'Modified the draft post with the %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2029, E_NOTICE, __( 'User created a new post with custom post type and saved it as draft', 'wp-security-audit-log' ), __( 'Created a new custom post called %PostTitle% of type %PostType%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2030, E_NOTICE, __( 'User published a post with custom post type', 'wp-security-audit-log' ), __( 'Published a custom post %PostTitle% of type %PostType%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2031, E_NOTICE, __( 'User modified a post with custom post type', 'wp-security-audit-log' ), __( 'Modified the custom post %PostTitle% of type %PostType%. Post URL is %PostUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2032, E_NOTICE, __( 'User modified a draft post with custom post type', 'wp-security-audit-log' ), __( 'Modified the draft custom post %PostTitle% of type is %PostType%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2033, E_WARNING, __( 'User permanently deleted post with custom post type', 'wp-security-audit-log' ), __( 'Permanently Deleted the custom post %PostTitle% of type %PostType%.', 'wp-security-audit-log' ) ),
					array( 2034, E_WARNING, __( 'User moved post with custom post type to trash', 'wp-security-audit-log' ), __( 'Moved the custom post %PostTitle% of type %PostType% to trash. Post URL was %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2035, E_CRITICAL, __( 'User restored post with custom post type from trash', 'wp-security-audit-log' ), __( 'The custom post %PostTitle% of type %PostType% has been restored from trash. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2036, E_NOTICE, __( 'User changed the category of a post with custom post type', 'wp-security-audit-log' ), __( 'Changed the category(ies) of the custom post %PostTitle% of type %PostType% from %OldCategories% to %NewCategories%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2037, E_NOTICE, __( 'User changed the URL of a post with custom post type', 'wp-security-audit-log' ), __( 'Changed the URL of the custom post %PostTitle% of type %PostType% from %OldUrl% to %NewUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2038, E_NOTICE, __( 'User changed the author or post with custom post type', 'wp-security-audit-log' ), __( 'Changed the author of custom post %PostTitle% of type %PostType% from %OldAuthor% to %NewAuthor%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2039, E_NOTICE, __( 'User changed the status of post with custom post type', 'wp-security-audit-log' ), __( 'Changed the status of custom post %PostTitle% of type %PostType% from %OldStatus% to %NewStatus%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2040, E_WARNING, __( 'User changed the visibility of a post with custom post type', 'wp-security-audit-log' ), __( 'Changed the visibility of the custom post %PostTitle% of type %PostType% from %OldVisibility% to %NewVisibility%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2041, E_NOTICE, __( 'User changed the date of post with custom post type', 'wp-security-audit-log' ), __( 'Changed the date of the custom post %PostTitle% of type %PostType% from %OldDate% to %NewDate%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2056, E_CRITICAL, __( 'User created a custom field for a custom post type', 'wp-security-audit-log' ), __( 'Created a new custom field %MetaKey% with value %MetaValue% in custom post %PostTitle% of type %PostType%.' . ' %EditorLinkPost%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2057, E_CRITICAL, __( 'User updated a custom field for a custom post type', 'wp-security-audit-log' ), __( 'Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in custom post %PostTitle% of type %PostType%' . ' %EditorLinkPost%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2058, E_CRITICAL, __( 'User deleted a custom field from a custom post type', 'wp-security-audit-log' ), __( 'Deleted the custom field %MetaKey% with id %MetaID% from custom post %PostTitle% of type %PostType%' . ' %EditorLinkPost%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2063, E_CRITICAL, __( 'User updated a custom field name for a custom post type', 'wp-security-audit-log' ), __( 'Changed the custom field name from %MetaKeyOld% to %MetaKeyNew% in custom post %PostTitle% of type %PostType%' . ' %EditorLinkPost%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2067, E_WARNING, __( 'User modified content for a published custom post type', 'wp-security-audit-log' ), __( 'Modified the content of the published custom post type %PostTitle%. Post URL is %PostUrl%.' . '%EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2068, E_NOTICE, __( 'User modified content for a draft post', 'wp-security-audit-log' ), __( 'Modified the content of the draft post %PostTitle%.' . '%RevisionLink%' . ' %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2070, E_NOTICE, __( 'User modified content for a draft custom post type', 'wp-security-audit-log' ), __( 'Modified the content of the draft custom post type %PostTitle%.' . '%EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2072, E_NOTICE, __( 'User modified content of a post', 'wp-security-audit-log' ), __( 'Modified the content of post %PostTitle% which is submitted for review.' . '%RevisionLink%' . ' %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2076, E_NOTICE, __( 'User scheduled a custom post type', 'wp-security-audit-log' ), __( 'Scheduled the custom post type %PostTitle% to be published %PublishingDate%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2088, E_NOTICE, __( 'User changed title of a custom post type', 'wp-security-audit-log' ), __( 'Changed the title of the custom post %OldTitle% to %NewTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2104, E_NOTICE, __( 'User opened a custom post type in the editor', 'wp-security-audit-log' ), __( 'Opened the custom post %PostTitle% of type %PostType% in the editor. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 2105, E_NOTICE, __( 'User viewed a custom post type', 'wp-security-audit-log' ), __( 'Viewed the custom post %PostTitle% of type %PostType%. View the post: %PostUrl%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Pages
				 *
				 * IMPORTANT: These alerts should not be removed from here
				 * for backwards compatibilty.
				 *
				 * @deprecated 3.1.0
				 */
				__( 'Pages', 'wp-security-audit-log' ) => array(
					array( 2004, E_NOTICE, __( 'User created a new WordPress page and saved it as draft', 'wp-security-audit-log' ), __( 'Created a new page called %PostTitle% and saved it as draft. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2005, E_NOTICE, __( 'User published a WordPress page', 'wp-security-audit-log' ), __( 'Published a page called %PostTitle%. Page URL is %PostUrl%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2006, E_NOTICE, __( 'User modified a published WordPress page', 'wp-security-audit-log' ), __( 'Modified the published page %PostTitle%. Page URL is %PostUrl%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2007, E_NOTICE, __( 'User modified a draft WordPress page', 'wp-security-audit-log' ), __( 'Modified the draft page %PostTitle%. Page ID is %PostID%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2009, E_WARNING, __( 'User permanently deleted a page from the trash', 'wp-security-audit-log' ), __( 'Permanently deleted the page %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 2013, E_WARNING, __( 'User moved WordPress page to the trash', 'wp-security-audit-log' ), __( 'Moved the page %PostTitle% to trash. Page URL was %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2015, E_CRITICAL, __( 'User restored a WordPress page from trash', 'wp-security-audit-log' ), __( 'Page %PostTitle% has been restored from trash. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2018, E_NOTICE, __( 'User changed page URL', 'wp-security-audit-log' ), __( 'Changed the URL of the page %PostTitle% from %OldUrl% to %NewUrl%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2020, E_NOTICE, __( 'User changed page author', 'wp-security-audit-log' ), __( 'Changed the author of the page %PostTitle% from %OldAuthor% to %NewAuthor%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2022, E_NOTICE, __( 'User changed page status', 'wp-security-audit-log' ), __( 'Changed the status of the page %PostTitle% from %OldStatus% to %NewStatus%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2026, E_WARNING, __( 'User changed the visibility of a page post', 'wp-security-audit-log' ), __( 'Changed the visibility of the page %PostTitle% from %OldVisibility% to %NewVisibility%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2028, E_NOTICE, __( 'User changed the date of a page post', 'wp-security-audit-log' ), __( 'Changed the date of the page %PostTitle% from %OldDate% to %NewDate%. %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2059, E_CRITICAL, __( 'User created a custom field for a page', 'wp-security-audit-log' ), __( 'Created a new custom field called %MetaKey% with value %MetaValue% in the page %PostTitle%' . ' %EditorLinkPage%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2060, E_CRITICAL, __( 'User updated a custom field value for a page', 'wp-security-audit-log' ), __( 'Modified the value of the custom field %MetaKey% from %MetaValueOld% to %MetaValueNew% in the page %PostTitle%' . ' %EditorLinkPage%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2061, E_CRITICAL, __( 'User deleted a custom field from a page', 'wp-security-audit-log' ), __( 'Deleted the custom field %MetaKey% with id %MetaID% from page %PostTitle%' . ' %EditorLinkPage%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2064, E_CRITICAL, __( 'User updated a custom field name for a page', 'wp-security-audit-log' ), __( 'Changed the custom field name from %MetaKeyOld% to %MetaKeyNew% in the page %PostTitle%' . ' %EditorLinkPage%.' . '<br>%MetaLink%.', 'wp-security-audit-log' ) ),
					array( 2066, E_WARNING, __( 'User modified content for a published page', 'wp-security-audit-log' ), __( 'Modified the content of the published page %PostTitle%. Page URL is %PostUrl%. %RevisionLink% %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2069, E_NOTICE, __( 'User modified content for a draft page', 'wp-security-audit-log' ), __( 'Modified the content of draft page %PostTitle%.' . '%RevisionLink%' . ' %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2075, E_NOTICE, __( 'User scheduled a page', 'wp-security-audit-log' ), __( 'Scheduled the page %PostTitle% to be published %PublishingDate%.' . ' %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2087, E_NOTICE, __( 'User changed title of a page', 'wp-security-audit-log' ), __( 'Changed the title of the page %OldTitle% to %NewTitle%.' . ' %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2102, E_NOTICE, __( 'User opened a page in the editor', 'wp-security-audit-log' ), __( 'Opened the page %PostTitle% in the editor. View the page: %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2103, E_NOTICE, __( 'User viewed a page', 'wp-security-audit-log' ), __( 'Viewed the page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2113, E_NOTICE, __( 'User disabled Comments/Trackbacks and Pingbacks on a draft post', 'wp-security-audit-log' ), __( 'Disabled %Type% on the draft post %PostTitle%. View the post: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2114, E_NOTICE, __( 'User enabled Comments/Trackbacks and Pingbacks on a draft post', 'wp-security-audit-log' ), __( 'Enabled %Type% on the draft post %PostTitle%. View the post: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2115, E_NOTICE, __( 'User disabled Comments/Trackbacks and Pingbacks on a published page', 'wp-security-audit-log' ), __( 'Disabled %Type% on the published page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2116, E_NOTICE, __( 'User enabled Comments/Trackbacks and Pingbacks on a published page', 'wp-security-audit-log' ), __( 'Enabled %Type% on the published page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2117, E_NOTICE, __( 'User disabled Comments/Trackbacks and Pingbacks on a draft page', 'wp-security-audit-log' ), __( 'Disabled %Type% on the draft page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log' ) ),
					array( 2118, E_NOTICE, __( 'User enabled Comments/Trackbacks and Pingbacks on a draft page', 'wp-security-audit-log' ), __( 'Enabled %Type% on the draft page %PostTitle%. View the page: %PostUrl%.', 'wp-security-audit-log' ) ),
				),
			),

			/**
			 * Section: WordPress & Multisite Management
			 */
			__( 'WordPress & Multisite Management', 'wp-security-audit-log' ) => array(
				/**
				 * Alerts: Database
				 */
				__( 'Database', 'wp-security-audit-log' ) => array(
					array( 5010, E_CRITICAL, __( 'Plugin created tables', 'wp-security-audit-log' ), __( 'Plugin %Plugin->Name% created these tables in the database: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5011, E_CRITICAL, __( 'Plugin modified tables structure', 'wp-security-audit-log' ), __( 'Plugin %Plugin->Name% modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5012, E_CRITICAL, __( 'Plugin deleted tables', 'wp-security-audit-log' ), __( 'Plugin %Plugin->Name% deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5013, E_CRITICAL, __( 'Theme created tables', 'wp-security-audit-log' ), __( 'Theme %Theme->Name% created these tables in the database: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5014, E_CRITICAL, __( 'Theme modified tables structure', 'wp-security-audit-log' ), __( 'Theme %Theme->Name% modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5015, E_CRITICAL, __( 'Theme deleted tables', 'wp-security-audit-log' ), __( 'Theme %Theme->Name% deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5016, E_CRITICAL, __( 'Unknown component created tables', 'wp-security-audit-log' ), __( 'An unknown component created these tables in the database: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5017, E_CRITICAL, __( 'Unknown component modified tables structure', 'wp-security-audit-log' ), __( 'An unknown component modified the structure of these database tables: %TableNames%.', 'wp-security-audit-log' ) ),
					array( 5018, E_CRITICAL, __( 'Unknown component deleted tables', 'wp-security-audit-log' ), __( 'An unknown component deleted the following tables from the database: %TableNames%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: MultiSite
				 */
				__( 'MultiSite', 'wp-security-audit-log' ) => array(
					array( 4008, E_CRITICAL, __( 'User granted Super Admin privileges', 'wp-security-audit-log' ), __( 'Granted Super Admin privileges to %TargetUsername%.', 'wp-security-audit-log' ) ),
					array( 4009, E_CRITICAL, __( 'User revoked from Super Admin privileges', 'wp-security-audit-log' ), __( 'Revoked Super Admin privileges from %TargetUsername%.', 'wp-security-audit-log' ) ),
					array( 4010, E_CRITICAL, __( 'Existing user added to a site', 'wp-security-audit-log' ), __( 'Added the existing user %TargetUsername% with %TargetUserRole% role to site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 4011, E_CRITICAL, __( 'User removed from site', 'wp-security-audit-log' ), __( 'Removed the user %TargetUsername% with role %TargetUserRole% from %SiteName% site.', 'wp-security-audit-log' ) ),
					array( 4012, E_CRITICAL, __( 'New network user created', 'wp-security-audit-log' ), __( 'Created a new network user %NewUserData->Username%.', 'wp-security-audit-log' ) ),
					array( 4013, E_CRITICAL, __( 'The forum role of a user was changed by another WordPress user', 'wp-security-audit-log' ), __( 'Change the forum role of the user %TargetUsername% from %OldRole% to %NewRole% by %UserChanger%.', 'wp-security-audit-log' ) ),
					array( 7000, E_CRITICAL, __( 'New site added on the network', 'wp-security-audit-log' ), __( 'Added the site %SiteName% to the network.', 'wp-security-audit-log' ) ),
					array( 7001, E_CRITICAL, __( 'Existing site archived', 'wp-security-audit-log' ), __( 'Archived the site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 7002, E_CRITICAL, __( 'Archived site has been unarchived', 'wp-security-audit-log' ), __( 'Unarchived the site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 7003, E_CRITICAL, __( 'Deactivated site has been activated', 'wp-security-audit-log' ), __( 'Activated the site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 7004, E_CRITICAL, __( 'Site has been deactivated', 'wp-security-audit-log' ), __( 'Deactivated the site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 7005, E_CRITICAL, __( 'Existing site deleted from network', 'wp-security-audit-log' ), __( 'Deleted the site %SiteName%.', 'wp-security-audit-log' ) ),
					array( 5008, E_CRITICAL, __( 'Activated theme on network', 'wp-security-audit-log' ), __( 'Network activated the theme %Theme->Name% installed in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
					array( 5009, E_CRITICAL, __( 'Deactivated theme from network', 'wp-security-audit-log' ), __( 'Network deactivated the theme %Theme->Name% installed in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Plugins & Themes
				 */
				__( 'Plugins & Themes', 'wp-security-audit-log' ) => array(
					array( 5000, E_CRITICAL, __( 'User installed a plugin', 'wp-security-audit-log' ), __( 'Installed the plugin %Plugin->Name% in %Plugin->plugin_dir_path%.', 'wp-security-audit-log' ) ),
					array( 5001, E_CRITICAL, __( 'User activated a WordPress plugin', 'wp-security-audit-log' ), __( 'Activated the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log' ) ),
					array( 5002, E_CRITICAL, __( 'User deactivated a WordPress plugin', 'wp-security-audit-log' ), __( 'Deactivated the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log' ) ),
					array( 5003, E_CRITICAL, __( 'User uninstalled a plugin', 'wp-security-audit-log' ), __( 'Uninstalled the plugin %PluginData->Name% which was installed in %PluginFile%.', 'wp-security-audit-log' ) ),
					array( 5004, E_WARNING, __( 'User upgraded a plugin', 'wp-security-audit-log' ), __( 'Upgraded the plugin %PluginData->Name% installed in %PluginFile%.', 'wp-security-audit-log' ) ),
					array( 5005, E_WARNING, __( 'User installed a theme', 'wp-security-audit-log' ), __( 'Installed the theme "%Theme->Name%" in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
					array( 5006, E_CRITICAL, __( 'User activated a theme', 'wp-security-audit-log' ), __( 'Activated the theme "%Theme->Name%", installed in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
					array( 5007, E_CRITICAL, __( 'User uninstalled a theme', 'wp-security-audit-log' ), __( 'Deleted the theme "%Theme->Name%" installed in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
					array( 5019, E_CRITICAL, __( 'A plugin created a post', 'wp-security-audit-log' ), __( 'A plugin automatically created the following %PostType% called %PostTitle%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 5020, E_CRITICAL, __( 'A plugin created a page', 'wp-security-audit-log' ), __( 'A plugin automatically created the following page: %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 5021, E_CRITICAL, __( 'A plugin created a custom post', 'wp-security-audit-log' ), __( 'A plugin automatically created the following custom post: %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 5025, E_CRITICAL, __( 'A plugin deleted a post', 'wp-security-audit-log' ), __( 'A plugin automatically deleted the following %PostType% called %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 5026, E_CRITICAL, __( 'A plugin deleted a page', 'wp-security-audit-log' ), __( 'A plugin automatically deleted the following page: %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 5027, E_CRITICAL, __( 'A plugin deleted a custom post', 'wp-security-audit-log' ), __( 'A plugin automatically deleted the following custom post: %PostTitle%.', 'wp-security-audit-log' ) ),
					array( 5031, E_WARNING, __( 'User updated a theme', 'wp-security-audit-log' ), __( 'Updated the theme "%Theme->Name%" installed in %Theme->get_template_directory%.', 'wp-security-audit-log' ) ),
					array( 2046, E_CRITICAL, __( 'User changed a file using the theme editor', 'wp-security-audit-log' ), __( 'Modified %File% with the Theme Editor.', 'wp-security-audit-log' ) ),
					array( 2051, E_CRITICAL, __( 'User changed a file using the plugin editor', 'wp-security-audit-log' ), __( 'Modified %File% with the Plugin Editor.', 'wp-security-audit-log' ) ),
					array( 2107, E_NOTICE, __( 'A plugin modified a page', 'wp-security-audit-log' ), __( 'Plugin modified the page %PostTitle%. View the page: %EditorLinkPage%.', 'wp-security-audit-log' ) ),
					array( 2108, E_NOTICE, __( 'A plugin modified a custom post', 'wp-security-audit-log' ), __( 'Plugin modified the custom post %PostTitle%. View the post: %EditorLinkPost%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: System Activity
				 */
				__( 'System Activity', 'wp-security-audit-log' ) => array(
					array( 0000, E_CRITICAL, __( 'Unknown Error', 'wp-security-audit-log' ), __( 'An unexpected error has occurred .', 'wp-security-audit-log' ) ),
					array( 0001, E_CRITICAL, __( 'PHP error', 'wp-security-audit-log' ), __( '%Message%.', 'wp-security-audit-log' ) ),
					array( 0002, E_WARNING, __( 'PHP warning', 'wp-security-audit-log' ), __( '%Message%.', 'wp-security-audit-log' ) ),
					array( 0003, E_NOTICE, __( 'PHP notice', 'wp-security-audit-log' ), __( '%Message%.', 'wp-security-audit-log' ) ),
					array( 0004, E_CRITICAL, __( 'PHP exception', 'wp-security-audit-log' ), __( '%Message%.', 'wp-security-audit-log' ) ),
					array( 0005, E_CRITICAL, __( 'PHP shutdown error', 'wp-security-audit-log' ), __( '%Message%.', 'wp-security-audit-log' ) ),
					array( 6000, E_NOTICE, __( 'Events automatically pruned by system', 'wp-security-audit-log' ), __( 'System automatically deleted %EventCount% alert(s).', 'wp-security-audit-log' ) ),
					array( 6001, E_CRITICAL, __( 'Option Anyone Can Register in WordPress settings changed', 'wp-security-audit-log' ), __( '%NewValue% the option "Anyone can register".', 'wp-security-audit-log' ) ),
					array( 6002, E_CRITICAL, __( 'New User Default Role changed', 'wp-security-audit-log' ), __( 'Changed the New User Default Role from %OldRole% to %NewRole%.', 'wp-security-audit-log' ) ),
					array( 6003, E_CRITICAL, __( 'WordPress Administrator Notification email changed', 'wp-security-audit-log' ), __( 'Changed the WordPress administrator notifications email address from %OldEmail% to %NewEmail%.', 'wp-security-audit-log' ) ),
					array( 6004, E_CRITICAL, __( 'WordPress was updated', 'wp-security-audit-log' ), __( 'Updated WordPress from version %OldVersion% to %NewVersion%.', 'wp-security-audit-log' ) ),
					array( 6005, E_CRITICAL, __( 'User changes the WordPress Permalinks', 'wp-security-audit-log' ), __( 'Changed the WordPress permalinks from %OldPattern% to %NewPattern%.', 'wp-security-audit-log' ) ),
					array( 6007, E_NOTICE, __( 'User requests non-existing pages (404 Error Pages)', 'wp-security-audit-log' ), __( 'Has requested a non existing page (404 Error Pages) %Attempts% %Msg%. %LinkFile%', 'wp-security-audit-log' ) ),
					array( 6023, E_NOTICE, __( 'Website Visitor User requests non-existing pages (404 Error Pages)', 'wp-security-audit-log' ), __( 'Website Visitor Has requested a non existing page (404 Error Pages) %Attempts% %Msg%. %LinkFile%', 'wp-security-audit-log' ) ),
					array( 6024, E_CRITICAL, __( 'Option WordPress Address (URL) in WordPress settings changed', 'wp-security-audit-log' ), __( 'Changed the WordPress address (URL) from %old_url% to %new_url%.', 'wp-security-audit-log' ) ),
					array( 6025, E_CRITICAL, __( 'Option Site Address (URL) in WordPress settings changed', 'wp-security-audit-log' ), __( 'Changed the site address (URL) from %old_url% to %new_url%.', 'wp-security-audit-log' ) ),
					array( 9999, E_CRITICAL, __( 'Advertising Add-ons.', 'wp-security-audit-log' ), __( '%PromoName% %PromoMessage%', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Menus
				 */
				__( 'Menus', 'wp-security-audit-log' ) => array(
					array( 2078, E_NOTICE, __( 'User created new menu', 'wp-security-audit-log' ), __( 'Created a new menu called %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2079, E_WARNING, __( 'User added content to a menu', 'wp-security-audit-log' ), __( 'Added the %ContentType% called %ContentName% to menu %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2080, E_WARNING, __( 'User removed content from a menu', 'wp-security-audit-log' ), __( 'Removed the %ContentType% called %ContentName% from the menu %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2081, E_CRITICAL, __( 'User deleted menu', 'wp-security-audit-log' ), __( 'Deleted the menu %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2082, E_WARNING, __( 'User changed menu setting', 'wp-security-audit-log' ), __( '%Status% the menu setting %MenuSetting% in %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2083, E_NOTICE, __( 'User modified content in a menu', 'wp-security-audit-log' ), __( 'Modified the %ContentType% called %ContentName% in menu %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2084, E_WARNING, __( 'User changed name of a menu', 'wp-security-audit-log' ), __( 'Changed the name of menu %OldMenuName% to %NewMenuName%.', 'wp-security-audit-log' ) ),
					array( 2085, E_NOTICE, __( 'User changed order of the objects in a menu', 'wp-security-audit-log' ), __( 'Changed the order of the %ItemName% in menu %MenuName%.', 'wp-security-audit-log' ) ),
					array( 2089, E_NOTICE, __( 'User moved objects as a sub-item', 'wp-security-audit-log' ), __( 'Moved %ItemName% as a sub-item of %ParentName% in menu %MenuName%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Widgets
				 */
				__( 'Widgets', 'wp-security-audit-log' ) => array(
					array( 2042, E_CRITICAL, __( 'User added a new widget', 'wp-security-audit-log' ), __( 'Added a new %WidgetName% widget in  %Sidebar%.', 'wp-security-audit-log' ) ),
					array( 2043, E_WARNING, __( 'User modified a widget', 'wp-security-audit-log' ), __( 'Modified the %WidgetName% widget in %Sidebar%.', 'wp-security-audit-log' ) ),
					array( 2044, E_CRITICAL, __( 'User deleted widget', 'wp-security-audit-log' ), __( 'Deleted the %WidgetName% widget from %Sidebar%.', 'wp-security-audit-log' ) ),
					array( 2045, E_NOTICE, __( 'User moved widget', 'wp-security-audit-log' ), __( 'Moved the %WidgetName% widget from %OldSidebar% to %NewSidebar%.', 'wp-security-audit-log' ) ),
					array( 2071, E_NOTICE, __( 'User changed widget position', 'wp-security-audit-log' ), __( 'Changed the position of the widget %WidgetName% in sidebar %Sidebar%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: Site Settings
				 */
				__( 'Site Settings', 'wp-security-audit-log' ) => array(
					array( 6008, E_CRITICAL, __( 'Enabled/Disabled the option Discourage search engines from indexing this site', 'wp-security-audit-log' ), __( '%Status% the option Discourage search engines from indexing this site.', 'wp-security-audit-log' ) ),
					array( 6009, E_CRITICAL, __( 'Enabled/Disabled comments on all the website', 'wp-security-audit-log' ), __( '%Status% comments on all the website.', 'wp-security-audit-log' ) ),
					array( 6010, E_CRITICAL, __( 'Enabled/Disabled the option Comment author must fill out name and email', 'wp-security-audit-log' ), __( '%Status% the option Comment author must fill out name and email.', 'wp-security-audit-log' ) ),
					array( 6011, E_CRITICAL, __( 'Enabled/Disabled the option Users must be logged in and registered to comment', 'wp-security-audit-log' ), __( '%Status% the option Users must be logged in and registered to comment.', 'wp-security-audit-log' ) ),
					array( 6012, E_CRITICAL, __( 'Enabled/Disabled the option to automatically close comments', 'wp-security-audit-log' ), __( '%Status% the option to automatically close comments after %Value% days.', 'wp-security-audit-log' ) ),
					array( 6013, E_NOTICE, __( 'Changed the value of the option Automatically close comments', 'wp-security-audit-log' ), __( 'Changed the value of the option Automatically close comments from %OldValue% to %NewValue% days.', 'wp-security-audit-log' ) ),
					array( 6014, E_CRITICAL, __( 'Enabled/Disabled the option for comments to be manually approved', 'wp-security-audit-log' ), __( '%Status% the option for comments to be manually approved.', 'wp-security-audit-log' ) ),
					array( 6015, E_CRITICAL, __( 'Enabled/Disabled the option for an author to have previously approved comments for the comments to appear', 'wp-security-audit-log' ), __( '%Status% the option for an author to have previously approved comments for the comments to appear.', 'wp-security-audit-log' ) ),
					array( 6016, E_CRITICAL, __( 'Changed the number of links that a comment must have to be held in the queue', 'wp-security-audit-log' ), __( 'Changed the number of links from %OldValue% to %NewValue% that a comment must have to be held in the queue.', 'wp-security-audit-log' ) ),
					array( 6017, E_CRITICAL, __( 'Modified the list of keywords for comments moderation', 'wp-security-audit-log' ), __( 'Modified the list of keywords for comments moderation.', 'wp-security-audit-log' ) ),
					array( 6018, E_CRITICAL, __( 'Modified the list of keywords for comments blacklisting', 'wp-security-audit-log' ), __( 'Modified the list of keywords for comments blacklisting.', 'wp-security-audit-log' ) ),
					array( 6019, E_CRITICAL, __( 'Created a New cron job', 'wp-security-audit-log' ), __( 'A new cron job called %name% was created and is scheduled to run %schedule%.', 'wp-security-audit-log' ) ),
					array( 6020, E_CRITICAL, __( 'Changed status of the cron job', 'wp-security-audit-log' ), __( 'The cron job %name% was %status%.', 'wp-security-audit-log' ) ),
					array( 6021, E_CRITICAL, __( 'Deleted the cron job', 'wp-security-audit-log' ), __( 'The cron job %name% was deleted.', 'wp-security-audit-log' ) ),
					array( 6022, E_NOTICE, __( 'Started the cron job', 'wp-security-audit-log' ), __( 'The cron job %name% has just started.', 'wp-security-audit-log' ) ),
				),
			),

			/**
			 * Section: Users Profiles & Activity
			 */
			__( 'Users Profiles & Activity', 'wp-security-audit-log' ) => array(
				/**
				 * Alerts: Other User Activity
				 */
				__( 'Other User Activity', 'wp-security-audit-log' ) => array(
					array( 1000, E_NOTICE, __( 'User logged in', 'wp-security-audit-log' ), __( 'Successfully logged in.', 'wp-security-audit-log' ) ),
					array( 1001, E_NOTICE, __( 'User logged out', 'wp-security-audit-log' ), __( 'Successfully logged out.', 'wp-security-audit-log' ) ),
					array( 1002, E_WARNING, __( 'Login failed', 'wp-security-audit-log' ), __( '%Attempts% failed login(s) detected.', 'wp-security-audit-log' ) ),
					array( 1003, E_WARNING, __( 'Login failed  / non existing user', 'wp-security-audit-log' ), __( '%Attempts% failed login(s) detected using non existing user. %LogFileText%', 'wp-security-audit-log' ) ),
					array( 1004, E_WARNING, __( 'Login blocked', 'wp-security-audit-log' ), __( 'Blocked from logging in because the same WordPress user is logged in from %ClientIP%.', 'wp-security-audit-log' ) ),
					array( 1005, E_WARNING, __( 'User logged in with existing session(s)', 'wp-security-audit-log' ), __( 'Successfully logged in. Another session from %IPAddress% for this user already exist.', 'wp-security-audit-log' ) ),
					array( 1006, E_CRITICAL, __( 'User logged out all other sessions with the same username', 'wp-security-audit-log' ), __( 'Logged out all other sessions with the same username.', 'wp-security-audit-log' ) ),
					array( 1007, E_CRITICAL, __( 'User session destroyed and logged out.', 'wp-security-audit-log' ), __( 'Logged out session %TargetSessionID% which belonged to %TargetUserName%', 'wp-security-audit-log' ) ),
					array( 2010, E_NOTICE, __( 'User uploaded file from Uploads directory', 'wp-security-audit-log' ), __( 'Uploaded the file %FileName% in %FilePath%.', 'wp-security-audit-log' ) ),
					array( 2011, E_WARNING, __( 'User deleted file from Uploads directory', 'wp-security-audit-log' ), __( 'Deleted the file %FileName% from %FilePath%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: User Profiles
				 */
				__( 'User Profiles', 'wp-security-audit-log' ) => array(
					array( 4000, E_CRITICAL, __( 'New user was created on WordPress', 'wp-security-audit-log' ), __( 'A new user %NewUserData->Username% was created with role of %NewUserData->Roles%.', 'wp-security-audit-log' ) ),
					array( 4001, E_CRITICAL, __( 'User created another WordPress user', 'wp-security-audit-log' ), __( '%UserChanger% created a new user %NewUserData->Username% with the role of %NewUserData->Roles%.', 'wp-security-audit-log' ) ),
					array( 4002, E_CRITICAL, __( 'The role of a user was changed by another WordPress user', 'wp-security-audit-log' ), __( 'Changed the role of the user %TargetUsername% from %OldRole% to %NewRole%%multisite_text%.', 'wp-security-audit-log' ) ),
					array( 4003, E_CRITICAL, __( 'User has changed his or her password', 'wp-security-audit-log' ), __( 'Changed the password.', 'wp-security-audit-log' ) ),
					array( 4004, E_CRITICAL, __( 'User changed another user\'s password', 'wp-security-audit-log' ), __( 'Changed the password for the user %TargetUserData->Username% with the role of %TargetUserData->Roles%.', 'wp-security-audit-log' ) ),
					array( 4005, E_NOTICE, __( 'User changed his or her email address', 'wp-security-audit-log' ), __( 'Changed the email address from %OldEmail% to %NewEmail%.', 'wp-security-audit-log' ) ),
					array( 4006, E_NOTICE, __( 'User changed another user\'s email address', 'wp-security-audit-log' ), __( 'Changed the email address of the user %TargetUsername% from %OldEmail% to %NewEmail%.', 'wp-security-audit-log' ) ),
					array( 4007, E_CRITICAL, __( 'User was deleted by another user', 'wp-security-audit-log' ), __( 'Deleted the user %TargetUserData->Username% with the role of %TargetUserData->Roles%.', 'wp-security-audit-log' ) ),
					array( 4014, E_NOTICE, __( 'User opened the profile page of another user', 'wp-security-audit-log' ), __( '%UserChanger% opened the profile page of the user %TargetUsername%.', 'wp-security-audit-log' ) ),
					array( 4015, E_NOTICE, __( 'User updated a custom field value for a user', 'wp-security-audit-log' ), __( 'Changed the value of the custom field %custom_field_name% from %old_value% to %new_value% for the user %TargetUsername%.', 'wp-security-audit-log' ) ),
					array( 4016, E_NOTICE, __( 'User created a custom field value for a user', 'wp-security-audit-log' ), __( 'Created the value of the custom field %custom_field_name% with %new_value% for the user %TargetUsername%.', 'wp-security-audit-log' ) ),
					array( 4017, E_NOTICE, __( 'User changed first name for a user', 'wp-security-audit-log' ), __( 'Changed the first name of the user %TargetUsername% from %old_firstname% to %new_firstname%', 'wp-security-audit-log' ) ),
					array( 4018, E_NOTICE, __( 'User changed last name for a user', 'wp-security-audit-log' ), __( 'Changed the last name of the user %TargetUsername% from %old_lastname% to %new_lastname%', 'wp-security-audit-log' ) ),
					array( 4019, E_NOTICE, __( 'User changed nickname for a user', 'wp-security-audit-log' ), __( 'Changed the nickname of the user %TargetUsername% from %old_nickname% to %new_nickname%', 'wp-security-audit-log' ) ),
					array( 4020, E_WARNING, __( 'User changed the display name for a user', 'wp-security-audit-log' ), __( 'Changed the Display name publicly of user %TargetUsername% from %old_displayname% to %new_displayname%', 'wp-security-audit-log' ) ),
				),
			),

			/**
			 * Section: Third Party Support
			 */
			__( 'Third Party Support', 'wp-security-audit-log' ) => array(
				/**
				 * Alerts: BBPress Forum
				 */
				__( 'BBPress Forum', 'wp-security-audit-log' ) => array(
					array( 8000, E_CRITICAL, __( 'User created new forum', 'wp-security-audit-log' ), __( 'Created new forum %ForumName%. Forum URL is %ForumURL%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8001, E_NOTICE, __( 'User changed status of a forum', 'wp-security-audit-log' ), __( 'Changed the status of the forum %ForumName% from %OldStatus% to %NewStatus%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8002, E_NOTICE, __( 'User changed visibility of a forum', 'wp-security-audit-log' ), __( 'Changed the visibility of the forum %ForumName% from %OldVisibility% to %NewVisibility%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8003, E_CRITICAL, __( 'User changed the URL of a forum', 'wp-security-audit-log' ), __( 'Changed the URL of the forum %ForumName% from %OldUrl% to %NewUrl%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8004, E_NOTICE, __( 'User changed order of a forum', 'wp-security-audit-log' ), __( 'Changed the order of the forum %ForumName% from %OldOrder% to %NewOrder%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8005, E_CRITICAL, __( 'User moved forum to trash', 'wp-security-audit-log' ), __( 'Moved the forum %ForumName% to trash.', 'wp-security-audit-log' ) ),
					array( 8006, E_WARNING, __( 'User permanently deleted forum', 'wp-security-audit-log' ), __( 'Permanently deleted the forum %ForumName%.', 'wp-security-audit-log' ) ),
					array( 8007, E_WARNING, __( 'User restored forum from trash', 'wp-security-audit-log' ), __( 'Restored the forum %ForumName% from trash.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8008, E_NOTICE, __( 'User changed the parent of a forum', 'wp-security-audit-log' ), __( 'Changed the parent of the forum %ForumName% from %OldParent% to %NewParent%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8009, E_WARNING, __( 'User changed forum\'s role', 'wp-security-audit-log' ), __( 'Changed the forum\'s auto role from %OldRole% to %NewRole%.', 'wp-security-audit-log' ) ),
					array( 8010, E_WARNING, __( 'User changed option of a forum', 'wp-security-audit-log' ), __( '%Status% the option for anonymous posting on forum.', 'wp-security-audit-log' ) ),
					array( 8011, E_NOTICE, __( 'User changed type of a forum', 'wp-security-audit-log' ), __( 'Changed the type of the forum %ForumName% from %OldType% to %NewType%.' . ' %EditorLinkForum%.', 'wp-security-audit-log' ) ),
					array( 8012, E_NOTICE, __( 'User changed time to disallow post editing', 'wp-security-audit-log' ), __( 'Changed the time to disallow post editing from %OldTime% to %NewTime% minutes in the forums.', 'wp-security-audit-log' ) ),
					array( 8013, E_WARNING, __( 'User changed the forum setting posting throttle time', 'wp-security-audit-log' ), __( 'Changed the posting throttle time from %OldTime% to %NewTime% seconds in the forums.', 'wp-security-audit-log' ) ),
					array( 8014, E_NOTICE, __( 'User created new topic', 'wp-security-audit-log' ), __( 'Created a new topic %TopicName%.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
					array( 8015, E_NOTICE, __( 'User changed status of a topic', 'wp-security-audit-log' ), __( 'Changed the status of the topic %TopicName% from %OldStatus% to %NewStatus%.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
					array( 8016, E_NOTICE, __( 'User changed type of a topic', 'wp-security-audit-log' ), __( 'Changed the type of the topic %TopicName% from %OldType% to %NewType%.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
					array( 8017, E_CRITICAL, __( 'User changed URL of a topic', 'wp-security-audit-log' ), __( 'Changed the URL of the topic %TopicName% from %OldUrl% to %NewUrl%.', 'wp-security-audit-log' ) ),
					array( 8018, E_NOTICE, __( 'User changed the forum of a topic', 'wp-security-audit-log' ), __( 'Changed the forum of the topic %TopicName% from %OldForum% to %NewForum%.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
					array( 8019, E_CRITICAL, __( 'User moved topic to trash', 'wp-security-audit-log' ), __( 'Moved the topic %TopicName% to trash.', 'wp-security-audit-log' ) ),
					array( 8020, E_WARNING, __( 'User permanently deleted topic', 'wp-security-audit-log' ), __( 'Permanently deleted the topic %TopicName%.', 'wp-security-audit-log' ) ),
					array( 8021, E_WARNING, __( 'User restored topic from trash', 'wp-security-audit-log' ), __( 'Restored the topic %TopicName% from trash.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
					array( 8022, E_NOTICE, __( 'User changed visibility of a topic', 'wp-security-audit-log' ), __( 'Changed the visibility of the topic %TopicName% from %OldVisibility% to %NewVisibility%.' . ' %EditorLinkTopic%.', 'wp-security-audit-log' ) ),
				),

				/**
				 * Alerts: WooCommerce
				 */
				__( 'WooCommerce', 'wp-security-audit-log' ) => array(
					array( 9000, E_NOTICE, __( 'User created a new product', 'wp-security-audit-log' ), __( 'Created a new product called %ProductTitle% and saved it as draft. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9001, E_NOTICE, __( 'User published a product', 'wp-security-audit-log' ), __( 'Published a product called %ProductTitle%. Product URL is %ProductUrl%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9002, E_NOTICE, __( 'User created a new product category', 'wp-security-audit-log' ), __( 'Created a new product category called %CategoryName% in WooCommerce. Product category slug is %Slug%.', 'wp-security-audit-log' ) ),
					array( 9003, E_NOTICE, __( 'User changed the category of a product', 'wp-security-audit-log' ), __( 'Changed the category of the product %ProductTitle% from %OldCategories% to %NewCategories%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9004, E_NOTICE, __( 'User modified the short description of a product', 'wp-security-audit-log' ), __( 'Modified the short description of the product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9005, E_NOTICE, __( 'User modified the text of a product', 'wp-security-audit-log' ), __( 'Modified the text of the product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9006, E_NOTICE, __( 'User changed the URL of a product', 'wp-security-audit-log' ), __( 'Changed the URL of the product %ProductTitle% from %OldUrl% to %NewUrl%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9007, E_NOTICE, __( 'User changed the Product Data of a product', 'wp-security-audit-log' ), __( 'Changed the Product Data of the product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9008, E_NOTICE, __( 'User changed the date of a product', 'wp-security-audit-log' ), __( 'Changed the date of the product %ProductTitle% from %OldDate% to %NewDate%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9009, E_NOTICE, __( 'User changed the visibility of a product', 'wp-security-audit-log' ), __( 'Changed the visibility of the product %ProductTitle% from %OldVisibility% to %NewVisibility%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9010, E_NOTICE, __( 'User modified the published product', 'wp-security-audit-log' ), __( 'Modified the published product %ProductTitle%. Product URL is %ProductUrl%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9011, E_NOTICE, __( 'User modified the draft product', 'wp-security-audit-log' ), __( 'Modified the draft product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9012, E_WARNING, __( 'User moved a product to trash', 'wp-security-audit-log' ), __( 'Moved the product %ProductTitle% to trash. Product URL was %ProductUrl%.', 'wp-security-audit-log' ) ),
					array( 9013, E_WARNING, __( 'User permanently deleted a product', 'wp-security-audit-log' ), __( 'Permanently deleted the product %ProductTitle%.', 'wp-security-audit-log' ) ),
					array( 9014, E_CRITICAL, __( 'User restored a product from the trash', 'wp-security-audit-log' ), __( 'Product %ProductTitle% has been restored from trash. View product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9015, E_NOTICE, __( 'User changed status of a product', 'wp-security-audit-log' ), __( 'Changed the status of the product %ProductTitle% from %OldStatus% to %NewStatus%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9016, E_WARNING, __( 'User changed type of a price', 'wp-security-audit-log' ), __( 'Changed the %PriceType% of the product %ProductTitle% from %OldPrice% to %NewPrice%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9017, E_WARNING, __( 'User changed the SKU of a product', 'wp-security-audit-log' ), __( 'Changed the SKU of the product %ProductTitle% from %OldSku% to %NewSku%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9018, E_CRITICAL, __( 'User changed the stock status of a product', 'wp-security-audit-log' ), __( 'Changed the stock status of the product %ProductTitle% from %OldStatus% to %NewStatus%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9019, E_WARNING, __( 'User changed the stock quantity', 'wp-security-audit-log' ), __( 'Changed the stock quantity of the product %ProductTitle% from %OldValue% to %NewValue%. View the product: %EditorLinkProduct%', 'wp-security-audit-log' ) ),
					array( 9020, E_WARNING, __( 'User set a product type', 'wp-security-audit-log' ), __( 'Set the product %ProductTitle% as %Type%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9021, E_WARNING, __( 'User changed the weight of a product', 'wp-security-audit-log' ), __( 'Changed the weight of the product %ProductTitle% from %OldWeight% to %NewWeight%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9022, E_WARNING, __( 'User changed the dimensions of a product', 'wp-security-audit-log' ), __( 'Changed the %DimensionType% dimensions of the product %ProductTitle% from %OldDimension% to %NewDimension%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9023, E_WARNING, __( 'User added the Downloadable File to a product', 'wp-security-audit-log' ), __( 'Added the Downloadable File %FileName% with File URL %FileUrl% to the product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9024, E_WARNING, __( 'User Removed the Downloadable File from a product', 'wp-security-audit-log' ), __( 'Removed the Downloadable File %FileName% with File URL %FileUrl% from the product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9025, E_WARNING, __( 'User changed the name of a Downloadable File in a product', 'wp-security-audit-log' ), __( 'Changed the name of a Downloadable File from %OldName% to %NewName% in product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9026, E_WARNING, __( 'User changed the URL of the Downloadable File in a product', 'wp-security-audit-log' ), __( 'Changed the URL of the Downloadable File %FileName% from %OldUrl% to %NewUrl% in product %ProductTitle%. View the product: %EditorLinkProduct%.', 'wp-security-audit-log' ) ),
					array( 9027, E_WARNING, __( 'User changed the Weight Unit', 'wp-security-audit-log' ), __( 'Changed the Weight Unit from %OldUnit% to %NewUnit% in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9028, E_WARNING, __( 'User changed the Dimensions Unit', 'wp-security-audit-log' ), __( 'Changed the Dimensions Unit from %OldUnit% to %NewUnit% in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9029, E_CRITICAL, __( 'User changed the Base Location', 'wp-security-audit-log' ), __( 'Changed the Base Location from %OldLocation% to %NewLocation% in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9030, E_CRITICAL, __( 'User Enabled/Disabled taxes', 'wp-security-audit-log' ), __( '%Status% taxes in the WooCommerce store.', 'wp-security-audit-log' ) ),
					array( 9031, E_CRITICAL, __( 'User changed the currency', 'wp-security-audit-log' ), __( 'Changed the currency from %OldCurrency% to %NewCurrency% in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9032, E_CRITICAL, __( 'User Enabled/Disabled the use of coupons during checkout', 'wp-security-audit-log' ), __( '%Status% the use of coupons during checkout in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9033, E_CRITICAL, __( 'User Enabled/Disabled guest checkout', 'wp-security-audit-log' ), __( '%Status% guest checkout in WooCommerce.', 'wp-security-audit-log' ) ),
					array( 9034, E_CRITICAL, __( 'User Enabled/Disabled cash on delivery', 'wp-security-audit-log' ), __( '%Status% the option Enable cash on delivery in WooCommerce.', 'wp-security-audit-log' ) ),
				),
				__( 'Yoast SEO', 'wp-security-audit-log' ) => array(
					array( 8801, E_NOTICE, __( 'User changed title of a SEO post', 'wp-security-audit-log' ), __( 'Changed the SEO title of the %PostStatus% %PostType% from %OldSEOTitle% to %NewSEOTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8802, E_NOTICE, __( 'User changed the meta description of a SEO post', 'wp-security-audit-log' ), __( 'Changed the Meta description of the %PostStatus% %PostType% titled %PostTitle% from %old_desc% to %new_desc%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8803, E_NOTICE, __( 'User changed setting to allow search engines to show post in search results of a SEO post', 'wp-security-audit-log' ), __( 'Changed the setting to allow search engines to show post in search results from %OldStatus% to %NewStatus% in the %PostStatus% %PostType% titled %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8804, E_NOTICE, __( 'User Enabled/Disabled the option for search engine to follow links of a SEO post', 'wp-security-audit-log' ), __( '%NewStatus% the option for search engine to follow links in the %PostType% titled %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8805, E_NOTICE, __( 'User set the meta robots advanced setting of a SEO post', 'wp-security-audit-log' ), __( 'Set the Meta Robots Advanced setting to %NewStatus%  in the %PostStatus% %PostType% titled %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8806, E_NOTICE, __( 'User changed the canonical URL of a SEO post', 'wp-security-audit-log' ), __( 'Changed the Canonical URL of the %PostStatus% %PostType% titled %PostTitle% from %OldCanonicalUrl% to %NewCanonicalUrl%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8807, E_NOTICE, __( 'User changed the focus keyword of a SEO post', 'wp-security-audit-log' ), __( 'Changed the focus keyword of the %PostStatus% %PostType% titled %PostTitle% from %old_keywords% to %new_keywords%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8808, E_NOTICE, __( 'User Enabled/Disabled the option Cornerston Content of a SEO post', 'wp-security-audit-log' ), __( '%Status% the option Cornerston Content on the %PostStatus% %PostType% titled %PostTitle%. %EditorLinkPost%.', 'wp-security-audit-log' ) ),
					array( 8809, E_WARNING, __( 'User changed the Title Separator setting', 'wp-security-audit-log' ), __( 'Changed the Title Separator from %old% to %new% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8810, E_WARNING, __( 'User changed the Homepage Title setting', 'wp-security-audit-log' ), __( 'Changed the Homepage Title from %old% to %new% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8811, E_WARNING, __( 'User changed the Homepage Meta description setting', 'wp-security-audit-log' ), __( 'Changed the Homepage Meta description from %old% to %new% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8812, E_WARNING, __( 'User changed the Company or Person setting', 'wp-security-audit-log' ), __( 'Changed the Company or Person setting from %old% to %new% in the YOAST SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8813, E_WARNING, __( 'User Enabled/Disabled the option Show Posts/Pages in Search Results in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the option Show %SEOPostType% in Search Results in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8814, E_WARNING, __( 'User changed the Posts/Pages title template in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( 'Changed the %SEOPostType% title template from %old% to %new% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8815, E_WARNING, __( 'User Enabled/Disabled SEO analysis in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% SEO analysis in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8816, E_WARNING, __( 'User Enabled/Disabled readability analysis in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% Readability analysis in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8817, E_WARNING, __( 'User Enabled/Disabled cornerstone content in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% Cornerstone content in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8818, E_WARNING, __( 'User Enabled/Disabled the text link counter in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the Text link counter in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8819, E_WARNING, __( 'User Enabled/Disabled XML sitemaps in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% XML Sitemaps in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8820, E_WARNING, __( 'User Enabled/Disabled ryte integration in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% Ryte Integration in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8821, E_WARNING, __( 'User Enabled/Disabled the admin bar menu in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the Admin bar menu in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8822, E_WARNING, __( 'User changed the Posts/Pages meta description template in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( 'Changed the %SEOPostType% meta description template from %old% to %new% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8823, E_WARNING, __( 'User set the option Date in Snippet Preview for Posts/Pages in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the option Date in Snippet Preview for %SEOPostType% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8824, E_WARNING, __( 'User set the option Yoast SEO Meta Box for Posts/Pages in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the option Yoast SEO Meta Box for %SEOPostType% in the Yoast SEO plugin settings.', 'wp-security-audit-log' ) ),
					array( 8825, E_WARNING, __( 'User Enabled/Disabled the advanced settings for authors in the Yoast SEO plugin settings', 'wp-security-audit-log' ), __( '%Status% the advanced settings for authors in the Yoast SEO settings.', 'wp-security-audit-log' ) ),
				),
			),
		)
	);
	// Load Custom alerts.
	load_include_custom_file( $wsal );
}
add_action( 'wsal_init', 'wsaldefaults_wsal_init' );
