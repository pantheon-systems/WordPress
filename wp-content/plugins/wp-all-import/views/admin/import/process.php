<h2 class="wpallimport-wp-notices"></h2>

<div class="inner-content wpallimport-step-6 wpallimport-wrapper">
	
	<div class="wpallimport-header">
		<div class="wpallimport-logo"></div>
		<div class="wpallimport-title">
			<p><?php _e('WP All Import', 'wp_all_import_plugin'); ?></p>
			<h2><?php _e('Import XML / CSV', 'wp_all_import_plugin'); ?></h2>					
		</div>
		<div class="wpallimport-links">
			<a href="http://www.wpallimport.com/support/" target="_blank"><?php _e('Support', 'wp_all_import_plugin'); ?></a> | <a href="http://www.wpallimport.com/documentation/" target="_blank"><?php _e('Documentation', 'wp_all_import_plugin'); ?></a>
		</div>

		<div class="clear"></div>	
		<div class="processing_step_1">

			<div class="clear"></div>

			<div class="step_description">
				<h2><?php _e('Import <span id="status">in Progress</span>', 'wp_all_import_plugin') ?></h2>
				<h3 id="process_notice"><?php _e('Importing may take some time. Please do not close your browser or refresh the page until the process is complete.', 'wp_all_import_plugin'); ?></h3>		
				
			</div>		
			<div id="processbar" class="rad14">
				<div class="rad14"></div>			
			</div>			
			<div id="import_progress">
				<span id="left_progress"><?php _e('Time Elapsed', 'wp_all_import_plugin');?> <span id="then">00:00:00</span></span>
				<span id="center_progress"><span id="percents_count">0</span>%</span>
				<span id="right_progress"><?php _e('Created','wp_all_import_plugin');?> <span id="created_count"><?php echo $update_previous->created; ?></span> / <?php _e('Updated','wp_all_import_plugin');?> <span id="updated_count"><?php echo $update_previous->updated; ?></span> <?php _e('of', 'wp_all_import_plugin');?> <span id="of"><?php echo $update_previous->count; ?></span> <?php _e('records', 'wp_all_import_plugin'); ?></span>				
			</div>			
		</div>
		
		<?php $custom_type = get_post_type_object( PMXI_Plugin::$session->options['custom_type'] ); ?>		

		<div id="import_finished">			
			<h1><?php _e('Import Complete!', 'wp_all_import_plugin'); ?></h1>						
			<div class="wpallimport-content-section wpallimport-console wpallimport-complete-warning">
				<h3><?php _e('Duplicate records detected during import', 'wp_all_import_plugin'); ?><a href="#help" class="wpallimport-help" title="<?php _e('The unique identifier is how WP All Import tells two items in your import file apart. If it is the same for two items, then the first item will be overwritten when the second is imported.', 'wp_all_import_plugin') ?>">?</a></h3>
				<h4>
					<?php printf(__('The file you are importing has %s records, but WP All Import only created <span class="inserted_count"></span> %s. It detected the other records in your file as duplicates. This could be because they actually are duplicates or it could be because your Unique Identifier is not unique for each record.<br><br>If your import file has no duplicates and you want to import all %s records, you should delete everything that was just imported and then edit your Unique Identifier so it\'s unique for each item.', 'wp_all_import_plugin'), $update_previous->count, $custom_type->labels->name, $update_previous->count); ?>
				</h4>				
				<input type="button" class="button button-primary button-hero wpallimport-large-button wpallimport-delete-and-edit" rel="<?php echo add_query_arg(array('id' => $update_previous->id, 'page' => 'pmxi-admin-manage', 'action' => 'delete_and_edit'), $this->baseUrl); ?>" value="<?php _e('Delete & Edit', 'wp_all_import_plugin'); ?>"/>				
			</div>
			<div class="wpallimport-content-section wpallimport-console wpallimport-orders-complete-warning">
				<h3><?php printf(__('<span id="skipped_count">%s</span> orders were skipped during this import', 'wp_all_import_plugin'), $update_previous->skipped); ?></h3>
				<h4>
					<?php printf(__('WP All Import is unable import an order when it cannot match the products or customer specified. <a href="%s" style="margin: 0;">See the import log</a> for a list of which orders were skipped and why.', 'wp_all_import_plugin'), add_query_arg(array('id' => $update_previous->id, 'page' => 'pmxi-admin-history', 'action' => 'log', 'history_id' => PMXI_Plugin::$session->history_id, '_wpnonce' => wp_create_nonce( '_wpnonce-download_log' )), $this->baseUrl)); ?>
				</h4>				
				<input type="button" class="button button-primary button-hero wpallimport-large-button wpallimport-delete-and-edit" rel="<?php echo add_query_arg(array('id' => $update_previous->id, 'page' => 'pmxi-admin-manage', 'action' => 'delete_and_edit'), $this->baseUrl); ?>" value="<?php _e('Delete & Edit', 'wp_all_import_plugin'); ?>"/>				
			</div>
			<h3 class="wpallimport-complete-success"><?php printf(__('WP All Import successfully imported your file <span>%s</span> into your WordPress installation!','wp_all_import_plugin'), (PMXI_Plugin::$session->source['type'] != 'url') ? basename(PMXI_Plugin::$session->source['path']) : PMXI_Plugin::$session->source['path'])?></h3>						
			<?php if ($ajax_processing): ?>
			<p class="wpallimport-log-details"><?php printf(__('There were <span class="wpallimport-errors-count">%s</span> errors and <span class="wpallimport-warnings-count">%s</span> warnings in this import. You can see these in the import log.', 'wp_all_import_plugin'), 0, 0); ?></p>
			<?php elseif ((int) PMXI_Plugin::$session->errors or (int) PMXI_Plugin::$session->warnings): ?>
			<p class="wpallimport-log-details" style="display:block;"><?php printf(__('There were <span class="wpallimport-errors-count">%s</span> errors and <span class="wpallimport-warnings-count">%s</span> warnings in this import. You can see these in the import log.', 'wp_all_import_plugin'), PMXI_Plugin::$session->errors, PMXI_Plugin::$session->warnings); ?></p>
			<?php endif; ?>
			<hr>
			<a href="<?php echo add_query_arg(array('id' => $update_previous->id, 'page' => 'pmxi-admin-history'), $this->baseUrl); ?>" id="download_log"><?php _e('View Logs','wp_all_import_plugin');?></a>			
			<a href="<?php echo add_query_arg(array('page' => 'pmxi-admin-manage'), remove_query_arg(array('id','page'), $this->baseUrl)); ?>" id="manage_imports"><?php _e('Manage Imports', 'wp_all_import_plugin') ?></a>
		</div>

	</div>

	<div class="wpallimport-content-section wpallimport-speed-up-notify">
		<button class="notice-dismiss dismiss-speed-up-notify" type="button">
			<span class="screen-reader-text"><?php _e('Hide this notice.', 'wp_all_import_plugin'); ?></span>
		</button>
		<div class="wpallimport-notify-wrapper">
			<div class="found_records speedup">
				<h3><?php _e('Want to speed up your import?', 'wp_all_import_plugin');?></h3>
				<h4><?php _e("Check out our guide on increasing import speed.", "wp_all_import_plugin"); ?></h4>
			</div>		
		</div>		
		<a class="button button-primary button-hero wpallimport-large-button wpallimport-speed-up-notify-read-more" href="http://www.wpallimport.com/documentation/troubleshooting/slow-imports/" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
		<span><?php _e('opens in new tab', 'wp_all_import_plugin'); ?></span>		
	</div>

	<div class="wpallimport-modal-message rad4">

		<div class="wpallimport-content-section" style="display:block; position: relative;">
			<div class="wpallimport-notify-wrapper">
				<div class="found_records terminated">
					<h3><?php _e('Your server terminated the import process', 'wp_all_import_plugin');?></h3>
					<h4 style="width: 77%; line-height: 25px;"><?php printf(__("<a href='%s' target='_blank'>Read more</a> about how to prevent this from happening again.", "wp_all_import_plugin"), "http://www.wpallimport.com/documentation/troubleshooting/terminated-imports/"); ?></h4>
				</div>		
			</div>		
			<input type="submit" id="wpallimport-try-again" style="position: absolute; top: 30%; right: 10px; display: block; padding-top: 1px;" value="<?php _e('Continue Import','wp_all_import_plugin');?>" class="button button-primary button-hero wpallimport-large-button">
			<span class="wp_all_import_restart_import"><?php printf(__("with <span id='wpallimport-new-records-per-iteration'>%s</span> records per iteration", 'wp_all_import_plugin'), ((ceil($update_previous->options['records_per_request']/2)) ? ceil($update_previous->options['records_per_request']/2) : 1)); ?></span>
		</div>		
		
	</div>
	
	<fieldset id="logwrapper">
		<legend><?php _e('Log','wp_all_import_plugin');?></legend>
		<div id="loglist"></div>		
	</fieldset>	

	<input type="hidden" class="count_failures" value="0"/>
	<input type="hidden" class="records_per_request" value="<?php echo $update_previous->options['records_per_request']; ?>"/>
	<span id="wpallimport-error-terminated" style="display:none;">
		<div class="wpallimport-content-section" style="display:block; position: relative;">
			<div class="wpallimport-notify-wrapper">
				<div class="found_records terminated" style="background-position: 0px 50% !important;">
					<h3><?php _e('Your server terminated the import process', 'wp_all_import_plugin');?></h3>
					<h4 style="width: 78%; line-height: 25px;"><?php _e("Ask your host to check your server's error log. They will be able to determine why your server is terminating the import process.", "wp_all_import_plugin"); ?></h4>
				</div>
			</div>		
			<a style="position: absolute; top: 35%; right: 10px; display: block; padding-top: 1px;" class="button button-primary button-hero wpallimport-large-button" href="http://www.wpallimport.com/documentation/troubleshooting/terminated-imports/" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
		</div>
	</span>

	<a href="http://soflyy.com/" target="_blank" class="wpallimport-created-by"><?php _e('Created by', 'wp_all_import_plugin'); ?> <span></span></a>
	
