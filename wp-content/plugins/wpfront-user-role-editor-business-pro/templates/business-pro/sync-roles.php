<?php
if (!defined('ABSPATH')) {
    exit();
}

if ($this->step === 'sitelist') {
    $wp_list_table = new WPFront_User_Role_Editor_MS_Sync_Sites_List_Table();
    $wp_list_table->prepare_items();
    $wp_list_table->display();
} else {
    ?>

    <div class="wrap sync-roles">
        <h2>
            <?php echo $this->__('Sync Roles'); ?>
        </h2>

        <?php if ($this->error !== NULL) { ?>
            <div class="error below-h2">
                <p>
                    <strong><?php echo $this->__('ERROR'); ?></strong>: <?php echo $this->error; ?>
                </p>
            </div>
        <?php } ?>

        <?php if ($this->step === 1) { ?>
            <div class="step-div step1">
                <h4>
                    <?php echo $this->__('Step 1: Select the source site'); ?>        
                </h4>

                <?php
                $wp_list_table = new WPFront_User_Role_Editor_MS_Sync_Sites_List_Table();
                $wp_list_table->prepare_items();
                ?>
                <form action = "" method = "get" id = "ms-search-step1">
                    <input type="hidden" name="page" value="<?php echo self::MENU_SLUG; ?>" />
                    <?php $wp_list_table->search_box(__('Search Sites'), 'site'); ?>
                </form>
                <form id="form-site-list-step1" action="<?php echo $this->network_sync_url() . '&step=2'; ?>" method="post">
                    <?php $wp_list_table->display(); ?>

                    <p class="submit">
                        <input type="submit" name="next-step" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                    </p>
                </form>
            </div>

            <script type = "text/javascript">

                (function($) {
                    $("div.step1 table th:last-child, div.step1 table td:last-child").hide();
                    $('div.step1 table thead .check-column, div.step1 table tfoot .check-column').html('');
                    $('div.step1 table tbody .check-column').html('<input type="radio" name="sourceblog" />').each(function() {
                        var $this = $(this);
                        $this.children().click(function() {
                            $(this).closest('table').children().children().removeClass('selected');
                            $(this).closest('tr').addClass('selected');
                        }).val($this.parent().children(':last').text());
                    }).first().children().prop('checked', true).closest('tr').addClass('selected');
                    $('#form-site-list-step1').submit(function() {
                        var $this = $(this);
                        $this.attr('action', $this.attr('action') + '&sb=' + $this.find('input:checked').val());
                    });
                })(jQuery);

            </script>

        <?php } ?>

        <?php if ($this->step === 2) { ?>
            <div class="step-div step2">
                <h4>
                    <?php echo $this->__('Step 2: Select destination sites'); ?>        
                </h4>
                <form id="form-step-2" method="post" action="<?php echo $this->network_sync_url() . '&sb=' . $this->source_blogid . '&step=3'; ?>">
                    <p>
                        <label><input type="radio" name="blogs" value="all" checked="true" /><?php echo $this->__('All Sites'); ?></label>
                    </p>
                    <p>
                        <label><input type="radio"  name="blogs" value="selected" /><?php echo $this->__('Selected Sites'); ?></label>
                    </p>
                </form>
                <form action = "" method = "get" id = "ms-search-step2" class="selected-sites-form">
                    <?php
                    $wp_list_table = new WPFront_User_Role_Editor_MS_Sync_Sites_List_Table();
                    $wp_list_table->prepare_items();
                    $wp_list_table->search_box(__('Search Sites'), 'site');
                    ?>
                </form>
                <form id="form-site-list-step2" action="<?php echo $this->network_sync_url() . '&step=2'; ?>" method="post" class="selected-sites-form">
                    <div id="site-list-container">
                        <?php
                        $wp_list_table->display();
                        ?>
                    </div>
                </form>
                <p class="submit">
                    <input type="submit" name="next-step" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                </p>
            </div>

            <script type="text/javascript">

                (function($) {
                    var $container = $('#site-list-container');
                    var selected_blogs = [];

                    function setCheckboxes() {
                        $("div.step2 table th:last-child, div.step2 table td:last-child").hide();
                        $('div.step2 table tbody .check-column').html('<input type="checkbox" name="allblogs[]" />').each(function() {
                            var $this = $(this);
                            var val = $.trim($this.parent().children(':last').text());
                            $this.children().val(val).prop('checked', $.inArray(val, selected_blogs) > -1);
                        });
                        $('div.step2 table thead .check-column input, div.step2 table tfoot .check-column input').click(function() {
                            var checked = $(this).prop('checked');
                            $(this).closest('table').find('.check-column input').prop('checked', checked);
                        });
                    }

                    function loadSiteList(extend) {
                        var data = {
                            "action": "wpfront_user_role_editor_sync_roles_step2_site_list"
                        };

                        $.get(ajaxurl, $.extend(data, extend), function(response) {
                            $container.html(response);
                            setCheckboxes();
                        });
                    }

                    $('#ms-search-step2').submit(function() {
                        var data = {};
                        $(this).find('input').each(function() {
                            var $this = $(this);
                            data[$this.attr('name')] = $this.val();
                        });
                        loadSiteList(data);
                        return false;
                    });

                    function getParam(a, name, def) {
                        var v = def;
                        var values = $(a).attr('href').split('?')[1];
                        values = values.split('&');
                        for (var i = 0; i < values.length; i++) {
                            var m = values[i].split('=');
                            if (m[0] == name) {
                                v = m[1];
                                break;
                            }
                        }
                        return v;
                    }

                    $container.on('click', 'a', function() {
                        var $this = $(this);
                        if ($this.parent().hasClass('pagination-links') || $this.parent().hasClass('view-switch')) {
                            loadSiteList({
                                'mode': getParam(this, 'mode', 'list'),
                                's': getParam(this, 's', ''),
                                'paged': getParam(this, 'paged', 1)
                            });
                            return false;
                        }
                    });

                    $('input[name="blogs"]').click(function() {
                        var $this = $(this);
                        if ($this.prop('checked')) {
                            if ($this.val() === 'all')
                                $('form.selected-sites-form').hide();
                            else
                                $('form.selected-sites-form').show();
                        }
                    }).triggerHandler('click');

                    function add_remove_value($this) {
                        var index = $.inArray($this.val(), selected_blogs);
                        if ($this.prop('checked')) {
                            if (index === -1)
                                selected_blogs.push($this.val());
                        } else {
                            if (index > -1)
                                selected_blogs.splice(index, 1);
                        }
                    }

                    $container.on('click', 'input', function() {
                        var $this = $(this);
                        if ($this.parent().hasClass('check-column')) {
                            var tag = $this.parent().parent().parent().prop('tagName').toLowerCase();
                            if (tag === 'tbody') {
                                add_remove_value($this);
                            } else {
                                $this.closest('table').children('tbody').find('input').each(function() {
                                    add_remove_value($(this));
                                });
                            }
                        }
                    });

                    $('#next-step').click(function() {
                        $('#form-step-2')
                                .append('<input type="hidden" name="selected-blogs" value="' + selected_blogs.join(',') + '" />')
                                .submit();
                    });

                    setCheckboxes();

                })(jQuery);

            </script>
        <?php } ?>

        <?php if ($this->step === 3) { ?>
            <div class="step-div step3">
                <h4>
                    <?php echo $this->__('Step 3: Choose settings'); ?>        
                </h4>

                <form action="<?php echo $this->network_sync_url() . '&sb=' . $this->source_blogid . '&step=4'; ?>" method="post">
                    <p>
                        <label>
                            <input type="checkbox" name="add" checked="true" /><?php echo $this->__('Add roles existing only in source'); ?>       
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="overwrite" checked="true" /><?php echo $this->__('Overwrite existing roles'); ?>       
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="remove" checked="true" /><?php echo $this->__('Remove roles existing only in destination'); ?>       
                        </label>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="update-new-user-default" checked="true" /><?php echo $this->__('Update new user default role'); ?>       
                        </label>
                    </p>

                    <input type="hidden" name="blogs" value="<?php echo $this->blogs; ?>" />
                    <input type="hidden" name="selected-blogs" value="<?php echo implode(',', $this->selected_blogs); ?>" />

                    <p class="submit">
                        <input type="submit" name="next-step" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
                    </p>
                </form>
            </div>

        <?php } ?>

        <?php if ($this->step === 4) { ?>
            <div class="step-div step4">
                <h4>
                    <?php echo $this->__('Step 4: Confirm and Sync'); ?>        
                </h4>
                <div id="confirm-data-container">
                    <p>
                        <?php
                        echo $this->__('Source: ');
                        switch_to_blog($this->source_blogid);
                        echo '<strong><a target="_blank" href="' . site_url() . '">' . get_bloginfo('name') . '</a></strong>';
                        restore_current_blog();
                        ?>        
                    </p>
                    <p>
                        <?php
                        echo $this->__('Destination: ');
                        echo '<strong>' . ($this->blogs === 'selected' ? ($this->__('Selected sites') . ' [' . sprintf($this->__('%s site(s) selected'), count($this->selected_blogs)) . ']') : $this->__('All Sites')) . '</strong>';
                        ?> 
                    </p>
                    <p>
                        <?php
                        echo $this->__('Add roles existing only in source: ');
                        echo '<strong>' . ($this->add ? $this->__('Yes') : $this->__('No')) . '</strong>';
                        ?> 
                    </p>
                    <p>
                        <?php
                        echo $this->__('Overwrite existing roles: ');
                        echo '<strong>' . ($this->overwrite ? $this->__('Yes') : $this->__('No')) . '</strong>';
                        ?> 
                    </p>
                    <p>
                        <?php
                        echo $this->__('Remove roles existing only in destination: ');
                        echo '<strong>' . ($this->remove ? $this->__('Yes') : $this->__('No')) . '</strong>';
                        ?> 
                    </p>
                    <p>
                        <?php
                        echo $this->__('Update new user default role: ');
                        echo '<strong>' . ($this->update_new_user_default ? $this->__('Yes') : $this->__('No')) . '</strong>';
                        ?> 
                    </p>
                    <p class="submit">
                        <input type="submit" name="sync-roles" id="sync-roles" class="button button-primary" value="<?php echo $this->__('Sync Roles'); ?>" />
                    </p>
                </div>
                <div id="sync-response-container">

                </div>
            </div>

            <script type="text/javascript">

                (function($) {
                    var data = {
                        "source": <?php echo $this->source_blogid; ?>,
                        "blogs": '<?php echo $this->blogs; ?>',
                        "selected_blogs": [<?php echo implode(',', $this->selected_blogs); ?>],
                        "add": <?php echo $this->add ? 'true' : 'false'; ?>,
                        "overwrite": <?php echo $this->overwrite ? 'true' : 'false'; ?>,
                        "remove": <?php echo $this->remove ? 'true' : 'false'; ?>,
                        "update_new_user_default": <?php echo $this->update_new_user_default ? 'true' : 'false'; ?>,
                        "max_blogid": <?php echo $this->get_ms_max_blog_id(); ?>,
                        "count_blogs": <?php echo $this->get_ms_count_blogs(); ?>
                    };

                    var count = 1;
                    var total_count = data.blogs === 'selected' ? data.selected_blogs.length : data.count_blogs;
                    var $container = $('#sync-response-container');
                    var sync_diff = 100;

                    function sync_server(id) {
                        var ajaxdata = {
                            "action": "wpfront_user_role_editor_sync_roles_sync_blog",
                            "source": data.source,
                            "destination": id,
                            "add": data.add ? 1 : 0,
                            "overwrite": data.overwrite ? 1 : 0,
                            "remove": data.remove ? 1 : 0,
                            "update_new_user_default": data.update_new_user_default ? 1 : 0,
                            "referer": <?php echo json_encode(esc_html($_SERVER['REQUEST_URI'])); ?>,
                            "nonce": <?php echo json_encode(wp_create_nonce(esc_html($_SERVER['REQUEST_URI']))); ?>
                        };

                        $.post(ajaxurl, ajaxdata, function(response) {
                            if (response != null) {
                                count++;
                                $line.append($('<a target="_blank" href="' + response.url + '">' + response.name + '</a>'));
                                if (response.result) {
                                    $line.children().first().removeClass('fa-refresh fa-spin').addClass('fa-check fa-1');
                                    $line.append($('<span>&mdash; ' + '<?php echo $this->__('SUCCESS'); ?>' + '</span>'));
                                } else {
                                    $line.children().first().removeClass('fa-refresh fa-spin').addClass('fa-times fa-1');
                                    $line.append($('<span>&mdash; ' + '<?php echo $this->__('FAIL'); ?>' + '</span>'));
                                }
                                $line = null;
                            }

                            setTimeout(sync, sync_diff);
                        }, 'json');
                    }

                    var counter = 0;
                    var $line = null;

                    function sync() {
                        counter++;
                        if (counter > data.max_blogid)
                            return;

                        if (data.blogs === 'selected' && data.selected_blogs.length == 0)
                            return;

                        if ($line == null) {
                            $line = $('<div class="sync-info-line" />');
                            $line.append($('<i class="fa fa-refresh fa-spin"></i>'));
                            $line.append($('<span class="sync-info">' + '<?php echo $this->__('Synching site'); ?> ' + count + ' / ' + total_count + '</span>'));
                            $container.append($line);
                        }

                        if (data.blogs === 'selected') {
                            sync_server(data.selected_blogs.pop());
                        } else {
                            sync_server(counter);
                        }
                    }

                    $('#sync-roles').click(function() {
                        $('#confirm-data-container').hide();
                        $container.show();
                        sync();
                    });
                })(jQuery);

            </script>
        <?php } ?>
    </div>

    <?php
} 