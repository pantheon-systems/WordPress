/* global redux_change, wp */

(function($) {
    "use strict";
    $.redux = $.redux || {};
    $(document).ready(function() {
        $.redux.wbc_importer();
    });
    $.redux.wbc_importer = function() {
        
        //demo depends
        var nectar_demo_depends = {
          "Agency" : {
            "plugins": ['js_composer_salient']
          },
          "App" : {
            "plugins": ['js_composer_salient']
          },
          "Ascend" : {
            "plugins": ['js_composer_salient','woocommerce']
          },
          "Band" : {
            "plugins": ['js_composer_salient']
          },
          "Blog-Magazine" : {
            "plugins": ['js_composer_salient']
          },
          "Blog-Ultimate" : {
            "plugins": ['js_composer_salient']
          },
          "Business" : {
            "plugins": ['js_composer_salient','woocommerce']
          },
          "Business-2" : {
            "plugins": ['js_composer_salient']
          },
          "Company-Startup" : {
            "plugins": ['js_composer_salient']
          },
          "Corporate" : {
            "plugins": ['js_composer_salient']
          },
          "Corporate-2" : {
            "plugins": ['js_composer_salient']
          },
          "Corporate-Creative" : {
            "plugins": ['js_composer_salient']
          },
          "Dark-Blog" : {
            "plugins": ['js_composer_salient']
          },
          "Ecommerce-Ultimate" : {
            "plugins": ['js_composer_salient','woocommerce','yith-woocommerce-ajax-navigation']
          },
          "Ecommerce-Creative" : {
            "plugins": ['js_composer_salient','woocommerce','yith-woocommerce-ajax-navigation', 'popup-maker']
          },
          "Old-School-Ecommerce" : {
            "plugins": ['js_composer_salient','woocommerce']
          },
          "Frostwave" : {
            "plugins": ['js_composer_salient','woocommerce']
          },
          "Fullscreen Portfolio Slider" : {
            "plugins": ['js_composer_salient']
          },
          "Landing Product" : {
            "plugins": ['js_composer_salient']
          },
          "Landing Service" : {
            "plugins": ['js_composer_salient']
          },
          "Minimal Portfolio" : {
            "plugins": ['js_composer_salient']
          },
          "Old-School-All-Purpose" : {
            "plugins": ['js_composer_salient','woocommerce']
          },
          "One-Page" : {
            "plugins": ['js_composer_salient']
          },
          "Photography" : {
            "plugins": ['js_composer_salient']
          },
          "Restaurant" : {
            "plugins": ['js_composer_salient']
          },
          "Simple Blog" : {
            "plugins": ['js_composer_salient']
          }
        };
      
        
        var $selected_demo;
        
        //open popup
        $('.wrap-importer.theme.not-imported, #wbc-importer-reimport').unbind('click').on('click', function(e) {
          
          //store demo for processing below
          $selected_demo = $(this);
          
          var $selected_demo_block = ($selected_demo.is('#wbc-importer-reimport')) ? $(this).parents('.wrap-importer') : $selected_demo;
          var $selected_demo_block_name = ($selected_demo_block.find('.theme-name').length > 0 ) ? $selected_demo_block.find('.theme-name').text() : '';
          
          //show required plugins
          $('.nectar-demo-importer-selection-modal .cnkt-plugin-installer .plugin, .nectar-demo-importer-selection-modal .cnkt-plugin-installer').hide();
          
          $.each(nectar_demo_depends,function(k,v){
            
            if(k == $selected_demo_block_name) {
              
              var plugins_required = v.plugins;
              if(typeof plugins_required != 'undefined' && plugins_required.length > 0) {
                
                for(var i=0; i<plugins_required.length; i++) {
                  
                  // if we can locate a plugin name that matches the required arr item
                  $('.nectar-demo-importer-selection-modal .cnkt-plugin-installer h4[data-slug]').each(function(){
                    if($(this).attr('data-slug') == plugins_required[i]) {
                      $(this).parents('.plugin').show();
                      $(this).parents('.cnkt-plugin-installer').show();
                    }
                  });
  
                } // for loop
                
              } // if required plugins is not empty
              
            } // if located single demo block
            
          }); // each
            
          

          //show modal
          $('.nectar-demo-importer-selection-modal .switch-options.salient').addClass('activated');
          $('.nectar-demo-importer-selection-modal-backdrop, .nectar-demo-importer-selection-modal').fadeIn(200);
          
          
          //set preview img
          $('.nectar-demo-importer-selection-modal .nectar-preview-img').css('background-image','url('+ $selected_demo_block.find('.wbc_image').attr('src') +')');
          $('.nectar-demo-importer-selection-modal .nectar-demo-preview-header h2').text($selected_demo_block_name);
          
        });
        
        //close popup
        $('.nectar-demo-importer-selection-modal a.close').unbind('click').on('click', function(e) {
          
          e.preventDefault();
          $('.nectar-demo-importer-selection-modal-backdrop, .nectar-demo-importer-selection-modal').fadeOut(250);
          
        });
        
        
        //selected classes
        $('.nectar-demo-importer-selection-modal a.theme-demo-import-option').unbind('click').on('click', function(e) {
          e.preventDefault();
          $(this).parents('.demo-importer-form-row').find('.switch-options.salient').toggleClass('activated');
        });
        
        $('.nectar-demo-importer-selection-modal .switch-options.salient').unbind('click').on('click', function(e) {
          e.preventDefault();
          $(this).toggleClass('activated');
        });
        
        //import demo
        $('.nectar-demo-importer-selection-modal a.submit').unbind('click').on('click', function(e) {
          
            e.preventDefault();
            
            if($selected_demo.length == 0) { return; }
            
            var $modal = $(this).parents('.nectar-demo-importer-selection-modal');
            
            //set parent equal to demo that was clicked before entering modal
            var parent = $selected_demo;

            var reimport = false;
            
            //set parent equal to demo that was clicked before entering modal when clicking reimport btn
            if (parent.is('#wbc-importer-reimport') ) {

                reimport = true;

                if (!$selected_demo.hasClass('rendered')) {
                    parent = $selected_demo.parents('.wrap-importer');
                }
            }
            


            if (parent.hasClass('imported') && reimport == false) {
              return;
            }
            

            if (reimport == true) {
                parent.removeClass('active imported').addClass('not-imported');
            }
            
            //return if nothing was chosen to import
            if( $modal.find('.switch-options.activated').length == 0 ) {
              return;
            }
            
            //close modal
            $('.nectar-demo-importer-selection-modal a.close').trigger('click');

            parent.find('.spinner').css('display', 'inline-block');

            parent.removeClass('active imported');

            parent.find('.importer-button').hide();

            var data = parent.data();
            
            var imported_demo = false;

            data.action = "redux_wbc_importer";
            data.demo_import_id = parent.attr("data-demo-id");
            data.nonce = parent.attr("data-nonce");
            data.type = 'import-demo-content';
            data.wbc_import = (reimport == true) ? 're-importing' : ' ';
            
            data.import_demo_content = ($modal.find('.import-nectar-theme-demo-content > .switch-options.activated').length > 0) ? 'true' : 'false';
            data.import_theme_option_settings = ($modal.find('.import-nectar-theme-option-settings > .switch-options.activated').length > 0) ? 'true' : 'false';
            data.import_demo_widgets = ($modal.find('.import-nectar-theme-demo-widgets > .switch-options.activated').length > 0) ? 'true' : 'false';

            parent.find('.wbc_image').css('opacity', '0.5');

            jQuery.post(ajaxurl, data, function(response) {
                parent.find('.wbc_image').css('opacity', '1');
                parent.find('.spinner').css('display', 'none');

                if (response.length > 0 && response.match(/Have fun!/gi) || response.length > 0 && response.match(/success/gi)) {

                    if (reimport == false) {
                        parent.addClass('rendered').find('.wbc-importer-buttons .importer-button').removeClass('import-demo-data');

                        var reImportButton = '<div id="wbc-importer-reimport" class="wbc-importer-buttons button-primary import-demo-data importer-button">Re-Import</div>';
                        parent.find('.theme-actions .wbc-importer-buttons').append(reImportButton);
                    }
                    parent.find('.importer-button:not(#wbc-importer-reimport)').removeClass('button-primary').addClass('button').text('Imported').show();
                    parent.find('.importer-button').attr('style', '');
                    parent.addClass('imported active').removeClass('not-imported');
                    imported_demo = true;
                    wbc_show_progress(data);
                    location.reload(true);
                } else {
                    parent.find('.import-demo-data').show();

                    if (reimport == true) {
                        parent.find('.importer-button:not(#wbc-importer-reimport)').removeClass('button-primary').addClass('button').text('Imported').show();
                        parent.find('.importer-button').attr('style', '');
                        parent.addClass('imported active').removeClass('not-imported');
                    }
                    
                    imported_demo = true;

                    alert('There was an error importing demo content: \n\n' + response.replace(/(<([^>]+)>)/gi, ""));
                }
            });

            function progress_bar(){
                var progress = '<div class="wbc-progress-back"><div class="wbc-progress-bar button-primary"><span class="wbc-progress-count">0%</span></div>';
                parent.prepend(progress);
                setTimeout(function(){
                    wbc_show_progress(data);
                },2000);
            }

            progress_bar();

            function wbc_show_progress( data ){
                
                data.action = "redux_wbc_importer_progress";

                if(imported_demo == false){
                    
                    jQuery.ajax({
                        url: ajaxurl,
                        data: data,
                        success:function(response){
                            var obj = jQuery.parseJSON(response);
                            if (response.length > 0 && typeof obj == 'object'){
                                var percentage = Math.floor((obj.imported_count / obj.total_post ) * 100);

                                percentage = (percentage > 0) ? percentage - 1 : percentage;
                                parent.find('.wbc-progress-bar').css('width',percentage+"%");
                                parent.find('.wbc-progress-count').text(percentage+"%");
                                setTimeout(function(){
                                    wbc_show_progress(data);
                                },2000);
                            }
                        }
                    });

                }else{
                    parent.find('.wbc-progress-back').remove();
                }
            }


            return false;
        });
    };
})(jQuery);