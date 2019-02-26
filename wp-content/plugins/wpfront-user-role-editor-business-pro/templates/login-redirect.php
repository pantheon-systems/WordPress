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
 * Template for WPFront User Role Editor Login Redirect
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2015 WPFront.com
 */
?>

<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap login-redirect">
    <h2 id="login-redirect">
        <?php echo $this->__('Login Redirect'); ?>
        <a href="<?php echo $this->login_redirect_add_new_url(); ?>" class="add-new-h2"><?php echo $this->__('Add New'); ?></a>
    </h2>
    <?php
    if (!empty($this->success_message)) {
        ?>
        <div class="updated">
            <p><?php echo $this->success_message; ?></p>
        </div>
        <?php
    }

    if (!empty($this->error_message)) {
        ?>
        <div class="error below-h2">
            <p><?php echo $this->error_message; ?></p>
        </div>
        <?php
    }
    ?>

    <?php if ($this->get_mode() === 'ADD') { ?>
        <div id="login-redirect-custom-role-disabled" class="error below-h2 hidden">
            <p><?php echo $this->__('Custom roles not supported in free version.') . ' ' . sprintf('<a target="_blank" href="https://wpfront.com/lgnred">%s</a>', $this->__('Upgrade to Pro.')); ?></p>
        </div>
    <?php } ?>

    <?php
    if ($this->get_mode() === 'LIST') {
        require_once($this->main->pluginDIR() . 'classes/class-wpfront-user-role-editor-login-redirect-list-table.php');

        $table = new WPFront_User_Role_Editor_Login_Redirect_List_Table($this);
        $table->prepare_items();
        $table->views();
        ?>
        <form action="" method="get" class="search-form">
            <input type="hidden" name="page" value="<?php echo WPFront_User_Role_Editor_Login_Redirect::MENU_SLUG; ?>" />
            <?php $table->search_box($this->__('Search'), 'login-redirect'); ?>
        </form>

        <form id="form-login-redirect" method='post'>
            <?php
            $this->main->create_nonce('_login_redirect');
            $table->display();
            ?>
            <input type="hidden" name="mode" value="BULK-DELETE" />
        </form>
        <?php
    } elseif ($this->get_mode() === 'ADD' || $this->get_mode() === 'EDIT') {
        ?> 
        <p>
            <?php echo $this->__('Enter the URL where the user will be redirected after login or on wp-admin access.'); ?>
        </p>
        <form id="form-login-redirect" method="post" class="validate">
            <?php $this->main->create_nonce('_login_redirect'); ?>
            <table class="form-table">
                <tbody>
                    <tr class="form-required <?php echo $this->valid_role ? '' : 'form-invalid' ?>">
                        <th scope="row">
                            <?php echo $this->__('Role'); ?><span class="description"> (<?php echo $this->__('required'); ?>)</span>
                        </th>
                        <td>
                            <?php if ($this->get_mode() === 'ADD') { ?>
                                <select name="role" id="login-redirect-role">
                                    <?php
                                    foreach ($this->get_roles() as $key => $value) {
                                        ?>
                                        <option value="<?php echo $key; ?>" data-supported="<?php echo $this->role_supported($key) ? 'true' : 'false'; ?>" <?php echo $key === $this->role ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            } else {
                                global $wp_roles;
                                $roles = $wp_roles->get_names();
                                ?>
                                <select disabled="true"><option><?php echo $roles[$this->role]; ?> </option></select>
                                <input type="hidden" value="<?php echo $this->role; ?>" name="role"  />
                                <input type="hidden" value="EDIT" name="mode"  />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="form-required <?php echo $this->valid_priority ? '' : 'form-invalid' ?>">
                        <th scope="row">
                            <?php echo $this->__('Priority'); ?><span class="description"> (<?php echo $this->__('required'); ?>)</span>
                        </th>
                        <td>
                            <input class="small-text" name="priority" type="number" value="<?php echo $this->priority; ?>" aria-required="true"  />
                        </td>
                    </tr>
                    <tr class="form-required <?php echo $this->valid_url ? '' : 'form-invalid' ?>">
                        <th scope="row">
                            <?php echo $this->__('Login Redirect URL'); ?><span class="description"> (<?php echo $this->__('required'); ?>)</span>
                        </th>
                        <td>
                            <input class="regular-text" name="url" type="text" value="<?php echo $this->url; ?>" aria-required="true"  />
                            <br />
                            <span class="description"><?php echo $this->__('[Relative to home URL (recommended) or absolute URL.]'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php echo $this->__('Logout Redirect URL'); ?>
                        </th>
                        <td>
                            <input class="regular-text" name="logout_url" type="text" value="<?php echo $this->logout_url; ?>" aria-required="true"  />
                            <br />
                            <span class="description"><?php echo $this->__('[Relative to home URL (recommended) or absolute URL.]'); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php echo $this->__('Deny WP-ADMIN'); ?>
                        </th>
                        <td>
                            <input name="deny_wpadmin" type="checkbox" <?php echo $this->deny_wpadmin ? 'checked' : ''; ?>  />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php echo $this->__('Disable Toolbar'); ?>
                        </th>
                        <td>
                            <input name="disable_toolbar" type="checkbox" <?php echo $this->disable_toolbar ? 'checked' : ''; ?>  />
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" id="login-redirect-add-edit" class="button button-primary" value="<?php echo $this->__('Submit'); ?>" />
            </p>
        </form>

    <?php } elseif ($this->get_mode() === 'DELETE') { ?>
        <form method="post" action="<?php echo $this->login_redirect_url(); ?>">
            <?php $this->main->create_nonce('_login_redirect'); ?>
            <p>
                <?php echo $this->__('The following role configurations will be deleted.'); ?>
            </p>
            <ol>
                <?php
                global $wp_roles;
                $roles = $wp_roles->get_names();
                foreach ($this->role as $role) {
                    ?>
                    <li>
                        <?php echo $role . ' [' . $roles[$role] . ']'; ?>
                        <input type="hidden" name="role[]" value="<?php echo $role; ?>" />
                    </li>
                    <?php
                }
                ?>
            </ol>
            <input type="hidden" name="mode" value="CONFIRM-DELETE" />
            <p class="submit">
                <input type="submit" class="button button-secondary" value="<?php echo $this->__('Confirm Delete'); ?>" />
            </p>
        </form>
    <?php } ?>
</div>

<?php if ($this->get_mode() === 'ADD') { ?>
    <script type="text/javascript">
        (function ($) {
            var role_check = function () {
                var $option = $("#login-redirect-role option:selected");

                if ($option.length === 0)
                    return;

                if ($option.data("supported")) {
                    $("#form-login-redirect input").prop("disabled", false)
                    $("#login-redirect-custom-role-disabled").addClass("hidden");
                } else {
                    $("#form-login-redirect input").prop("disabled", true)
                    $("#login-redirect-custom-role-disabled").removeClass("hidden");
                }
            };

            $("#login-redirect-role").change(role_check);
            role_check();
        })(jQuery);
    </script>
<?php } ?>