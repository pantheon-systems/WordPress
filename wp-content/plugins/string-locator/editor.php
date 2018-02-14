<?php
	global $string_locator;
	$editor_content = "";
	$file = $_GET['string-locator-path'];
	$details = array();
	$this_url = admin_url( ( is_multisite() ? 'network/admin.php' : 'tools.php' ) . '?page=string-locator' );

	if ( 'core' == $_GET['file-type'] ) {
		$details = array(
			'name'        => 'WordPress',
			'version'     => get_bloginfo( 'version' ),
			'author'      => array(
				'uri'     => 'https://wordpress.org/',
				'name'    => 'WordPress'
			),
			/* translators: The WordPress description, used when a core file is opened in the editor. */
			'description' => __( 'WordPress is web software you can use to create a beautiful website or blog. We like to say that WordPress is both free and priceless at the same time.', 'string-locator' )
		);
	}
	elseif ( 'theme' == $_GET['file-type'] ) {
		$themedata = wp_get_theme( $_GET['file-reference'] );

		$details = array(
			'name'        => $themedata->get( 'Name' ),
			'version'     => $themedata->get( 'Version' ),
			'author'      => array(
				'uri'     => $themedata->get( 'AuthorURI' ),
				'name'    => $themedata->get( 'Author' )
			),
			'description' => $themedata->get( 'Description' ),
			'parent'      => $themedata->get( 'parent' )
		);
	}
	else {
		$plugins = get_plugins();

		foreach( $plugins AS $pluginname => $plugindata ) {
			$pluginref = explode( '/', $pluginname );

			if ( $pluginref[0] == $_GET['file-reference'] ) {
				$details = array(
					'name'        => $plugindata['Name'],
					'version'     => $plugindata['Version'],
					'author'      => array(
						'uri'     => $plugindata['AuthorURI'],
						'name'    => $plugindata['Author']
					),
					'description' => $plugindata['Description']
				);
			}
		}
	}

	if ( ! $string_locator->failed_edit ) {
		$readfile = fopen( $file, "r" );
		if ( $readfile )
		{
			while ( ( $readline = fgets( $readfile ) ) !== false )
			{
				$editor_content .= $readline;
			}
		}
	}
	else {
		$editor_content = stripslashes( $_POST['string-locator-editor-content'] );
	}
?>
<div class="wrap">
	<h1>
		<?php
			/* translators: Title on the editor page. */
			_e( 'String Locator - Code Editor', 'string-locator' );
		?>
		<a href="<?php echo esc_url( $this_url . '&restore=true' ); ?>" class="button button-primary"><?php _e( 'Return to search results', 'string-locator' ); ?></a>
	</h1>

	<form action="<?php echo ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" id="string-locator-edit-form" method="post">
		<div class="string-locator-edit-wrap">
			<textarea name="string-locator-editor-content" class="string-locator-editor" id="code-editor" data-editor-goto-line="<?php echo $_GET['string-locator-line']; ?>" data-editor-language="<?php echo $string_locator->string_locator_language; ?>" autofocus="autofocus"><?php echo esc_html( $editor_content ); ?></textarea>
		</div>

		<div class="string-locator-sidebar-wrap">
			<div class="string-locator-details">
				<div class="string-locator-theme-details">
					<h2><?php echo $details['name']; ?> <small>v. <?php echo $details['version']; ?></small></h2>
					<p>
						<?php _e( 'By', 'string-locator' ); ?> <a href="<?php echo $details['author']['uri']; ?>" target="_blank"><?php echo $details['author']['name']; ?></a>
					</p>
					<p>
						<?php echo $details['description'] ?>
					</p>
				</div>

				<div class="string-locator-actions">
					<?php wp_nonce_field( 'string-locator-edit_' . $_GET['edit-file'] ); ?>
					<p>
						<label>
							<input type="checkbox" name="string-locator-smart-edit" checked="checked">
							<?php _e( 'Enable a smart-scan of your code to help detect bracket mismatches before saving.', 'string-locator' ); ?>
						</label>
					</p>

					<?php if ( isset( $details['parent'] ) && ! $details['parent'] ) { ?>
					<div class="notice notice-warning inline below-h2">
						<p>
							<?php _e( 'It seems you are making direct edits to a theme.', 'string-locator' ); ?>
						</p>

						<p>
							<?php _e( 'When making changes to a theme, it is recommended you make a <a href="https://codex.wordpress.org/Child_Themes">Child Theme</a>.', 'string-locator' ); ?>
						</p>
					</div>

					<p>

					</p>
					<?php } ?>

					<?php if ( ! stristr( $file, 'wp-content' ) ) { ?>
						<div class="notice notice-warning inline below-h2">
							<p>
								<strong><?php _e( 'Warning:', 'string-locator' ); ?></strong> <?php _e( 'You appear to be editing a Core file.', 'string-locator' ); ?>
							</p>
							<p>
								<?php _e( 'Keep in mind that edits to core files will be lost when WordPress is updated. Please consider <a href="https://make.wordpress.org/core/handbook/">contributing to WordPress core</a> instead.', 'string-locator' ); ?>
							</p>
						</div>
					<?php } ?>

					<p class="submit">
						<input type="submit" name="submit" class="button button-primary" value="<?php _e( 'Save changes', 'string-locator' ); ?>">
					</p>
				</div>
			</div>

			<?php
			$function_info = get_defined_functions();
			$function_help = '';

			foreach( $function_info['user'] AS $user_func ) {
				if ( strstr( $editor_content, $user_func . '(' ) ) {
					$function_object = new ReflectionFunction( $user_func );
					$attrs = $function_object->getParameters();

					$attr_strings = array();

					foreach( $attrs AS $attr ) {
						$arg = '';

						if ( $attr->isPassedByReference() ) {
							$arg .= '&';
						}

						if ( $attr->isOptional() ) {
							$arg = sprintf(
								'[ %s$%s ]',
								$arg,
								$attr->getName()
							);
						} else {
							$arg = sprintf(
								'%s$%s',
								$arg,
								$attr->getName()
							);
						}

						$attr_strings[] = $arg;
					}

					$function_help .= sprintf(
						'<p><a href="%s" target="_blank">%s</a></p>',
						esc_url( sprintf( 'https://developer.wordpress.org/reference/functions/%s/', $user_func ) ),
						$user_func . '( ' . implode( ', ', $attr_strings ) . ' )'
					);
				}
			}
			?>

			<div class="string-locator-details">
				<div class="string-locator-theme-details">
					<h2><?php esc_html_e( 'File information', 'string-locator' ); ?></h2>

					<p>
						<?php esc_html_e( 'File location:', 'string-locator' ); ?>
						<br />
						<span class="string-locator-italics">
							<?php echo esc_html( str_replace( ABSPATH, '', $file ) ); ?>
						</span>
					</p>
				</div>
			</div>

			<?php if ( ! empty( $function_help ) ) { ?>
			<div class="string-locator-details">

				<div class="string-locator-theme-details">
					<h2><?php _e( 'WordPress Functions', 'string-locator' ); ?></h2>

					<?php echo $function_help; ?>
				</div>
			</div>
			<?php }?>
		</div>
	</form>
</div>