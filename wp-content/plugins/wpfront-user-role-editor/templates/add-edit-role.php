<?php
/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Template for WPFront User Role Editor Add Edit Role
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap role-add-new">
    <h2 id="add-new-role">
        <?php
        echo $this->role == NULL ? $this->__('Add New Role') : $this->__('Edit Role');
        if ($this->role != NULL && $this->can_create()) {
            printf('<a href="%s" class="add-new-h2">%s</a>', $this->add_new_url(), $this->__('Add New'));
        }
        ?>
    </h2>

    <?php if ($this->is_role_exists()) { ?>
        <div class="error below-h2">
            <p>
                <strong><?php echo $this->__('ERROR'); ?></strong>: <?php echo $this->__('This role already exists in this site.'); ?>
            </p>
        </div>
    <?php } ?>

    <?php if ($this->is_error()) { ?>
        <div class="error below-h2">
            <p>
                <strong><?php echo $this->__('ERROR'); ?></strong>: <?php echo $this->__('There was an unexpected error while performing this action.'); ?>
            </p>
        </div>
    <?php } ?>

    <?php
    if ($this->role == NULL) {
        printf('<p>%s</p>', $this->__('Create a brand new role and add it to this site.'));
    }
    ?>

    <form method="post" id="createuser" name="createuser" class="validate">
        <?php $this->main->create_nonce(); ?>
        <table class="form-table">
            <tbody>
                <tr class="form-field form-required <?php echo $this->is_display_name_valid() ? '' : 'form-invalid' ?>">
                    <th scope="row">
                        <label for="display_name">
                            <?php echo $this->__('Display Name'); ?> <span class="description">(<?php echo $this->__('required'); ?>)</span>
                        </label>
                    </th>
                    <td>
                        <input name="display_name" type="text" id="display_name" value="<?php echo $this->get_display_name(); ?>" aria-required="true" <?php echo $this->is_display_name_disabled() ? 'disabled' : ''; ?> />
                    </td>
                </tr>
                <tr class="form-field form-required <?php echo $this->is_role_name_valid() ? '' : 'form-invalid' ?>">
                    <th scope="row">
                        <label for="role_name">
                            <?php echo $this->__('Role Name'); ?> <span class="description">(<?php echo $this->__('required'); ?>)</span>
                        </label>
                    </th>
                    <td>
                        <input name="role_name" type="text" id="role_name" value="<?php echo $this->get_role_name(); ?>" aria-required="true" <?php echo $this->is_role_name_disabled() ? 'disabled' : ''; ?> />
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="form-table sub-head">
            <tbody>
                <tr>
                    <th class="sub-head">
            <h3> <?php echo $this->__('Capabilities'); ?></h3>
            </th>
            <td class="sub-head-controls">
                <div>
                    <select <?php echo!$this->is_editable ? 'disabled' : ''; ?>>
                        <option value=""><?php echo $this->__('Copy from'); ?></option>
                        <?php
                        foreach ($this->get_copy_from() as $key => $value) {
                            printf('<option value="%s">%s</option>', $key, $value);
                        }
                        ?>
                    </select>
                    <input type="button" id="cap_apply" name="cap_apply" class="button action" value="<?php echo $this->__('Apply'); ?>" <?php echo!$this->is_editable ? 'disabled' : ''; ?> />  
                </div>
                <div class="spacer"></div>
                <div>
                    <input type="button" class="button action chk-helpers select-all" value="<?php echo $this->__('Select All'); ?>" <?php echo!$this->is_editable ? 'disabled' : ''; ?> />               
                    <input type="button" class="button action chk-helpers select-none" value="<?php echo $this->__('Select None'); ?>" <?php echo!$this->is_editable ? 'disabled' : ''; ?> />
                </div>
            </td>
            </tr>
            </tbody>
        </table>

        <div class="metabox-holder">
            <?php
            foreach ($this->get_capability_groups() as $key => $value) {
                add_meta_box("postbox-$key", $this->postbox_title($value), array($this, 'postbox_render'), self::MENU_SLUG, 'normal', 'default', $value);
            }
            do_meta_boxes(self::MENU_SLUG, 'normal', null);
            ?>
        </div>

        <?php
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        ?>

        <p class="submit">
            <input type="submit" name="createrole" id="createusersub" class="button button-primary" value="<?php echo $this->role == NULL ? $this->__('Add New Role') : $this->__('Update Role'); ?>" <?php echo $this->is_submit_disabled() ? 'disabled' : ''; ?> />
        </p>
    </form>
    <?php $this->footer(); ?>
