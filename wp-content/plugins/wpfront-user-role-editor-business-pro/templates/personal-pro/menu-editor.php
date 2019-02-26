<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap menu-editor">
    <h2>
        <?php echo $this->__('Menu Editor'); ?>
    </h2>

    <?php
    if ($this->result != NULL) {
        ?>
        <div class="<?php echo $this->result->status ? 'updated' : 'error'; ?>">
            <p>
                <?php echo $this->result->message; ?>
            </p>
        </div>
        <?php
    }
    ?>

    <p>
        <?php
        echo $this->__("Select a role below to edit menu for that role. Deselect a menu from the grid to remove that menu. "
                . "Disabled menu items are already hidden, because the selected role doesn't have the capability to display that menu. "
                . "Grid displays menus which the current user has access to. ");
        ?>        
    </p>

    <form id="form-menu-editor" method="POST">

        <?php $this->main->create_nonce(); ?>

        <table class="wp-list-table form-table">
            <tr>
                <th scope="row">
                    <?php echo $this->__('Override Role'); ?>
                </th>
                <td>
                    <select id="edit_role" name="role">
                        <?php
                        foreach ($this->roles as $key => $value) {
                            $selected = '';
                            if ($key == $this->current_role->name)
                                $selected = 'selected';
                            echo "<option value='$key' $selected>$value</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo $this->__('Override Type'); ?>
                </th>
                <td>
                    <select name="override-type">
                        <option value="<?php echo self::OVERRIDE_TYPE_SOFT; ?>" <?php echo $this->override_type === self::OVERRIDE_TYPE_SOFT ? 'selected' : ''; ?>><?php echo $this->__('Soft'); ?></option>
                        <option value="<?php echo self::OVERRIDE_TYPE_HARD; ?>" <?php echo $this->override_type === self::OVERRIDE_TYPE_HARD ? 'selected' : ''; ?>><?php echo $this->__('Hard'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo $this->__('Hide New Menus'); ?>
                </th>
                <td>
                    <input name="hide-new-menus" type="checkbox" <?php echo $this->hide_new_menu ? 'checked' : ''; ?> />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo $this->__('Disable For Secondary Role'); ?>
                </th>
                <td>
                    <input name="disable-for-secondary-role" type="checkbox" <?php echo $this->disable_for_secondary_role ? 'checked' : ''; ?> />
                </td>
            </tr>
        </table>

        <p></p>

        <div class="tablenav top">
            <div class="alignleft">
                <select name="copyfrom">
                    <option>
                        <?php echo $this->__('Copy from'); ?>
                    </option>
                    <option value="_restore_">
                        [&mdash;<?php echo $this->__('Restore default'); ?>&mdash;]
                    </option>
                    <?php
                    foreach ($this->roles as $key => $value) {
                        echo "<option value='$key'>$value</option>";
                    }
                    ?>
                </select>
                <input type="submit" id="doaction" name="doaction" class="button" value="<?php echo $this->__('Apply'); ?>" />
            </div>
            <div class="alignright">
                <div class="helper-area-div">
                    <label>
                        <input id="hide-deselected-items" type="checkbox" /><?php echo $this->__('Hide deselected item(s)'); ?>
                    </label>
                </div>
                <div class="helper-area-div">
                    <label>
                        <input id="hide-disabled-items" type="checkbox" /><?php echo $this->__('Hide disabled item(s)'); ?>
                    </label>
                </div>
                <div class="helper-area-div legend-holder">
                    <div class="legend">
                        <div class="menu-active"></div>
                        <?php echo $this->__('Has Capability'); ?>
                    </div>
                    &nbsp;
                    <div class="legend">
                        <div class="menu-inactive"></div>
                        <?php echo $this->__('No Capability'); ?>
                    </div>
                </div>
            </div>
        </div>

        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th class="manage-column column-cb check-column" scope="col">
                        <label class="screen-reader-text" for="cb-select-all-1">
                            <?php echo $this->__('Select All'); ?>
                        </label>
                        <input id="cb-select-all-1" type="checkbox" />
                    </th>
                    <th class="menu-active-status"></th>
                    <th class="manage-column" scope="col">
                        <?php echo $this->__('Name'); ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo $this->__('Capability'); ?>
                    </th>
                    <th class="manage-column" scope="col">
                        <?php echo $this->__('Menu Slug'); ?>
                    </th>
                    <th class="expand-column" scope="row">
                        <i class="fa fa-minus fa-1 all"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php
                $count = 0;
                foreach ($this->menu as $menu) {
                    if ($menu != NULL) {
                        $count++;
                        ?>
                        <tr class="alternate row-<?php echo $count; ?> <?php echo $menu->disabled ? 'disabled' : ''; ?>">
                            <th class="check-column" scope="row">
                                <label class="screen-reader-text" for="cb-select-<?php echo $count; ?>">
                                    <?php printf($this->__('Select %s'), $menu->name); ?>
                                </label>
                                <input id="cb-select-<?php echo $count; ?>" name="parent-menu[<?php echo esc_attr(urlencode($menu->slug)); ?>]" class="select-menu menu-parent select-<?php echo $count; ?>" type="checkbox" data-count="<?php echo $count; ?>" <?php echo $menu->disabled ? 'disabled' : ''; ?> <?php echo $menu->has_access ? 'checked' : ''; ?> />
                            </th>
                            <td class="menu-active-status">
                                <?php if ($menu->has_capability) { ?>
                                    <div class="menu-active"></div>
                                <?php } else { ?>
                                    <div class="menu-inactive"></div>
                                <?php } ?>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $menu->name; ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $menu->capability; ?>
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    <?php echo $menu->slug; ?>
                                </strong>
                            </td>
                            <th class="expand-column" scope="row">
                                <i class="fa fa-minus fa-1"></i>
                            </th>
                        </tr>
                        <?php
                        foreach ($menu->children as $submenu) {
                            ?>
                            <tr class="sub-items row-<?php echo $count; ?> <?php echo $submenu->disabled ? 'disabled' : ''; ?>">
                                <th class="check-column" scope="row">
                                    <label class="screen-reader-text" for="cb-select-<?php echo $submenu->name; ?>">
                                        <?php printf($this->__('Select %s'), $submenu->name); ?>
                                    </label>
                                    <input id="cb-select-<?php echo $submenu->name; ?>" name="child-menu[<?php echo esc_attr(urlencode($menu->slug)); ?>][<?php echo esc_attr(urlencode($submenu->slug)); ?>]" class="select-menu" type="checkbox" data-count="<?php echo $count; ?>" <?php echo $submenu->disabled ? 'disabled' : ''; ?> <?php echo $submenu->has_access ? 'checked' : ''; ?> />
                                </th>
                                <td class="menu-active-status">
                                    <?php if ($submenu->has_capability) { ?>
                                        <div class="menu-active"></div>
                                    <?php } else { ?>
                                        <div class="menu-inactive"></div>
                                    <?php } ?>
                                </td>
                                <td>
                                    -&nbsp;
                                    <strong>
                                        <?php echo $submenu->name; ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php echo $submenu->capability; ?>
                                </td>
                                <td>
                                    <?php echo $submenu->slug; ?>
                                </td>
                                <th class="expand-column" scope="row">
                                </th>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $this->__('Save Changes'); ?>" />
        </p>
    </form>

</div>

<script type="text/javascript">

    (function($) {
        $('div.menu-editor table.wp-list-table th.expand-column i').click(function() {
            var $this = $(this);

            if ($this.hasClass('all')) {
                if ($this.hasClass('fa-minus')) {
                    $('div.menu-editor table.wp-list-table tbody th.expand-column i').removeClass('fa-plus').addClass('fa-minus').click();
                }
                else {
                    $('div.menu-editor table.wp-list-table tbody th.expand-column i').removeClass('fa-minus').addClass('fa-plus').click();
                }
            }

            $this.toggleClass('fa-minus fa-plus');

            if ($this.hasClass('fa-minus')) {
                var tr = $this.closest('tr').next();
                while (tr.hasClass('sub-items')) {
                    tr.removeClass('collapsed');
                    tr = tr.next();
                }
            }
            else {
                var tr = $this.closest('tr').next();
                while (tr.hasClass('sub-items')) {
                    tr.addClass('collapsed');
                    tr = tr.next();
                }
            }

        });

        var count = $('div.menu-editor table.wp-list-table input.select-menu').click(function() {
            var $this = $(this);
            var count = $this.data('count');
            if ($this.hasClass('select-' + count)) {
                $this.closest('table').find('tr.sub-items.row-' + count + ' input.select-menu:enabled').prop('checked', $this.prop('checked'));
            }
            else {
                if ($this.prop('checked')) {
                    $this.closest('table').find('input.select-menu.select-' + count).prop('checked', true);
                }
                else {
                    if ($this.closest('table').find('tr.sub-items.row-' + count + ' input.select-menu:checked').length == 0) {
                        $this.closest('table').find('input.select-menu.select-' + count).prop('checked', false);
                    }
                }
            }
        }).filter(':not(:checked)').length;

        $('#cb-select-all-1').click(function(event) {
            event.stopImmediatePropagation();
            var $this = $(this);

            $('div.menu-editor table.wp-list-table input.select-menu.menu-parent:enabled:visible').prop('checked', $this.prop('checked')).each(function(i, e) {
                var $e = $(e);
                var count = $e.data('count');
                if ($e.hasClass('select-' + count)) {
                    $e.closest('table').find('tr.sub-items.row-' + count + ' input.select-menu:enabled').prop('checked', $this.prop('checked'));
                }
            });
        }).prop('checked', count == 0);

        $('#edit_role').change(function() {
            var url = "<?php echo $this->get_edit_menu_url(''); ?>";
            $(location).attr("href", url + $(this).val());
        });

        $('#form-menu-editor').submit(function() {
            var $this = $(this);
            $('div.menu-editor table.wp-list-table input.select-menu').each(function(i, e) {
                var $e = $(e);
                $this.append($('<input type="hidden" />').attr('name', $e.attr('name')).attr('value', $e.prop('checked')));
                $e.attr('name', '');
            });
        });

        $('#doaction').click(function() {
            var $this = $(this);
            var $select = $this.prev();
            var index = $select.prop('selectedIndex');

            if (index == 0)
                return false;

            if (index == 1)
                return true;

            $this.prop('disabled', true);

            var data = {
                "action": "wpfront_user_role_editor_copy_menus",
                "role": $select.val()
            };
            $.post(ajaxurl, data, function(response) {
                $('#cb-select-all-1').prop('checked', false);
                $('table.wp-list-table input.select-menu').prop('checked', true);
                for (var i = 0; i < response.length; i++) {
                    var name;
                    if (response[i][1] == '')
                        name = 'parent-menu[' + response[i][0] + ']';
                    else
                        name = 'child-menu[' + response[i][1] + '][' + response[i][0] + ']';
                    $('table.wp-list-table input[name="' + name + '"]').prop('checked', false);
                }

                $this.prop('disabled', false);
            }, 'json');

            return false;
        });

        $('#hide-disabled-items').click(function() {
            if ($(this).prop('checked'))
                $('div.menu-editor table.wp-list-table tr.disabled').addClass('hide-disabled');
            else
                $('div.menu-editor table.wp-list-table tr').removeClass('hide-disabled');
        }).prop('checked', false);

        $('#hide-deselected-items').click(function() {
            if ($(this).prop('checked')) {
                $('div.menu-editor table.wp-list-table tbody input.select-menu.menu-parent:not(:checked)').each(function(i, e) {
                    var $e = $(e);
                    var count = $e.data('count');
                    $e.closest('table').find('tr.row-' + count).addClass('hide-deselected');
                });
                $('div.menu-editor table.wp-list-table tbody input.select-menu:not(:checked)').closest('tr').addClass('hide-deselected');
            }
            else
                $('div.menu-editor table.wp-list-table tr').removeClass('hide-deselected');
        }).prop('checked', false);
    })(jQuery);

</script>