</div>

<script type="text/javascript">
//<![CDATA[
(function($){

	window.onbeforeunload = function () {
		return 'WARNING:\nImport process in under way, leaving the page will interrupt\nthe operation and most likely to cause leftovers in posts.';
	};

	var odd = false;
	var interval;

	function write_log(){			
			
		$('.progress-msg').each(function(i){ 
												
			if ($('#loglist').find('p').length > 350) $('#loglist').html('');										

			<?php if ( ! $ajax_processing ): ?>
				if ($(this).find('.processing_info').length) {
					$('#created_count').html($(this).find('.created_count').html());
					$('#updated_count').html($(this).find('.updated_count').html());
					$('#percents_count').html($(this).find('.percents_count').html());					
				}
			<?php endif; ?>

			if ( ! $(this).find('.processing_info').length ){ 
				$('#loglist').append('<p ' + ((odd) ? 'class="odd"' : 'class="even"') + '>' + $(this).html() + '</p>');
				odd = !odd;
			}
			//$('#loglist').animate({ scrollTop: $('#loglist').get(0).scrollHeight }, 0);			
			$(this).remove();			
		});	
	}

	$('.dismiss-speed-up-notify').click(function(e){
		e.preventDefault();
    	$.post('admin.php?page=pmxi-admin-settings&action=dismiss_speed_up', {dismiss: true}, function (data) {}, 'html');
    	$('.wpallimport-speed-up-notify').addClass('dont_show_again').slideUp();
    });

    $('.wpallimport-speed-up-notify-read-more').click(function(e){
    	e.preventDefault();
    	$.post('admin.php?page=pmxi-admin-settings&action=dismiss_speed_up', {dismiss: true}, function (data) {}, 'html');
    	$('.wpallimport-speed-up-notify').addClass('dont_show_again').slideUp();
    	window.open($(this).attr('href'), '_blank');
    });

	$('#status').each(function () {

		var then = $('#then');		
		start_date = wpai_moment().sod();		
		update = function(){
			var duration = wpai_moment.duration({'seconds' : 1});
			start_date.add(duration); 
			
			if ($('#process_notice').is(':visible') && ! $('.wpallimport-modal-message').is(':visible')){
				then.html(start_date.format('HH:mm:ss'));				
			} 
		};
		update();
		setInterval(update, 1000);

		var records_per_request = $('.records_per_request').val();
		var execution_time = 0;
		
		var $this = $(this);		
		interval = setInterval(function () {															
			
			write_log();	

			var percents = $('#percents_count').html();
			$('#processbar div').css({'width': ((parseInt(percents) > 100 || percents == undefined) ? 100 : percents) + '%'});					

			execution_time++;

			if ( execution_time == 300 && parseInt(percents) < 10 && ! $('.wpallimport-speed-up-notify').hasClass('dont_show_again') && ! $('.wpallimport-modal-message').is(':visible'))
			{
				$('.wpallimport-speed-up-notify').show();
			}			

		}, 1000);
		
		$('#processbar').css({'visibility':'visible'});		

	
	
	<?php if ( $ajax_processing ): ?>

		var import_id = '<?php echo $update_previous->id; ?>';		

		function parse_element(failures){			
			
			$.get('admin.php?page=pmxi-admin-import&action=process&id=' + import_id + '&failures=' + failures + '&_wpnonce=' + wp_all_import_security, {}, function (data) {								

				// responce with error
				if (data != null && typeof data.created != "undefined"){

					$('.wpallimport-modal-message').hide();
					$('#created_count').html(data.created);	
					$('.inserted_count').html(data.created);	
					$('#updated_count').html(data.updated);
					$('#skipped_count').html(data.skipped);
					$('#warnings').html(data.warnings);
					$('#errors').html(data.errors);
					$('#percents_count').html(data.percentage);
				    $('#processbar div').css({'width': data.percentage + '%'});

				    records_per_request = data.records_per_request;

					if ( data.done ){
						clearInterval(update);		
						clearInterval(interval);	

						setTimeout(function() {
							
							$('#loglist').append(data.log);
							$('#process_notice').hide();
							$('.processing_step_1').hide();	

							// detect broken auto-created Unique ID and notify user
							<?php if ( $this->isWizard and $update_previous->options['wizard_type'] == 'new' and ! $update_previous->options['deligate']): ?>
								if ( data.imported != data.created )
								{									
									$('.wpallimport-complete-warning').show();
								}																
							<?php endif; ?>

							<?php if ( ! $update_previous->options['deligate'] and ! empty($update_previous->options['custom_type']) and $update_previous->options['custom_type'] == 'shop_order' and empty($update_previous->options['is_import_specified'])): ?>
								if ( data.skipped > 0 )
								{									
									$('.wpallimport-orders-complete-warning').show();
								}
							<?php endif; ?>


							$('#import_finished').fadeIn();								
							
							if ( parseInt(data.errors) || parseInt(data.warnings)){			
								$('.wpallimport-log-details').find('.wpallimport-errors-count').html(data.errors);
								$('.wpallimport-log-details').find('.wpallimport-warnings-count').html(data.warnings);
								$('.wpallimport-log-details').show();
							}
							
						}, 1000);						
					} 
					else
					{ 
						$('#loglist').append(data.log);
						parse_element(0);
					}

					write_log();

				} else {
					var count_failures = parseInt($('.count_failures').val());
					count_failures++;
					$('.count_failures').val(count_failures);

					if (data != null && typeof data != 'undefined' && typeof data.log != 'undefined'){
						$('#loglist').append(data.log);
						write_log();
					}

					if (data != null && typeof data != 'undefined' && parseInt(data.records_per_request)){
						records_per_request = data.records_per_request;
					}

					if (count_failures > 4 || records_per_request < 2){
						$('#process_notice').hide();
						$('.wpallimport-modal-message').html($('#wpallimport-error-terminated').html()).show();
						var errorMessage = "Import failed, please check logs";
						if (data != null && typeof data != 'undefined' && typeof data.responseText != 'undefined'){
						    errorMessage = data.responseText;
						}
						$('#status').html('Error ' + '<span class="pmxi_error_msg">' + errorMessage + '</span>');
						
						clearInterval(update);					
						window.onbeforeunload = false;

						var request = {
							action:'import_failed',			
							id: '<?php echo $update_previous->id; ?>',
							security: wp_all_import_security
					    };	

					    $.ajax({
							type: 'POST',
							url: ajaxurl,
							data: request,
							success: function(response) {
								
							},
							error: function(request) {							
								
							},			
							dataType: "json"
						});

					}
					else{
						$('#wpallimport-records-per-iteration').html(records_per_request);
						$('#wpallimport-new-records-per-iteration').html(Math.ceil(parseInt(records_per_request)/2));
						records_per_request = Math.ceil(parseInt(records_per_request)/2);
						$('.wpallimport-modal-message').show();							
						//parse_element(1);
					}				
					return;
				}								

			}, 'json').fail(function(data) { 													

				var count_failures = parseInt($('.count_failures').val());
				count_failures++;
				$('.count_failures').val(count_failures);

				if (count_failures > 4 || records_per_request < 2 ){					
					$('#process_notice').hide();					
					$('.wpallimport-modal-message').html($('#wpallimport-error-terminated').html()).show();
					
					if (data != null && typeof data != 'undefined'){
						$('#status').html('Error ' + '<span class="pmxi_error_msg">' + data.responseText + '</span>');
					}
					else{
						$('#status').html('Error');
					}
					clearInterval(update);					
					window.onbeforeunload = false;

					var request = {
						action:'import_failed',			
						id: '<?php echo $update_previous->id; ?>',
						security: wp_all_import_security						
				    };	

				    $.ajax({
						type: 'POST',
						url: ajaxurl,
						data: request,
						success: function(response) {
							
						},
						error: function(request) {							
							
						},			
						dataType: "json"
					});
				}
				else{
					$('#wpallimport-records-per-iteration').html(records_per_request);
					$('#wpallimport-new-records-per-iteration').html(Math.ceil(parseInt(records_per_request)/2));
					records_per_request = Math.ceil(parseInt(records_per_request)/2);
					$('.wpallimport-modal-message').show();					
					//parse_element(1);
				}
												
			});			
		}		
		
		$('#wpallimport-try-again').click(function(e){
			e.preventDefault();
			parse_element(1);
			$('.wpallimport-modal-message').hide();			
		});

		$('#processbar').css({'visibility':'visible'});

		parse_element(0);

	<?php else: ?>

		complete = function(){
			if ($('#status').html() == 'Complete'){
				setTimeout(function() {
					$('#process_notice').hide();
					$('.processing_step_1').hide();	
					$('#import_finished').fadeIn();								
				}, 1000);
				clearInterval(update);
				clearInterval(complete);				
			}
		};			
		setInterval(complete, 1000);
		complete();		
		
	<?php endif; ?>

	});

})(jQuery);

//]]>
</script>
