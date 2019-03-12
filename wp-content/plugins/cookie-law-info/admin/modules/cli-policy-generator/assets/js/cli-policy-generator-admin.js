(function( $ ) {
	//'use strict';
	$(function() {

		var CLI_pg=
		{
			cli_pg_lefth_tmr:null,
			cli_pg_editormode_tmr:null,
			active_elm:null,
			autosave_interval:2000,
			onPrg:false,
			Set:function()
			{
				this.leftBoxHeight();
				this.tabView();
				this.regAddNew();
				setTimeout(function(){
					CLI_pg.editOnChange();
				},1000);
				this.regAutoSaveForPreview();
				this.regSaveData();
				this.refreshCurrentPolicyPageId();
			},
			refreshCurrentPolicyPageId:function()
			{
				var data = {
			            action: 'cli_policy_generator',
			            security: cli_policy_generator.nonces.cli_policy_generator,
			            cli_policy_generator_action:'get_policy_pageid',
			        };
			        $.ajax({
						url: cli_policy_generator.ajax_url,
			            data: data,
			            //datatype:'json',
			            type: 'POST',
			            success:function(data) 
			            {
			            	data=JSON.parse(data);
							if(data.response===true)
							{
								$('[name="cli_pg_policy_pageid"]').val(data.page_id);
								if(data.page_id==0){
									$('[name="cli_pg_save_currentpage"]').hide();
								}else
								{
									$('[name="cli_pg_save_currentpage"]').show();
								}
							}
			            },
			            error:function()
			            {
			            	
			            }
					});
			},
			genContentData:function()
			{
				var content_data={};
				var v=0;
				$('.cli_pg_left .cli_pg_left_menu').each(function(){
					var elm=$(this);
					if(elm.find('[name="cli_pg_enabled_block_checkbox"]').is(':checked'))
					{
						var temp={
							'ord':v,
							'hd':elm.find('.cli_pg_content_hd').text(),
							'content':elm.find('.cli_pg_content_hid').html()
						};
						content_data[v]=temp;
						v++;
					}
				});
				return content_data;
			},
			regSaveData:function()
			{ 
				$('[name="cli_pg_save_newpage"], [name="cli_pg_save_currentpage"]').on('click',function(){
					if(CLI_pg.onPrg)
					{
						return false;
					}
					var pg_id=$(this).attr('name')=='cli_pg_save_currentpage' ? $('[name="cli_pg_policy_pageid"]').val() : 0;
					var content_data=CLI_pg.genContentData();
					var data = {
			            action: 'cli_policy_generator',
			            security: cli_policy_generator.nonces.cli_policy_generator,
			            cli_policy_generator_action:'save_contentdata',
			            content_data:content_data,
			            page_id:pg_id,
			            enable_webtofee_powered_by:($('[name="enable_webtofee_powered_by"]').is(':checked') ? 1 : 0)
			        };
			        $('.cli_pg_footer a').css({'opacity':.5,'cursor':'default'});
			        CLI_pg.onPrg=true;
			        $.ajax({
						url: cli_policy_generator.ajax_url,
			            data: data,
			            //datatype:'json',
			            type: 'POST',
			            success:function(data) 
			            {
			            	data=JSON.parse(data); console.log(data);
			            	CLI_pg.onPrg=false;
			            	$('.cli_pg_footer a').css({'opacity':1,'cursor':'pointer'});
							if(data.response===true)
							{
								cli_notify_msg.success(cli_policy_generator.labels.success);
								var edit_page_url=$("<textarea></textarea>").html(data.url).text();
								window.location.href=edit_page_url;
							}else
							{
								cli_notify_msg.error(cli_policy_generator.labels.error);
							}
			            },
			            error:function()
			            {
			            	CLI_pg.onPrg=false;
			            	$('.cli_pg_footer a').css({'opacity':1,'cursor':'pointer'});
			            	cli_notify_msg.error(cli_policy_generator.labels.error);
			            }
					});
				});
			},
			regAutoSaveForPreview:function()
			{
				var content_data=this.genContentData();
				
				var data = {
		            action: 'cli_policy_generator',
		            security: cli_policy_generator.nonces.cli_policy_generator,
		            cli_policy_generator_action:'autosave_contant_data',
		            content_data:content_data,
		            page_id:cli_policy_generator.page_id,
		            enable_webtofee_powered_by:($('[name="enable_webtofee_powered_by"]').is(':checked') ? 1 : 0)
		        };
		        $.ajax({
					url: cli_policy_generator.ajax_url,
		            data: data,
		            datatype:'json',
		            type: 'POST',
		            success:function(data) 
		            {
		               setTimeout(function(){
		               		CLI_pg.regAutoSaveForPreview();
		               },CLI_pg.autosave_interval);
		            },
		            error:function()
		            {
		            	setTimeout(function(){
		               		CLI_pg.regAutoSaveForPreview();
		               },CLI_pg.autosave_interval);
		            }
				});

			},
			editOnChange:function()
			{
				$('[name="cli_pg_hd_field"]').keyup(function(){
					CLI_pg.active_elm.find('.cli_pg_content_hd').html($(this).val());
				});
				$('#cli_pg_content').change(function(){
					CLI_pg.active_elm.find('.cli_pg_content_hid').html($(this).val());
				});
				this.cli_pg_editormode_tmr=setInterval(function(){
					var act_ed=tinymce.activeEditor;
					if(act_ed!==null)
					{
						clearInterval(CLI_pg.cli_pg_editormode_tmr);
						act_ed.on('keyup',function(){
							var act_ed_content=act_ed.getContent();
							CLI_pg.active_elm.find('.cli_pg_content_hid').html(act_ed_content);
						});
					}
				},1000);
			},
			regAddNew:function()
			{	
				$('.cli_pg_addnew_hd_btn').click(function(){
					$('.cli_pg_samplehid_block .cli_pg_left_menu').clone().insertBefore($(this));
				});
			},
			tabView:function()
			{
				$(document).on('click','.cli_pg_left .cli_pg_left_menu',function(e){
					e.stopPropagation();
					CLI_pg.active_elm=$(this);

					/* active menu */
					$('.cli_pg_left_menu').css({'background':'#f3f3f3'}).removeClass('cli_pg_menu_active');
					$(this).css({'background':'#fff'}).addClass('cli_pg_menu_active');

					/* heading */
					var hd=$(this).find('.cli_pg_content_hd').text();
					$('[name="cli_pg_hd_field"]').val(hd);

					/* description */
					var html=$(this).find('.cli_pg_content_hid').html();
					var editor_id='cli_pg_content';
					if($('#wp-'+editor_id+'-wrap').hasClass('html-active'))
					{ 
					    $('#'+editor_id+'').val(html);
					}else
					{
					    var activeEditor=tinyMCE.get(editor_id);
					    if(activeEditor!==null)
					    { 
					        $('#'+editor_id+'').val(html);
					        activeEditor.setContent(html);
					    }
					}
				});
				$(document).on('click','.cli_pg_content_delete',function(e){
					e.stopPropagation();
					var elm=$(this).parents('.cli_pg_left_menu');
					if(elm.hasClass('cli_pg_menu_active'))
					{
						elm.remove();
						$('.cli_pg_left .cli_pg_left_menu:eq(0)').click();
					}else
					{
						elm.remove();
					}
				});
				setTimeout(function(){
					$('.cli_pg_left .cli_pg_left_menu:eq(0)').click();
				},1000);
			},
			leftBoxHeight:function()
			{
				clearTimeout(CLI_pg.cli_pg_lefth_tmr);
				$(window).resize(function() {
					CLI_pg.cli_pg_lefth_tmr=setTimeout(function()
					{
						$('.cli_pg_left').css({'min-height':$('.cli_pg_right').outerHeight()});
					},500);
				});
				setTimeout(function()
				{
					$('.cli_pg_left').css({'min-height':$('.cli_pg_right').outerHeight()});
				},300);
			}
		}
		CLI_pg.Set();

	});
})( jQuery );