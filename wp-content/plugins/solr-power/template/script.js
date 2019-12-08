var $j = jQuery.noConflict();

        function switch1() {
            if ($j('#solrconnect_single').is(':checked')) {
                $j('#solr_admin_tab2').css('display', 'block');
                $j('#solr_admin_tab2_btn').addClass('solr_admin_on');
                $j('#solr_admin_tab3').css('display', 'none');
                $j('#solr_admin_tab3_btn').removeClass('solr_admin_on');
            }
            if ($j('#solrconnect_separated').is(':checked')) {
                $j('#solr_admin_tab2').css('display', 'none');
                $j('#solr_admin_tab2_btn').removeClass('solr_admin_on');
                $j('#solr_admin_tab3').css('display', 'block');
                $j('#solr_admin_tab3_btn').addClass('solr_admin_on');
            }
        }


        function doLoad($type, $prev) {
            $j.post(solr.ajax_url, {action:'solr_options', security: solr.security, method: 'load', type: $type, prev: $prev}, function(response) {
                  var data = JSON.parse(response);
                  $j('#percentspan').text(data.percent + "%");
                  if (!data.end) {
                      doLoad(data.type, data.last);
                  } else {
                      $j('#percentspan').remove();
                      enableAll();
                  }
            		});

               // handleResults, "json");
        }

        function handleResults(data) {

            $j('#percentspan').text(data.percent + "%");
            if (!data.end) {
                doLoad(data.type, data.last);
            } else {
                $j('#percentspan').remove();
                enableAll();
            }
        }

        function disableAll() {
            $j.each(solr.post_types,function(index,value){
                $j('[name=s4wp_postload_' + value.post_type).attr('disabled', 'disabled');
            });
            $j('[name=s4wp_deleteall]').attr('disabled', 'disabled');
            $j('[name=s4wp_init_blogs]').attr('disabled', 'disabled');
            $j('[name=s4wp_optimize]').attr('disabled', 'disabled');
            $j('[name=s4wp_ping]').attr('disabled', 'disabled');
            $j('#settingsbutton').attr('disabled', 'disabled');
        }
        function enableAll() {
            $j.each(solr.post_types,function(index,value){
                $j('[name=s4wp_postload_' + value.post_type + ']').removeAttr('disabled');
            });
            $j('[name=s4wp_postload]').removeAttr('disabled');
            $j('[name=s4wp_deleteall]').removeAttr('disabled');
            $j('[name=s4wp_init_blogs]').removeAttr('disabled');
            $j('[name=s4wp_optimize]').removeAttr('disabled');
           // $j('[name=s4wp_pageload]').removeAttr('disabled');
            $j('[name=s4wp_ping]').removeAttr('disabled');
            $j('#settingsbutton').removeAttr('disabled');
        }

        $percentspan = '<span style="font-size:1.2em;font-weight:bold;margin:20px;padding:20px" id="percentspan">0%</span>';

        $j(document).ready(function() {
          switch1();

            $j.each(solr.post_types,function(index,value){
                $j('.s4wp_postload_' + value).click(function() {
                $j(this).after($percentspan);
                disableAll();
                doLoad(value, 0);
            });
            $j('.s4wp_pageload_' + value).click(function() {
                $j(this).after($percentspan);
                disableAll();
                doLoad(value, 0);
            });
            });
            
            
      });