</div>

<script type="text/javascript">
    (function ($) {
        var roleExists = <?php echo $this->role == NULL ? 'false' : 'true'; ?>;
        var isMultisite = <?php echo $this->multisite ? 'false' : 'true'; ?>;

        function change_select_all(chk) {
            var chks = chk.closest("div.main").find("input");
            if (chks.length == chks.filter(":checked").length) {
                chk.closest("div.postbox").find("input.select-all").prop("checked", true);
            }
            else {
                chk.closest("div.postbox").find("input.select-all").prop("checked", false);
            }
        }

        $("div.role-add-new div.postbox input.select-all").click(function () {
            $(this).closest('div.postbox').find("input").prop("checked", $(this).prop("checked"));
        }).parent().click(function (event) {
            event.stopPropagation();
        });
        
        $("div.role-add-new div.postbox div.main input").click(function () {
            change_select_all($(this));
        });
        
        $("div.role-add-new table.sub-head td.sub-head-controls input.chk-helpers").click(function () {
            if ($(this).hasClass('select-all')) {
                $("div.role-add-new div.postbox").find("input:not(:disabled)").prop("checked", true);
            }
            else if ($(this).hasClass('select-none')) {
                $("div.role-add-new div.postbox").find("input:not(:disabled)").prop("checked", false);
            }
        });

        if (!roleExists) {
            $("#display_name").keyup(function () {
                if ($.trim($(this).val()) == "")
                    return;
                $("#role_name").val($.trim($(this).val()).toLowerCase().replace(/ /g, "_").replace(/\W/g, ""));
            });
            
            $("#role_name").blur(function () {
                var ele = $(this);
                var str = $.trim(ele.val()).toLowerCase();
                str = str.replace(/ /g, "_").replace(/\W/g, "");
                ele.val(str);
                if (str != "") {
                    ele.parent().parent().removeClass("form-invalid");
                }
            });
        }

        $("#display_name").blur(function () {
            if ($.trim($(this).val()) != "") {
                $(this).parent().parent().removeClass("form-invalid");
            }
            $("#role_name").blur();
        });
        
        $("#createusersub").click(function () {
            var role_name = $("#role_name");
            var display_name = $("#display_name");
            if ($.trim(role_name.val()) == "") {
                role_name.parent().parent().addClass("form-invalid");
            }

            if ($.trim(display_name.val()) == "") {
                display_name.parent().parent().addClass("form-invalid");
            }

            if ($.trim(display_name.val()) == "") {
                display_name.focus();
                return false;
            }

            if ($.trim(role_name.val()) == "") {
                role_name.focus();
                return false;
            }

            return true;
        });
        
        $("#cap_apply").click(function () {
            if ($(this).prev().val() == "")
                return;
            var button = $(this).prop("disabled", true);
            var data = {
                "action": "wpfront_user_role_editor_copy_capabilities",
                "role": $(this).prev().val(),
                "referer": <?php echo json_encode(esc_html($_SERVER['REQUEST_URI'])); ?>,
                "nonce": <?php echo json_encode(wp_create_nonce(esc_html($_SERVER['REQUEST_URI']))); ?>
            };

            data["multisite"] = isMultisite;

            $.post(ajaxurl, data, function (response) {
                $("div.role-add-new div.postbox input").prop("checked", false);
                for (m in response) {
                    change_select_all($("div.role-add-new input#" + m).prop("checked", response[m]));
                }
                button.prop("disabled", false);
            }, 'json');
        });

        $("div.role-add-new div.postbox div.main input:first-child").each(function () {
            change_select_all($(this));
        });

        //postbox
        $(function () {
            $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
            postboxes.add_postbox_toggles('<?php echo self::MENU_SLUG; ?>');
            $('div.postbox div.main.hidden').closest('div.postbox').addClass('hide-if-js');
            $('div.postbox div.main.visible').closest('div.postbox').removeClass('hide-if-js');
        });
    })(jQuery);
</script>