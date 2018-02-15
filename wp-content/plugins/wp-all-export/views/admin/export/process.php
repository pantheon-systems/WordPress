<h2 class="wpallexport-wp-notices"></h2>

<div class="inner-content wpallexport-step-6 wpallexport-wrapper">

    <div class="wpallexport-header">
        <div class="wpallexport-logo"></div>
        <div class="wpallexport-title">
            <p><?php _e('WP All Export', 'wp_all_export_plugin'); ?></p>
            <h2><?php _e('Export to XML / CSV', 'wp_all_export_plugin'); ?></h2>
        </div>
        <div class="wpallexport-links">
            <a href="http://www.wpallimport.com/support/"
               target="_blank"><?php _e('Support', 'wp_all_export_plugin'); ?></a> | <a
                href="http://www.wpallimport.com/documentation/"
                target="_blank"><?php _e('Documentation', 'wp_all_export_plugin'); ?></a>
        </div>

        <div class="clear"></div>
        <div class="processing_step_1">

            <div class="clear"></div>

            <div class="step_description">
                <h2><?php _e('Export <span id="status">in Progress...</span>', 'wp_all_export_plugin') ?></h2>
                <h3 id="process_notice"><?php _e('Exporting may take some time. Please do not close your browser or refresh the page until the process is complete.', 'wp_all_export_plugin'); ?></h3>
            </div>
            <div
                class="wpallexport_process_wrapper_<?php echo $update_previous->id; ?> wpallexport_process_parent_wrapper">
                <div class="wpallexport_processbar rad14">
                    <div class="rad14"></div>
                </div>
                <div class="export_progress">
                    <span class="left_progress"><?php _e('Time Elapsed', 'wp_all_export_plugin'); ?> <span id="then">00:00:00</span></span>
                    <span class="center_progress"><span class="percents_count">0</span>%</span>
                    <span class="right_progress"><?php _e('Exported', 'wp_all_export_plugin'); ?> <span
                            class="created_count"><?php echo $update_previous->exported; ?></span></span>
                </div>
            </div>
            <?php
            if (XmlExportWooCommerceOrder::$is_active && $update_previous->options['export_type'] == 'specific'):

                $exportList = new PMXE_Export_List();
                foreach ($exportList->getBy('parent_id', $update_previous->id)->convertRecords() as $child_export) {
                    $is_render_child_progress = true;
                    switch ($child_export->export_post_type) {
                        case 'product':
                            if (!$update_previous->options['order_include_poducts']) $is_render_child_progress = false;
                            break;
                        case 'shop_coupon':
                            if (!$update_previous->options['order_include_coupons']) $is_render_child_progress = false;
                            break;
                        case 'shop_customer':
                            if (!$update_previous->options['order_include_customers']) $is_render_child_progress = false;
                            break;
                    }

                    if (!$is_render_child_progress) continue;

                    ?>
                    <div class="clear"></div>
                    <div
                        class="wpallexport_process_wrapper_<?php echo $child_export->id; ?> wpallexport_process_child_wrapper">
                        <div class="wpallexport_processbar rad14">
                            <div class="rad14"></div>
                        </div>
                        <div class="export_progress">
							<span class="left_progress">
								<span class="center_progress">
									<span
                                        class="percents_count">0</span>%</span> <?php printf(__("Export %ss", "wp_all_export_plugin"), ucwords(str_replace("_", " ", str_replace("shop", "", $child_export->export_post_type)))); ?></span>
                            <span class="right_progress"><?php _e('Exported', 'wp_all_export_plugin'); ?> <span
                                    class="created_count">0</span></span>
                        </div>
                    </div>
                    <?php
                }

            endif;
            ?>
        </div>

        <div id="export_finished">
            <h3><?php _e('WP All Export successfully exported your data!', 'wp_all_export_plugin'); ?></h3>
            <p style="margin-bottom: 40px;"><?php _e('Download Data', 'wp_all_export_plugin'); ?></p>
            <div class="input wp_all_export_download">
                <div class="input">
                    <button class="button button-primary button-hero wpallexport-large-button download_data"
                            rel="<?php echo add_query_arg(array('action' => 'download', 'id' => $update_previous->id, '_wpnonce' => wp_create_nonce('_wpnonce-download_feed')), $this->baseUrl); ?>"><?php echo strtoupper(wp_all_export_get_export_format($update_previous->options)); ?></button>
                </div>
                <?php if (!empty($update_previous->options['split_large_exports'])): ?>
                    <div class="input" style="margin-left: 10px;">
                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('page' => 'pmxe-admin-manage', 'id' => $update_previous->id, 'action' => 'split_bundle', '_wpnonce' => wp_create_nonce('_wpnonce-download_split_bundle')), $this->baseUrl); ?>"><?php printf(__('Split %ss', 'wp_all_export_plugin'), strtoupper(wp_all_export_get_export_format($update_previous->options))); ?></button>
                    </div>
                <?php endif; ?>
                <?php if (PMXE_Export_Record::is_bundle_supported($update_previous->options)): ?>
                    <div class="input" style="margin-left: 10px;">
                        <button class="button button-primary button-hero wpallexport-large-button download_data"
                                rel="<?php echo add_query_arg(array('page' => 'pmxe-admin-manage', 'id' => $update_previous->id, 'action' => 'bundle', '_wpnonce' => wp_create_nonce('_wpnonce-download_bundle')), $this->baseUrl); ?>"><?php _e('Bundle', 'wp_all_export_plugin'); ?></button>
                        <span><?php _e('Settings & Data for WP All Import', 'wp_all_export_plugin'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <hr>
            <a href="<?php echo add_query_arg(array('page' => 'pmxe-admin-manage'), remove_query_arg(array('id', 'page'), $this->baseUrl)); ?>"
               id="manage_imports"><?php _e('Manage Exports', 'wp_all_export_plugin') ?></a>
        </div>

    </div>

    <a href="http://soflyy.com/" target="_blank"
       class="wpallexport-created-by"><?php _e('Created by', 'wp_all_export_plugin'); ?> <span></span></a>

</div>

<script type="text/javascript">
    (function ($) {
        function toHHMMSS(string) {
            var sec_num = parseInt(string, 10); // don't forget the second param
            var hours = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = sec_num - (hours * 3600) - (minutes * 60);

            if (hours < 10) {
                hours = "0" + hours;
            }
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
            return hours + ':' + minutes + ':' + seconds;

        }

        $(function () {

            $('#status').each(function () {
                var $this = $(this);
                if ($this.html().match(/\.{3}$/)) {
                    var dots = 0;
                    var status = $this.html().replace(/\.{3}$/, '');
                    var interval;
                    interval = setInterval(function () {
                        if ($this.html().match(new RegExp(status + '\\.{1,3}$', ''))) {
                            $this.html(status + '...'.substr(0, dots++ % 3 + 1));
                        } else {
                            $('#process_notice').hide();
                            clearInterval(interval);
                        }
                    }, 1000);
                }

                var then = $('#then');
                var start_date = new Date();
                var current_date = new Date();

                update = function () {
                    current_date = Date.now();
                    var duration = Math.floor((current_date - start_date) / 1000);
                    duration = toHHMMSS(duration);
                    if ($('#process_notice').is(':visible')) then.html(duration);
                };
                update();
                setInterval(update, 1000);

                interval = setInterval(function () {

                    $('div[class^=wpallexport_process_wrapper]').each(function () {
                        var percents = $(this).find('.percents_count').html();
                        $(this).find('.wpallexport_processbar div').css({'width': ((parseInt(percents) > 100 || percents == undefined) ? 100 : percents) + '%'});
                    });

                }, 1000);

                $('.wpallexport_processbar').css({'visibility': 'visible'});

            });

            var request = {
                action: 'wpallexport',
                security: wp_all_export_security
            };

            function wp_all_export_process(queue_export) {

                var $URL = ajaxurl;

                if (queue_export !== false) {
                    $URL += '?id=' + queue_export;
                }
                else {
                    if (typeof export_id != "undefined") {
                        if ($URL.indexOf("?") == -1) {
                            $URL += '?id=' + export_id;
                        }
                        else {
                            $URL += '&id=' + export_id;
                        }
                    }
                }

                $.ajax({
                    type: 'POST',
                    url: $URL,
                    data: request,
                    success: function (response) {

                        if (response === null) {
                            $('#status').html('Error');
                            window.onbeforeunload = false;
                            $('#process_notice').after(request.responseText);
                            return;
                        }

                        var $process_wrapper = $('.wpallexport_process_wrapper_' + response.export_id);

                        $process_wrapper.find('.created_count').html(response.exported);
                        $process_wrapper.find('.percents_count').html(response.percentage);
                        $process_wrapper.find('.wpallexport_processbar div').css({'width': response.percentage + '%'});

                        if (response.done) {
                            if (response.queue_export) {
                                wp_all_export_process(response.queue_export);
                            }
                            else {
                                $('#status').html('Complete');
                                window.onbeforeunload = false;

                                setTimeout(function () {

                                    $('#export_finished').fadeIn();

                                }, 1000);
                            }
                        }
                        else {
                            wp_all_export_process(response.export_id);
                        }
                    },
                    error: function (request, status, error) {
                        $('#status').html('Error');
                        window.onbeforeunload = false;
                        $('#process_notice').after(request.responseText);
                    },
                    dataType: "json"
                });
            };

            wp_all_export_process(<?php echo $update_previous->id; ?>);

            window.onbeforeunload = function () {
                return 'WARNING:\nExport process in under way, leaving the page will interrupt\nthe operation and most likely to cause leftovers in posts.';
            };

        });
    })(jQuery);

    //]]>
</script>