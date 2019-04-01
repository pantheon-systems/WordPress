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
 * Template for WPFront User Role Editor Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit();
}

@$this->main->options_page_header($this->__('WPFront User Role Editor Settings'));

$this->main->menu_walker_override_warning();
?>

<table class="form-table">
    <?php
    if ($this->multisite && wp_is_large_network()) {
        ?>
        <tr>
            <th scope="row">
                <?php echo $this->__('Enable Large Network Functionalities'); ?>
            </th>
            <td>
                <input type="checkbox" name="enable_large_network_functionalities" <?php echo $this->ms_enable_large_network_functionalities() ? 'checked' : ''; ?> />
            </td>
        </tr>
    <?php } ?>
    <tr>
        <th scope="row">
            <?php echo $this->__('Display Deprecated Capabilities'); ?>
        </th>
        <td>
            <input type="checkbox" name="display_deprecated" <?php echo $this->display_deprecated() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->__('Remove Non-Standard Capabilities on Restore'); ?>
        </th>
        <td>
            <input type="checkbox" name="remove_nonstandard_capabilities_restore" <?php echo $this->remove_nonstandard_capabilities_restore() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->__('Override Edit Permissions'); ?>
        </th>
        <td>
            <input type="checkbox" name="override_edit_permissions" <?php echo $this->override_edit_permissions() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->__('Disable Navigation Menu Permissions'); ?>
        </th>
        <td>
            <input type="checkbox" name="disable_navigation_menu_permissions" <?php echo $this->disable_navigation_menu_permissions() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <?php echo $this->__('Override Navigation Menu Permissions'); ?>
        </th>
        <td>
            <input type="checkbox" name="override_navigation_menu_permissions" <?php echo $this->override_navigation_menu_permissions() ? 'checked' : ''; ?> />
        </td>
    </tr>
    <?php if ($this->main->enable_pro_only_options() && !$this->multisite) { ?>
        <tr>
            <th scope="row">
                <?php echo $this->__('Customize Permissions (custom post types)'); ?>
            </th>
            <td>
                <?php
                $post_types = $this->get_custom_post_type_list();
                if (empty($post_types))
                    echo $this->__('No customizable post types found.');
                else {
                    foreach ($post_types as $key => $value) {
                        ?>
                        <div class="options-list">
                            <label>
                                <input name="custom-post-types" type="checkbox" value="<?php echo $key; ?>" <?php echo $value->enabled ? 'checked' : ''; ?> />
                                <?php echo $this->__($value->label); ?>
                            </label>
                        </div>
                        <?php
                    }
                }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <?php echo $this->__('Disable Extended Permissions (post types)'); ?>
            </th>
            <td>
                <?php
                $post_types = $this->get_extendable_post_types();
                
                foreach ($post_types as $key => $value) {
                    ?>
                    <div class="options-list">
                        <label>
                            <input name="extendable-post-types" type="checkbox" value="<?php echo $key; ?>" <?php echo $value->enabled ? 'checked' : ''; ?> />
                            <?php echo $this->__($value->label); ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
        <?php
    }
    if ($this->main->enable_multisite_only_options($this->multisite)) {
        ?>
        <tr>
            <th scope="row">
                <?php echo $this->__('Remove Data on Uninstall'); ?>
            </th>
            <td>
                <input type="checkbox" name="remove_data_on_uninstall" <?php echo $this->remove_data_on_uninstall() ? 'checked' : ''; ?> />
            </td>
        </tr>
    <?php } ?>
</table>

<input type="hidden" name="nonce" value="<?php echo wp_create_nonce($_SERVER['REQUEST_URI']); ?>" />
<input type="hidden" name="referer" value="<?php echo esc_html($_SERVER['REQUEST_URI']); ?>" />

<?php if ($this->multisite) {
    ?>
    <input type="hidden" name="multisite" value="true" />
<?php }
?>

<?php @$this->main->options_page_footer('user-role-editor-plugin-settings/', 'user-role-editor-plugin-faq/'); ?>

<script type="text/javascript">
    (function ($) {
        $("#wpfront-user-role-editor-options #submit").click(function () {
            $(this).prop("disabled", true);

            var fields = $("#wpfront-user-role-editor-options form").find("input");
            var data = {};
            fields.each(function (i, e) {
                var ele = $(e);
                if (ele.attr("type") == "checkbox") {
                    if (ele.attr("name") == "custom-post-types" || ele.attr("name") == "extendable-post-types") {
                        data[ele.attr("name") + "[" + ele.val() + "]"] = ele.prop("checked");
                    }
                    else {
                        data[ele.attr("name")] = ele.prop("checked");
                    }
                }
                else
                    data[ele.attr("name")] = ele.val();
            });
            data["action"] = "wpfront_user_role_editor_update_options";

            $.post(ajaxurl, data, function (url) {
                $(location).attr("href", url);
            });

            return false;
        });
    })(jQuery);
</script>
